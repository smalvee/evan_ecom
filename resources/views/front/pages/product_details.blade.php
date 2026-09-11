@extends('front.layouts.new_app')

@section('content')
    <?php
    
    use App\Models\ProductImage;
    
    use App\Models\NewProduct;
    use App\Models\ProductVariant;
    
    ?>

    <style>
        .image-link {
            position: absolute;
            inset: 0;
            z-index: 10;
        }

        .custom-btn {
            width: 60px;
            height: 30px;
            border-radius: 5px;
            border: 1px;
            background-color: #d99f46;
        }
    </style>
    <!-- Product Left Sidebar Start -->
    <section class="product-section">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-xxl-9 col-xl-8 col-lg-7 wow fadeInUp">
                    <div class="row g-4">



                        <div class="col-xl-6 wow fadeInUp">
                            <div class="product-left-box">
                                <div class="row g-2">

                                    {{-- Main Image --}}
                                    <div class="col-xxl-10 col-lg-12 col-md-10 order-xxl-2 order-lg-1 order-md-2">
                                        <div class="product-main-2 no-arrow">
                                            @foreach ($variants as $variant)
                                                @foreach (ProductImage::where('product_id', $variant->id)->where('is_thumb', 0)->get() as $image)
                                                    <div>
                                                        <div class="slider-image" style="pointer-events: none">
                                                            <img src="{{ asset('uploads/products/large/' . $image->image) }}"
                                                                class="img-fluid blur-up lazyload"
                                                                alt="{{ $product_info->name }}"
                                                                style="pointer-events: none;">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Thumbnails --}}
                                    <div class="col-xxl-2 col-lg-12 col-md-2 order-xxl-1 order-lg-2 order-md-1">
                                        <div class="left-slider-image-2 left-slider no-arrow slick-top">
                                            @foreach ($variants as $variant)
                                                @foreach (ProductImage::where('product_id', $variant->id)->where('is_thumb', 0)->get() as $image)
                                                    <div>
                                                        <div class="slider-image">
                                                            <img src="{{ asset('uploads/products/small/' . $image->image) }}"
                                                                class="img-fluid blur-up lazyload"
                                                                alt="{{ $product_info->name }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ================= RIGHT : PRODUCT INFO ================= --}}
                        <div class="col-xl-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="right-box-contain">

                                {{-- Discount --}}
                                <h6 class="offer-top" id="discount-label" style="display:none;">
                                    DISCOUNT
                                </h6>

                                {{-- Name --}}
                                <h2 class="name">{{ $product_info->name }}</h2>

                                {{-- SKU --}}

                                <p class="sku text-content mb-1">
                                    SKU: <span id="sku-value">{{ $defaultVariant->sku ?? '—' }}</span>
                                </p>
                                <div class="product-contain">
                                    {!! \Illuminate\Support\Str::limit(strip_tags($product_info->description), 300) !!}

                                </div>
                                <p style="display: none;">Selected Variant ID: <span
                                        id="selected-variant">{{ $defaultVariant->id ?? '—' }}</span>
                                </p>
                                <br>


                                {{-- Price --}}
                                <div class="price-rating">
                                    <h3 class="theme-color price">
                                        {{ $variants->first()->selling_price ?? 'As Pre Order' }} Tk
                                        @if ($variants->first()->selling_price < $variants->first()->compare_price)
                                            <del>{{ $variants->first()->compare_price ?? 'As Pre Order' }} Tk</del>
                                        @endif

                                    </h3>
                                </div>

                                {{-- ================= VARIATIONS ================= --}}
                                @if (!empty($groupedVariations))
                                    @foreach ($groupedVariations as $variationName => $values)
                                        <div class="product-package"
                                            style="display: flex; gap: calc(-5px + 8 * (100vw - 320px) / 1600);">
                                            <div class="product-title">
                                                <h4>{{ $variationName }}</h4>
                                            </div>
                                            <ul class="select-package"
                                                style="display: flex; gap: calc(-5px + 8 * (100vw - 320px) / 1600);">
                                                @foreach ($values as $value)
                                                    <li style=" padding-top: 12px;">
                                                        <a href="javascript:void(0)"
                                                            style="background: transparent; border: none;">
                                                            <button type="button"
                                                                class="btn btn-warning btn-sm variation-btn-b"
                                                                style="border: 1px solid black; padding: 0.15rem 0.4rem; font-size: 0.75rem;"
                                                                data-variation="{{ $variationName }}"
                                                                data-value="{{ $value }}">
                                                                {{ $value }}
                                                            </button>


                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                @endif


                                {{-- ================= QUANTITY + CART ================= --}}
                                <div class="note-box product-package price-qty">
                                    <div class="cart_qty qty-box product-qty">
                                        <div class="counter">
                                            <div class="qty-left-minus" data-type="minus" data-field="">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <input class="form-control input-number qty-input" type="number"
                                                name="quantity" value="1" min="1" step="1" readonly>

                                            <div class="qty-right-plus" data-type="plus" data-field="">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <button
                                        onclick="location.href = '{{ route('front.sing.checkout', $defaultVariant->id) }}';"
                                        class="btn btn-md bg-dark cart-button text-white w-50">Buy Now</button> --}}

                                    <button
                                        onclick="
                                            let qty = document.querySelector('.qty-input').value;
                                            let variantId = document.getElementById('selected-variant').textContent;

                                            // Build URL dynamically using JS variable
                                            let url = '/singel-product/' + variantId + '?qty=' + qty;
                                            location.href = url;
                                        "
                                        class="btn btn-md bg-dark text-white w-50">
                                        Buy Now
                                    </button>





                                    <button
                                        onclick="addToCart(this, document.getElementById('selected-variant').textContent)"
                                        class="btn btn-md bg-dark cart-button text-white w-50">Add To Cart
                                    </button>
                                </div>
                            </div>
                        </div>




















                        <div class="col-12">
                            <div class="product-section-box">
                                <ul class="nav nav-tabs custom-nav" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                            data-bs-target="#description" type="button" role="tab">Description</button>
                                    </li>
                                </ul>

                                <div class="tab-content custom-tab" id="myTabContent">
                                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                                        <div class="product-description">
                                            <div class="nav-desh">
                                                {!! $product_info->description !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-5 d-none d-lg-block wow fadeInUp">
                    <div class="right-sidebar-box">

                        <!-- Trending Product -->
                        <div class="pt-25">
                            <div class="category-menu">
                                <h3>Trending Products</h3>
                            </div>
                        </div>

                        <!-- Banner Section -->
                        <div class="ratio_156 pt-25">
                            <div class="home-contain">
                                <a href="{{ $advertise->url_06 }}" target="_blank" class="image-link"></a>
                                <img src="{{ asset('uploads/add/' . $advertise->image_06) }}"
                                    class="bg-img blur-up lazyload" alt="">
                                <div class="home-detail p-top-left home-p-medium">
                                    <div>
                                        <h6 class="text-yellow home-banner"></h6>
                                        <h3 class="text-uppercase fw-normal"><span class="theme-color fw-bold"></h3>
                                        <h3 class="fw-light"></h3>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Product Left Sidebar End -->


    <!-- Related Product Section Start -->
    <section class="product-list-section section-b-space">
        <div class="container-fluid-lg">
            <div class="title">
                <h2>Related Products</h2>
                <span class="title-leaf">
                    <svg class="icon-width">
                        <use xlink:href="../assets/svg/leaf.svg#leaf"></use>
                    </svg>
                </span>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="slider-6_1 product-wrapper">

                        @if (!empty($related_product))
                            @foreach ($related_product as $related_pro)
                                @php
                                    $product_details = ProductVariant::where('product_id', $related_pro->id)->first();
                                    $product_Image = ProductImage::where('product_id', $product_details->id)
                                        ->where('is_thumb', 1)
                                        ->first();

                                @endphp

                                <div>
                                    <div class="product-box-4 wow fadeInUp">
                                        <div class="product-image">


                                            <a href="{{ route('Product_details.home', $related_pro->slug) }}">
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
                                            <a href="{{ route('Product_details.home', $related_pro->slug) }}">
                                                <h5 class="name">
                                                    {{ $related_pro->name }}</h5>
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
    <!-- Related Product Section End -->
@endsection

@section('customJs')
    <script>
        let selectedVariantId = null;
    </script>

    @if (!empty($groupedVariations))
        <script>
            const selected = {};
            const variantMap = @json($variantMap);



            // Get variant key
            function getVariantKey() {
                return Object.values(selected).filter(Boolean).join('-');
            }

            // Update product images
            function updateImages(images) {
                const main = document.querySelector('.product-main-2');
                const thumb = document.querySelector('.left-slider-image-2');

                main.innerHTML = '';
                thumb.innerHTML = '';

                images.forEach(img => {
                    const html = `
            <div>
                <div class="slider-image">
                    <img src="/uploads/products/large/${img}" class="img-fluid blur-up lazyload">
                </div>
            </div>`;
                    main.insertAdjacentHTML('beforeend', html);
                    thumb.insertAdjacentHTML('beforeend', html);
                });

                if (window.$) {
                    $('.product-main-2').slick('refresh');
                    $('.left-slider-image-2').slick('refresh');
                }
            }

            // Update buttons, price, SKU, images
            function updateOptions() {
                Object.keys(selected).forEach(variationName => {
                    document.querySelectorAll(
                            `.variation-btn-b[data-variation="${variationName}"]`
                        )
                        .forEach(btn => {
                            const value = btn.dataset.value;

                            if (variationName === 'Color') {
                                // Color buttons always visible
                                btn.style.display = 'inline-block';
                                btn.classList.toggle('active', selected[variationName] === value);
                                return;
                            }

                            // Size buttons depend on selected Color
                            let possible = false;
                            Object.keys(variantMap).forEach(key => {
                                if (selected.Color && !key.startsWith(selected.Color)) return;
                                if (key.split('-').includes(value)) possible = true;
                            });

                            btn.style.display = possible ? 'inline-block' : 'none';
                            btn.classList.toggle('active', selected[variationName] === value);
                        });
                });

                // Determine variant to display
                let variant = null;

                if (Object.values(selected).every(v => v)) {
                    // All selected
                    const key = getVariantKey();
                    variant = variantMap[key];
                } else if (selected.Color) {
                    // Only color selected → pick first size for that color
                    const firstKey = Object.keys(variantMap).find(key => key.startsWith(selected.Color + '-'));
                    variant = variantMap[firstKey];
                    selected.Size = firstKey.split('-')[1]; // auto-select first size
                }

                if (!variant) return;

                // Update price and SKU
                let priceHTML = `${variant.price} Tk`;
                if (variant.compare_price > variant.price) {
                    priceHTML += ` <del class="text-content">${variant.compare_price} Tk</del>`;
                }

                document.querySelector('.price').innerHTML = priceHTML;
                document.getElementById('sku-value').textContent = variant.sku;
                document.getElementById('selected-variant').textContent = variant.id;

                // Update images
                if (variant.images && variant.images.length) {
                    updateImages(variant.images);
                }

                // Sync all buttons
                syncButtons();
            }

            // Sync active class for both button sets
            function syncButtons() {
                Object.keys(selected).forEach(variationName => {
                    document.querySelectorAll(
                            `.variation-btn-b[data-variation="${variationName}"]`
                        )
                        .forEach(btn => {
                            btn.classList.toggle('active', selected[variationName] === btn.dataset.value);
                        });
                });
            }

            // Handle button clicks
            document.querySelectorAll('.variation-btn-b').forEach(btn => {
                btn.addEventListener('click', function() {
                    const variation = this.dataset.variation;
                    const value = this.dataset.value;

                    selected[variation] = value;

                    // Reset size only if Color changes
                    if (variation === 'Color') {
                        selected.Size = null;
                    }

                    updateOptions();
                });
            });

            // Initial run
            updateOptions();
        </script>
    @endif

    <script>
        document.addEventListener("click", function(e) {
            const qtyInput = document.querySelector(".qty-input");
            if (!qtyInput) return;

            let value = parseInt(qtyInput.value) || 1;

            if (value < 1) {
                qtyInput.value = 1;
            }
        });
    </script>

    <script>
        document.querySelector(".qty-left-minus").addEventListener("click", function() {
            const input = document.querySelector(".qty-input");
            let val = parseInt(input.value) || 1;

            if (val <= 1) {
                input.value = 1;
                return;
            }
            input.value = val - 1;
        });
    </script>
@endsection
