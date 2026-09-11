<div class="mobile-menu d-md-none d-block mobile-cart">
    <ul>
        <li class="">
            <a href="{{ route('front.home') }}">
                <i class="iconly-Home icli"></i>
                <span>Home</span>
            </a>
        </li>

        {{-- <li class="mobile-category">
            <a href="" data-bs-toggle="offcanvas" data-bs-target="#primaryMenu">
                <i class="iconly-Category icli"></i>
                <span>Category</span>
            </a>
        </li> --}}

        <li>
            <a href="javascript:void(0)" class="search-box" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="iconly-Search icli"></i>
                <span>Search</span>
            </a>
        </li>

        <li>
            <a href="javascript:void(0)" class="notifi-wishlist">
                <i class="iconly-Heart icli"></i>
                <span>My Wish</span>
            </a>
        </li>

        <li>
            <a href="{{ route('front.cart') }}">

                <i class="iconly-Bag-2 icli fly-cate"></i>
                <span class="cart-badge" id="cart-mob-nav-count">{{ count($cartContent) }}</span>
                <span>Cart</span>
            </a>
        </li>
    </ul>
</div>
