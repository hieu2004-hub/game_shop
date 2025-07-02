<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <!-- SB Admin 2 thường sử dụng Font Awesome cho icons, đảm bảo đã được include -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Có thể bỏ orderDetail.css nếu các style của nó được thay thế hoàn toàn bằng Bootstrap/SB Admin 2 -->
    {{-- <link rel="stylesheet" href="{{ asset('./assets/css/orderDetail.css') }}"> --}}
    <style>
        /* Tùy chỉnh nhỏ nếu cần cho ảnh hoặc các thành phần khác */
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .product-table img {
            max-width: 80px; /* Điều chỉnh kích thước ảnh sản phẩm */
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .customer-info-section p {
            margin-bottom: 0.5rem; /* Khoảng cách giữa các dòng thông tin khách hàng */
        }
        .customer-info-section p:last-child {
            margin-bottom: 0;
        }
        .order-actions .btn {
            margin-right: 10px; /* Khoảng cách giữa các nút hành động */
            margin-bottom: 10px; /* Để xuống dòng trên màn hình nhỏ */
        }
        .total-label {
            text-align: right;
            font-weight: bold;
            font-size: 1.1em;
        }
        .total-amount {
            font-weight: bold;
            font-size: 1.2em;
            color: #1cc88a; /* Màu xanh lá cây đẹp cho tổng tiền */
        }

        /* FIX: Đảm bảo viền cho hàng cuối cùng và cạnh bảng */
        .table-bordered tbody tr:last-child td {
            border-bottom: 1px solid #dee2e6 !important;
            border-right: 1px solid #dee2e6 !important;
            border-left: 1px solid #dee2e6 !important;
        }

        /* NEW: Viền dưới cho hàng tổng cộng trong tfoot */
        .table-bordered tfoot tr td {
            border-bottom: 1px solid #dee2e6 !important;
            border-top: 1px solid #dee2e6 !important; /* Đảm bảo có viền trên nếu cần */
            border-right: 1px solid #dee2e6 !important;
            border-left: 1px solid #dee2e6 !important;
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Chi tiết Đơn Hàng</h1>
            <a href="{{ route('admin.viewOrders') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại Danh sách Đơn Hàng
            </a>
        </div>

        <!-- Order Summary Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Mã đơn hàng: #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h6>
                <h6 class="m-0 font-weight-bold text-info">Trạng thái: {{ $order->status }}</h6>
            </div>
            <div class="card-body customer-info-section">
                <h5 class="text-gray-800 mb-3">Thông tin Khách hàng & Đơn hàng</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tên khách hàng:</strong> {{ $order->name }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Phương thức nhận hàng:</strong> {{ $order->delivery_method ?? 'Chưa xác định' }}</p>
                        <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method ?? 'Chưa xác định' }}</p>
                        <p><strong>Trạng thái thanh toán:</strong> {{ $order->payment_status ?? 'Chưa xác định' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product List Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách Sản phẩm</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-gradient-primary text-white">
                            <tr>
                                <th>STT</th>
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
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $item->product->productName }}</td>
                                    <td>
                                        <img src="{{ asset('Product Image/' . $item->product->productImage) }}" alt="{{ $item->product->productName }}" width="75" height="75">
                                    </td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($totalPricePerItem, 0, ',', '.') }} VNĐ</td>
                                </tr>

                                <!-- ====================================================== -->
                                <!-- SỬA: XÓA BỎ ĐIỀU KIỆN IF BÊN NGOÀI -->
                                <!-- ====================================================== -->
                                {{-- Chỉ hiển thị hàng này nếu sản phẩm có các bản ghi bảo hành được tạo ra --}}
                                @if($item->productInstances->isNotEmpty())
                                    <tr class="table-info">
                                        <td colspan="6" class="text-start p-3">
                                            <strong class="text-dark"><i class="fas fa-barcode"></i> Mã bảo hành (Serial Number):</strong>
                                            <ul class="list-unstyled mb-0 mt-2">
                                                @foreach($item->productInstances as $instance)
                                                    <li class="mb-1">
                                                        <code>{{ $instance->serial_number }}</code>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endif
                                <!-- ====================================================== -->
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="total-label" style="text-align: right">Tổng Cộng:</td>
                                <td class="total-amount">{{ number_format($grandTotal, 0, ',', '.') }} VNĐ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Actions Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hành động Đơn hàng</h6>
            </div>
            <div class="card-body order-actions">

                {{-- =================================================================== --}}
                {{-- QUY TRÌNH CHO ĐƠN COD --}}
                {{-- =================================================================== --}}
                @if ($order->payment_method == 'Tiền mặt')

                    {{-- Giai đoạn 1: Đơn đang chờ xử lý và chưa thanh toán --}}
                    @if ($order->status == 'Chờ Xử Lý' && $order->payment_status == 'Chưa thanh toán')
                        <a href="{{ route('admin.confirmPayment', $order->id) }}" class="btn btn-primary btn-icon-split"
                        onclick="confirmation(event, 'Xác nhận đã nhận đủ tiền mặt?', 'Hành động này xác nhận bạn đã nhận tiền từ shipper cho đơn hàng này.')">
                        <span class="icon text-white-50"><i class="fas fa-cash-register"></i></span>
                        <span class="text">Xác nhận đã nhận tiền (COD)</span>
                        </a>
                        <a href="{{ route('admin.cancelOrder', $order->id) }}" class="btn btn-danger btn-icon-split"
                        onclick="confirmation(event, 'Bạn chắc chắn muốn hủy đơn hàng này?', 'Đơn hàng chưa thanh toán sẽ được hủy ngay và hàng được trả về kho.')">
                        <span class="icon text-white-50"><i class="fas fa-times"></i></span>
                        <span class="text">Hủy Đơn Hàng</span>
                        </a>
                    @endif

                    {{-- Giai đoạn 2: Đã nhận tiền nhưng chưa xác nhận giao hàng --}}
                    @if ($order->status == 'Chờ Xử Lý' && $order->payment_status == 'Đã thanh toán')
                        <a href="{{ route('admin.confirmOrder', $order->id) }}" class="btn btn-success btn-icon-split"
                            onclick="confirmation(event, 'Xác nhận đơn hàng này?', 'Đơn hàng sẽ được chuyển sang trạng thái đã xác nhận để chuẩn bị giao.')">
                            <span class="icon text-white-50"><i class="fas fa-check"></i></span>
                            <span class="text">Xác Nhận Đơn</span>
                            </a>
                        {{-- SỬA: Cho phép hủy đơn ở giai đoạn này --}}
                        <a href="{{ route('admin.cancelOrder', $order->id) }}" class="btn btn-danger btn-icon-split"
                        onclick="confirmation(event, 'Bạn chắc chắn muốn hủy đơn hàng này?', 'Đơn hàng đã được thanh toán. Hủy đơn sẽ chuyển sang trạng thái Chờ Hoàn Tiền.')">
                        <span class="icon text-white-50"><i class="fas fa-times"></i></span>
                        <span class="text">Hủy Đơn Hàng</span>
                        </a>
                    @endif

                @endif

                {{-- =================================================================== --}}
                {{-- QUY TRÌNH CHO ĐƠN THANH TOÁN TRƯỚC (Momo) --}}
                {{-- =================================================================== --}}
                @if ($order->payment_method != 'Tiền mặt')
                    @if ($order->status == 'Chờ Xử Lý')
                        <a href="{{ route('admin.confirmOrder', $order->id) }}" class="btn btn-success btn-icon-split"
                            onclick="confirmation(event, 'Xác nhận đơn hàng này?', 'Đơn hàng sẽ được chuyển sang trạng thái đã xác nhận để chuẩn bị giao.')">
                            <span class="icon text-white-50"><i class="fas fa-check"></i></span>
                            <span class="text">Xác Nhận Đơn</span>
                            </a>
                        <a href="{{ route('admin.cancelOrder', $order->id) }}" class="btn btn-danger btn-icon-split"
                        onclick="confirmation(event, 'Bạn chắc chắn muốn hủy đơn hàng này?', 'Đơn hàng đã được thanh toán. Hủy đơn sẽ chuyển sang trạng thái Chờ Hoàn Tiền.')">
                        <span class="icon text-white-50"><i class="fas fa-times"></i></span>
                        <span class="text">Hủy Đơn Hàng</span>
                        </a>
                    @endif
                @endif

                {{-- =================================================================== --}}
                {{-- THÔNG BÁO CHUNG CHO CÁC TRẠNG THÁI KHÁC --}}
                {{-- =================================================================== --}}
                @if ($order->payment_status == 'Chờ hoàn tiền')
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i> Đang chờ khách hàng xác nhận đã nhận được tiền hoàn.
                    </div>
                @endif

                @if ($order->status == 'Đã Xác Nhận' || $order->status == 'Đã Nhận Được Hàng' || $order->status == 'Hủy Đơn Hàng')
                    <div class="alert alert-secondary" role="alert">
                        <i class="fas fa-lock"></i> Đơn hàng đã ở trạng thái cuối, không thể thực hiện thêm hành động.
                    </div>
                @endif
            </div>
        </div>
    </div>
  </main>

  @include('admin.adminscript')
  {{-- SweetAlert2 CDN --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script type="text/javascript">
    function confirmation(ev, titleText, bodyText) {
        ev.preventDefault();
        var urlToRedirect = ev.currentTarget.getAttribute('href');
        Swal.fire({ // Đã thay đổi swal thành Swal.fire cho SweetAlert2
            title: titleText || "Bạn chắc chưa?",
            text: bodyText || "Bạn sẽ không thể hoàn tác hành động này!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = urlToRedirect;
            }
        });
    }
  </script>
</body>

</html>
