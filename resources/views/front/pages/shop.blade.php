@extends('front.layouts.new_app')

@section('content')

    <?php
    use App\Models\NewProduct;
    use App\Models\ProductImage;
    use App\Models\ProductVariant;
    ?>

    <!-- Shop Section Start -->
    <section class="section-b-space shop-section">
        <div class="container-fluid-lg">
            <div class="row">



                <!-- Left Sidebar -->
                <div class="col-custom-3">
                    <div class="left-box wow fadeInUp">
                        <div class="shop-left-sidebar">
                            <div class="accordion custom-accordion" id="accordionExample">
                                <!-- Categories -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne">
                                            <span>Categories</span>
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <ul class="category-list custom-padding">
                                                @if (!empty($categories))
                                                    @foreach ($categories as $category)
                                                        @php
                                                            $productCount = NewProduct::where(
                                                                'cat_id',
                                                                $category->id,
                                                            )->count();
                                                        @endphp

                                                        <li class="nav-item" role="presentation">
                                                            <a href="{{ route('product_shop.home', $category->slug) }}"><button
                                                                    class="form-control"
                                                                    style="border: none; text-align: left;
               @if ($url_slug == $category->slug) background-color: #d99f46; color: white @endif">
                                                                    {{ $category->name }}
                                                                </button>
                                                            </a>
                                                        </li>
                                                        {{-- <li>
                                                            <div class="form-check ps-0 m-0 category-list-box">
                                                             

                                                                    <div 

                                                                    @if ($url_slug == $category->slug) 
                                                                        style="items Active"
                                                                    @endif
                                                                    
                                                                    >
                                                                        <a style="color: black" href=""><h4>{{ $category->name }}</h4></a>
                                                                    </div>
                                                            
                                                            </div>
                                                        </li> --}}
                                                    @endforeach
                                                @endif
                                            </ul>
                                            <!-- Reset Filters Button -->
                                            <button id="reset-filters" class="btn btn-secondary mt-3"
                                                style="width:100%;">Reset Filters</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseThree">
                                            <span>Price</span>
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <div class="range-slider">
                                                <input type="text" class="js-range-slider" id="price-range"
                                                    value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product List -->
                <div class="col-custom-">
                    <div class="show-button">
                        <div class="filter-button-group mt-0">
                            <div class="filter-button d-inline-block d-lg-none">
                                <a><i class="fa-solid fa-filter"></i> Filter Menu</a>
                            </div>
                        </div>

                        <div class="top-filter-menu">

                            <div class="grid-option d-none d-md-block">
                                <ul>
                                    <li class="three-grid">
                                        <a href="javascript:void(0)">
                                            <img src="{{ asset('new-front-assets/svg/grid-3.svg') }}"
                                                class="blur-up lazyload" alt="">
                                        </a>
                                    </li>
                                    <li class="grid-btn d-xxl-inline-block d-none active">
                                        <a href="javascript:void(0)">
                                            <img src="{{ asset('new-front-assets/svg/grid-4.svg') }}"
                                                class="blur-up lazyload d-lg-inline-block d-none" alt="">
                                            <img src="{{ asset('new-front-assets/svg/grid.svg') }}"
                                                class="blur-up lazyload img-fluid d-lg-none d-inline-block" alt="">
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div
                        class="row g-sm-4 g-3 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-2 row-cols-md-3 row-cols-2 product-list-section">
                        @if (!empty($all_products))
                            @foreach ($all_products as $product_info)
                                @php
                                    $product_details = ProductVariant::where('product_id', $product_info->id)->first();
                                    $product_Image = ProductImage::where('product_id', $product_details->id)
                                        ->where('is_thumb', 1)
                                        ->first();
                                @endphp
                                <!-- Wrapper div with Bootstrap grid classes -->

                                <div>
                                    <div class="product-box-4 wow fadeInUp" data-category="{{ $product_info->cat_id }}"
                                        data-price="{{ $product_details->selling_price ?? 0 }}">
                                        <div class="product-image">
                                            {{-- <div class="label-flex">
                                                <button class="btn p-0 wishlist btn-wishlist notifi-wishlist">
                                                    <i class="iconly-Heart icli"></i>
                                                </button>
                                            </div> --}}

                                            <a href="{{ route('Product_details.home', $product_info->slug) }}">
                                                @if (!empty($product_Image))
                                                    <img src="{{ asset('uploads/products/thumb/' . $product_Image->image) }}"
                                                        class="img-fluid blur-up lazyload" alt="">
                                                @endif
                                            </a>


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
                                            <a href="{{ route('Product_details.home', $product_info->slug) }}">
                                                <h5 class="name">
                                                    {{ \Illuminate\Support\Str::limit($product_info->name, 40) }}</h5>
                                            </a>
                                            <h5 class="price theme-color">৳
                                                {{ $product_details->selling_price }}


                                                @if ($product_details->selling_price < $product_details->compare_price)
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
    <!-- Shop Section End -->

@endsection

@section('customJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const categoryCheckboxes = document.querySelectorAll('.checkbox_animated');
            const priceInput = document.getElementById('price-range');
            const productWrappers = document.querySelectorAll('.product-list-section > div');

            let prices = Array.from(productWrappers).map(p =>
                parseFloat(p.querySelector('.product-box-4').dataset.price)
            );

            let minPrice = Math.min(...prices);
            let maxPrice = Math.max(...prices);

            // Init slider
            $(priceInput).ionRangeSlider({
                type: "double",
                min: minPrice,
                max: maxPrice,
                from: minPrice,
                to: maxPrice,
                grid: true,
                onFinish: filterProducts
            });

            function filterProducts() {
                const selectedCategories = Array.from(categoryCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                const slider = $(priceInput).data("ionRangeSlider");
                const fromPrice = slider.result.from;
                const toPrice = slider.result.to;

                productWrappers.forEach(wrapper => {
                    const product = wrapper.querySelector('.product-box-4');
                    const category = product.dataset.category;
                    const price = parseFloat(product.dataset.price);

                    const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(
                        category);
                    const priceMatch = price >= fromPrice && price <= toPrice;

                    wrapper.style.display = (categoryMatch && priceMatch) ? '' : 'none';
                });
            }

            categoryCheckboxes.forEach(cb =>
                cb.addEventListener('change', filterProducts)
            );

            // Reset
            document.getElementById('reset-filters').addEventListener('click', function() {
                categoryCheckboxes.forEach(cb => cb.checked = false);

                $(priceInput).data("ionRangeSlider").update({
                    from: minPrice,
                    to: maxPrice
                });

                productWrappers.forEach(wrapper => wrapper.style.display = '');
            });

        });
    </script>
@endsection
