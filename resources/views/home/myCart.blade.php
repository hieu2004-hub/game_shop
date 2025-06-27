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
            Giỏ hàng của bạn đang trống.
            <br>
            <a href="{{ url('/') }}" class="continue-shopping-link">
                <i class="fas fa-arrow-left"></i> Quay lại trang chủ để mua sắm
            </a>
        </div>
    @else
        <div class="cart-wrapper">
            <div class="cart-items-section">
                <div class="cart-header">
                    <h2>Giỏ Hàng</h2>
                </div>

                {{-- Product List Headers (for larger screens) --}}
                <div class="product-list-headers">
                    <span class="header-col">Chi tiết sản phẩm</span>
                    <span class="header-col">Số lượng</span>
                    <span class="header-col">Đơn giá</span>
                    <span class="header-col">Tổng</span>
                </div>

                <div class="product-list">
                    @foreach($cart as $item)
                        <div class="product-item" data-product-id="{{ $item->product->id }}" data-available-stock="{{ $item->product->stockQuantity }}">
                            <div class="product-details">
                                <img src="{{ asset('Product Image/' . $item->product->productImage) }}" alt="{{ $item->product->productName }}">
                                <div class="info">
                                    <span class="product-name">{{ $item->product->productName }}</span>
                                    <span class="product-category">{{ $item->product->productCategory }}</span>
                                    <a href="{{ url('delete-cart/' . $item->id) }}" class="remove-item" onclick="confirmation(event)">Xóa</a>
                                </div>
                            </div>
                            <div class="quantity-price-total">
                                <div class="quantity-control">
                                    <button type="button" class="quantity-btn minus">-</button>
                                    <input type="number" class="quantity-input" value="{{ $item->quantity }}" min="1">
                                    <button type="button" class="quantity-btn plus">+</button>
                                </div>
                                <span class="price">{{ number_format($item->product->productPrice, 0, ',', '.') }} VNĐ</span>
                                <span class="item-total">{{ number_format($item->quantity * $item->product->productPrice, 0, ',', '.') }} VNĐ</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="{{ url('/') }}" class="continue-shopping-link">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>

            <div class="order-summary-section">
                <div class="summary-line">
                    <span>Số sản phẩm: <span id="summary-item-count">{{ $cart->sum('quantity') }}</span></span>
                </div>
                <div class="total-cost-line">
                    <span>Tổng giá: </span>
                    <span class="total-cost-value grand-total" id="summary-grand-total">{{ number_format($cart->sum(function($item) { return $item->quantity * $item->product->productPrice; }), 0, ',', '.')}} VNĐ</span>
                </div>

                <a href="{{url('checkout')}}" style="text-align: center" class="checkout-btn" id="checkout-button">Thanh toán</a>
            </div>
        </div>
    @endif
    </div> {{-- End of .container --}}
  </main>

  <footer>
    @include('home.footer')
  </footer>

  {{-- Toastr CDN for notifications --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <script src="{{ asset('./assets/js/script.js') }}"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configure Toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Define shippingCost, or ensure it's defined elsewhere
        const shippingCost = 0; // Đặt phí vận chuyển mặc định là 0 hoặc giá trị bạn muốn

        const checkoutButton = document.getElementById('checkout-button');

        if (checkoutButton) {
            checkoutButton.addEventListener('click', function(event) {
                let valid = true;
                let errorMessage = '';

                document.querySelectorAll('.product-item').forEach(itemRow => { // Lặp qua từng dòng sản phẩm
                    const quantityInput = itemRow.querySelector('.quantity-input'); // Lấy input số lượng trong dòng này
                    const quantity = parseInt(quantityInput.value);
                    const stockQuantity = parseInt(itemRow.getAttribute('data-available-stock'));

                    if (quantity > stockQuantity) {
                        errorMessage += `Không đủ hàng trong kho cho sản phẩm: ${itemRow.querySelector('.product-name').innerText}. Số lượng tối đa bạn có thể đặt là: ${stockQuantity}\n`;
                        valid = false;
                    }
                });

                if (!valid) {
                    alert(errorMessage);
                    event.preventDefault();
                }
            });
        } else {
            console.error("Checkout button not found");
        }

        // --- JavaScript for Quantity Plus/Minus Buttons ---
        document.querySelectorAll('.quantity-control').forEach(control => {
            const minusBtn = control.querySelector('.minus');
            const plusBtn = control.querySelector('.plus');
            const quantityInput = control.querySelector('.quantity-input');

            minusBtn.addEventListener('click', () => {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue > parseInt(quantityInput.min)) {
                    quantityInput.value = currentValue - 1;
                    quantityInput.dispatchEvent(new Event('change')); // Kích hoạt sự kiện 'change' để cập nhật
                } else {
                    toastr.error('Số lượng không thể nhỏ hơn 1.');
                }
            });

            plusBtn.addEventListener('click', () => {
                let currentValue = parseInt(quantityInput.value);
                quantityInput.value = currentValue + 1;
                quantityInput.dispatchEvent(new Event('change')); // Kích hoạt sự kiện 'change' để cập nhật
            });
        });


        // --- Code để cập nhật số lượng theo thời gian thực ---
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('.product-item');
                const productId = row.getAttribute('data-product-id');
                const newQuantity = parseInt(this.value);
                const oldQuantity = parseInt(this.getAttribute('data-old-quantity') || this.value); // Lưu lại số lượng cũ

                // Cập nhật số lượng cũ vào thuộc tính data để khôi phục nếu lỗi
                this.setAttribute('data-old-quantity', oldQuantity);

                if (isNaN(newQuantity) || newQuantity < 1) {
                    this.value = 1;
                    toastr.error('Số lượng không hợp lệ. Đã đặt lại về 1.');
                    updateGrandTotals(); // Cập nhật tổng sau khi đặt lại
                    return;
                }

                fetch(`/update-cart/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Server response for quantity update:', data);
                    if (data.success) {
                        const itemTotalElement = row.querySelector('.item-total');
                        const originalPriceText = row.querySelector('.price').innerText;
                        // Loại bỏ dấu chấm và " VNĐ" để chuyển thành số
                        const originalPrice = parseFloat(originalPriceText.replace(/\./g, '').replace(' VNĐ', ''));

                        const newItemTotal = newQuantity * originalPrice;
                        itemTotalElement.innerText = `${newItemTotal.toLocaleString('vi-VN')} VNĐ`; // Định dạng lại số

                        updateGrandTotals(); // Gọi hàm cập nhật tổng sau khi thành công
                        toastr.success('Số lượng đã được cập nhật.');
                    } else {
                        toastr.error(data.message || 'Có lỗi xảy ra khi cập nhật số lượng.');
                        // Khôi phục số lượng về giá trị cũ nếu server báo lỗi
                        if (data.current_quantity_in_cart !== undefined) {
                            this.value = data.current_quantity_in_cart;
                        } else {
                            this.value = oldQuantity; // Khôi phục về số lượng trước khi gửi request
                        }
                        updateGrandTotals(); // Cập nhật tổng để phản ánh số lượng đã khôi phục
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi gửi yêu cầu AJAX:', error);
                    toastr.error('Có lỗi xảy ra khi gửi yêu cầu cập nhật.');
                    this.value = oldQuantity; // Khôi phục số lượng nếu có lỗi mạng/server
                    updateGrandTotals(); // Cập nhật tổng để phản ánh số lượng đã khôi phục
                });
            });
        });

        // Function to update all grand totals
        function updateGrandTotals() {
            let subtotal = 0;
            let totalItemsCount = 0;
            document.querySelectorAll('.product-item').forEach(row => {
                const quantityInput = row.querySelector('.quantity-input');
                if (quantityInput) {
                    const quantity = parseInt(quantityInput.value);
                    const priceElement = row.querySelector('.price');
                    if (priceElement) {
                        // Loại bỏ dấu chấm và " VNĐ" để chuyển thành số
                        const price = parseFloat(priceElement.innerText.replace(/\./g, '').replace(' VNĐ', ''));
                        subtotal += (quantity * price);
                        totalItemsCount += quantity;
                    }
                }
            });

            // Cập nhật "Số sản phẩm"
            const summaryItemCountElement = document.getElementById('summary-item-count');
            if (summaryItemCountElement) {
                summaryItemCountElement.innerText = totalItemsCount;
            }

            // Cập nhật "Tổng giá"
            const grandTotalElement = document.getElementById('summary-grand-total');
            if (grandTotalElement) {
                const finalTotal = subtotal + shippingCost;
                grandTotalElement.innerText = `${finalTotal.toLocaleString('vi-VN')} VNĐ`; // Định dạng lại số
            }
        }

        // Gọi hàm cập nhật tổng khi trang được tải lần đầu
        updateGrandTotals();
    });
  </script>

  {{-- SweetAlert2 (phiên bản hiện đại) --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script type="text/javascript">
    function confirmation(ev) {
        ev.preventDefault();
        var urlToRedirect = ev.currentTarget.getAttribute('href');
        Swal.fire({
            title: "Bạn chắc chắn không?",
            text: "Bạn có muốn xóa sản phẩm này khỏi giỏ hàng không?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Có, xóa nó!',
            cancelButtonText: 'Hủy'
        })
        .then((result) => {
            if (result.isConfirmed) {
                window.location.href = urlToRedirect;
            }
        });
    }
  </script>

</body>
</html>
