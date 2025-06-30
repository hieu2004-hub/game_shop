<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('./assets/css/adminOrder.css') }}">
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Quản lý Đơn Hàng</h1>
        </div>

        <!-- SỬA: Cập nhật card bộ lọc để thêm ô tìm kiếm -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tìm kiếm & Bộ lọc</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.viewOrders') }}" method="GET" class="row g-3 align-items-end">
                    <!-- Ô tìm kiếm -->
                    <div class="col-md-4">
                        <label for="search" class="form-label">Tìm kiếm đơn hàng:</label>
                        <!-- SỬA DÒNG NÀY -->
                        <input type="text" name="search" id="search" class="form-control" placeholder="Mã đơn hàng, tên, SĐT..." value="{{ request('search') }}">
                    </div>
                    <!-- Bộ lọc trạng thái xử lý -->
                    <div class="col-md-3">
                        <label for="status" class="form-label">Trạng thái xử lý:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">-- Tất cả --</option>
                            <option value="Chờ Xử Lý" @if(request('status') == 'Chờ Xử Lý') selected @endif>Chờ Xử Lý</option>
                            <option value="Đã Xác Nhận" @if(request('status') == 'Đã Xác Nhận') selected @endif>Đã Xác Nhận</option>
                            <option value="Đã Nhận Được Hàng" @if(request('status') == 'Đã Nhận Được Hàng') selected @endif>Đã Nhận Được Hàng</option>
                            <option value="Hủy Đơn Hàng" @if(request('status') == 'Hủy Đơn Hàng') selected @endif>Hủy Đơn Hàng</option>
                            <option value="Thanh toán thất bại" @if(request('status') == 'Thanh toán thất bại') selected @endif>Thanh toán thất bại</option>
                        </select>
                    </div>
                    <!-- Bộ lọc trạng thái thanh toán -->
                    <div class="col-md-3">
                        <label for="payment_status" class="form-label">Trạng thái thanh toán:</label>
                        <select name="payment_status" id="payment_status" class="form-control">
                            <option value="">-- Tất cả --</option>
                            <option value="Đã thanh toán" @if(request('payment_status') == 'Đã thanh toán') selected @endif>Đã thanh toán</option>
                            <option value="Chưa thanh toán" @if(request('payment_status') == 'Chưa thanh toán') selected @endif>Chưa thanh toán (COD)</option>
                            <option value="Chờ hoàn tiền" @if(request('payment_status') == 'Chờ hoàn tiền') selected @endif>Chờ hoàn tiền</option>
                            <option value="Đã hoàn tiền" @if(request('payment_status') == 'Đã hoàn tiền') selected @endif>Đã hoàn tiền</option>
                            <option value="Đã hủy" @if(request('payment_status') == 'Đã hủy') selected @endif>Đã hủy (COD)</option>
                        </select>
                    </div>
                    <!-- Nút bấm -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lọc / Tìm</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <!-- SỬA: Tiêu đề động -->
                <h6 class="m-0 font-weight-bold text-primary">
                    @if(request()->hasAny(['search', 'status', 'payment_status']))
                        Kết quả lọc/tìm kiếm
                    @else
                        Danh sách đơn hàng
                    @endif
                </h6>
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
                                    <td colspan="7" class="text-center">Không có đơn hàng nào phù hợp với tiêu chí.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {!! $orders->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
        <br>
    </div>
  </main>

  @include('admin.adminscript')
</body>

</html>
