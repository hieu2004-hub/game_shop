<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userID = Auth::id();
        $query = Order::where('userID', $userID)->orderBy('updated_at', 'desc')
            ->with(['orderItems.product']); // Eager load order items and their products

        // Lấy trạng thái từ request (ví dụ: ?status=pending)
        $statusFilter = $request->query('status');

        if ($statusFilter) {
            // Áp dụng bộ lọc trạng thái nếu có
            switch ($statusFilter) {
                case 'pending':
                    $query->where('status', 'Chờ Xử Lý');
                    break;
                case 'confirmed':
                    $query->where('status', 'Đã Xác Nhận');
                    break;
                case 'cancelled':
                    $query->where('status', 'Hủy Đơn Hàng');
                    break;
                case 'received': // THÊM CASE MỚI NÀY
                    $query->where('status', 'Đã Nhận Được Hàng');
                    break;
                // Có thể thêm các trạng thái khác nếu cần
            }
        }

        $orders = $query->paginate(10);

        return view('home.order', compact('orders'));
    }

    /**
     * Display the specified order details.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Lấy đơn hàng theo ID và đảm bảo nó thuộc về người dùng hiện tại
        // Thêm điều kiện where('userID', Auth::id()) để đảm bảo người dùng chỉ xem được đơn hàng của mình
        $order = Order::with('orderItems.product')
            ->where('userID', Auth::id()) // RẤT QUAN TRỌNG ĐỂ ĐẢM BẢO BẢO MẬT
            ->find($id);

        if (!$order) {
            abort(404, 'Order not found or you do not have permission to view it.'); // Cung cấp thông báo rõ ràng hơn
        }

        // SỬA LỖI NÀY: Chỉ truyền biến $order (số ít) vào view
        return view('home.orderDetail', compact('order'));
    }

    // Bạn có thể thêm các phương thức khác liên quan đến quản lý đơn hàng của người dùng tại đây (ví dụ: cancelOrderUser)
}
