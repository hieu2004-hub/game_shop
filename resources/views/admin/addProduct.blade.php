<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <!-- SB Admin 2 thường sử dụng Font Awesome cho icons, đảm bảo đã được include -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Bạn có thể giữ productCRUD.css nếu nó chứa các style độc đáo không xung đột,
         nhưng ưu tiên dùng Bootstrap/SB Admin 2 classes -->
    <!-- <link rel="stylesheet" href="{{asset('assets/css/productCRUD.css')}}"> -->

    <!-- NEW: CKEditor 5 Classic Build CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

    <style>
        /* Tùy chỉnh nhỏ nếu cần để CKEditor hiển thị đẹp hơn */
        .ck-editor__editable_inline {
            min-height: 200px; /* Chiều cao tối thiểu cho CKEditor */
            border: 1px solid #d1d3e2; /* Màu viền giống input của SB Admin 2 */
            border-radius: 0.35rem; /* Bo tròn góc */
            padding: 0.75rem 1rem; /* Padding bên trong */
        }
        .form-check-input {
            margin-top: 0.3rem; /* Căn chỉnh checkbox */
            margin-left: 0; /* Bỏ margin mặc định của Bootstrap */
        }
        .form-check-label {
            margin-left: 0.5rem; /* Khoảng cách giữa checkbox và label */
        }
        /* Đảm bảo các label và input có khoảng cách hợp lý */
        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            box-sizing: border-box; /* Đảm bảo padding không làm tăng chiều rộng */
        }
        .form-group select {
            height: calc(1.5em + 0.75rem + 2px); /* Chiều cao phù hợp với input */
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">
    @include('admin.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                <h1 class="h3 mb-0 text-gray-800">Thêm Sản phẩm</h1>
                <a href="{{ url('viewProduct') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
                </a>
            </div>

            <!-- Form Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm mới</h6>
                </div>
                <div class="card-body">
                    <form action="{{ url('createProduct') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label for="productName" class="col-sm-3 col-form-label">Tên sản phẩm</label>
                            <div class="col-sm-9">
                                <input type="text" name="productName" id="productName" class="form-control" required value="{{ old('productName') }}">
                                @error('productName')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="productPrice" class="col-sm-3 col-form-label">Giá</label>
                            <div class="col-sm-9">
                                <input type="number" name="productPrice" id="productPrice" class="form-control" required min="0" value="{{ old('productPrice') }}">
                                @error('productPrice')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Bảo hành</label>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <input type="checkbox" name="is_warrantable" id="is_warrantable" value="1" class="form-check-input" {{ old('is_warrantable', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_warrantable">Sản phẩm có bảo hành?</label>
                                </div>
                                @error('is_warrantable')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="default_warranty_months" class="col-sm-3 col-form-label">Thời gian bảo hành mặc định (tháng)</label>
                            <div class="col-sm-9">
                                <input type="number" name="default_warranty_months" id="default_warranty_months" class="form-control" min="0" value="{{ old('default_warranty_months', 12) }}">
                                @error('default_warranty_months')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="productCategory" class="col-sm-3 col-form-label">Danh mục</label>
                            <div class="col-sm-9">
                                <select name="productCategory" id="productCategory" class="form-control" required>
                                    <option value="">Chọn Danh mục</option>
                                    <option value="console" {{ old('productCategory') == 'console' ? 'selected' : '' }}>Console</option>
                                    <option value="disc" {{ old('productCategory') == 'disc' ? 'selected' : '' }}>Disc</option>
                                    <option value="controller" {{ old('productCategory') == 'controller' ? 'selected' : '' }}>Controller</option>
                                    <option value="accessory" {{ old('productCategory') == 'accessory' ? 'selected' : '' }}>Accessory</option>
                                </select>
                                @error('productCategory')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="productBrand" class="col-sm-3 col-form-label">Hãng</label>
                            <div class="col-sm-9">
                                <select name="productBrand" id="productBrand" class="form-control" required>
                                    <option value="">Chọn Hãng</option>
                                    <option value="sony" {{ old('productBrand') == 'sony' ? 'selected' : '' }}>Sony</option>
                                    <option value="nintendo" {{ old('productBrand') == 'nintendo' ? 'selected' : '' }}>Nintendo</option>
                                    <option value="xbox" {{ old('productBrand') == 'xbox' ? 'selected' : '' }}>Xbox</option>
                                </select>
                                @error('productBrand')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="productImage" class="col-sm-3 col-form-label">Hình ảnh</label>
                            <div class="col-sm-9">
                                <input type="file" name="productImage" id="productImage" class="form-control-file" required>
                                @error('productImage')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="productDescription" class="col-sm-3 col-form-label">Mô tả sản phẩm</label>
                            <div class="col-sm-9">
                                <textarea name="productDescription" id="productDescription" class="form-control">{{ old('productDescription') }}</textarea>
                                @error('productDescription')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                    <i class="fas fa-plus"></i> Thêm sản phẩm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>
    <!--   Core JS Files   -->
    @include('admin.adminscript')
    <script>
        ClassicEditor
            .create(document.querySelector('#productDescription'))
            .catch(error => {
                console.error( error );
            });

        // Optional: Logic để ẩn/hiện thời gian bảo hành mặc định
        document.addEventListener('DOMContentLoaded', function() {
            const isWarrantableCheckbox = document.getElementById('is_warrantable');
            const defaultWarrantyMonthsGroup = document.getElementById('default_warranty_months').closest('.form-group.row');

            function toggleWarrantyMonths() {
                if (isWarrantableCheckbox.checked) {
                    defaultWarrantyMonthsGroup.style.display = 'flex'; // Use flex to maintain row layout
                    document.getElementById('default_warranty_months').setAttribute('required', 'required');
                } else {
                    defaultWarrantyMonthsGroup.style.display = 'none';
                    document.getElementById('default_warranty_months').removeAttribute('required');
                }
            }

            isWarrantableCheckbox.addEventListener('change', toggleWarrantyMonths);

            // Initial call to set visibility based on current state
            toggleWarrantyMonths();
        });
    </script>
</body>

</html>
