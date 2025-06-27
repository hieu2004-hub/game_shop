<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    <link rel="stylesheet" href="{{ asset('./assets/css/productDetail.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

  <header>
    @include('home.header')
  </header>

  <main>
    <div class="product-detail-wrapper">
        <div class="product-content-top"> {{-- Container cho phần trên (ảnh và thông tin) --}}
            <div class="product-image-container">
                <img src="{{ asset('Product Image/' . $productDetail->productImage) }}" alt="{{ $productDetail->productName }}" class="product-image">
            </div>
            <div class="product-info-details">
                <h1 class="product-name-title">{{ $productDetail->productName }}</h1>
                <p class="product-price">Giá: <span>{{ number_format($productDetail->productPrice, 0, ',', '.') }} VNĐ</span></p>

                {{-- Thêm các icon để tăng tính trực quan --}}
                <p class="product-category"><i class="fas fa-tag"></i> Danh mục: <span>{{ $productDetail->productCategory }}</span></p>
                <p class="product-brand"><i class="fas fa-building"></i> Hãng: <span>{{ $productDetail->productBrand }}</span></p>

                @if ($productDetail->is_warrantable == 1)
                    <p class="product-warranty"><i class="fas fa-shield-alt"></i> Bảo hành: <strong>Hỗ Trợ</strong></p>
                @else
                    <p class="product-warranty"><i class="fas fa-times-circle"></i> Bảo hành: <strong class="not-supported">Không hỗ trợ</strong></p>
                @endif

                {{-- BẮT ĐẦU PHẦN LOGIC HIỂN THỊ DỰA TRÊN TỒN KHO --}}
                @if ($productDetail->total_stock_quantity > 0)
                    {{-- Trường hợp: Sản phẩm còn hàng --}}
                    <p class="product-stock"><i class="fas fa-check-circle"></i> Tình trạng: <span style="color: #28a745; font-weight: bold;">Còn hàng</span> (<span style="font-weight: bold;">{{ $productDetail->total_stock_quantity }}</span> sản phẩm)</p>

                    <div class="quantity-section">
                        <label for="quantity">Số lượng:</label>
                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn minus-btn">-</button>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" readonly>
                            <button type="button" class="quantity-btn plus-btn">+</button>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button class="add-to-cart-btn" onclick="addToCart({{ $productDetail->id }})">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                    </div>
                @elseif ($productDetail->total_stock_quantity == 0 && $productDetail->warehouseStocks->isEmpty())
                    {{-- Trường hợp: Sản phẩm mới thêm, chưa nhập hàng (chưa có lô hàng nào) --}}
                    <p class="product-stock"><i class="fas fa-truck-loading"></i> Tình trạng: <span style="color: #ffc107; font-weight: bold;">Sắp về hàng</span></p>
                    <div class="action-buttons">
                        <button class="add-to-cart-btn disabled" disabled>
                            <i class="fas fa-truck-loading"></i> Sắp về hàng
                        </button>
                    </div>
                @else
                    {{-- Trường hợp: Đã nhập hàng nhưng hết hàng (tồn kho = 0, nhưng đã có lô hàng) --}}
                    <p class="product-stock"><i class="fas fa-times-circle"></i> Tình trạng: <span style="color: #dc3545; font-weight: bold;">Tạm thời hết hàng</span></p>
                    <div class="action-buttons">
                        <button class="add-to-cart-btn disabled" disabled>
                            <i class="fas fa-times-circle"></i> Tạm thời hết hàng
                        </button>
                    </div>
                @endif
                {{-- KẾT THÚC PHẦN LOGIC HIỂN THỊ DỰA TRÊN TỒN KHO --}}

            </div>
        </div>

        <hr class="section-divider"> {{-- Đường phân cách --}}

        <div class="product-description-section">
            <div class="description-content-inner"> {{-- NEW WRAPPER --}}
                <h2>Mô tả sản phẩm</h2>
                {!! $productDetail->productDescription ?? '<p>Chưa có mô tả chi tiết cho sản phẩm này.</p>' !!}
            </div> {{-- END NEW WRAPPER --}}
        </div>
    </div>
  </main>

  <footer>
    @include('home.footer')
  </footer>

  <script src="{{ asset('./assets/js/script.js') }}"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const minusBtn = document.querySelector('.minus-btn');
        const plusBtn = document.querySelector('.plus-btn');
        const addToCartBtn = document.querySelector('.add-to-cart-btn');

        // Chỉ chạy logic tăng/giảm và thêm vào giỏ nếu các phần tử tồn tại và nút không bị disabled
        if (quantityInput && minusBtn && plusBtn && addToCartBtn && !addToCartBtn.disabled) {
            const stockQuantity = parseInt('{{ $productDetail->total_stock_quantity }}');

            minusBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });

            plusBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                // Kiểm tra số lượng hiện tại nhỏ hơn tổng số lượng tồn kho
                if (currentValue < stockQuantity) {
                    quantityInput.value = currentValue + 1;
                } else {
                    alert('Số lượng đã đạt tối đa trong kho!');
                }
            });

            // Gán hàm addToCart vào window để có thể gọi từ onclick
            window.addToCart = function(productId) {
                const quantity = quantityInput.value;
                window.location.href = '{{ url("addToCart") }}' + '/' + productId + '?quantity=' + quantity;
            };
        } else {
            // Nếu nút thêm vào giỏ bị disabled hoặc không tồn tại, đảm bảo hàm addToCart không gây lỗi
            window.addToCart = function(productId) {
                // Không làm gì cả, vì nút đã bị vô hiệu hóa
                console.log('Sản phẩm không có sẵn để thêm vào giỏ.');
            };
        }
    });
  </script>
</body>
</html>
