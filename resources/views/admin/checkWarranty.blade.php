<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <!-- SB Admin 2 thường sử dụng Font Awesome cho icons, đảm bảo đã được include -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Loại bỏ link Bootstrap CDN vì admin.admincss đã bao gồm Bootstrap (hoặc SB Admin 2 dựa trên nó) -->
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    <style>
        /* Tùy chỉnh nhỏ nếu cần */
        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            box-sizing: border-box;
        }
        .card-body p {
            margin-bottom: 0.5rem; /* Khoảng cách giữa các dòng thông tin */
        }
        .card-body p:last-child {
            margin-bottom: 0;
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Kiểm Tra Bảo Hành Sản Phẩm</h1>
            {{-- Bạn có thể thêm nút quay lại nếu có trang trước --}}
            {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
            </a> --}}
        </div>

        <!-- Form Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Nhập thông tin sản phẩm</h6>
            </div>
            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('warranty.check') }}" method="GET">
                    <div class="form-group row">
                        <label for="serial_number" class="col-sm-3 col-form-label">Nhập số Serial:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="serial_number" name="serial_number" required placeholder="Ví dụ: SN-ABC-123" value="{{ request('serial_number') }}">
                            @error('serial_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mt-4">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-search"></i> Kiểm Tra
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @isset($productInstance)
            <!-- Warranty Info Card -->
            <div class="card shadow mb-4 mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin Bảo hành:</h6>
                </div>
                <div class="card-body">
                    <p class="card-text"><strong>Tên sản phẩm: </strong>{{ $productInstance->product->productName }}</p>
                    <p class="card-text"><strong>Số Serial: </strong> {{ $productInstance->serial_number }}</p>
                    <p class="card-text"><strong>Ngày mua: </strong>{{ \Carbon\Carbon::parse($productInstance->purchase_date)->format('d-m-Y') }}</p>
                    <p class="card-text"><strong>Ngày bắt đầu bảo hành: </strong>{{ \Carbon\Carbon::parse($productInstance->warranty_start_date)->format('d-m-Y') }}</p>
                    <p class="card-text"><strong>Ngày hết hạn bảo hành: </strong>{{ \Carbon\Carbon::parse($productInstance->warranty_end_date)->format('d-m-Y') }}</p>

                    @if($productInstance->notes)
                        <p class="card-text"><strong>Ghi chú: </strong> {{ $productInstance->notes }}</p>
                    @endif
                </div>
            </div>
        @endisset

        {{-- Hiển thị lỗi nếu có (nếu bạn muốn hiển thị lỗi ngoài session error) --}}
        @isset($error_message_from_controller) {{-- Đặt tên biến rõ ràng để tránh trùng với $error của Laravel --}}
            <div class="alert alert-danger mt-4 alert-dismissible fade show" role="alert">
                {{ $error_message_from_controller }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endisset
    </div>
  </main>

<!--   Core JS Files   -->
@include('admin.adminscript')
</body>

</html>
