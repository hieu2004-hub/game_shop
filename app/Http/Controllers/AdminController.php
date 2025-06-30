<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Warehouse;
use App\Models\ProductWarehouseStock;
use App\Models\ProductInstance;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // =====================================================================
        // 1. ĐƠN HÀNG CẦN XỬ LÝ
        // =====================================================================
        $pendingOrdersCount = Order::where('status', 'Chờ Xử Lý')->count();

        // =====================================================================
        // 2. TÍNH TOÁN DOANH THU & LỢI NHUẬN THEO THỜI GIAN
        // Logic mới: Chỉ tính trên các đơn hàng có trạng thái "Đã Nhận Được Hàng"
        // =====================================================================

        // Hàm closure để tái sử dụng logic tính toán
        $calculateMetrics = function ($query) {
            $items = $query->get();
            $revenue = 0;
            $profit = 0;
            foreach ($items as $item) {
                $itemRevenue = $item->quantity * $item->price;
                $revenue += $itemRevenue;
                if ($item->import_price_at_sale > 0) {
                    $profit += $item->quantity * ($item->price - $item->import_price_at_sale);
                }
            }
            return ['revenue' => $revenue, 'profit' => $profit];
        };

        // --- Doanh thu & Lợi nhuận HÔM NAY ---
        $todayQuery = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'Đã Nhận Được Hàng')
                ->whereDate('updated_at', Carbon::today()); // Dựa vào ngày đơn hàng được cập nhật sang trạng thái cuối
        });
        $todayMetrics = $calculateMetrics($todayQuery);
        $dailyRevenue = $todayMetrics['revenue'];
        $dailyProfit = $todayMetrics['profit'];


        // --- Doanh thu & Lợi nhuận THÁNG NÀY ---
        $monthlyQuery = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'Đã Nhận Được Hàng')
                ->whereMonth('updated_at', Carbon::now()->month)
                ->whereYear('updated_at', Carbon::now()->year);
        });
        $monthlyMetrics = $calculateMetrics($monthlyQuery);
        $monthlyRevenue = $monthlyMetrics['revenue'];
        $monthlyProfit = $monthlyMetrics['profit'];

        // =====================================================================
        // 3. CHUẨN BỊ DỮ LIỆU CHO BIỂU ĐỒ (Doanh thu 7 ngày gần nhất)
        // =====================================================================
        $chartData = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'Đã Nhận Được Hàng')
                ->where('updated_at', '>=', Carbon::now()->subDays(6)); // Lấy dữ liệu từ 6 ngày trước đến nay
        })
            ->select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('SUM(price * quantity) as daily_revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('daily_revenue', 'date');

        // Tạo mảng đầy đủ cho 7 ngày, điền 0 vào những ngày không có doanh thu
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d/m'); // Định dạng ngày cho nhãn biểu đồ
            $chartValues[] = $chartData->get($dateString, 0); // Lấy doanh thu của ngày, nếu không có thì mặc định là 0
        }

        // =====================================================================
        // 4. TRUYỀN DỮ LIỆU SANG VIEW
        // =====================================================================
        return view('admin.dashBoard', compact(
            'pendingOrdersCount',
            'dailyRevenue',
            'dailyProfit',
            'monthlyRevenue',
            'monthlyProfit',
            'chartLabels',
            'chartValues'
        ));
    }
    public function profile()
    {
        // Lấy thông tin của người dùng admin hiện tại
        $admin = Auth::user();
        return view('admin.adminProfile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'userName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'current_password' => 'nullable|required_with:new_password|current_password', // Xác thực mật khẩu hiện tại
            'new_password' => 'nullable|min:8|confirmed', // Mật khẩu mới và xác nhận
        ], [
            'userName.required' => 'Tên người dùng không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác.',
            'current_password.required_with' => 'Vui lòng nhập mật khẩu hiện tại nếu bạn muốn thay đổi mật khẩu mới.',
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        // Cập nhật thông tin cơ bản
        $admin->userName = $request->userName;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->address = $request->address;

        // Cập nhật mật khẩu nếu có
        if ($request->filled('new_password')) {
            $admin->password = $request->new_password; // Will be hashed by the model's mutator
        }

        $admin->save();

        toastr()->success('Thông tin tài khoản đã được cập nhật thành công!');
        return redirect()->back();
    }

    public function adminSearchProducts(Request $request)
    {
        $search = $request->search;
        $products = Product::with('warehouseStocks.warehouse')
            ->where('productName', 'like', '%' . $search . '%')
            ->orWhere('productCategory', 'like', '%' . $search . '%')
            ->orWhere('productBrand', 'like', '%' . $search . '%')
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        // THÊM: Lấy danh sách kho để truyền vào modal cho trang tìm kiếm
        $warehouses = Warehouse::orderBy('name')->get();

        return view('admin.adminSearch', compact('products', 'warehouses'));
    }

    public function addProduct()
    {
        return view('admin.addProduct');
    }

    public function createProduct(Request $request)
    {
        $data = new Product;
        $data->productName = $request->productName;
        $data->productPrice = $request->productPrice;
        $data->productCategory = $request->productCategory;
        $data->productDescription = $request->productDescription;
        $data->productBrand = $request->productBrand;
        $data->is_warrantable = $request->has('is_warrantable');
        $data->default_warranty_months = $request->default_warranty_months;
        $image = $request->productImage;
        if ($image) {
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $request->productImage->move('Product Image', $filename);
            $data->productImage = $filename;
        }
        $data->save();
        toastr()->timeOut(5000)->closeButton()->addSuccess('Sản phẩm đã được thêm thành công!');

        // SỬA: Thay vì redirect thường, chúng ta redirect với session flash
        // để kích hoạt modal nhập kho ở trang danh sách sản phẩm.
        return redirect()->route('admin.viewProduct');
    }

    public function viewProduct()
    {
        $products = Product::with('warehouseStocks.warehouse')
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        // THÊM: Lấy danh sách kho để truyền vào modal
        $warehouses = Warehouse::orderBy('name')->get();

        return view('admin.viewProduct', compact('products', 'warehouses'));
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            ProductWarehouseStock::where('product_id', $id)->delete();
            $product->delete();
            toastr()->timeOut(5000)->closeButton()->addSuccess('Product Deleted Successfully!');
        } else {
            toastr()->timeOut(5000)->closeButton()->addError('Sản phẩm không tồn tại!');
        }
        return redirect()->back();
    }
    public function editProduct($id)
    {
        $product = Product::find($id);
        return view('admin.updateProduct', compact('product'));
    }
    public function updateProduct($id, Request $request)
    {
        $data = Product::find($id);
        $data->productName = $request->productName;
        $data->productPrice = $request->productPrice;
        $data->productCategory = $request->productCategory;
        $data->productDescription = $request->productDescription;
        $data->productBrand = $request->productBrand;
        $data->is_warrantable = $request->has('is_warrantable');
        $data->default_warranty_months = $request->default_warranty_months;
        $image = $request->productImage;
        if ($image) {
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $request->productImage->move('Product Image', $filename);
            $data->productImage = $filename;
        }
        $data->save();
        toastr()->timeOut(5000)->closeButton()->addSuccess('Product Updated Successfully!');
        return redirect('viewProduct');
    }
    public function viewOrder(Request $request)
    {
        $query = Order::orderBy('updated_at', 'desc')->with(['user']);

        // 1. Lọc theo trạng thái xử lý
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 2. Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // 3. SỬA: Logic tìm kiếm thông minh hơn
        if ($request->filled('search')) {
            $search = $request->input('search');
            $searchId = ltrim($search, '#'); // Bỏ dấu # nếu có

            // Kiểm tra xem chuỗi tìm kiếm (sau khi bỏ #) có phải là một số nguyên hay không
            if (ctype_digit($searchId)) {
                // --- TRƯỜNG HỢP 1: Người dùng nhập số (tìm kiếm chính xác) ---
                // Ưu tiên tìm chính xác ID hoặc chính xác SĐT
                $query->where(function ($q) use ($searchId) {
                    $q->where('id', '=', $searchId) // Tìm ID chính xác
                        ->orWhere('phone', '=', $searchId); // Tìm SĐT chính xác
                });
            } else {
                // --- TRƯỜNG HỢP 2: Người dùng nhập chữ (tìm kiếm tương đối) ---
                // Tìm kiếm tương đối trên các trường văn bản
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            }
        }

        $orders = $query->paginate(6)->withQueryString();

        return view('admin.order', compact('orders'));
    }

    public function showOrderDetail($id)
    {
        $order = Order::with(['orderItems.product', 'orderItems.warehouse'])->find($id);

        if (!$order) {
            toastr()->timeOut(5000)->closeButton()->addError('Đơn hàng không tồn tại hoặc đã bị xóa!');
            return redirect()->route('admin.viewOrders');
        }

        return view('admin.orderDetail', compact('order'));
    }

    public function cancelOrder($id)
    {
        $order = Order::find($id);

        if (!$order) {
            toastr()->error('Đơn hàng không tồn tại!');
            return redirect()->back();
        }

        if ($order->status == 'Hủy Đơn Hàng' || $order->status == 'Đã Nhận Được Hàng') {
            toastr()->info('Không thể hủy đơn hàng ở trạng thái này.');
            return redirect()->back();
        }

        if ($order->payment_status == 'Đã thanh toán') {
            $order->status = 'Hủy Đơn Hàng';
            $order->payment_status = 'Chờ hoàn tiền';
            $order->save();
            toastr()->success('Đơn hàng đã được hủy. Vui lòng tiến hành hoàn tiền cho khách hàng.');
        } else {
            DB::transaction(function () use ($order) {
                $order->status = 'Hủy Đơn Hàng';
                $order->payment_status = 'Đã hủy';
                $order->save();

                foreach ($order->orderItems as $item) {
                    $stockEntry = ProductWarehouseStock::where('product_id', $item->productID)
                        ->where('warehouse_id', $item->warehouse_id_at_sale)
                        ->where('batch_identifier', $item->batch_identifier_at_sale)
                        ->first();
                    if ($stockEntry) {
                        $stockEntry->quantity += $item->quantity;
                        $stockEntry->save();
                    }
                    $item->productInstances()->delete();
                }
            });
            toastr()->success('Đơn hàng đã được hủy thành công và hàng đã được trả về kho.');
        }

        return redirect()->back();
    }

    public function confirmOrder($id)
    {
        $order = Order::find($id);
        if ($order && $order->status == 'Chờ Xử Lý') {
            $order->status = 'Đã Xác Nhận';
            $order->save();
            toastr()->success('Đơn hàng đã được xác nhận thành công!');
        } else {
            toastr()->error('Không thể xác nhận đơn hàng này!');
        }
        return redirect()->back();
    }

    public function confirmRefund($id)
    {
        $order = Order::find($id);

        if (!$order) {
            toastr()->error('Đơn hàng không tồn tại!');
            return redirect()->back();
        }

        if ($order->payment_status !== 'Chờ hoàn tiền') {
            toastr()->error('Không thể thực hiện hành động này cho đơn hàng.');
            return redirect()->back();
        }

        DB::transaction(function () use ($order) {
            $order->payment_status = 'Đã hoàn tiền';
            $order->save();

            foreach ($order->orderItems as $item) {
                $stockEntry = ProductWarehouseStock::where('product_id', $item->productID)
                    ->where('warehouse_id', $item->warehouse_id_at_sale)
                    ->where('batch_identifier', $item->batch_identifier_at_sale)
                    ->first();
                if ($stockEntry) {
                    $stockEntry->quantity += $item->quantity;
                    $stockEntry->save();
                }
                $item->productInstances()->delete();
            }
        });

        toastr()->success('Đã xác nhận hoàn tiền thành công. Hàng đã được trả về kho.');
        return redirect()->back();
    }

    public function confirmPayment($id)
    {
        $order = Order::find($id);

        if (!$order) {
            toastr()->error('Đơn hàng không tồn tại!');
            return redirect()->back();
        }

        if ($order->payment_method == 'Tiền mặt' && $order->payment_status == 'Chưa thanh toán') {
            $order->payment_status = 'Đã thanh toán';
            $order->save();
            toastr()->success('Đã xác nhận thanh toán cho đơn hàng thành công!');
        } else {
            toastr()->error('Không thể thực hiện hành động này cho đơn hàng.');
        }

        return redirect()->back();
    }

    public function viewWarehouses()
    {
        $warehouses = Warehouse::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.viewWarehouses', compact('warehouses'));
    }

    public function addWarehouse()
    {
        return view('admin.addWarehouse');
    }

    public function createWarehouse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:warehouses',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Warehouse::create($request->all());

        toastr()->timeOut(5000)->closeButton()->addSuccess('Kho hàng đã được thêm thành công!');
        return redirect()->route('admin.viewWarehouses');
    }

    public function editWarehouse($id)
    {
        $warehouse = Warehouse::find($id);
        if (!$warehouse) {
            toastr()->timeOut(5000)->closeButton()->addError('Kho hàng không tồn tại!');
            return redirect()->route('admin.viewWarehouses');
        }
        return view('admin.updateWarehouse', compact('warehouse'));
    }

    public function updateWarehouse(Request $request, $id)
    {
        $warehouse = Warehouse::find($id);
        if (!$warehouse) {
            toastr()->timeOut(5000)->closeButton()->addError('Kho hàng không tồn tại!');
            return redirect()->route('admin.viewWarehouses');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name,' . $id,
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $warehouse->update($request->all());

        toastr()->timeOut(5000)->closeButton()->addSuccess('Kho hàng đã được cập nhật thành công!');
        return redirect()->route('admin.viewWarehouses');
    }

    public function deleteWarehouse($id)
    {
        $warehouse = Warehouse::find($id);
        if (!$warehouse) {
            toastr()->timeOut(5000)->closeButton()->addError('Kho hàng không tồn tại!');
            return redirect()->back();
        }

        $stockCount = ProductWarehouseStock::where('warehouse_id', $id)->sum('quantity');
        if ($stockCount > 0) {
            toastr()->timeOut(5000)->closeButton()->addError('Không thể xóa kho này vì vẫn còn ' . $stockCount . ' sản phẩm tồn kho. Vui lòng chuyển hoặc xóa hết sản phẩm trước.');
            return redirect()->back();
        }

        $warehouse->delete();

        toastr()->timeOut(5000)->closeButton()->addSuccess('Kho hàng đã được xóa thành công!');
        return redirect()->back();
    }

    // SỬA: Cập nhật phương thức để nhận ID sản phẩm từ URL
    public function receiveProductForm(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $product = null; // Khởi tạo sản phẩm là null

        // Kiểm tra nếu có product_id được truyền qua URL
        if ($request->has('product_id')) {
            $product = Product::find($request->product_id);
        }

        // Truyền biến product sang view
        return view('admin.receiveProduct', compact('warehouses', 'product'));
    }

    public function receiveProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            // Đổi tên 'import_price' thành 'total_import_price' để rõ ràng hơn
            'total_import_price' => 'required|numeric|min:0',
            'batch_identifier' => 'nullable|string|max:255',
            'received_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $quantity = (int) $request->quantity;
        $totalImportPrice = (float) $request->total_import_price;

        // TÍNH TOÁN LẠI: Lấy giá nhập trên mỗi sản phẩm
        $unitImportPrice = ($quantity > 0) ? $totalImportPrice / $quantity : 0;

        $batchIdentifier = $request->batch_identifier ?: 'BATCH_' . time() . '_' . uniqid();

        try {
            $stockEntry = ProductWarehouseStock::firstOrNew([
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'batch_identifier' => $batchIdentifier,
            ]);

            // Nếu là lô mới, khởi tạo số lượng
            if (!$stockEntry->exists) {
                $stockEntry->quantity = 0;
            }

            $stockEntry->quantity += $quantity;
            // LƯU GIÁ TRÊN TỪNG SẢN PHẨM, KHÔNG LƯU TỔNG GIÁ LÔ
            $stockEntry->import_price = $unitImportPrice;
            $stockEntry->received_date = $request->received_date ?: now();
            $stockEntry->notes = $request->notes;
            $stockEntry->save();

            toastr()->timeOut(5000)->closeButton()->addSuccess('Sản phẩm đã được nhập kho thành công!');
        } catch (\Exception $e) {
            \Log::error('Lỗi khi nhập kho sản phẩm: ' . $e->getMessage());
            toastr()->timeOut(5000)->closeButton()->addError('Có lỗi xảy ra khi nhập kho sản phẩm: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function searchProductsAjax(Request $request)
    {
        $search = $request->get('q');
        $products = [];

        if ($search) {
            $products = Product::where('productName', 'LIKE', '%' . $search . '%')
                ->whereDoesntHave('warehouseStocks', function ($query) {
                    $query->where('quantity', '>', 0);
                })
                ->select('id', 'productName')
                ->get();
        }

        return response()->json($products);
    }

    public function viewWarehouseStock(Request $request, $warehouse_id)
    {
        $warehouse = Warehouse::find($warehouse_id);
        if (!$warehouse) {
            toastr()->timeOut(5000)->closeButton()->addError('Kho hàng không tồn tại!');
            return redirect()->route('admin.viewWarehouses');
        }

        // Lấy từ khóa tìm kiếm từ request
        $search = $request->input('search');

        // Bắt đầu query
        $query = ProductWarehouseStock::where('warehouse_id', $warehouse_id);

        // Nếu có từ khóa tìm kiếm, lọc theo tên sản phẩm
        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('productName', 'LIKE', '%' . $search . '%');
            });
        }

        // Lấy kết quả và phân trang
        $stockEntries = $query->with('product')
            ->orderBy('received_date', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]); // Giữ lại từ khóa search khi chuyển trang

        // Truyền cả $search sang view
        return view('admin.viewWarehouseStock', compact('warehouse', 'stockEntries', 'search'));
    }

    public function editStockEntry($stock_id)
    {
        $stockEntry = ProductWarehouseStock::find($stock_id);
        if (!$stockEntry) {
            toastr()->timeOut(5000)->closeButton()->addError('Lô hàng không tồn tại!');
            return redirect()->back();
        }
        $product = Product::find($stockEntry->product_id);
        $warehouse = Warehouse::find($stockEntry->warehouse_id);

        return view('admin.updateStockEntry', compact('stockEntry', 'product', 'warehouse'));
    }

    public function updateStockEntry(Request $request, $stock_id)
    {
        $stockEntry = ProductWarehouseStock::find($stock_id);
        if (!$stockEntry) {
            toastr()->timeOut(5000)->closeButton()->addError('Lô hàng không tồn tại!');
            return redirect()->back();
        }

        $request->validate([
            'quantity' => 'required|integer|min:0',
            'import_price' => 'required|numeric|min:0',
            'batch_identifier' => 'nullable|string|max:255',
            'received_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $stockEntry->quantity = $request->quantity;
            $stockEntry->import_price = $request->import_price;
            $stockEntry->batch_identifier = $request->batch_identifier ?: $stockEntry->batch_identifier;
            $stockEntry->received_date = $request->received_date ?: $stockEntry->received_date;
            $stockEntry->notes = $request->notes;
            $stockEntry->save();

            toastr()->timeOut(5000)->closeButton()->addSuccess('Thông tin lô hàng đã được cập nhật thành công!');
        } catch (\Exception $e) {
            \Log::error('Lỗi khi cập nhật lô hàng: ' . $e->getMessage());
            toastr()->timeOut(5000)->closeButton()->addError('Có lỗi xảy ra khi cập nhật lô hàng: ' . $e->getMessage());
        }

        return redirect()->route('admin.viewWarehouseStock', $stockEntry->warehouse_id);
    }

    public function reduceStockEntry(Request $request, $stock_id)
    {
        $stockEntry = ProductWarehouseStock::find($stock_id);
        if (!$stockEntry) {
            toastr()->timeOut(5000)->closeButton()->addError('Lô hàng không tồn tại!');
            return redirect()->back();
        }

        $request->validate([
            'quantity_to_reduce' => 'required|integer|min:1',
            'reduction_notes' => 'nullable|string|max:1000',
        ]);

        $quantityToReduce = $request->quantity_to_reduce;

        if ($quantityToReduce > $stockEntry->quantity) {
            toastr()->timeOut(5000)->closeButton()->addError('Số lượng muốn trả vượt quá số lượng tồn kho hiện có của lô hàng!');
            return redirect()->back();
        }

        try {
            $stockEntry->quantity -= $quantityToReduce;
            $stockEntry->notes = $stockEntry->notes . "\n[Trả " . $quantityToReduce . " SP]: " . ($request->reduction_notes ?: 'Không có ghi chú') . " vào " . now()->format('Y-m-d H:i:s');
            $stockEntry->save();

            toastr()->timeOut(5000)->closeButton()->addSuccess('Số lượng lô hàng đã được trả thành công!');
        } catch (\Exception $e) {
            \Log::error('Lỗi khi giảm số lượng lô hàng: ' . $e->getMessage());
            toastr()->timeOut(5000)->closeButton()->addError('Có lỗi xảy ra khi giảm số lượng lô hàng: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function showCheckForm()
    {
        return view('admin.checkWarranty');
    }

    public function check(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string|max:255',
        ]);

        $serialNumber = $request->input('serial_number');
        $productInstance = ProductInstance::with(['product', 'owner'])->where('serial_number', $serialNumber)->first();

        $error = null;
        if (!$productInstance) {
            $error = 'Không tìm thấy sản phẩm với số Serial này. Vui lòng kiểm tra lại.';
        }

        return view('admin.checkWarranty', compact('productInstance', 'error'));
    }
}
