<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <link rel="stylesheet" href="{{ asset('assets/css/receiveProduct.css') }}">
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="form-container">
            <h2>Nhập Hàng Vào Kho</h2>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.receiveProduct') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="product_search">Tìm kiếm sản phẩm:</label>
                    <input type="text" class="form-control" id="product_search" placeholder="Nhập tên sản phẩm..." autocomplete="off">
                    <input type="hidden" name="product_id" id="product_id" value="{{ old('product_id') }}" required>
                    <div id="product_search_results"></div>
                </div>

                <div class="form-group">
                    <label for="warehouse_id">Kho nhận hàng:</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                        <option value="">-- Chọn kho --</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Số lượng:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                </div>

                <div class="form-group">
                    <label for="import_price">Giá nhập (VNĐ):</label>
                    <input type="number" step="0.01" class="form-control" id="import_price" name="import_price" value="{{ old('import_price') }}" min="0" required>
                </div>

                <div class="form-group">
                    <label for="batch_identifier">Mã lô hàng (tùy chọn):</label>
                    <input type="text" class="form-control" id="batch_identifier" name="batch_identifier" value="{{ old('batch_identifier') }}">
                    <small class="form-text text-muted">Nếu để trống, hệ thống sẽ tự động tạo mã.</small>
                </div>

                <div class="form-group">
                    <label for="received_date">Ngày nhập (tùy chọn):</label>
                    <input type="date" class="form-control" id="received_date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}">
                </div>

                <div class="form-group">
                    <label for="notes">Ghi chú (tùy chọn):</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-submit">Nhập Hàng</button>
            </form>
        </div>
    </div>
  </main>

  @include('admin.adminscript')

  {{-- Thêm jQuery nếu bạn chưa có, hoặc dùng vanilla JS --}}
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {
        // Debounce function để giới hạn số lần gọi AJAX
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), delay);
            };
        }

        $('#product_search').on('keyup', debounce(function() {
            var query = $(this).val();
            var resultsContainer = $('#product_search_results');

            if (query.length > 1) { // Chỉ tìm kiếm khi độ dài chuỗi > 1 (có thể điều chỉnh)
                $.ajax({
                    url: "{{ route('admin.searchProductsAjax') }}",
                    method: 'GET',
                    data: { q: query },
                    success: function(data) {
                        resultsContainer.empty();
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                resultsContainer.append('<div data-product-id="' + value.id + '">' + value.productName + '</div>');
                            });
                            resultsContainer.show();
                        } else {
                            // Hiển thị thông báo khi không tìm thấy kết quả
                            resultsContainer.append('<div style="color: #666; font-style: italic;">Không tìm thấy sản phẩm nào.</div>');
                            resultsContainer.show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                        resultsContainer.empty().hide(); // Ẩn kết quả nếu có lỗi
                    }
                });
            } else {
                resultsContainer.empty().hide();
            }
        }, 0)); // Giảm debounce xuống 150ms để phản hồi nhanh hơn

        // Xử lý khi click vào một kết quả tìm kiếm
        $(document).on('click', '#product_search_results div', function() {
            var productId = $(this).data('product-id');
            var productName = $(this).text();

            // Chỉ gán nếu đây không phải là thông báo "Không tìm thấy"
            if (productId) {
                $('#product_id').val(productId); // Gán ID sản phẩm vào hidden input
                $('#product_search').val(productName); // Hiển thị tên sản phẩm đã chọn vào input text
            }
            $('#product_search_results').empty().hide(); // Ẩn kết quả
        });

        // Ẩn kết quả khi click ra ngoài
        $(document).on('click', function(e) {
            // Kiểm tra xem click có phải là bên trong form-group chứa input tìm kiếm không
            if (!$(e.target).closest('.form-group').is($('.form-group:has(#product_search)'))) {
                $('#product_search_results').empty().hide();
            }
        });
    });
  </script>
</body>

</html>
