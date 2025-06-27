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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Doanh thu và lợi nhuận (cho các đơn hàng đã được thanh toán)
        $paidOrderItems = OrderItem::whereHas('order', function ($query) {
            $query->where('payment_status', 'Đã thanh toán');
        })->get();

        $totalRevenue = 0;
        $totalProfit = 0;

        foreach ($paidOrderItems as $item) {
            $totalRevenue += $item->quantity * $item->price;
            // Đảm bảo import_price_at_sale không null và lớn hơn 0 để tính lợi nhuận
            if ($item->import_price_at_sale !== null && $item->import_price_at_sale > 0) {
                $totalProfit += $item->quantity * ($item->price - $item->import_price_at_sale);
            }
        }

        // 2. Số đơn hàng chờ xử lý
        $pendingOrdersCount = Order::where('status', 'Chờ Xử Lý')->count();

        // 3. Tổng số đơn hàng
        $totalOrdersCount = Order::count();

        // 4. Tổng số sản phẩm
        $totalProductsCount = Product::count();

        // 5. Tổng số người dùng (giả sử usertype 0 là người dùng thường)
        $totalUsersCount = User::where('usertype', 0)->count(); // Thay đổi nếu usertype của admin không phải 1

        return view('admin.dashBoard', compact(
            'totalRevenue',
            'totalProfit',
            'pendingOrdersCount',
            'totalOrdersCount',
            'totalProductsCount',
            'totalUsersCount'
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
        $products = Product::with('warehouseStocks')
            ->where('productName', 'like', '%' . $search . '%')
            ->orWhere('productCategory', 'like', '%' . $search . '%')
            ->orWhere('productBrand', 'like', '%' . $search . '%')
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        return view('admin.adminSearch', compact('products'));
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
        // THÊM DÒNG NÀY
        $data->is_warrantable = $request->has('is_warrantable'); // Checkbox
        $data->default_warranty_months = $request->default_warranty_months; // Input number
        $image = $request->productImage;
        if ($image) {
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $request->productImage->move('Product Image', $filename);
            $data->productImage = $filename;
        }
        $data->save();
        toastr()->timeOut(5000)->closeButton()->addSuccess('Product Added Successfully!');
        return redirect('viewProduct');
    }

    public function viewProduct()
    {
        $products = Product::with('warehouseStocks')
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        return view('admin.viewProduct', compact('products'));
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
        // THÊM DÒNG NÀY
        $data->is_warrantable = $request->has('is_warrantable'); // Checkbox
        $data->default_warranty_months = $request->default_warranty_months; // Input number
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
    // Phương thức viewOrder đã được cập nhật để xử lý lọc
    public function viewOrder(Request $request)
    {
        $query = Order::orderBy('updated_at', 'desc')->with(['user']);

        // Lọc theo trạng thái xử lý
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->paginate(10)->withQueryString(); // withQueryString để giữ lại tham số filter khi chuyển trang
        return view('admin.order', compact('orders'));
    }

    public function showOrderDetail($id)
    {
        $order = Order::with(['orderItems.product', 'orderItems.warehouse'])->find($id);

        if (!$order) {
            toastr()->timeOut(5000)->closeButton()->addError('Đơn hàng không tồn tại hoặc đã bị xóa!');
            return redirect()->route('admin.viewOrders'); // Chuyển hướng về trang danh sách đơn hàng
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

        // Nếu đơn đã thanh toán, chuyển sang chờ hoàn tiền
        if ($order->payment_status == 'Đã thanh toán') {
            $order->status = 'Hủy Đơn Hàng';
            $order->payment_status = 'Chờ hoàn tiền';
            $order->save();
            toastr()->success('Đơn hàng đã được hủy. Vui lòng tiến hành hoàn tiền cho khách hàng.');
        } else { // Đơn COD chưa thanh toán
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
            $order->status = 'Đã Xác Nhận'; // Hoặc 'Đang Giao Hàng' tùy quy trình của bạn
            $order->save();
            toastr()->success('Đơn hàng đã được xác nhận thành công!');
        } else {
            toastr()->error('Không thể xác nhận đơn hàng này!');
        }
        return redirect()->back();
    }

    /**
     * HÀM MỚI: Admin xác nhận đã hoàn tiền thủ công
     */
    public function confirmRefund($id)
    {
        $order = Order::find($id);

        if (!$order) {
            toastr()->error('Đơn hàng không tồn tại!');
            return redirect()->back();
        }

        // Chỉ xác nhận hoàn tiền cho đơn đang ở trạng thái chờ hoàn tiền
        if ($order->payment_status !== 'Chờ hoàn tiền') {
            toastr()->error('Không thể thực hiện hành động này cho đơn hàng.');
            return redirect()->back();
        }

        DB::transaction(function () use ($order) {
            $order->payment_status = 'Đã hoàn tiền'; // Cập nhật trạng thái đã hoàn tiền
            $order->save();

            // Bây giờ mới trả hàng về kho
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

    /**
     * HÀM MỚI: Admin xác nhận đã nhận tiền cho đơn COD
     */
    public function confirmPayment($id)
    {
        $order = Order::find($id);

        if (!$order) {
            toastr()->error('Đơn hàng không tồn tại!');
            return redirect()->back();
        }

        // Chỉ xác nhận cho đơn COD chưa thanh toán
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

        Warehouse::create([
            'name' => $request->name,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'phone' => $request->phone,
        ]);

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

        $warehouse->update([
            'name' => $request->name,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'phone' => $request->phone,
        ]);

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

    // --- Receive Product Functionality ---
    public function receiveProductForm()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        return view('admin.receiveProduct', compact('warehouses'));
    }

    public function receiveProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'import_price' => 'required|numeric|min:0',
            'batch_identifier' => 'nullable|string|max:255',
            'received_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $batchIdentifier = $request->batch_identifier ?: 'BATCH_' . time() . '_' . uniqid();

        try {
            $stockEntry = ProductWarehouseStock::firstOrNew([
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'batch_identifier' => $batchIdentifier,
            ]);

            $stockEntry->quantity += $request->quantity;
            $stockEntry->import_price = $request->import_price;
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
                // THÊM DÒNG NÀY ĐỂ LOẠI TRỪ SẢN PHẨM ĐÃ CÓ TỒN KHO
                ->whereDoesntHave('warehouseStocks', function ($query) {
                    $query->where('quantity', '>', 0); // Kiểm tra nếu có bất kỳ tồn kho nào lớn hơn 0
                })
                ->select('id', 'productName')
                ->get();
        }

        return response()->json($products);
    }

    public function viewWarehouseStock($warehouse_id)
    {
        $warehouse = Warehouse::find($warehouse_id);
        if (!$warehouse) {
            toastr()->timeOut(5000)->closeButton()->addError('Kho hàng không tồn tại!');
            return redirect()->route('admin.viewWarehouses');
        }

        $stockEntries = ProductWarehouseStock::where('warehouse_id', $warehouse_id)
            ->with('product')
            ->orderBy('received_date', 'desc')
            ->paginate(10);

        return view('admin.viewWarehouseStock', compact('warehouse', 'stockEntries'));
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

    /**
     * Hiển thị form kiểm tra bảo hành.
     *
     * @return \Illuminate\View\View
     */
    public function showCheckForm()
    {
        return view('admin.checkWarranty');
    }

    /**
     * Xử lý yêu cầu kiểm tra bảo hành và hiển thị kết quả.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function check(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string|max:255',
        ]);

        $serialNumber = $request->input('serial_number');

        // Tìm ProductInstance dựa trên serial_number
        // Eager load product và owner để hiển thị thông tin chi tiết
        $productInstance = ProductInstance::with(['product', 'owner'])->where('serial_number', $serialNumber)->first();

        $error = null;
        if (!$productInstance) {
            $error = 'Không tìm thấy sản phẩm với số Serial này. Vui lòng kiểm tra lại.';
        }

        // Truyền productInstance và error vào view để hiển thị thông tin
        return view('admin.checkWarranty', compact('productInstance', 'error'));
    }
}
