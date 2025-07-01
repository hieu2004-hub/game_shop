<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductWarehouseStock;
use App\Models\ProductInstance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function home()
    {
        // Eager load warehouseStocks để tính tổng tồn kho qua accessor total_stock_quantity
        $products = Product::with('warehouseStocks')->orderBy('created_at', 'desc')->paginate(4);
        return view('home.index', compact('products'));
    }

    public function loginHome()
    {
        // Eager load warehouseStocks để tính tổng tồn kho qua accessor total_stock_quantity
        $products = Product::with('warehouseStocks')->orderBy('created_at', 'desc')->paginate(4);
        return view('home.index', compact('products'));
    }

    public function warranty()
    {
        return view('home.warranty');
    }

    public function shipping()
    {
        return view('home.shipping');
    }
    public function userProfile()
    {
        $user = Auth::user(); // Lấy thông tin người dùng hiện tại
        return view('home.myProfile', compact('user'));
    }
    public function updateUserProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'userName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
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
        $user->userName = $request->userName;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;

        // Cập nhật mật khẩu nếu có
        if ($request->filled('new_password')) {
            $user->forceFill([
                'password' => Hash::make($request->new_password),
            ]);
        }

        $user->save();

        toastr()->success('Thông tin tài khoản đã được cập nhật thành công!');
        return redirect()->back();
    }


    public function productDetails($id)
    {
        // Cập nhật dòng này: Eager load warehouseStocks để tính tổng tồn kho
        $productDetail = Product::with('warehouseStocks')->find($id);
        return view('home.productDetail', compact('productDetail'));
    }

    public function addToCart($id)
    {
        $user = Auth::user();
        $userID = $user->id;
        $quantity = request()->query('quantity', 1);

        $product = Product::with('warehouseStocks')->find($id); // Eager load warehouseStocks

        if (!$product) {
            toastr()->error('Sản phẩm không tồn tại.');
            return redirect()->back();
        }

        // Kiểm tra tổng số lượng tồn kho của sản phẩm bằng accessor total_stock_quantity
        if ($product->total_stock_quantity < $quantity) {
            toastr()->error('Không đủ hàng trong kho cho ' . $product->productName . '. Chỉ còn ' . $product->total_stock_quantity . ' sản phẩm.');
            return redirect()->back();
        }

        $cartItem = Cart::where('userID', $userID)
            ->where('productID', $id)
            ->first();

        if ($cartItem) {
            // Nếu sản phẩm đã có trong giỏ hàng, chỉ thông báo và KHÔNG tăng số lượng
            toastr()->info('Sản phẩm "' . $product->productName . '" đã có trong giỏ hàng của bạn.');
        } else {
            // Nếu sản phẩm chưa có trong giỏ hàng, tạo mới
            $cart = new Cart();
            $cart->userID = $userID;
            $cart->productID = $id;
            $cart->quantity = $quantity;
            $cart->save();
            toastr()->success('Sản phẩm "' . $product->productName . '" đã được thêm vào giỏ hàng thành công!');
        }

        return redirect()->back();
    }

    public function cartCount()
    {
        $user = Auth::user();
        if ($user) {
            return Cart::where('userID', $user->id)->distinct('productID')->count('productID');
        }
        return 0;
    }

    public function myCart()
    {
        $cart = [];
        $count = 0;

        if (Auth::check()) {
            $userID = Auth::id();
            // Eager load product và warehouseStocks của product để tính total_stock_quantity
            $cart = Cart::where('userID', $userID)->with(['product.warehouseStocks'])->get();
            $count = $cart->count();
        }

        return view('home.myCart', compact('cart', 'count'));
    }

    public function updateCart(Request $request, $id)
    {
        $user = Auth::user();
        Log::info("updateCart called for userID: {$user->id}, productID: {$id}, newQuantity: {$request->quantity}");

        $cartItem = Cart::where('userID', $user->id)->where('productID', $id)->first();

        if ($cartItem) {
            $quantity = $request->quantity;
            $product = Product::with('warehouseStocks')->find($id); // Eager load warehouseStocks

            if (!$product) {
                Log::warning("Product not found for ID: {$id}");
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
            }

            // Kiểm tra tổng số lượng tồn kho của sản phẩm bằng accessor total_stock_quantity
            if ($quantity > $product->total_stock_quantity) {
                Log::info("Quantity {$quantity} exceeds total stock {$product->total_stock_quantity} for product {$id}");
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng yêu cầu vượt quá số lượng trong kho. Tổng số lượng tối đa bạn có thể đặt là: ' . $product->total_stock_quantity,
                ]);
            }

            $cartItem->quantity = $quantity;
            $cartItem->save();
            Log::info("Cart item {$cartItem->id} updated to quantity {$quantity}");
            return response()->json(['success' => true]);
        } else {
            Log::warning("Cart item not found for userID: {$user->id}, productID: {$id}");
            return response()->json(['success' => false, 'message' => 'Sản phẩm không còn trong giỏ hàng của bạn.']);
        }
    }

    public function deleteCart($id)
    {
        $data = Cart::find($id);
        $data->delete();
        toastr()->timeOut(5000)->closeButton()->success('Product removed from Cart Successfully!');
        return redirect()->back();
    }

    // SỬA PHƯƠNG THỨC NÀY
    public function checkout(Request $request)
    {
        $user = Auth::user();

        // 1. Lấy danh sách ID các item trong giỏ hàng đã được chọn từ request
        $selectedCartItemIds = $request->query('selected_items', []);

        // 2. Nếu không có gì được chọn, quay lại giỏ hàng
        if (empty($selectedCartItemIds)) {
            toastr()->error('Vui lòng chọn sản phẩm trong giỏ hàng trước khi thanh toán.');
            return redirect()->route('myCart');
        }

        // 3. Chỉ lấy các item trong giỏ hàng có ID nằm trong danh sách được chọn
        $cartItems = Cart::where('userID', $user->id)
            ->whereIn('id', $selectedCartItemIds)
            ->with('product.warehouseStocks')
            ->get();

        // 4. Kiểm tra phòng trường hợp người dùng sửa URL
        if ($cartItems->isEmpty() || count($selectedCartItemIds) != $cartItems->count()) {
            toastr()->error('Sản phẩm đã chọn không hợp lệ. Vui lòng thử lại.');
            return redirect()->route('myCart');
        }

        // 5. Tính tổng tiền
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->quantity * $item->product->productPrice;
        }
        $grandTotal = $subtotal;

        return view('home.checkout', compact('user', 'cartItems', 'subtotal', 'grandTotal'));
    }

    // SỬA PHƯƠNG THỨC NÀY
    public function confirmOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'delivery_method' => 'required|in:Giao tận nơi,Đến cửa hàng nhận',
            'payment_method' => 'required|in:Tiền mặt,Thẻ ngân hàng/Ví điện tử',
            // Thêm validation cho mảng ID ẩn
            'selected_cart_item_ids' => 'required|array',
            'selected_cart_item_ids.*' => 'exists:carts,id',
        ]);

        $userID = Auth::user()->id;
        $selectedCartItemIds = $request->input('selected_cart_item_ids');

        // Lấy lại các mục trong giỏ hàng đã được chọn để xử lý
        $cartItems = Cart::where('userID', $userID)->whereIn('id', $selectedCartItemIds)->get();

        // Kiểm tra tồn kho lần cuối
        foreach ($cartItems as $cartItem) {
            $product = Product::with('warehouseStocks')->find($cartItem->productID);
            if (!$product || $product->total_stock_quantity < $cartItem->quantity) {
                toastr()->error('Không đủ hàng trong kho cho sản phẩm: ' . $product->productName);
                return redirect()->route('myCart');
            }
        }

        $order = null;
        $grandTotal = 0;

        try {
            DB::transaction(function () use ($request, $userID, $cartItems, &$order, &$grandTotal) {
                // Tạo đơn hàng
                $order = new Order;
                $order->name = $request->name;
                $order->address = $request->address;
                $order->phone = $request->phone;
                $order->userID = $userID;
                $order->delivery_method = $request->delivery_method;
                $order->payment_method = $request->payment_method;
                $order->momo_order_id = 'GAMESHOP' . time() . '_' . $userID;
                $order->status = 'Chờ Xử Lý';
                $order->payment_status = ($request->payment_method == 'Tiền mặt') ? 'Chưa thanh toán' : 'Đã thanh toán';
                $order->save();

                // Lặp qua các item đã chọn trong giỏ hàng
                foreach ($cartItems as $cartItem) {
                    $product = Product::find($cartItem->productID);
                    $quantityToDeduct = $cartItem->quantity;
                    $grandTotal += $quantityToDeduct * $product->productPrice;

                    $availableStocks = ProductWarehouseStock::where('product_id', $product->id)
                        ->where('quantity', '>', 0)
                        ->orderBy('received_date', 'asc')
                        ->get();
                    $deductedQuantity = 0;

                    foreach ($availableStocks as $stock) {
                        if ($deductedQuantity >= $quantityToDeduct)
                            break;
                        $canDeduct = min($quantityToDeduct - $deductedQuantity, $stock->quantity);
                        $stock->quantity -= $canDeduct;
                        $stock->save();

                        $orderItem = new OrderItem;
                        $orderItem->orderID = $order->id;
                        $orderItem->productID = $product->id;
                        $orderItem->quantity = $canDeduct;
                        $orderItem->price = $product->productPrice;
                        $orderItem->import_price_at_sale = $stock->import_price;
                        $orderItem->batch_identifier_at_sale = $stock->batch_identifier;
                        $orderItem->warehouse_id_at_sale = $stock->warehouse_id;
                        $orderItem->save();

                        $deductedQuantity += $canDeduct;

                        if ($product->is_warrantable) {
                            for ($i = 0; $i < $canDeduct; $i++) {
                                ProductInstance::create([
                                    'product_id' => $product->id,
                                    'order_item_id' => $orderItem->id,
                                    'user_id' => $userID,
                                    'serial_number' => 'SN-' . $product->id . '-' . uniqid(),
                                    'purchase_date' => Carbon::now(),
                                    'warranty_duration_months' => $product->default_warranty_months ?? 0,
                                    'warranty_status' => 'pending_activation',
                                    'notes' => 'Được mua trong đơn hàng #' . $order->id . ' - ' . $product->productName,
                                ]);
                            }
                        }
                    }
                }
                // Xóa các item đã chọn khỏi giỏ hàng
                Cart::where('userID', $userID)->whereIn('id', $cartItems->pluck('id'))->delete();
            });
        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            toastr()->error('Đã xảy ra lỗi khi tạo đơn hàng. Vui lòng thử lại.');
            return redirect()->route('myCart');
        }

        if ($request->payment_method == 'Thẻ ngân hàng/Ví điện tử') {
            $paymentController = new PaymentController();
            $momoResponse = $paymentController->createMomoPaymentRequest($order->momo_order_id, $grandTotal, $order->id);
            if (isset($momoResponse['payUrl'])) {
                return redirect()->to($momoResponse['payUrl']);
            } else {
                $order->status = 'Thanh toán thất bại';
                $order->save();
                toastr()->error('Không thể tạo yêu cầu thanh toán. Vui lòng thử lại.');
                return redirect()->route('myCart');
            }
        } else {
            toastr()->success('Sản phẩm đã được đặt hàng thành công!');
            return redirect()->route('thankYou');
        }
    }

    public function cancelOrderByUser($id)
    {
        $order = Order::where('id', $id)->where('userID', Auth::id())->first();

        if (!$order) {
            toastr()->error('Đơn hàng không tồn tại hoặc bạn không có quyền!');
            return redirect()->back();
        }

        // Chỉ cho phép hủy khi đơn hàng đang ở trạng thái 'Chờ Xử Lý'
        if ($order->status !== 'Chờ Xử Lý') {
            toastr()->error('Không thể hủy đơn hàng ở trạng thái này.');
            return redirect()->back();
        }

        // Nếu đơn hàng đã thanh toán, chuyển sang trạng thái chờ hoàn tiền
        if ($order->payment_status == 'Đã thanh toán') {
            $order->status = 'Hủy Đơn Hàng';
            $order->payment_status = 'Chờ hoàn tiền';
            $order->save();
            toastr()->success('Yêu cầu hủy đơn hàng đã được gửi. Cửa hàng sẽ xử lý hoàn tiền cho bạn trong thời gian sớm nhất.');
        } else { // Nếu là đơn COD (chưa thanh toán)
            DB::transaction(function () use ($order) {
                $order->status = 'Hủy Đơn Hàng';
                $order->payment_status = 'Đã hủy'; // Đánh dấu là đã hủy thanh toán
                $order->save();

                // Trả lại hàng vào kho ngay lập tức vì chưa mất tiền
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
            toastr()->success('Đơn hàng của bạn đã được hủy thành công!');
        }

        return redirect()->back();
    }

    public function confirmReceivedByUser($id)
    {
        $order = Order::find($id);

        if ($order && $order->userID == Auth::id() && $order->status == 'Đã Xác Nhận') { // Chỉ cho phép xác nhận nếu đơn hàng đã được xác nhận (từ admin)
            DB::transaction(function () use ($order) {
                $order->status = 'Đã Nhận Được Hàng';

                // Nếu là đơn COD, lúc này mới xác nhận là "đã thanh toán"
                if ($order->payment_method == 'Tiền mặt') {
                    $order->payment_status = 'Đã thanh toán'; // Đánh dấu là đã thanh toán
                }

                $order->save();

                $receivedDate = Carbon::now(); // Ngày người dùng xác nhận đã nhận hàng

                // Duyệt qua từng OrderItem trong đơn hàng
                foreach ($order->orderItems as $orderItem) {
                    // Tìm tất cả ProductInstance liên quan đến OrderItem này
                    $productInstances = $orderItem->productInstances()->where('warranty_status', 'pending_activation')->get();

                    foreach ($productInstances as $instance) {
                        // Cập nhật ngày bắt đầu bảo hành
                        $instance->warranty_start_date = $receivedDate;

                        // Tính lại ngày hết hạn bảo hành dựa trên ngày nhận hàng
                        if ($instance->warranty_duration_months > 0) {
                            $instance->warranty_end_date = $receivedDate->copy()->addMonths($instance->warranty_duration_months);
                            $instance->warranty_status = 'active'; // Chuyển trạng thái sang active
                        } else {
                            $instance->warranty_status = 'not_applicable'; // Nếu không có thời gian bảo hành
                        }
                        $instance->save();
                    }
                }
            });

            toastr()->timeOut(5000)->closeButton()->success('Đơn hàng đã được xác nhận là đã nhận! Bảo hành sản phẩm của bạn đã được kích hoạt.');
        } else {
            toastr()->timeOut(5000)->closeButton()->error('Không thể xác nhận đơn hàng này hoặc bạn không có quyền!');
        }
        return redirect()->back();
    }
}
