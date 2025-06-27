<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <!-- SB Admin 2 thường sử dụng Font Awesome cho icons, đảm bảo đã được include -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        /* Tùy chỉnh nhỏ nếu cần */
        .img-thumbnail-sm {
            width: 80px; /* Kích thước nhỏ hơn cho ảnh trong bảng */
            height: 80px;
            object-fit: cover; /* Đảm bảo ảnh không bị méo */
            border-radius: 0.35rem; /* Bo tròn nhẹ */
        }
        .action-buttons {
            display: flex;
            gap: 8px; /* Khoảng cách giữa các nút */
        }
        /* Đảm bảo input-group không bị dính */
        .input-group > .form-control,
        .input-group > .form-select {
            position: relative;
            flex: 1 1 auto;
            width: 1%;
            min-width: 0;
        }
        .input-group .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        .input-group-append .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
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
            <h1 class="h3 mb-0 text-gray-800">Quản lý Sản phẩm</h1>
            <a href="{{url('addProduct')}}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Thêm sản phẩm
            </a>
        </div>

        <!-- Search Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tìm kiếm sản phẩm</h6>
            </div>
            <div class="card-body">
                {{-- Form tìm kiếm, đảm bảo không có form-inline gây xung đột --}}
                <form action="{{ url('adminSearchProducts') }}" method="GET" class="w-100">
                    <div class="input-group"> {{-- Bỏ w-100 ở đây, để input-group tự điều chỉnh --}}
                        <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Tìm kiếm theo tên, danh mục, thương hiệu..." aria-label="Search" aria-describedby="basic-addon2" value="{{ request('search') }}">
                        {{-- <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div> --}}
                    </div>
                </form>
            </div>
        </div>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" cellspacing="0">
                        <thead>
                            <tr class="bg-gradient-primary text-white">
                                <th>Tên</th>
                                <th>Giá</th>
                                <th>Danh mục</th>
                                <th>Thương hiệu</th>
                                <th>Ảnh</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                            <tr>
                                <td>{{ $item->productName }}</td>
                                <td>{{ number_format($item->productPrice, 0, ',', '.') }} VNĐ</td>
                                <td>{{ $item->productCategory }}</td>
                                <td>{{ $item->productBrand }}</td>
                                <td>
                                    <img src="{{ asset('Product Image/' . $item->productImage) }}" alt="{{ $item->productName }}" class="img-thumbnail-sm">
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ url('deleteProduct', $item->id) }}" onclick="confirmation(event)" class="btn btn-danger btn-circle btn-sm" title="Xóa sản phẩm">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="{{ url('editProduct', $item->id) }}" class="btn btn-primary btn-circle btn-sm" title="Cập nhật sản phẩm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {!! $products->appends(['search' => request()->input('search')])->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>

    </div>
  </main>
  <!--   Core JS Files   -->
  @include('admin.adminscript')

  <!-- Custom script for confirmation if needed -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- SweetAlert2 CDN --}}
  <script>
    function confirmation(ev) {
      ev.preventDefault();
      var urlToRedirect = ev.currentTarget.getAttribute('href');
      Swal.fire({
        title: 'Bạn có chắc chắn muốn xóa sản phẩm này?',
        text: "Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Có, xóa nó!',
        cancelButtonText: 'Hủy'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = urlToRedirect;
        }
      })
    }
  </script>
</body>

</html>
