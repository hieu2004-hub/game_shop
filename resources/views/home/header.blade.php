@php
    $cartCount = 0;
    if (Auth::check()) {
        $cartCount = \App\Models\Cart::where('userID', Auth::id())->distinct('productID')->count('productID'); // Count distinct products
    }
@endphp

<div class="header-main">
    <div class="container">
        <a href="/" class="header-logo">
            <img src="{{ asset('./assets/images/logo/gamelogo.png') }}" alt="logo" width="100%" height="120" />
        </a>

        <form action="{{ url('search') }}" method="GET">
            @csrf
            <div class="header-search-container">
                <input type="search" name="search" class="search-field" placeholder="Tìm kiếm sản phẩm..." />
            </div>
        </form>

        <div class="header-user-actions">
            @if (Route::has('login'))
                @auth
                    <strong>{{ Auth::user()->userName }}</strong>
                @else
                    <a href="{{ url('/login') }}">
                        <button class="action-btn">
                            <ion-icon name="log-in-outline"></ion-icon>
                        </button>
                    </a>

                    <a href="{{ url('/register') }}">
                        <button class="action-btn">
                            <ion-icon name="person-add-outline"></ion-icon>
                        </button>
                    </a>
                @endauth
            @endif
        </div>
    </div>
</div>

<nav class="desktop-navigation-menu">
    <div class="container">
        <ul class="desktop-menu-category-list">
            <li class="menu-category">
                <a href="/" class="menu-title">Trang chủ</a>
            </li>

            <li class="menu-category">
                <a href="{{ url('products') }}" class="menu-title">Sản phẩm</a>

                <div class="dropdown-panel">
                    <ul class="dropdown-panel-list">
                        <li class="menu-title">
                            <a href="{{ url('products/console') }}">Máy chơi game</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{ url('products/console/sony') }}">Sony</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{ url('products/console/nintendo') }}">Nintendo</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{ url('products/console/xbox') }}">Xbox</a>
                        </li>

                        <li class="panel-list-item">
                        </li>
                    </ul>

                    <ul class="dropdown-panel-list">
                        <li class="menu-title">
                            <a href="{{ url('products/controller') }}">Tay cầm</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{ url('products/controller/sony') }}">Sony</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{url('products/controller/nintendo')}}">Nintendo</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{ url('products/controller/xbox') }}">Xbox</a>
                        </li>

                        <li class="panel-list-item">
                        </li>
                    </ul>

                    <ul class="dropdown-panel-list">
                        <li class="menu-title">
                            <a href="{{url('products/disc')}}">Đĩa</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{url('products/disc/sony')}}">Sony</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{url('products/disc/nintendo')}}">Nintendo</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{url('products/disc/xbox')}}">Xbox</a>
                        </li>

                        <li class="panel-list-item">
                        </li>
                    </ul>

                    <ul class="dropdown-panel-list">
                        <li class="menu-title">
                            <a href="{{url('products/accessory')}}">Phụ kiện</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{url('products/accessory/sony')}}">Sony</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{url('products/accessory/nintendo')}}">Nintendo</a>
                        </li>

                        <li class="panel-list-item">
                            <a href="{{url('products/accessory/xbox')}}">Xbox</a>
                        </li>

                        <li class="panel-list-item">
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-category">
                <a href="{{url('products/sony')}}" class="menu-title">Sony</a>

                <ul class="dropdown-list">
                    <li class="dropdown-item">
                        <a href="{{url('products/sony/ps5')}}">PS5</a>
                    </li>

                    <li class="dropdown-item">
                        <a href="{{url('products/sony/ps4')}}">PS4</a>
                    </li>

                    <li class="dropdown-item">
                        <a href="{{url('products/sony/psvita')}}">PS Vita</a>
                    </li>

                    <li class="dropdown-item">
                        <a href="{{url('products/sony/psp')}}">PSP</a>
                    </li>
                </ul>
            </li>

            <li class="menu-category">
                <a href="{{url('products/nintendo')}}" class="menu-title">Nintendo</a>

                <ul class="dropdown-list">
                    <li class="dropdown-item">
                        <a href="{{url('products/nintendo/switch')}}">Nintendo Switch</a>
                    </li>
                    <li class="dropdown-item">
                        <a href="{{url('products/nintendo/3ds')}}">Nintendo 3DS</a>
                    </li>
                </ul>
            </li>

            <li class="menu-category">
                <a href="{{url('products/xbox')}}" class="menu-title">Xbox</a>

                <ul class="dropdown-list">
                    <li class="dropdown-item">
                        <a href="{{url('products/xbox/seriesx')}}">Xbox Series</a>
                    </li>

                    <li class="dropdown-item">
                        <a href="{{url('products/xbox/one')}}">Xbox One</a>
                    </li>

                    <li class="dropdown-item">
                        <a href="{{url('products/xbox/360')}}">Xbox 360</a>
                    </li>
                </ul>
            </li>

            <li class="menu-category">
                <a href="#" class="menu-title">Chính Sách</a>
                <ul class="dropdown-list">
                    <li class="dropdown-item">
                        <a href="{{route('user.warranty')}}">Bảo hành</a>
                    </li>
                    <li class="dropdown-item">
                        <a href="{{route('user.shipping')}}">Vận Chuyển</a>
                    </li>
                </ul>
            </li>

            @if(Route::has('login'))
                @auth
                    <li class="menu-category">
                        <a class="menu-title">Khác</a>
                        <ul class="dropdown-list">
                            <li class="dropdown-item">
                                <a href="{{url('myCart')}}">Giỏ hàng</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="{{url('my-orders')}}">Đơn đặt hàng</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="{{ route('user.profile') }}">Thông tin cá nhân</a>
                            </li>
                        </ul>
                    </li>
                @endauth
            @endif
        </ul>
    </div>
</nav>
