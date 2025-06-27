<div class="container">
    <div class="product-main">
        <div class="product-header">
            <h2 style="text-align: center" class="product-title">Sản phẩm mới nhất</h2>
            <span></span>
        </div>
        <div class="product-grid">
            @foreach ($products as $product)
            <div class="showcase">
                <div class="showcase-banner">
                    <img src="Product Image/{{$product->productImage}}" alt="product image"
                    class="product-img default" sizes="100"/>
                    <img src="Product Image/{{$product->productImage}}" alt="product image"
                    class="product-img hover" />
                    <div class="showcase-actions">
                        <a href="{{ route('addToCart', $product->id) }}" class="btn-action">
                            <ion-icon name="bag-add-outline"></ion-icon>
                        </a>
                    </div>
                </div>
                <div class="showcase-content">
                    <p class="showcase-category">{{ $product->productBrand }}</p>
                    <p class="showcase-brand">{{ $product->productCategory }}</p>

                    <h3>
                        <a href="{{ route('productDetails', $product->id) }}" class="showcase-title">{{ $product->productName }}</a>
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
        <a href="{{ url('products') }}" class="btn btn-primary">Xem thêm sản phẩm</a>
    </div>
</div>
