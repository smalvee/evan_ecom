<nav id="customNav" class="py-2 border-bottom">
    <div class="container d-flex justify-content-between align-items-center">

        <!-- Left -->
        <div class="d-flex align-items-center">
            <!-- Desktop: Search icon -->
            <a href="#" class="fs-5 nav-icon d-none d-lg-inline-block me-2" data-bs-toggle="modal"
                data-bs-target="#exampleModal">
                <i class="bi bi-search"></i>
            </a>

            <!-- Mobile: Hamburger menu -->
            <button class="nav-icon btn p-0 d-lg-none me-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fas fa-bars fs-5"></i>
            </button>
        </div>

        <!-- Center: Logo -->
        <div class="d-flex justify-content-center flex-grow-1">
            <a href="{{ route('front.home') }}" class="navbar-brand fw-bold fs-4 mx-auto">
                <img src="{{ asset('front-assets/images/logo.jpg') }}" alt="Logo" style="width: 150px;">
            </a>
        </div>

        <!-- Right -->
        <div class="d-flex align-items-center">
            @php
                use Gloudemans\Shoppingcart\Facades\Cart;
                $cartCount = Cart::content()->count();
            @endphp

            <!-- Desktop: User + Cart -->
            <a href="{{ route('account.userLogin') }}" class="fs-5 nav-icon d-none d-lg-inline-block me-3">
                <i class="bi bi-person"></i>
            </a>

            <a href="{{ request()->routeIs('front.cart') ? route('front.cart') : 'javascript:void(0)' }}"
                class="fs-5 nav-icon d-none d-lg-inline-block position-relative me-3"
                @if (!request()->routeIs('front.cart')) data-bs-toggle="offcanvas" data-bs-target="#cartSidebar" @endif>
                <i class="bi bi-cart"></i>
                @if ($cartCount > 0)
                    <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill"
                        style="background-color:#fc8934; color:#fff; min-width:18px; min-height:18px; font-size:0.7rem;">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>

            <!-- Mobile: User icon -->
            <a href="{{ route('account.userLogin') }}" class="fs-5 nav-icon d-lg-none">
                <i class="bi bi-person"></i>
            </a>
        </div>

    </div>
</nav>



<!-- Bottom Nav Row (full width, no container) -->
<nav id="customNav" class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
            <ul class=" container navbar-nav w-100 d-flex justify-content-between text-center">
                <li class="nav-item flex-fill">                    
                    <a class="nav-link custom-link" href="{{ route('front.home') }}">
                        <span>Home</span>
                    </a>
                </li>
                <li class="nav-item flex-fill">                    
                    <a class="nav-link custom-link" href="{{ route('product_shop_offer.home', 1) }}">
                        <span>Offer Zone</span>
                    </a>
                </li>
                

                @if (!empty($categories))
                    @foreach ($categories as $category)
                        <li class="nav-item flex-fill">
                            <a class="nav-link custom-link" href="{{ route('product_shop.home', $category->id) }}">
                                <span>{{ $category->name }}</span>
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>

            <!-- Mobile only Login/Dashboard button -->
            <div class="mt-3 d-lg-none text-center w-100">
                @php $user = Auth::user(); @endphp

                @if (!empty($user))
                    <a class="btn btn-outline-danger w-100" href="{{ route('account.logout') }}">
                        Logout
                    </a>
                @else
                    <a class="btn btn-outline-primary w-100" href="{{ route('account.userLogin') }}">
                        Login
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>







<!-- Large modal -->
<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Search Products</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body (Search Input) -->
            <div class="modal-body">
                <div style="position: relative; width: 100%;">
                    <input id="searchInput" type="text" class="form-control" placeholder="Search products..."
                        style="padding-left: 35px;">
                    <!-- search icon -->
                    <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #888;">
                        🔍
                    </span>
                </div>
            </div>

            <!-- Footer (Search Results) -->
            <div class="modal-footer" id="searchResults"
                style="text-align:left; display:block; max-height:300px; overflow-y:auto;">

            </div>

        </div>
    </div>
</div>


<!-- Bottom Nav (Mobile Only) -->
<nav class="mobile-bottom-nav d-lg-none d-flex justify-content-around align-items-center shadow"
    style="position:fixed; bottom:0; left:0; right:0; background:#fff; height:60px;">

    <a href="/" class="nav-item text-center">
        <i class="fas fa-home fs-5"></i>
    </a>

    <a href="/" class="nav-item text-center">
        <i class="fas fa-th-large fs-5"></i>
    </a>

    <!-- Cart button with badge -->
    @php
        $cartCount = Cart::content()->count();
    @endphp
    <button class="nav-item position-relative border-0 bg-transparent" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#cartSidebar">
        <i class="fas fa-shopping-cart fs-5"></i>
        @if ($cartCount > 0)
            <span class="cart-badge position-absolute top-0 translate-middle badge rounded-pill"
                style="background-color:#fc8934; color:#fff; font-size:0.7rem; min-width:18px; min-height:18px;">
                {{ $cartCount }}
            </span>
        @endif
    </button>

    <a href="#" class="nav-item text-center" data-bs-toggle="modal" data-bs-target="#exampleModal">
        <i class="fas fa-search fs-5"></i>
    </a>
</nav>
