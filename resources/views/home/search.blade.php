<!DOCTYPE html>
<html lang="en">

<head>
    @include('home.homecss')
</head>

<body>

    <header>
        @include('home.header')
    </header>

    <main>
        <div class="container">
            {{-- Kiểm tra nếu không có sản phẩm nào được tìm thấy --}}
            @if ($products->isEmpty())
                <div class="alert alert-warning text-center" role="alert" style="margin-top: 50px; padding: 30px; border-radius: 8px;">
                    <h4 class="alert-heading" style="color: #856404;">Không tìm thấy sản phẩm nào!</h4>
                    <p style="margin-bottom: 0;">Rất tiếc, không có sản phẩm nào phù hợp với từ khóa: "<strong style="color: #856404;">{{ request()->search }}</strong>".</p>
                    <hr style="border-top: 1px solid #ffeeba;">
                    <p class="mb-0">Vui lòng thử lại với từ khóa tìm kiếm khác.</p>
                </div>
            @else
                <div class="product-main">
                    <div class="product-grid">
                        @foreach ($products as $product)
                            <div class="showcase">
                                <div class="showcase-banner">
                                    <img src="{{ asset('Product Image/' . $product->productImage) }}" alt="product image"
                                        class="product-img default" sizes="100" />
                                    <img src="{{ asset('Product Image/' . $product->productImage) }}"
                                        alt="product image" class="product-img hover" />
                                    <div class="showcase-actions">
                                        {{-- Thay đổi button thành thẻ a và trỏ đến route addToCart --}}
                                        <a href="{{ route('addToCart', $product->id) }}" class="btn-action">
                                            <ion-icon name="bag-add-outline"></ion-icon>
                                        </a>
                                    </div>
                                </div>
                                <div class="showcase-content">
                                    <h3>
                                        <a href="{{ route('productDetails', $product->id) }}"
                                            class="showcase-title">{{ $product->productName }}</a>
                                    </h3>
                                    <div class="price-box">
                                        <p class="price">{{ number_format($product->productPrice, 0, ',', '.') }} VNĐ</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    {!! $products->appends(['search' => request()->input('search')])->links() !!}
                </div>
            @endif
        </div>
    </main>

    <footer>
        @include('home.footer')
    </footer>

    <script src="{{ asset('./assets/js/script.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    {{-- Thêm jQuery (nếu chưa có trong homecss) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Thêm JS của Toastr --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Kiểm tra nếu collection products rỗng
            @if ($products->isEmpty())
                // Cấu hình Toastr
                toastr.options = {
                    "closeButton": true, // Nút đóng
                    "progressBar": true, // Thanh tiến trình
                    "positionClass": "toast-top-right", // Vị trí hiển thị
                    "showDuration": "300", // Thời gian hiển thị
                    "hideDuration": "1000", // Thời gian ẩn
                    "timeOut": "5000", // Thời gian tồn tại
                    "extendedTimeOut": "1000", // Thời gian tồn tại khi di chuột qua
                    "showEasing": "swing", // Hiệu ứng hiển thị
                    "hideEasing": "linear", // Hiệu ứng ẩn
                    "showMethod": "fadeIn", // Phương thức hiển thị
                    "hideMethod": "fadeOut" // Phương thức ẩn
                };
                // Hiển thị thông báo warning
                toastr.warning('Không tìm thấy sản phẩm nào phù hợp với từ khóa "' + '{{ request()->search }}' + '".', 'Thông báo tìm kiếm');
            @endif
        });
    </script>

</body>

</html>
