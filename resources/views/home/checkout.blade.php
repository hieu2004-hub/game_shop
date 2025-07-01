<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    <link rel="stylesheet" href="{{ asset('./assets/css/checkout.css') }}">
</head>
<body>

  <header>
    @include('home.header')
  </header>

  <main>
    <div class="checkout-wrapper">
        <div class="checkout-container">
            <div class="checkout-left">
                <div class="checkout-header">
                    <h1 class="logo">Game Shop</h1>
                    <a href="{{ route('myCart') }}" class="continue-shopping">← Quay lại giỏ hàng</a>
                </div>

                <!-- THÊM MỚI: Khu vực hiển thị thông báo lỗi -->
                @if(session('error'))
                    <div class="alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="checkout-section shipping-address">
                    <div class="section-content">
                        <!-- Form giữ nguyên như cũ -->
                        <form action="{{ route('placeOrder') }}" method="POST" id="checkout-form">
                           <!-- ... Toàn bộ nội dung form của bạn giữ nguyên ... -->
                           @csrf
                            @if(isset($cartItems))
                                @foreach($cartItems as $item)
                                    <input type="hidden" name="selected_cart_item_ids[]" value="{{ $item->id }}">
                                @endforeach
                            @endif
                            <div class="form-group">
                                <label for="name">Họ và tên:</label>
                                <input type="text" id="name" name="name"
                                       value="{{ old('name', $user ? $user->userName : '') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Số điện thoại:</label>
                                <input type="tel" id="phone" name="phone"
                                       value="{{ old('phone', $user ? $user->phone : '') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Địa chỉ:</label>
                                <input type="text" id="address" name="address"
                                       value="{{ old('address', $user ? $user->address : '') }}" required>
                            </div>

                            <div class="checkout-section delivery-method">
                                <h3>Phương thức nhận hàng</h3>
                                <div class="options-group">
                                    <label class="radio-label">
                                        <input type="radio" name="delivery_method" value="Giao tận nơi" checked>
                                        Giao tận nơi
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="delivery_method" value="Đến cửa hàng nhận">
                                        Đến cửa hàng nhận
                                    </label>
                                </div>
                            </div>

                            <div class="checkout-section payment-method">
                                <h3>Phương thức thanh toán</h3>
                                <div class="options-group">
                                    <label class="radio-label">
                                        <input type="radio" name="payment_method" value="Tiền mặt" checked>
                                        Tiền mặt
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="payment_method" value="Thẻ ngân hàng/Ví điện tử">
                                        Thẻ ngân hàng/Ví điện tử (Momo)
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="continue-button-container">
                    <button class="btn btn-primary" type="submit" form="checkout-form">Đặt hàng</button>
                </div>
            </div>

            <div class="checkout-right">
                <!-- Phần tóm tắt đơn hàng giữ nguyên -->
                <div class="order-summary-box">
                    <h3>Hóa Đơn</h3>
                    @if(!isset($cartItems) || $cartItems->isEmpty())
                        <p>Không có sản phẩm nào để thanh toán.</p>
                    @else
                        <div class="summary-product-list">
                            @foreach($cartItems as $item)
                                <div class="summary-product-item">
                                    <img src="{{ asset('Product Image/' . $item->product->productImage) }}" alt="{{ $item->product->productName }}" class="item-image">
                                    <div class="item-info">
                                        <span class="item-name">{{ $item->product->productName }}</span>
                                        <span class="item-quantity">Số lượng: {{ $item->quantity }}</span>
                                    </div>
                                    <span class="item-price">{{ number_format($item->quantity * $item->product->productPrice, 0, ',', '.') }} VNĐ</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="summary-totals">
                            <div class="total-row grand-total-row">
                                <span>Tổng cộng: </span>
                                <span>{{ number_format($grandTotal, 0, ',', '.') }} VNĐ</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
  </main>

  <footer>
    @include('home.footer')
  </footer>

  <script src="{{ asset('./assets/js/script.js') }}"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
