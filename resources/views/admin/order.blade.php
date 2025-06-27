<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <!-- SB Admin 2 thường sử dụng Font Awesome cho icons, đảm bảo đã được include -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Có thể bỏ cart.css nếu các style của nó được thay thế hoàn toàn bằng Bootstrap/SB Admin 2 -->
    {{-- <link rel="stylesheet" href="{{ asset('./assets/css/cart.css') }}"> --}}
    <style>
        /* Tùy chỉnh nhỏ nếu cần cho ảnh hoặc các thành phần khác */
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .form-label {
            font-weight: bold;
        }

        /* Custom styles for status badges */
        .status-badge {
            display: inline-block;
            padding: .25em .4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            color: #fff; /* Default text color */
        }

        /* Status colors for order processing status */
        .status-badge[data-status="Chờ Xử Lý"] {
            background-color: #ffc107; /* warning */
            color: #212529; /* dark text for light background */
        }
        .status-badge[data-status="Đã Xác Nhận"] {
            background-color: #007bff; /* primary */
        }
        .status-badge[data-status="Đã Nhận Được Hàng"] {
            background-color: #28a745; /* success */
        }
        .status-badge[data-status="Hủy Đơn Hàng"] {
            background-color: #dc3545; /* danger */
        }
        .status-badge[data-status="Thanh toán thất bại"] {
            background-color: #6c757d; /* secondary/grey for payment failure */
        }

        /* Status colors for payment status */
        .status-badge[data-status="Đã thanh toán"] {
            background-color: #28a745; /* success */
        }
        .status-badge[data-status="Chưa thanh toán"] {
            background-color: #ffc107; /* warning */
            color: #212529; /* dark text */
        }
        .status-badge[data-status="Chờ hoàn tiền"] {
            background-color: #6c757d; /* secondary */
        }
        .status-badge[data-status="Đã hoàn tiền"] {
            background-color: #17a2b8; /* info */
        }
        .status-badge[data-status="Đã hủy"] {
            background-color: #dc3545; /* danger */
        }

        /* FIX: Đảm bảo viền cho hàng cuối cùng và cạnh bảng */
        .table-bordered tbody tr:last-child td {
            border-bottom: 1px solid #dee2e6 !important;
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
            <h1 class="h3 mb-0 text-gray-800">Quản lý Đơn Hàng</h1>
            {{-- Bạn có thể thêm nút thêm mới hoặc quay lại nếu cần --}}
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Bộ lọc đơn hàng</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.viewOrders') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="status" class="form-label">Trạng thái xử lý:</label>
                        <select name="status" id="status" class="form-control"> {{-- Changed to form-control --}}
                            <option value="">-- Tất cả --</option>
                            <option value="Chờ Xử Lý" @if(request('status') == 'Chờ Xử Lý') selected @endif>Chờ Xử Lý</option>
                            <option value="Đã Xác Nhận" @if(request('status') == 'Đã Xác Nhận') selected @endif>Đã Xác Nhận</option>
                            <option value="Đã Nhận Được Hàng" @if(request('status') == 'Đã Nhận Được Hàng') selected @endif>Đã Nhận Được Hàng</option>
                            <option value="Hủy Đơn Hàng" @if(request('status') == 'Hủy Đơn Hàng') selected @endif>Hủy Đơn Hàng</option>
                            <option value="Thanh toán thất bại" @if(request('status') == 'Thanh toán thất bại') selected @endif>Thanh toán thất bại</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="payment_status" class="form-label">Trạng thái thanh toán:</label>
                        <select name="payment_status" id="payment_status" class="form-control"> {{-- Changed to form-control --}}
                            <option value="">-- Tất cả --</option>
                            <option value="Đã thanh toán" @if(request('payment_status') == 'Đã thanh toán') selected @endif>Đã thanh toán</option>
                            <option value="Chưa thanh toán" @if(request('payment_status') == 'Chưa thanh toán') selected @endif>Chưa thanh toán (COD)</option>
                            <option value="Chờ hoàn tiền" @if(request('payment_status') == 'Chờ hoàn tiền') selected @endif>Chờ hoàn tiền</option>
                            <option value="Đã hoàn tiền" @if(request('payment_status') == 'Đã hoàn tiền') selected @endif>Đã hoàn tiền</option>
                            <option value="Thanh toán thất bại" @if(request('payment_status') == 'Thanh toán thất bại') selected @endif>Thanh toán thất bại</option>
                            <option value="Đã hủy" @if(request('payment_status') == 'Đã hủy') selected @endif>Đã hủy (COD)</option>
                        </select>
                    </div>
                    <div class="col-md-2"> {{-- Removed d-flex align-items-end as form-control already takes full height --}}
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn hàng</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-gradient-primary text-white">
                            <tr>
                                <th>STT</th>
                                <th>Mã Đơn Hàng</th>
                                <th>Tên Khách Hàng</th>
                                <th>Phương Thức TT</th>
                                <th>Trạng Thái TT</th>
                                <th>Trạng Thái Xử Lý</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $loop->index + $orders->firstItem() }}</td>
                                    <td><strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td>{{ $order->name }}</td>
                                    <td>{{ $order->payment_method }}</td>
                                    <td>
                                        <span class="status-badge" data-status="{{ $order->payment_status }}">
                                            {{ $order->payment_status }}
                                        </span>
                                    </td>
                                    <td>
                                         <span class="status-badge" data-status="{{ $order->status }}">
                                            {{ $order->status }}
                                         </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.viewOrderDetail', $order->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Không có đơn hàng nào phù hợp.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {!! $orders->appends(request()->query())->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
        <br>
    </div>
  </main>

  @include('admin.adminscript')
</body>

</html>
