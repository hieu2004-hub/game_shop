<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <!-- SB Admin 2 thường sử dụng Font Awesome cho icons, đảm bảo đã được include -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Có thể bỏ productCRUD.css và warehouse.css nếu các style của chúng được thay thế hoàn toàn bằng Bootstrap/SB Admin 2 -->
    <link rel="stylesheet" href="{{ asset('assets/css/warehouse.css') }}">
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Danh Sách Kho Hàng</h1>
            <a href="{{ route('admin.addWarehouse') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Thêm Kho Mới
            </a>
        </div>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin chi tiết các kho hàng</h6>
            </div>
            <div class="card-body">
                @if($warehouses->isEmpty())
                    <div class="alert alert-info text-center" role="alert">
                        Không có kho hàng nào được tìm thấy.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="bg-gradient-primary text-white">
                                <tr>
                                    <th>STT</th>
                                    <th>Tên Kho</th>
                                    <th>Địa Chỉ</th>
                                    <th>Người Liên Hệ</th>
                                    <th>Điện Thoại</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warehouses as $warehouse)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $warehouse->name }}</td>
                                        <td>{{ $warehouse->address ?? 'N/A' }}</td>
                                        <td>{{ $warehouse->contact_person ?? 'N/A' }}</td>
                                        <td>{{ $warehouse->phone ?? 'N/A' }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.editWarehouse', $warehouse->id) }}" class="btn btn-primary btn-circle btn-sm" title="Sửa kho">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.deleteWarehouse', $warehouse->id) }}" class="btn btn-danger btn-circle btn-sm" onclick="confirmation(event)" title="Xóa kho">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                {{-- Nút xem chi tiết tồn kho --}}
                                                <a href="{{ route('admin.viewWarehouseStock', $warehouse->id) }}" class="btn btn-info btn-circle btn-sm" title="Xem tồn kho">
                                                    <i class="fas fa-box"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {!! $warehouses->links('pagination::bootstrap-4') !!}
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
    function confirmation(ev) {
        ev.preventDefault();
        var urlToRedirect = ev.currentTarget.getAttribute('href');
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa kho này?',
            text: "Hành động này không thể hoàn tác! Tất cả sản phẩm trong kho này cũng sẽ bị ảnh hưởng.",
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
        });
    }
  </script>
</body>

</html>
