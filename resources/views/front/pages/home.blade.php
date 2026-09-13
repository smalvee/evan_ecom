@extends('front.layouts.new_app')

@section('content')
    <?php
    
    use App\Models\NewProduct;
    use App\Models\ProductVariant;
    use App\Models\ProductImage;
    use App\Models\SubCategory;
    ?>

    <style>
        .image-link {
            position: absolute;
            inset: 0;
            z-index: 10;
        }
    </style>
    <!-- Home Section Start -->
    <section class="home-section-2 home-section-bg pt-0 overflow-hidden">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="slider-animate">
                        <div>
                            <div class="home-contain rounded-0 p-0">
                                {{-- <img src="{{ asset('new-front-assets/images/grocery/banner/1.jpg') }}"
                                    class="img-fluid bg-img blur-up lazyload" alt=""> --}}

                                @if (!empty($banner->image))
                                    <img src="{{ asset('/uploads/banners/' . $banner->image) }}" alt="Banner"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                        class="img-fluid bg-img blur-up lazyload">
                                @else
                                    <img src="{{ asset('new-front-assets/images/grocery/banner/1.jpg') }}" alt="Banner"
                                        class="img-fluid bg-img blur-up lazyload">
                                @endif
                                <div class="home-detail home-big-space p-center-left home-overlay position-relative">
                                    <div class="container-fluid-lg">
                                        <div style="visibility: hidden;">
                                            <h6 class="ls-expanded theme-color text-uppercase">Weekend Special offer
                                            </h6>
                                            <h1 class="heding-2">Premium Quality Dry Fruits</h1>
                                            <h2 class="content-2">Dryfruits shopping made Easy</h2>
                                            <h5 class="text-content">Fresh & Top Quality Dry Fruits are available here!
                                            </h5>
                                            <button
                                                class="btn theme-bg-color btn-md text-white fw-bold mt-md-4 mt-2 mend-auto"
                                                onclick="location.href = 'shop-left-sidebar.html';">Shop Now <i
                                                    class="fa-solid fa-arrow-right icon"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Home Section End -->

    <!-- Category Section Start -->

    <section class="category-section-3">
        <div class="container-fluid-lg">
            <div class="title">
                <h2>Shop By Categories</h2>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="category-slider-1 arrow-slider wow fadeInUp">
                        @if (!empty($categories))
                            @foreach ($categories as $category)
                                <div>
                                    <div class="category-box-list">
                                        <a href="{{ route('product_shop.home', $category->slug) }}" class="category-name">
                                            <h4>{{ \Illuminate\Support\Str::limit($category->name, 8) }}</h4>
                                            {{-- <h6>29 items</h6> --}}
                                        </a>
                                        <div class="category-box-view">
                                            <a href="{{ route('product_shop.home', $category->slug) }}">
                                                @if (!empty($category->image))
                                                    <img src="{{ asset('uploads/category/' . $category->image) }}"
                                                        class="img-fluid blur-up lazyload" alt="">
                                                @endif
                                            </a>
                                            <button
                                                onclick="location.href = '{{ route('product_shop.home', $category->slug) }}';"
                                                class="btn shop-button">
                                                <span>Shop Now</span>
                                                <i class="fas fa-angle-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Section End -->

    <!-- Discount Section Start -->
    @if (!empty($advertise) && $advertise->isSlotActive(1))
        <section>
            <div class="container-fluid-lg">
                <div class="row">
                    <div class="col-12">
                        <div class="banner-contain hover-effect">
                            <a href="{{ $advertise->url_01 }}" target="_blank" class="d-block">
                                <img src="{{ asset('uploads/add/' . $advertise->image_01) }}"
                                    class="bg-img blur-up lazyload" alt="{{ $advertise->name_01 }}">
                                <div class="banner-details p-center p-sm-4 p-3 text-white text-center">
                                    <div style="visibility: hidden;">
                                        <h3 class="lh-base fw-bold text-white">
                                            Get $3 Cashback! Min Order of $30
                                        </h3>
                                        <h6 class="coupon-code code-2">ASDFGH</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!-- Discount Section End -->

    <!-- Banner Section Start -->
    <section class="ratio_60">
        <div class="container-fluid-lg">
            <div class="row g-3">
                @if (!empty($advertise) && $advertise->isSlotActive(2))
                <div class="col-xxl-3 col-sm-6">
                    <a href="{{ $advertise->url_02 }}" target="_blank" class="banner-contain-2 hover-effect">
                        <img src="{{ asset('uploads/add/' . $advertise->image_02) }}" class="bg-img blur-up lazyload"
                            alt="{{ $advertise->name_02 }}">
                        <div class="banner-detail p-top-left">
                            <div>
                                {{-- <div class="banner-detail-box mb-md-3 mb-1">
                                    <h6 class="text-danger">5% OFF</h6>
                                    <h4 class="mt-2">New Items</h4>
                                    <h6 class="mt-2 text-content">Daily Essentials</h6>
                                </div> --}}
                            </div>
                        </div>
                    </a>
                </div>
                @endif

                @if (!empty($advertise) && $advertise->isSlotActive(3))
                <div class="col-xxl-3 col-sm-6">
                    <a href="{{ $advertise->url_03 }}" target="_blank" class="banner-contain-2 hover-effect">
                        <img src="{{ asset('uploads/add/' . $advertise->image_03) }}" class="bg-img blur-up lazyload"
                            alt="{{ $advertise->name_03 }}">
                        <div class="banner-detail p-top-left">
                            <div style="visibility: hidden;">
                                <div class="banner-detail-box mb-md-3 mb-1">
                                    <h6 class="text-danger">5% OFF</h6>
                                    <h4 class="mt-2">Save More</h4>
                                    <h6 class="mt-2 text-content">Fresh Toast Rusk</h6>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif

                @if (!empty($advertise) && $advertise->isSlotActive(4))
                <div class="col-xxl-3 col-sm-6">
                    <a href="{{ $advertise->url_04 }}" target="_blank" class="banner-contain-2 hover-effect">
                        <img src="{{ asset('uploads/add/' . $advertise->image_04) }}" class="bg-img blur-up lazyload"
                            alt="{{ $advertise->name_04 }}">
                        <div class="banner-detail p-top-left">
                            {{-- <div>
                                <div class="banner-detail-box mb-md-3 mb-1">
                                    <h6 class="text-danger">5% OFF</h6>
                                    <h4 class="mt-2">Fresh Every Day!</h4>
                                    <h6 class="mt-2 text-content">Delivered @ Home</h6>
                                </div>
                            </div> --}}
                        </div>
                    </a>
                </div>
                @endif

                @if (!empty($advertise) && $advertise->isSlotActive(5))
                <div class="col-xxl-3 col-sm-6">
                    <a href="{{ $advertise->url_05 }}" target="_blank" class="banner-contain-2 hover-effect">
                        <img src="{{ asset('uploads/add/' . $advertise->image_05) }}" class="bg-img blur-up lazyload"
                            alt="{{ $advertise->name_05 }}">
                        <div class="banner-detail p-top-left">
                            {{-- <div>
                                <div class="banner-detail-box mb-md-3 mb-1">
                                    <h6 class="text-danger">5% OFF</h6>
                                    <h4 class="mt-2">Hot Deals</h4>
                                    <h6 class="mt-2 text-content">Fresh Cake</h6>
                                </div>
                            </div> --}}
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>
    <!-- Banner Section End -->

    <br>
    <br>

    <section class="product-section">
        <div class="container-fluid-lg">
            @foreach ($categories as $category)
                @php
                    $check_product = NewProduct::where('cat_id', $category->id);
                @endphp
                @if ($check_product->count() > 0)
                    <div class="title title-flex-2" style="padding-top: 20px">

                        <h2>{{ $category->name }} </h2>
                        <ul class="nav nav-tabs tab-style-color-2 tab-style-color" id="myTab">
                            <li class="nav-item">
                                <button class="nav-link btn active" id="{{ $category->slug }}-tab" data-bs-toggle="tab"
                                    data-bs-target="#{{ $category->slug }}" type="button">All</button>
                            </li>
                            @php
                                $sub_cats = SubCategory::where('category_id', $category->id)->get();
                            @endphp

                            @foreach ($sub_cats as $sub_cat)
                                @php
                                    $check_product_sub = NewProduct::where('sub_cat_id', $sub_cat->id);
                                @endphp
                                @if ($check_product_sub->count() > 0)
                                    <li class="nav-item">
                                        <button class="nav-link btn" id="{{ $sub_cat->slug }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#{{ $sub_cat->slug }}" type="button">
                                            {{ $sub_cat->name }}</button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="{{ $category->slug }}" role="tabpanel">
                            <div class="row g-8">
                                @php
                                    $products = NewProduct::where('cat_id', $category->id)->where('status', 1)->get();
                                @endphp

                                @foreach ($products as $product)
                                    @php
                                        $product_details = ProductVariant::where('product_id', $product->id)->first();
                                        $product_Image = ProductImage::where('product_id', $product_details->id)
                                            ->where('is_thumb', 1)
                                            ->first();
                                    @endphp
                                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 wow fadeInUp">
                                        <div class="product-box-4">
                                            <div class="product-image">
                                                {{-- <div class="label-flex">
                                                    <button class="btn p-0 wishlist btn-wishlist notifi-wishlist">
                                                        <i class="iconly-Heart icli"></i>
                                                    </button>
                                                </div> --}}

                                                <a href="{{ route('Product_details.home', $product->slug) }}">
                                                    @if (!empty($product_Image))
                                                        <img src="{{ asset('uploads/products/thumb/' . $product_Image->image) }}"
                                                            class="img-fluid blur-up lazyload" alt="">
                                                    @endif
                                                </a>

                                                <ul class="option">
                                                    {{-- <li data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Quick View">
                                                        <a href="javascript:void(0)" data-bs-toggle="modal"
                                                            data-bs-target="#view">
                                                            <i class="iconly-Show icli"></i>
                                                        </a>
                                                    </li> --}}
                                                    {{-- <li data-bs-toggle="tooltip" data-bs-placement="top" title="Compare">
                                                        <a href="compare.html">
                                                            <i class="iconly-Swap icli"></i>
                                                        </a>
                                                    </li> --}}
                                                </ul>
                                            </div>

                                            <div class="product-detail">
                                                {{-- <ul class="rating">
                                                    <li>
                                                        <i data-feather="star" class="fill"></i>
                                                    </li>
                                                    <li>
                                                        <i data-feather="star" class="fill"></i>
                                                    </li>
                                                    <li>
                                                        <i data-feather="star" class="fill"></i></i>
                                                    </li>
                                                    <li>
                                                        <i data-feather="star" class="fill"></i></i>
                                                    </li>
                                                    <li>
                                                        <i data-feather="star"></i>
                                                    </li>
                                                </ul> --}}
                                                <a href="{{ route('Product_details.home', $product->slug) }}">
                                                    <h5 class="name">
                                                        {{-- {{ \Illuminate\Support\Str::limit($product->name, 25) }} --}}
                                                        {{ $product->name }}
                                                    </h5>

                                                </a>
                                                <h5 class="price theme-color">৳ {{ $product_details->selling_price }}
                                                    @if ((float) $product_details->selling_price < (float) $product_details->compare_price)
                                                        <del>৳ {{ $product_details->compare_price }}</del>
                                                    @endif
                                                </h5>
                                                <div
                                                    class="price-qty d-flex flex-column flex-md-row align-items-center gap-2 justify-content-center">

                                                    <a href="{{ route('front.sing.checkout', $product_details->id) }}"><button
                                                            style="background-color: #d99f46; border: none; color: white; padding: 5px 15px; cursor: pointer;"
                                                            class="btn-sm btn-cart">
                                                            Buy Now
                                                        </button></a>
                                                    <button
                                                        style="background-color: #d99f46; border: none; color: white; padding: 5px 15px; cursor: pointer;"
                                                        class="btn-sm btn-cart"
                                                        onclick="addToCart(this, {{ $product_details->id }})">
                                                        Add To Cart
                                                    </button>
                                                </div>
                                                {{-- <div class="price-qty"
                                                    style="display: flex; justify-content: center; align-items: center;">
                                                    <a href="{{ route('front.sing.checkout', $product_details->id) }}"><button
                                                        style="background-color: #d99f46; border: none; color: white; padding: 5px 15px; cursor: pointer;"
                                                        class="btn-sm btn-cart">
                                                        Buy Now
                                                    </button></a>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                        @foreach ($sub_cats as $sub_cat)
                            <div class="tab-pane fade" id="{{ $sub_cat->slug }}" role="tabpanel">
                                <div class="row g-8">

                                    @php
                                        $products = NewProduct::where('sub_cat_id', $sub_cat->id)
                                            ->where('status', 1)
                                            ->get();
                                    @endphp

                                    @if ($products->count() > 0)
                                        @foreach ($products as $product)
                                            @php
                                                $product_details = ProductVariant::where(
                                                    'product_id',
                                                    $product->id,
                                                )->first();
                                                $product_Image = ProductImage::where('product_id', $product_details->id)
                                                    ->where('is_thumb', 1)
                                                    ->first();
                                            @endphp

                                            <div class="col-xxl-2 col-lg-3 col-md-4 col-6">
                                                <div class="product-box-4">
                                                    <div class="product-image">
                                                        {{-- <div class="label-flex">
                                                            <button class="btn p-0 wishlist btn-wishlist notifi-wishlist">
                                                                <i class="iconly-Heart icli"></i>
                                                            </button>
                                                        </div> --}}

                                                        <a href="{{ route('Product_details.home', $product->slug) }}">
                                                            @if (!empty($product_Image))
                                                                <img src="{{ asset('uploads/products/thumb/' . $product_Image->image) }}"
                                                                    class="img-fluid blur-up lazyload" alt="">
                                                            @endif
                                                        </a>

                                                        <ul class="option">

                                                        </ul>
                                                    </div>

                                                    <div class="product-detail">
                                                        {{-- <ul class="rating">
                                                            <li>
                                                                <i data-feather="star" class="fill"></i>
                                                            </li>
                                                            <li>
                                                                <i data-feather="star" class="fill"></i>
                                                            </li>
                                                            <li>
                                                                <i data-feather="star" class="fill"></i>
                                                            </li>
                                                            <li>
                                                                <i data-feather="star" class="fill"></i>
                                                            </li>
                                                            <li>
                                                                <i data-feather="star"></i>
                                                            </li>
                                                        </ul> --}}
                                                        <a href="{{ route('Product_details.home', $product->slug) }}">
                                                            <h5 class="name">
                                                                {{ $product->name }}
                                                            </h5>
                                                        </a>
                                                        <h5 class="price theme-color">৳
                                                            {{ $product_details->selling_price }}


                                                            @if ((float) $product_details->selling_price < (float) $product_details->compare_price)
                                                                <del>৳ {{ $product_details->compare_price }}</del>
                                                            @endif


                                                        </h5>

                                                        <div
                                                            class="price-qty d-flex flex-column flex-md-row align-items-center gap-2 justify-content-center">

                                                            <a
                                                                href="{{ route('front.sing.checkout', $product_details->id) }}"><button
                                                                    style="background-color: #d99f46; border: none; color: white; padding: 5px 15px; cursor: pointer;"
                                                                    class="btn-sm btn-cart">
                                                                    Buy Now
                                                                </button></a>
                                                            <button
                                                                style="background-color: #d99f46; border: none; color: white; padding: 5px 15px; cursor: pointer;"
                                                                class="btn-sm btn-cart"
                                                                onclick="addToCart(this, {{ $product_details->id }})">
                                                                Add To Cart
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-xxl-2 col-lg-3 col-md-4 col-12 text-center">
                                            <h2 style="color: red">No Products</h2>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach



                    </div>
                @endif
            @endforeach




        </div>
    </section>


    <!-- Newsletter Section Start -->
    @if (!empty($advertise) && $advertise->isSlotActive(7))
        <section class="newsletter-section-2 section-b-space">
            <div class="container-fluid-lg">
                <div class="row">
                    <div class="col-12">
                        <div class="newsletter-box hover-effect">
                            <a href="{{ $advertise->url_07 }}" target="_blank" class="image-link"></a>


                            <img src=" {{ asset('uploads/add/' . $advertise->image_07) }}" class="img-fluid bg-img"
                                alt="{{ $advertise->name_07 }}">

                            <div class="row">
                                <div class="col-xxl-8 col-xl-7">
                                    <div class="newsletter-detail p-center-left text-white">
                                        <div style="visibility: hidden;">
                                            <h2>Subscribe to the newsletter</h2>
                                            <h4>Join our subscribers list to get the latest news, updates and special offers
                                                delivered directly in your inbox.</h4>
                                            <form class="row g-2">
                                                <div class="col-sm-10 col-12">
                                                    <div class="newsletter-form">
                                                        <input type="email" class="form-control" id="email"
                                                            placeholder="Enter your email">
                                                        <button type="submit"
                                                            class="btn bg-white theme-color btn-md fw-500
                                                            submit-button">Subscribe</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!-- Newsletter Section End -->

    <!-- Add to cart Modal Start -->
    <div class="add-cart-box">
        <div class="add-image">
            <img src="../assets/images/cake/pro/1.jpg" class="img-fluid blur-up lazyload" alt="">
        </div>

        <div class="add-contain">
            <h6>Added to Cart</h6>
        </div>
    </div>
    <!-- Add to cart Modal End -->


@endsection

@section('customJs')
@endsection
