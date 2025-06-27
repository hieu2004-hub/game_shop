<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    <link rel="stylesheet" href="{{ asset('./assets/css/thankYou.css') }}">
    <title>Cảm ơn bạn đã đặt hàng!</title>
    {{-- Thêm Font Awesome để có icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header>
        @include('home.header')
    </header>

    <main>
        <div class="thank-you-container">
            <i class="fas fa-check-circle icon-success"></i> {{-- Icon thành công --}}
            <h1>Cảm ơn bạn đã đặt hàng!</h1>
            <p>Đơn hàng của bạn đã được tiếp nhận thành công. Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.</p>
            <p>Bạn có thể theo dõi trạng thái đơn hàng của mình trong phần "Đơn hàng của tôi" để biết thêm chi tiết về quá trình giao hàng.</p>
            <div class="button-group">
                <a href="{{ url('/') }}" class="btn">Tiếp tục mua sắm</a>
                <a href="{{ route('my.orders') }}" class="btn">Xem đơn hàng của tôi</a>
            </div>
        </div>
    </main>

    <footer>
        @include('home.footer')
    </footer>
</body>
</html>
