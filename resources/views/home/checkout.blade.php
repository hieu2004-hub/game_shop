<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    <link rel="stylesheet" href="{{ asset('./assets/css/checkout.css') }}">
    <style>
    </style>
</head>
<body>

  <header>
    @include('home.header')
  </header>

  <main>
    <div class="checkout-wrapper"> {{-- Wrapper chính cho toàn bộ trang checkout --}}
        <div class="checkout-container">
            {{-- Cột trái: Thông tin địa chỉ, giao hàng, thanh toán --}}
            <div class="checkout-left">
                <div class="checkout-header">
                    <h1 class="logo">Game Shop</h1> {{-- Tên thương hiệu --}}
                    <a href="{{ url('/') }}" class="continue-shopping">Tiếp tục mua sắm &rarr;</a>
                </div>

                {{-- Phần thông tin khách hàng và form --}}
                <div class="checkout-section shipping-address">
                    <div class="section-content">
                        <form action="{{ url('placeOrder') }}" method="POST" id="checkout-form">
                            @csrf
                            {{-- <input type="hidden" name="total_momo" value="{{ $grandTotal }}"> --}}
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

                            {{-- NEW: Phần chọn phương thức nhận hàng --}}
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

                            {{-- NEW: Phần chọn phương thức thanh toán --}}
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

                {{-- Nút "Đặt hàng" --}}
                <div class="continue-button-container">
                    <button class="btn btn-primary" type="submit" form="checkout-form">Đặt hàng</button>
                </div>
            </div>

            {{-- Cột phải: Tóm tắt đơn hàng --}}
            <div class="checkout-right">
                <div class="order-summary-box">
                    <h3>Hóa Đơn</h3>
                    @if($cartItems->isEmpty())
                        <p>Giỏ hàng của bạn đang trống.</p>
                    @else
                        <div class="summary-product-list"> {{-- Thẻ div mới để chứa tất cả sản phẩm --}}
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

                        {{-- Phần tổng cộng --}}
                        <div class="summary-totals">
                            <div class="total-row grand-total-row">
                                <span>Tổng giá: </span>
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

  {{-- <script>
    document.getElementById('checkout-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

        if (paymentMethod === 'Thẻ ngân hàng/Ví điện tử') {
            // Redirect to the momo payment route
            this.action = '{{ route("momo.payment") }}'; // Update the action URL
            this.submit(); // Submit the form to momo payment route
        } else {
            // For cash payment, proceed to place the order normally
            this.action = '{{ route("placeOrder") }}'; // Update the action URL
            this.submit(); // Submit the form to place the order
        }
    });
  </script> --}}
</body>
</html>
