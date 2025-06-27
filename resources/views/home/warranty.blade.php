<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    <title>Chính sách bảo hành</title>
    {{-- Thêm Font Awesome để có các icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('./assets/css/warranty.css') }}">
</head>
<body>

<header>
    @include('home.header')
</header>

<main>
    <div class="warranty-container">
        <h1>CHÍNH SÁCH BẢO HÀNH</h1>

        <h2 class="section-heading">Điều kiện bảo hành</h2>
        <ul class="conditions-list">
            <li>Sản phẩm vẫn còn trong thời gian bảo hành</li>
            <li>Sản phẩm được bảo hành trong trường hợp khách hàng sử dụng đúng hướng dẫn sử dụng</li>
            <li>Sản phẩm bảo hành sẽ được sửa chữa và thay thế miễn phí các linh kiện, bộ phận bị hư hỏng về phần cứng</li>
            <li>Phiếu bảo hành được ghi đầy đủ thông tin</li>
            <li>Sự hư hỏng của sản phẩm phải thực sự rõ ràng</li>
            <li>Trong trường hợp làm mất phiếu bảo hành thì vẫn sẽ được bảo hành nếu tem bảo hành của shop vẫn còn nguyên vẹn</li>
        </ul>

        <h2 class="exclusion-heading">Điều kiện bảo hành không áp dụng cho các trường hợp:</h2>
        <ul class="exclusions-list">
            <li>Phiếu bảo hành bị tẩy xóa hoặc làm rách</li>
            <li>Phiếu bảo hành bị sửa đổi, thông tin không chính xác</li>
            <li>Khách hàng tự ý tháo gỡ làm ảnh hưởng đến phần cứng bên trong sản phẩm</li>
            <li>Khách hàng dùng không đúng quy định của PlayStation, dẫn đến bị máy bị banned</li>
            <li>Sản phẩm rơi rớt, gãy vỡ, cấn móp dẫn đến biến dạng</li>
            <li>Sản phẩm vô nước, có dấu hiệu bị rỉ sét hoặc bị ăn mòn</li>
            <li>Lỗi do khách tự ý Jailbreak sản phẩm, tự ý chỉnh sửa file hệ thống làm sản phẩm không lên nguồn và không lên hình</li>
        </ul>
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
