<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('./assets/css/viewWarehouseStock.css') }}">
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Tồn Kho của Kho: {{ $warehouse->name }}</h1>
            <a href="{{ route('admin.viewWarehouses') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay Lại
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.viewWarehouseStock', $warehouse->id) }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm trong kho này..." value="{{ $search ?? '' }}">
                        {{-- <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button> --}}
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    @if ($search)
                        Kết quả tìm kiếm cho "{{ $search }}"
                    @else
                        Chi tiết tồn kho
                    @endif
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-gradient-primary text-white">
                            <tr>
                                <th>STT</th>
                                <th class="text-start">Tên Sản Phẩm</th>
                                <th>Mã Lô Hàng</th>
                                <th>Số Lượng</th>
                                <th>Giá Nhập</th>
                                <th>Ngày Nhập</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockEntries as $stockEntry)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td class="text-start">
                                        <span class="truncate-text" title="{{ $stockEntry->product->productName }}">
                                            {{ $stockEntry->product->productName }}
                                        </span>
                                    </td>
                                    <td>{{ $stockEntry->batch_identifier ?? 'N/A' }}</td>
                                    <td>{{ $stockEntry->quantity }}</td>
                                    <td>{{ number_format($stockEntry->import_price, 0, ',', '.') }} VNĐ</td>
                                    <td>{{ $stockEntry->received_date ? \Carbon\Carbon::parse($stockEntry->received_date)->format('d-m-Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-info btn-sm"
                                                onclick="openEditModal(
                                                    {{ $stockEntry->id }},
                                                    {{ json_encode($stockEntry->batch_identifier) }},
                                                    {{ $stockEntry->quantity }},
                                                    {{ $stockEntry->import_price }},
                                                    {{ json_encode($stockEntry->received_date ? \Carbon\Carbon::parse($stockEntry->received_date)->format('Y-m-d') : '') }},
                                                    {{ json_encode($stockEntry->notes) }}
                                                )">
                                                <i class="fas fa-edit"></i>
                                                </button>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="openReduceModal({{ $stockEntry->id }}, '{{ $stockEntry->product->productName }}', {{ $stockEntry->quantity }})">
                                            <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        @if ($search)
                                            Không tìm thấy sản phẩm nào khớp với "{{ $search }}" trong kho này.
                                        @else
                                            Kho này hiện không có sản phẩm nào.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {!! $stockEntries->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>
  </main>

  <!-- THÊM LẠI: HTML cho các Modal -->
  <!-- Edit Stock Entry Modal -->
  <div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStockModalLabel">Chỉnh Sửa Lô Hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStockForm" action="" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_batch_identifier">Mã lô hàng:</label>
                        <input type="text" class="form-control" id="edit_batch_identifier" name="batch_identifier">
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_quantity">Số lượng:</label>
                        <input type="number" class="form-control" id="edit_quantity" name="quantity" min="0" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_import_price">Giá nhập:</label>
                        <input type="number" step="1" class="form-control" id="edit_import_price" name="import_price" min="0" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_received_date">Ngày nhập:</label>
                        <input type="date" class="form-control" id="edit_received_date" name="received_date">
                    </div>
                    <div class="form-group">
                        <label for="edit_notes">Ghi chú:</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
  </div>

  <!-- Reduce Stock Entry Modal -->
  <div class="modal fade" id="reduceStockModal" tabindex="-1" role="dialog" aria-labelledby="reduceStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reduceStockModalLabel">Trả Hàng / Giảm Số Lượng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reduceStockForm" action="" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <p>Sản phẩm: <strong id="reduce_product_name"></strong></p>
                    <p>Số lượng hiện tại: <strong id="reduce_current_quantity"></strong></p>
                    <div class="form-group mb-3">
                        <label for="quantity_to_reduce">Số lượng muốn giảm:</label>
                        <input type="number" class="form-control" id="quantity_to_reduce" name="quantity_to_reduce" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="reduction_notes">Lý do giảm (tùy chọn):</label>
                        <textarea class="form-control" id="reduction_notes" name="reduction_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Giảm số lượng</button>
                </div>
            </form>
        </div>
    </div>
  </div>

  @include('admin.adminscript')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script type="text/javascript">
    function openEditModal(id, batchIdentifier, quantity, importPrice, receivedDate, notes) {
        var form = document.getElementById('editStockForm');
        form.action = "{{ url('admin/stock-entries/update') }}/" + id;

        document.getElementById('edit_batch_identifier').value = batchIdentifier;
        document.getElementById('edit_quantity').value = quantity;
        document.getElementById('edit_import_price').value = importPrice;
        document.getElementById('edit_received_date').value = receivedDate;
        document.getElementById('edit_notes').value = notes;

        var myModal = new bootstrap.Modal(document.getElementById('editStockModal'));
        myModal.show();
    }

    function openReduceModal(id, productName, currentQuantity) {
        var form = document.getElementById('reduceStockForm');
        form.action = "{{ url('admin/stock-entries/reduce') }}/" + id;

        document.getElementById('reduce_product_name').innerText = productName;
        document.getElementById('reduce_current_quantity').innerText = currentQuantity;
        document.getElementById('quantity_to_reduce').max = currentQuantity;
        document.getElementById('quantity_to_reduce').value = 1;
        document.getElementById('reduction_notes').value = '';

        var myModal = new bootstrap.Modal(document.getElementById('reduceStockModal'));
        myModal.show();
    }
  </script>

</body>
</html>
