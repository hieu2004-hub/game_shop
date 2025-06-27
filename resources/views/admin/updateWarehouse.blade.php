<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <link rel="stylesheet" href="{{ asset('assets/css/warehouseForm.css') }}">
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="form-container">
            <h2>Cập Nhật Kho Hàng</h2>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.updateWarehouse', $warehouse->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Tên Kho:</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $warehouse->name) }}" required>
                </div>
                <div class="form-group">
                    <label for="address">Địa Chỉ:</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $warehouse->address) }}">
                </div>
                <div class="form-group">
                    <label for="contact_person">Người Liên Hệ:</label>
                    <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $warehouse->contact_person) }}">
                </div>
                <div class="form-group">
                    <label for="phone">Số Điện Thoại:</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $warehouse->phone) }}">
                </div>
                <button type="submit" class="btn btn-submit">Cập Nhật Kho</button>
            </form>
        </div>
    </div>
  </main>

  @include('admin.adminscript')
</body>

</html>
