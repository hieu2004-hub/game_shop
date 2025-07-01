<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('./assets/css/myCart.css') }}">
</head>
<body>

  <header>
    @include('home.header')
  </header>

  <main>
    <div class="container">
    @if($cart->isEmpty())
        <div class="empty-cart-message">
            Giỏ hàng của bạn đang trống. <br>
            <a href="{{ url('/') }}" class="continue-shopping-link"><i class="fas fa-arrow-left"></i> Quay lại trang chủ</a>
        </div>
    @else
        <form action="{{ route('checkout') }}" method="GET" id="cart-form">
        <div class="cart-wrapper">
            <div class="cart-items-section">
                <div class="cart-header"><h2>Giỏ Hàng</h2></div>

                <div class="product-list-headers">
                    <div class="checkbox-col">
                        <input type="checkbox" id="select-all-checkbox">
                    </div>
                    <span class="header-col" style="text-align: left;">Chi tiết sản phẩm</span>
                    <span class="header-col">Số lượng</span>
                    <span class="header-col">Đơn giá</span>
                    <span class="header-col">Tổng</span>
                </div>

                <div class="product-list">
                    @foreach($cart as $item)
                        <div class="product-item" data-price="{{ $item->product->productPrice }}" data-product-id="{{ $item->product->id }}">
                            <div class="checkbox-col">
                                <input type="checkbox" class="product-checkbox" name="selected_items[]" value="{{ $item->id }}">
                            </div>
                            <div class="product-details">
                                <img src="{{ asset('Product Image/' . $item->product->productImage) }}" alt="{{ $item->product->productName }}">
                                <div class="info">
                                    <span class="product-name">{{ $item->product->productName }}</span>
                                    <a href="{{ url('delete-cart/' . $item->id) }}" class="remove-item" onclick="confirmation(event)">Xóa</a>
                                </div>
                            </div>
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn minus">-</button>
                                <input type="number" class="quantity-input" value="{{ $item->quantity }}" min="1">
                                <button type="button" class="quantity-btn plus">+</button>
                            </div>
                            <span class="price">{{ number_format($item->product->productPrice, 0, ',', '.') }} VNĐ</span>
                            <span class="item-total">{{ number_format($item->quantity * $item->product->productPrice, 0, ',', '.') }} VNĐ</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ url('/') }}" class="continue-shopping-link"><i class="fas fa-arrow-left"></i> Tiếp tục mua sắm</a>
            </div>

            <div class="order-summary-section">
                <div class="summary-line">
                    <span>Đã chọn: <span id="summary-item-count">0</span> sản phẩm</span>
                </div>
                <div class="total-cost-line">
                    <span>Tổng tạm tính: </span>
                    <span class="total-cost-value grand-total" id="summary-grand-total">0 VNĐ</span>
                </div>
                <button type="submit" class="checkout-btn" id="checkout-button" disabled>Thanh toán</button>
            </div>
        </div>
        </form>
    @endif
    </div>
  </main>

  <footer>
    @include('home.footer')
  </footer>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right" };

        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const checkoutButton = document.getElementById('checkout-button');
        const cartForm = document.getElementById('cart-form');

        function updateTotalAndItemTotal(quantityInput) {
            const productItem = quantityInput.closest('.product-item');
            const quantity = parseInt(quantityInput.value);
            const price = parseFloat(productItem.dataset.price);
            const itemTotalEl = productItem.querySelector('.item-total');
            itemTotalEl.innerText = `${(quantity * price).toLocaleString('vi-VN')} VNĐ`;
            updateSummary();
        }

        function updateSummary() {
            let totalItems = 0;
            let grandTotal = 0;
            let selectedCount = 0;

            productCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const productItem = checkbox.closest('.product-item');
                    const quantity = parseInt(productItem.querySelector('.quantity-input').value);
                    const price = parseFloat(productItem.dataset.price);
                    totalItems += quantity;
                    grandTotal += quantity * price;
                    selectedCount++;
                }
            });

            document.getElementById('summary-item-count').innerText = totalItems;
            document.getElementById('summary-grand-total').innerText = `${grandTotal.toLocaleString('vi-VN')} VNĐ`;
            checkoutButton.disabled = selectedCount === 0;
        }

        selectAllCheckbox.addEventListener('change', () => {
            productCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
            updateSummary();
        });

        productCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                selectAllCheckbox.checked = Array.from(productCheckboxes).every(c => c.checked);
                updateSummary();
            });
        });

        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const control = this.closest('.quantity-control');
                const input = control.querySelector('.quantity-input');
                let value = parseInt(input.value);
                if (this.classList.contains('plus')) {
                    value++;
                } else if (this.classList.contains('minus') && value > 1) {
                    value--;
                }
                input.value = value;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function(e) {
                const productItem = this.closest('.product-item');
                const productId = productItem.dataset.productId;
                const newQuantity = this.value;
                updateTotalAndItemTotal(this);

                fetch(`/update-cart/${productId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ quantity: newQuantity })
                }).then(res => res.json()).then(data => {
                    if (!data.success) {
                        toastr.error(data.message);
                        // Optional: revert quantity if server fails
                    }
                });
            });
        });

        cartForm.addEventListener('submit', function(e) {
            if (document.querySelectorAll('.product-checkbox:checked').length === 0) {
                e.preventDefault();
                toastr.error('Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
            }
        });

        updateSummary();
    });

    function confirmation(ev) {
        ev.preventDefault();
        var urlToRedirect = ev.currentTarget.getAttribute('href');
        Swal.fire({
            title: "Bạn chắc chắn không?",
            text: "Bạn có muốn xóa sản phẩm này khỏi giỏ hàng không?",
            icon: "warning",
            showCancelButton: true, confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33', confirmButtonText: 'Có, xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => { if (result.isConfirmed) { window.location.href = urlToRedirect; } });
    }
  </script>

</body>
</html>
