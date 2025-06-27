<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    <title>Chính sách vận chuyển</title>
    {{-- Thêm Font Awesome để có các icon (nếu muốn dùng trong tương lai) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('./assets/css/shipping.css') }}">
</head>
<body>

<header>
    @include('home.header')
</header>

<main>
    <div class="shipping-container">
        <h1>CHÍNH SÁCH VẬN CHUYỂN</h1>

        <h2>Đối với nội thành</h2>
        <p>Thời gian giao hàng nhanh chóng chỉ từ 30 - 60p thông qua các dịch vụ Grab, Lalamove .v.v.</p>
        <p>Phí vận chuyển áp dụng từ 20.000 - 70.000đ tùy khu vực (nhân viên sẽ liên hệ và báo cụ thể phí vận chuyển cho bạn)</p>

        <h2>Đối với các Tỉnh Thành Phố khác trên toàn quốc</h2>
        <p>Thời gian giao hàng từ 1 - 3 ngày thông qua các dịch vụ chuyển phát nhanh Giao Hàng Tiết Kiệm, Viettel Post .v.v.</p>
        <p>Phí vận chuyển áp dụng từ 30.000 - 500.000đ tùy vào khu vực, đơn hàng to, nặng, cồng kềnh .v.v.</p>

        <p class="note">Lưu ý: Thời gian vận chuyển đối với các tình thành phố khác trên toàn quốc có thể sẽ lâu hơn và không bao gồm Thứ 7, Chủ Nhật và các ngày lễ</p>
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
