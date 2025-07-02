<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    <link rel="stylesheet" href="{{ asset('./assets/css/orderDetail.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

<header>
    @include('home.header')
</header>

<main class="main-content">
    <div class="order-detail-container">
        {{-- Header Section --}}
        <div class="order-header">
            <a href="{{ route('my.orders') }}" class="back-link"><i class="fas fa-arrow-left"></i> Lịch sử đơn hàng</a>
            <div class="order-info">
                Mã đơn hàng: <strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong> | Trạng thái: <strong>{{ $order->status }}</strong>
            </div>
        </div>
        <hr class="divider">

        {{-- Customer Info Section --}}
        <div class="customer-info-section">
            <h3>Thông tin khách hàng</h3>
            <p><i class="fas fa-user"></i> <span>Tên khách hàng:</span> <strong>{{ $order->name }}</strong></p>
            <p><i class="fas fa-map-marker-alt"></i> <span>Địa chỉ:</span> <strong>{{ $order->address }}</strong></p>
            <p><i class="fas fa-phone"></i> <span>Số điện thoại:</span> <strong>{{ $order->phone }}</strong></p>
            <p><i class="fas fa-calendar-alt"></i> <span>Ngày đặt hàng:</span> <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong></p>
            <p><i class="fas fa-truck"></i> <span>Phương thức nhận hàng:</span> <strong>{{ $order->delivery_method ?? 'Chưa có thông tin' }}</strong></p>
            <p><i class="fas fa-money-check-alt"></i> <span>Phương thức thanh toán:</span> <strong>{{ $order->payment_method ?? 'Chưa có thông tin' }}</strong></p>
            <p><i class="fas fa-wallet"></i> <span>Thanh toán:</span> <strong>{{ $order->payment_status ?? 'Chưa có thông tin' }}</strong></p>
        </div>
        <hr class="divider">

        {{-- Product List Table --}}
        <div class="product-list-section">
            <h3>Sản phẩm trong đơn hàng</h3>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Tên Sản Phẩm</th>
                        <th>Ảnh</th>
                        <th>Đơn Giá</th>
                        <th>Số Lượng</th>
                        <th>Tổng Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @foreach($order->orderItems as $item)
                        @php
                            $totalPricePerItem = $item->price * $item->quantity;
                            $grandTotal += $totalPricePerItem;
                        @endphp
                        <tr>
                            <td data-label="Tên Sản Phẩm">{{ $item->product->productName }}</td>
                            <td data-label="Ảnh">
                                <img src="{{ asset('Product Image/' . $item->product->productImage) }}" alt="{{ $item->product->productName }}">
                            </td>
                            <td data-label="Đơn Giá">{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                            <td data-label="Số Lượng">{{ $item->quantity }}</td>
                            <td data-label="Tổng Giá">{{ number_format($totalPricePerItem, 0, ',', '.') }} VNĐ</td>
                        </tr>

                        <!-- SỬA: Thay đổi điều kiện hiển thị -->
                        {{-- Hiển thị thông tin bảo hành ngay khi đơn hàng được admin xác nhận (chuẩn bị giao) trở đi, trừ khi đơn bị hủy --}}
                        @if (in_array($order->status, ['Đã Xác Nhận', 'Đã Nhận Được Hàng']))
                            {{-- Chỉ hiển thị hàng này nếu sản phẩm có thông tin bảo hành --}}
                            @if($item->productInstances->isNotEmpty())
                                <tr class="warranty-details-row">
                                    <td colspan="5">
                                        <div class="warranty-details-content">
                                            <strong><i class="fas fa-shield-alt icon-warranty"></i> Thông tin bảo hành:</strong>
                                            <ul>
                                                @foreach($item->productInstances as $instance)
                                                    <li>
                                                        Mã bảo hành: <strong>{{ $instance->serial_number }}</strong>
                                                        {{-- Chỉ hiển thị ngày tháng khi đã nhận hàng --}}
                                                        @if($instance->warranty_start_date)
                                                            - Bắt đầu: <strong>{{ \Carbon\Carbon::parse($instance->warranty_start_date)->format('d/m/Y') }}</strong>
                                                            - Kết thúc: <strong>{{ \Carbon\Carbon::parse($instance->warranty_end_date)->format('d/m/Y') }}</strong>
                                                        @else
                                                            <span class="text-muted">- (Sẽ được kích hoạt khi bạn xác nhận đã nhận hàng)</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @elseif($item->product->is_warrantable)
                                {{-- Trường hợp sản phẩm có bảo hành nhưng chưa có instance (lỗi hệ thống) --}}
                                <tr class="warranty-details-row">
                                    <td colspan="5">
                                        <div class="warranty-details-content">
                                            <strong><i class="fas fa-info-circle text-warning"></i> Thông tin bảo hành sẽ được cập nhật khi đơn hàng được xử lý.</strong>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endif
                        <!-- KẾT THÚC SỬA -->

                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="total-label">Tổng Cộng:</td>
                        <td class="total-amount">{{ number_format($grandTotal, 0, ',', '.') }} VNĐ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <hr class="divider">

        {{-- Order Actions (Buttons) --}}
        <div class="order-actions">
            @if ($order->status == 'Chờ Xử Lý')
                <a href="{{ route('user.cancelOrder', $order->id) }}" class="btn btn-danger" onclick="confirmation(event, 'Bạn có chắc muốn hủy đơn?', 'Bạn sẽ không thể hoàn tác hành động này!')">
                    <i class="fas fa-times-circle"></i> Hủy Đơn Hàng
                </a>
            @endif
            @if ($order->status == 'Đã Xác Nhận')
                <a href="{{ route('user.confirmReceived', $order->id) }}" class="btn btn-success" onclick="confirmation(event, 'Bạn chắc chắn đã nhận được hàng?', 'Chỉ bấm xác nhận nếu bạn đã nhận được hàng!')">
                    <i class="fas fa-check-circle"></i> Đã Nhận Được Hàng
                </a>
            @endif
            @if ($order->payment_status == 'Chờ hoàn tiền')
                <div class="refund-notice">
                    <p>Cửa hàng đã xử lý yêu cầu hủy và đang thực hiện hoàn tiền cho bạn. Vui lòng bấm xác nhận khi bạn đã nhận được tiền.</p>
                </div>
                <a href="{{ route('user.confirmRefundReceived', $order->id) }}" class="btn btn-primary" onclick="confirmation(event, 'Bạn chắc chắn đã nhận được tiền hoàn?', 'Hành động này sẽ hoàn tất quy trình hủy đơn hàng.')">
                    <i class="fas fa-hand-holding-usd"></i> Tôi đã nhận được tiền hoàn
                </a>
            @endif
        </div>
    </div>
</main>

<footer>
    @include('home.footer')
</footer>

<script src="{{ asset('./assets/js/script.js') }}"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    function confirmation(ev, titleText, bodyText) {
        ev.preventDefault();
        var urlToRedirect = ev.currentTarget.getAttribute('href');
        swal({
            title: titleText,
            text: bodyText,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willPerform) => {
            if (willPerform) {
                window.location.href = urlToRedirect;
            }
        });
    }
</script>
</body>
</html>
