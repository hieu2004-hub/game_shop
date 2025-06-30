<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/adminViewProduct.css') }}">
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
                <form action="{{ url('adminSearchProducts') }}" method="GET" class="w-100">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Tìm kiếm theo tên, danh mục, thương hiệu..." value="{{ request('search') }}">
                        {{-- <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button> --}}
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
                                <th>Ảnh</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                            <tr>
                                <td>
                                    <!-- SỬA: Bọc tên sản phẩm trong span để rút gọn và hiển thị đầy đủ khi hover -->
                                    <span class="truncate-text" title="{{ $item->productName }}">
                                        {{ $item->productName }}
                                    </span>
                                </td>
                                <td>{{ number_format($item->productPrice, 0, ',', '.') }} VNĐ</td>
                                <td>{{ $item->productCategory }}</td>
                                <td>
                                    <img src="{{ asset('Product Image/' . $item->productImage) }}" alt="{{ $item->productName }}" class="img-thumbnail-sm">
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ url('editProduct', $item->id) }}" class="btn btn-primary btn-circle btn-sm" title="Cập nhật sản phẩm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if($item->warehouseStocks->isNotEmpty())
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-circle btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Xem trong kho">
                                                    <i class="fas fa-warehouse"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @foreach($item->warehouseStocks->unique('warehouse_id') as $stock)
                                                        <li>
                                                            <!-- SỬA DÒNG NÀY -->
                                                            <a class="dropdown-item" href="{{ route('admin.viewWarehouseStock', ['warehouse_id' => $stock->warehouse_id, 'search' => $item->productName]) }}">
                                                                Kho: {{ $stock->warehouse->name ?? 'N/A' }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <button type="button" class="btn btn-success btn-circle btn-sm receive-stock-btn"
                                                    data-bs-toggle="modal" data-bs-target="#receiveStockModal"
                                                    data-product-id="{{ $item->id }}" data-product-name="{{ $item->productName }}"
                                                    title="Nhập kho sản phẩm">
                                                <i class="fas fa-dolly-flatbed"></i>
                                            </button>
                                        @endif

                                        <a href="{{ url('deleteProduct', $item->id) }}" onclick="confirmation(event)" class="btn btn-danger btn-circle btn-sm" title="Xóa sản phẩm">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {!! $products->appends(['search' => request()->input('search')])->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nhập Kho -->
    <div class="modal fade" id="receiveStockModal" tabindex="-1" aria-labelledby="receiveStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.receiveProduct') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" id="modal_product_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="receiveStockModalLabel">Nhập kho cho sản phẩm: <span id="modal_product_name"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_warehouse_id" class="form-label">Chọn kho</label>
                            <select name="warehouse_id" id="modal_warehouse_id" class="form-control" required>
                                <option value="">-- Chọn kho hàng --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal_quantity" class="form-label">Số lượng nhập</label>
                            <input type="number" name="quantity" id="modal_quantity" class="form-control" required min="1">
                        </div>
                        <!-- SỬA ĐOẠN NÀY -->
                        <div class="mb-3">
                            <label for="modal_total_import_price" class="form-label">Tổng giá nhập cho cả lô hàng</label>
                            <input type="number" name="total_import_price" id="modal_total_import_price" class="form-control" required min="0" placeholder="Ví dụ: 5000000">
                        </div>
                        <!-- KẾT THÚC SỬA -->
                         <div class="mb-3">
                            <label for="modal_received_date" class="form-label">Ngày nhập</label>
                            <input type="date" name="received_date" id="modal_received_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Xác nhận nhập kho</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

  </main>

  @include('admin.adminscript')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    document.addEventListener('DOMContentLoaded', function () {
        var receiveStockModal = document.getElementById('receiveStockModal');
        if (receiveStockModal) {
            receiveStockModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var productId = button.getAttribute('data-product-id');
                var productName = button.getAttribute('data-product-name');

                var modalTitle = receiveStockModal.querySelector('#modal_product_name');
                var modalProductIdInput = receiveStockModal.querySelector('#modal_product_id');

                modalTitle.textContent = productName;
                modalProductIdInput.value = productId;
            });
        }

        @if (session('show_receive_modal_for'))
            var productId = {{ session('show_receive_modal_for') }};
            var triggerButton = document.querySelector('.receive-stock-btn[data-product-id="' + productId + '"]');
            if (triggerButton) {
                triggerButton.click();
            }
        @endif
    });
  </script>
</body>

</html>
