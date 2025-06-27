<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    {{-- Nhúng file CSS mới --}}
    <link rel="stylesheet" href="{{ asset('./assets/css/cart.css') }}">
    {{-- Thư viện icon (Font Awesome) để có các icon đẹp --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>
<body>

  <header>
    @include('home.header')
  </header>

  <main>
    <div class="my-orders-container">
        <h1 class="page-title">Đơn Hàng Của Tôi</h1>

        <!-- Thanh Lọc Trạng Thái -->
        <div class="filter-bar">
            <form action="{{ route('my.orders') }}" method="GET" class="w-100" style="max-width: 400px;">
                <select name="status" class="form-select" id="orderStatusFilter" onchange="filterOrders()">
                    {{-- Option "Tất Cả" --}}
                    <option value="{{ route('my.orders') }}"
                        @if(!request()->has('status')) selected @endif> {{-- Chọn nếu không có tham số status --}}
                        Tất Cả
                    </option>
                    {{-- Option "Chờ Xử Lý" --}}
                    <option value="{{ route('my.orders', ['status' => 'pending']) }}"
                        @if(request()->query('status') == 'pending') selected @endif>
                        Chờ Xử Lý
                    </option>
                    {{-- Option "Đã Xác Nhận" --}}
                    <option value="{{ route('my.orders', ['status' => 'confirmed']) }}"
                        @if(request()->query('status') == 'confirmed') selected @endif>
                        Đã Xác Nhận
                    </option>
                    {{-- Option "Đã Hủy" --}}
                    <option value="{{ route('my.orders', ['status' => 'cancelled']) }}"
                        @if(request()->query('status') == 'cancelled') selected @endif>
                        Đã Hủy
                    </option>
                    {{-- THÊM OPTION MỚI NÀY --}}
                    <option value="{{ route('my.orders', ['status' => 'received']) }}"
                        @if(request()->query('status') == 'received') selected @endif>
                        Đã Nhận Được Hàng
                    </option>
                </select>
            </form>
        </div>

        <!-- Danh sách đơn hàng -->
        @forelse($orders as $order)
            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <span class="order-id">Đơn hàng #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div>
                        {{-- Dùng str_replace để tạo class CSS hợp lệ từ trạng thái tiếng Việt --}}
                        <span class="order-id">Trạng thái: </span> <strong>{{ $order->status }}</strong>
                    </div>
                </div>
                <div class="order-card-body">
                    <div class="order-info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="order-info-item">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Tổng tiền: {{ number_format($order->orderItems->sum(function($item) { return $item->price * $item->quantity; }), 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="order-info-item">
                        <i class="fas fa-truck"></i>
                        <span>Giao đến: {{ Str::limit($order->address, 30) }}</span>
                    </div>
                     <div class="order-info-item">
                        <i class="fas fa-credit-card"></i>
                        <span>Thanh toán: {{ $order->payment_method }}</span>
                    </div>
                </div>
                <div class="order-card-footer">
                    <a href="{{ route('order.details', $order->id) }}" class="btn-view-details">Xem Chi Tiết</a>
                </div>
            </div>
        @empty
            <div class="empty-orders-message">
                <h4>Không có đơn hàng nào</h4>
                <p>Có vẻ như bạn chưa có đơn hàng nào trong trạng thái này.</p>
            </div>
        @endforelse

        <!-- Phân trang -->
        <div class="d-flex justify-content-center mt-4">
            {!! $orders->appends(request()->query())->links() !!}
        </div>
    </div>
  </main>

  <footer>
    @include('home.footer')
  </footer>

  <script src="{{ asset('./assets/js/script.js') }}"></script>
  <script>
    function filterOrders() {
        var selectBox = document.getElementById("orderStatusFilter");
        var selectedValue = selectBox.options[selectBox.selectedIndex].value;
        window.location.href = selectedValue;
    }
  </script>
  {{-- Các script bootstrap có thể không cần thiết nữa nếu không dùng đến các component của nó --}}

</body>
</html>
