@extends('front.layouts.app')

@section('content')
    <!-- Banner Section -->
    <section id="customBanner">
        <a href="#">

            @if (!empty($banner->image))
                <img src="{{ asset('/uploads/banners/' . $banner->image) }}" alt="Banner" class="img-fluid w-100">
            @else
                <img src="{{ asset('admin-assets/img/deafult-cover.png') }}" alt="Banner" class="img-fluid w-100">
            @endif
        </a>
    </section>
    <!-- All Products Section -->
    <section id="allProducts" class="py-5">
        <div class="container">
            <!-- Section Title -->
            <h2 class="text-center fw-bold mb-4"><span style="font-size: 3rem">All Products</span></h2>

            <!-- Toggle Buttons (mobile only) -->
            <div class="d-flex justify-content-end mb-4 d-sm-none" id="viewToggle"> <!-- visible only on mobile -->
                <button id="btnTwo"
                    class="btn btn-primary me-2 d-flex align-items-center justify-content-center p-2 active"
                    onclick="setGrid('two')">
                    <img src="{{ asset('front-assets/icon/hamburger.png') }}" alt="2 per row" class="img-fluid"
                        style="width: 20px; height: auto;">
                </button>
                <button id="btnOne" class="btn btn-secondary d-flex align-items-center justify-content-center p-2"
                    onclick="setGrid('one')">
                    <img src="{{ asset('front-assets/icon/menu.png') }}" alt="1 per row" class="img-fluid"
                        style="width: 20px; height: auto;">
                </button>
            </div>



            <!-- Products Grid -->
            <div class="row g-4 justify-content-center py-3" id="productGrid">
                @if ($products->isNotEmpty())
                    @foreach ($products as $product)
                        @php
                            $productImage = $product->product_image->first();
                        @endphp
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2 custom-col mb-4 grid-item">
                            <div class="product-card p-3 text-center h-100 rounded border"
                                style="display: flex; flex-direction: column; justify-content: space-between; height: 100%; border-width: 1px !important; overflow: hidden;">
            
                                <!-- Product Image -->
                                <a href="{{ route('Product_details.home', $product->slug) }}">
                                    @if (!empty($productImage->image))
                                        <img src="{{ asset('uploads/products/large/' . $productImage->image) }}"
                                            alt="{{ $product->title }}" class="img-fluid mb-3"
                                            style="width: 100%; height: 180px; object-fit: cover; border-radius: 6px; display: block; margin: 0 auto;">
                                    @else
                                        <img src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                            class="img-fluid img-thumbnail mb-3" alt="Default image"
                                            style="width: 100%; height: 180px; object-fit: cover; border-radius: 6px; display: block; margin: 0 auto;">
                                    @endif
                                </a>
            
                                <!-- Product Title -->
                                <a href="{{ route('Product_details.home', $product->slug) }}">
                                    <h6 style="color: black; font-size: 1rem; font-weight: 600;"
                                        title="{{ $product->title }}">
                                        {{ \Illuminate\Support\Str::limit($product->title, 10) }}
                                    </h6>
                                </a>
            
                                <!-- Product Price -->
                                <p class="mb-3 fw-semibold">
                                    TK: {{ number_format($product->price, 2) }}
                                    @if (!empty($product->compare_price))
                                        <span class="text-muted text-decoration-line-through ms-2">
                                            TK:{{ number_format($product->compare_price, 2) }}
                                        </span>
                                    @endif
                                </p>
            
                                <!-- Quick Add Button -->
                                <a href="javascript:void(0)" onclick="addToCart({{ $product->id }})">
                                    <button class="btn btn-sm btn-primary quick-add-btn w-100" data-bs-toggle="offcanvas"
                                        data-bs-target="#cartSidebar">
                                        Quick Add
                                    </button>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>




    <!-- All Collection Section -->
    <section class="my-5">
        <div class="container">
            <!-- Header -->
            <h2 class="section-header display-4 fw-bold text-dark mb-4 text-center">Collection</h2>

            <!-- Collection Grid -->
            <div class="d-flex flex-wrap justify-content-start gap-3 collection-row">

                @if (!empty($categories))
                    @foreach ($categories as $category)
                        <a href="{{ route('product_shop.home', $category->id) }}" class="collection-item text-center">
                            @if (!empty($category->image))
                                <img style="width: 300px" src="{{ asset('/uploads/category/thumb/' . $category->image) }}"
                                    class="img-thumbnail" width="50">
                            @else
                                <img style="width: 300px" src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                    class="img-thumbnail" width="50">
                            @endif
                            <p class="mb-0 collection-text">{{ $category->name }}</p>
                        </a>
                    @endforeach
                @endif

                <!-- Collection Item 1 -->

            </div>
        </div>
    </section>

    <!-- Quick Add Popup (Cart Sidebar) -->
    {{-- <div class="offcanvas offcanvas-end" tabindex="-1" id="cartSidebar">
        <div class="offcanvas-header border-bottom">
            <h5 class="mb-0">Shopping Cart</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column">

            <!-- Cart Items (Top Section) -->
            <div class="cart-items">
                <!-- Product Example -->
                <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                    <img src="https://ghorerbazar.com/cdn/shop/files/sundarban-honey-1kg-and-500tk-discount.jpg?v=1754112444&width=120"
                        alt="Product" class="me-3 rounded product-img">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Sundarban Honey</h6>
                        <p class="mb-2">৳500</p>

                        <!-- Qty Controls -->
                        <div class="input-group input-group-sm" style="max-width: 180px;">
                            <button class="btn btn-outline-secondary minus">-</button>
                            <input type="text" class="form-control text-center qty" value="1">
                            <button class="btn btn-outline-secondary plus">+</button>
                            <button class="btn btn-danger btn-sm remove ms-2">Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Footer (Fixed Bottom) -->
            <div class="cart-footer mt-auto border-top pt-3 bg-light">
                <!-- Note / Coupon -->
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-sticky"></i> Note</button>
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-ticket-perforated"></i>
                        Coupon</button>
                </div>

                <!-- Subtotal -->
                <div class="d-flex justify-content-between mb-3">
                    <strong>Subtotal:</strong>
                    <strong id="cartSubtotal">৳500</strong>
                </div>

                <!-- Buttons -->
                <div class="d-grid gap-2">
                    <a href="{{ route('front.cart') }}" class="btn btn-dark">View Cart</a>
                    <a href="#" class="btn text-white" style="background:#FC8934;">Cash on Delivery</a>
                </div>
            </div>
        </div>
    </div> --}}



@endsection

@section('customJs')
@endsection
