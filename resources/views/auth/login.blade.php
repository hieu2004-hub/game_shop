<!DOCTYPE html>
<html lang="en">
    <head>
        @include('home.homecss')
        <link rel="stylesheet" href="{{ asset('./assets/css/login.css') }}">
        {{-- Thêm Font Awesome để có các icon nếu cần trong tương lai --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <title>Đăng Nhập</title>
    </head>
    <body>

    <header>
        @include('home.header')
    </header>

    <main>
        <div class="login-wrapper">
            <h1>Đăng Nhập</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button class="btn btn-primary" type="submit">Đăng Nhập</button>
            </form>
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
