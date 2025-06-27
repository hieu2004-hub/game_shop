<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.homecss')
    <link rel="stylesheet" href="{{ asset('./assets/css/myProfile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Thông tin cá nhân</title>
</head>
<body>

  <header>
    @include('home.header')
  </header>

  <main>
    <div class="profile-container">
        <div class="profile-card"> {{-- Added a wrapper div for the card effect --}}
            <h2>Thông tin cá nhân</h2>
            <form action="{{ route('user.updateProfile') }}" method="POST">
                @csrf
                @method('PATCH') {{-- Sử dụng PATCH method cho update --}}

                <div class="form-group">
                    <label for="userName">Tên người dùng:</label>
                    <input type="text" id="userName" name="userName" value="{{ old('userName', $user->userName) }}" required>
                    @error('userName')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Địa chỉ:</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}">
                    @error('address')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <h3>Thay đổi mật khẩu (nếu muốn)</h3>

                <div class="form-group">
                    <label for="current_password">Mật khẩu hiện tại:</label>
                    <input type="password" id="current_password" name="current_password">
                    @error('current_password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password">Mật khẩu mới:</label>
                    <input type="password" id="new_password" name="new_password">
                    @error('new_password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Xác nhận mật khẩu mới:</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation">
                    @error('new_password_confirmation')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </form>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật thông tin
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="p-0 m-0">
                        @csrf
                        <button type="submit" class="btn btn-danger logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </div>
        </div> {{-- End of profile-card --}}
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
