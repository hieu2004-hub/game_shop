<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <!-- SB Admin 2 thường sử dụng Font Awesome cho icons, đảm bảo đã được include -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Quản lý thông tin tài khoản Admin</h1>
        </div>

        <!-- Form Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin tài khoản</h6>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('admin.updateProfile') }}" method="POST">
                    @csrf
                    @method('PATCH') {{-- Sử dụng PATCH method cho update --}}

                    <div class="form-group row">
                        <label for="userName" class="col-sm-3 col-form-label">Tên người dùng:</label>
                        <div class="col-sm-9">
                            <input type="text" id="userName" name="userName" class="form-control" value="{{ old('userName', $admin->userName) }}" required>
                            @error('userName')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label">Email:</label>
                        <div class="col-sm-9">
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="phone" class="col-sm-3 col-form-label">Số điện thoại:</label>
                        <div class="col-sm-9">
                            <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone', $admin->phone) }}">
                            @error('phone')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="address" class="col-sm-3 col-form-label">Địa chỉ:</label>
                        <div class="col-sm-9">
                            <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $admin->address) }}">
                            @error('address')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4"> {{-- Đường kẻ ngang để phân tách phần mật khẩu --}}

                    <h5 class="mb-3 text-gray-800">Thay đổi mật khẩu (nếu muốn)</h5>

                    <div class="form-group row">
                        <label for="current_password" class="col-sm-3 col-form-label">Mật khẩu hiện tại:</label>
                        <div class="col-sm-9">
                            <input type="password" id="current_password" name="current_password" class="form-control">
                            @error('current_password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="new_password" class="col-sm-3 col-form-label">Mật khẩu mới:</label>
                        <div class="col-sm-9">
                            <input type="password" id="new_password" name="new_password" class="form-control">
                            @error('new_password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="new_password_confirmation" class="col-sm-3 col-form-label">Xác nhận mật khẩu mới:</label>
                        <div class="col-sm-9">
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control">
                            @error('new_password_confirmation')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Nhóm các nút "Cập nhật" và "Đăng xuất" vào một div mới --}}
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="fas fa-save"></i> Cập nhật thông tin
                        </button>
                    </div>
                </form>
                {{-- Nút Đăng xuất --}}
                <form method="POST" action="{{ route('logout') }}" class="p-0 m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg shadow-sm">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </button>
                </form>
            </div>
          </div>
        </div>
      </div>
  </main>
  <!--   Core JS Files   -->
  @include('admin.adminscript')
</body>

</html>
