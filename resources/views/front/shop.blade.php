@extends('front.layouts.app')

@section('content')
    <!-- Top header -->
    <section style="background:#FC8934;color:#fff;" class="py-3">
        <div class="container-fluid px-5 text-center">
            @if (!empty($selectedCategory->name))
                <h1 class="fw-bold mb-1">{{ $selectedCategory->name }}</h1>
            @else
                <h1 class="fw-bold mb-1">Offer Zone</h1>
            @endif
        </div>
    </section>

    <!-- ====== Styles for grid overrides (put in your CSS file if preferred) ====== -->
    <style>
        /* Desktop: ensure 4 per row (matches col-lg-3) */
        @media (min-width: 992px) {
            #productGrid .row>.customcol {
                flex: 0 0 25% !important;
                max-width: 25% !important;
            }
        }

        /* Mobile-only overrides (apply only below 576px) */
        @media (max-width: 575.98px) {
            /* default: let Bootstrap handle default (col-6 -> 50%). We'll override when classes are present */

            /* two-col mobile */
            #productGrid.two-col .row>.customcol {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }

            /* one-col mobile */
            #productGrid.one-col .row>.customcol {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
        }


        /* Small nicety for product card heights so cards align nicely */
        .product-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
    </style>

    <!-- Shop Page Section -->
    <section id="shopPage" class="py-5">
        <div class="container-fluid px-5">
            <div class="row">
                <!-- Sidebar Filter -->
                {{-- <div class="d-lg-none mb-3">
                    <button class="btn w-100 d-flex justify-content-between align-items-center px-3 py-2" type="button"
                        data-bs-toggle="collapse" data-bs-target="#mobileFilters" aria-expanded="false"
                        aria-controls="mobileFilters" style="background:#FC8934; color:#fff; border-radius:8px;">
                        <span class="fw-bold">Filters</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div> --}}



                <!-- Toggle Buttons (mobile only: visible only on xs because of d-sm-none) -->
                <div class="d-flex justify-content-between mb-4 d-sm-none align-items-center">

                    <!-- Left: Filter Button -->
                    <button class="btn d-flex align-items-center p-2" data-bs-toggle="collapse"
                        data-bs-target="#mobileFilters">
                        Filter <span style="padding-left: 2rem"><i class="fa-solid fa-angle-down"></i></span>
                    </button>

                    <!-- Right: Toggle Buttons -->
                    <div id="viewToggle" class="d-flex">
                        <button id="btnTwo"
                            class="btn btn-primary me-2 d-flex align-items-center justify-content-center p-2 active"
                            type="button" aria-pressed="true">
                            <img src="{{ asset('front-assets/icon/hamburger.png') }}" alt="2 per row"
                                style="width: 20px; height: auto;">
                        </button>
                        <button id="btnOne"
                            class="btn btn-secondary d-flex align-items-center justify-content-center p-2" type="button"
                            aria-pressed="false">
                            <img src="{{ asset('front-assets/icon/menu.png') }}" alt="1 per row"
                                style="width: 20px; height: auto;">
                        </button>
                    </div>

                </div>


                <aside class="col-lg-3 col-md-4 mb-4">
                    <div id="mobileFilters" class="collapse d-lg-block">
                        <div class="filter-box p-4"
                            style="background:#fff; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.08); position:relative;">

                            <!-- Close Button -->
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                                data-bs-toggle="collapse" data-bs-target="#mobileFilters" aria-label="Close"></button>

                            <h5 class="fw-bold mb-4 text-primary border-bottom pb-2">Filters</h5>
                            <h6 class="fw-bold mb-3" onclick="toggleCategories()"
                                style="cursor:pointer; display:flex; align-items:center; justify-content:space-between;">
                                Categories
                                <span id="categoryArrow">▲</span>
                            </h6>

                            <ul class="list-unstyled filter-list mb-4" id="categoryList">
                                @if (!empty($categories))
                                    @foreach ($categories as $category)
                                        <li class="mb-2">
                                            <!-- Main Category -->
                                            <a href="{{ route('product_shop.home', $category->id) }}"
                                                style="text-decoration:none; display:block; padding:8px 12px; border-radius:6px; transition:0.3s;
                          color: {{ isset($selectedCategory) && $selectedCategory->id == $category->id ? '#fff' : '#333' }};
                          background: {{ isset($selectedCategory) && $selectedCategory->id == $category->id ? '#FC8934' : 'transparent' }};
                          font-weight: {{ isset($selectedCategory) && $selectedCategory->id == $category->id ? 'bold' : 'normal' }};"
                                                onmouseover="this.style.background='{{ isset($selectedCategory) && $selectedCategory->id == $category->id ? '#0056b3' : '#f5f5f5' }}';"
                                                onmouseout="this.style.background='{{ isset($selectedCategory) && $selectedCategory->id == $category->id ? '#FC8934' : 'transparent' }}';">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>


                            <h6 class="fw-bold mb-3" onclick="toggleBrand()"
                                style="cursor:pointer; display:flex; align-items:center; justify-content:space-between;">
                                Brands
                                <span id="brandArrow">▲</span>
                            </h6>

                            <ul class="list-unstyled filter-list mb-4" id="brandList">
                                @if (!empty($brandList))
                                    @foreach ($brandList as $brand)
                                        <li class="mb-2 d-flex align-items-center">
                                            <input type="checkbox" id="brand_{{ $brand->id }}" name="brands[]"
                                                value="{{ $brand->id }}" class="form-check-input me-2 brand-filter">
                                            <label for="brand_{{ $brand->id }}" class="form-check-label">
                                                {{ $brand->name }}
                                            </label>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>




                            <h6 class="fw-bold mb-3" onclick="toggleavailability()"
                                style="cursor:pointer; display:flex; align-items:center; justify-content:space-between;">
                                Availablity
                                <span id="availabilityListArrow">▲</span>
                            </h6>

                            <ul class="list-unstyled filter-list mb-4" id="availabilityList">
                                <li class="mb-2 d-flex align-items-center">
                                    <input type="checkbox" id="inStock" class="form-check-input me-2" checked>
                                    <label for="inStock" class="form-check-label">In Stock</label>
                                </li>

                                <li class="mb-2 d-flex align-items-center">
                                    <input type="checkbox" id="outStock" class="form-check-input me-2" disabled>
                                    <label for="outStock" class="form-check-label text-muted">Out of Stock</label>
                                </li>
                            </ul>


                            <h6 class="fw-bold mb-3" onclick="toggleaprice()"
                                style="cursor:pointer; display:flex; align-items:center; justify-content:space-between;">
                                Price
                                <span id="priceArrow">▲</span>
                            </h6>

                            <ul class="list-unstyled filter-list mb-4" id="priceList">

                                <div id="sliderContainer" style="position:relative; height:40px; margin-bottom:10px;">
                                    <!-- Slider track -->
                                    <div id="sliderTrack"
                                        style="position:absolute; height:8px; background:#eee; border-radius:4px; top:50%; transform:translateY(-50%); width:100%;">
                                    </div>
                                    <div id="sliderRange"
                                        style="position:absolute; height:8px; background:#FC8934; border-radius:4px; top:50%; transform:translateY(-50%);">
                                    </div>

                                    <!-- Thumbs -->
                                    <div id="thumbMin"
                                        style="position:absolute; width:16px; height:16px; background:#000; border-radius:50%; top:50%; transform:translate(-50%, -50%); cursor:pointer;">
                                    </div>
                                    <div id="thumbMax"
                                        style="position:absolute; width:16px; height:16px; background:#000; border-radius:50%; top:50%; transform:translate(-50%, -50%); cursor:pointer;">
                                    </div>
                                </div>

                                <div style="display:flex; justify-content:space-between; font-size:14px; margin-top:5px;">
                                    <input type="number" id="inputMin" value="50" min="0" max="5000"
                                        style="width:45%; padding:5px; border:1px solid #ccc; border-radius:4px; text-align:center;">
                                    <span style="align-self:center;">To</span>
                                    <input type="number" id="inputMax" value="4000" min="0" max="5000"
                                        style="width:45%; padding:5px; border:1px solid #ccc; border-radius:4px; text-align:center;">
                                </div>


                            </ul>


                        </div>
                    </div>
                </aside>


                <!-- Products Grid -->
                <div class="col-lg-9 col-md-8" id="productGrid">
                    <div id="allProducts">
                        <div class="row g-4 justify-content-center py-3">
                            @if ($products->isNotEmpty())
                                @foreach ($products as $product)
                                    @php
                                        $productImage = $product->product_image->first();
                                    @endphp

                                    <div class="col-6 col-md-4 col-lg-3 customcol" data-price="{{ $product->price }}"
                                        data-brand-id="{{ $product->brand_id }}">
                                        <div class="product-card p-3 text-center d-flex flex-column h-100" style="border-width: 1px !important;">
                                            <a href="{{ route('Product_details.home', $product->slug) }}">
                                                @if (!empty($productImage->image))
                                                    <img style="width: 300px; border: none;"
                                                        src="{{ asset('uploads/products/large/' . $productImage->image) }}"
                                                        class="img-thumbnail" width="50"
                                                        alt="{{ $product->title }}">
                                                @else
                                                    <img style="width: 300px; border: none;"
                                                        src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                                        class="img-thumbnail" width="50"
                                                        alt="{{ $product->title }}">
                                                @endif
                                            </a>

                                            <a href="{{ route('Product_details.home', $product->slug) }}">
                                                <h6 style="color: black" class="product-title mb-2"
                                                    title="{{ $product->title }}">
                                                    {{ \Illuminate\Support\Str::limit($product->title, 10) }}
                                                </h6>
                                            </a>

                                            <h5 style="display: none;">{{ $product->brand_id }}</h5>

                                            <p class="mb-3 fw-semibold">
                                                TK: {{ number_format($product->price, 2) }}
                                                @if (!empty($product->compare_price))
                                                    <span class="text-muted text-decoration-line-through ms-2">
                                                        TK: {{ number_format($product->compare_price, 2) }}
                                                    </span>
                                                @endif
                                            </p>

                                            <a href="javascript:void(0)" onclick="addToCart({{ $product->id }})"
                                                class="mt-auto">
                                                <button class="btn quick-add-btn w-100" data-bs-toggle="offcanvas"
                                                    data-bs-target="#cartSidebar">
                                                    Quick Add
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-6 col-md-4 col-lg-3 customcol">
                                    <h3>No Product Found</h3>
                                </div>
                            @endif
                        </div>
                    </div>
                </div><!-- end productGrid -->
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        // small helper: safe query selector
        function qs(selector) {
            return document.querySelector(selector);
        }

        function qsa(selector) {
            return Array.from(document.querySelectorAll(selector));
        }

        // Preview images script (guarded)
        (function() {
            const previewImages = qsa('.preview-img');
            const mainImage = qs('#mainProductImg');
            if (previewImages.length && mainImage) {
                previewImages.forEach(img => {
                    img.addEventListener('click', function() {
                        mainImage.src = this.src;
                        previewImages.forEach(i => i.classList.remove('active'));
                        this.classList.add('active');
                    });
                });
            }
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productGrid = document.getElementById('productGrid');
            const btnOne = document.getElementById('btnOne');
            const btnTwo = document.getElementById('btnTwo');

            if (!productGrid || !btnOne || !btnTwo) {
                // If any element missing, do nothing (prevents JS errors)
                return;
            }

            function setGrid(view) {
                // Remove classes first
                productGrid.classList.remove('one-col', 'two-col');

                if (view === 'one') {
                    productGrid.classList.add('one-col');
                    btnOne.classList.add('active-custom1', 'active');
                    btnOne.classList.remove('active-custom2');
                    btnTwo.classList.remove('active-custom1', 'active');
                    btnTwo.classList.add('active-custom2');
                    btnOne.setAttribute('aria-pressed', 'true');
                    btnTwo.setAttribute('aria-pressed', 'false');
                } else {
                    productGrid.classList.add('two-col');
                    btnTwo.classList.add('active-custom1', 'active');
                    btnTwo.classList.remove('active-custom2');
                    btnOne.classList.remove('active-custom1', 'active');
                    btnOne.classList.add('active-custom2');
                    btnTwo.setAttribute('aria-pressed', 'true');
                    btnOne.setAttribute('aria-pressed', 'false');
                }
            }

            // Attach listeners (this avoids duplicate-firing if you keep inline onclick)
            btnOne.addEventListener('click', function(e) {
                e.preventDefault();
                setGrid('one');
            });
            btnTwo.addEventListener('click', function(e) {
                e.preventDefault();
                setGrid('two');
            });

            // Default: two-column on mobile
            setGrid('two');
        });
    </script>

    <script>
        function toggleCategories() {
            let list = document.getElementById("categoryList");
            let arrow = document.getElementById("categoryArrow");

            if (list.style.display === "none") {
                list.style.display = "block";
                arrow.textContent = "▲"; // up arrow
            } else {
                list.style.display = "none";
                arrow.textContent = "▼"; // down arrow
            }
        }

        function toggleavailability() {
            let list = document.getElementById("availabilityList");
            let arrow = document.getElementById("availabilityListArrow");

            if (list.style.display === "none") {
                list.style.display = "block";
                arrow.textContent = "▲"; // up arrow
            } else {
                list.style.display = "none";
                arrow.textContent = "▼"; // down arrow
            }
        }

        function toggleaprice() {
            let list = document.getElementById("priceList");
            let arrow = document.getElementById("priceArrow");

            if (list.style.display === "none") {
                list.style.display = "block";
                arrow.textContent = "▲"; // up arrow
            } else {
                list.style.display = "none";
                arrow.textContent = "▼"; // down arrow
            }
        }

        function toggleBrand() {
            let list = document.getElementById("brandList");
            let arrow = document.getElementById("brandArrow");

            if (list.style.display === "none") {
                list.style.display = "block";
                arrow.textContent = "▲"; // up arrow
            } else {
                list.style.display = "none";
                arrow.textContent = "▼"; // down arrow
            }
        }
    </script>

    {{-- filter by price and brands  --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sliderContainer = document.getElementById('sliderContainer');
            const thumbMin = document.getElementById('thumbMin');
            const thumbMax = document.getElementById('thumbMax');
            const sliderRange = document.getElementById('sliderRange');
            const inputMin = document.getElementById('inputMin');
            const inputMax = document.getElementById('inputMax');

            const brandFilters = document.querySelectorAll(".brand-filter");
            const products = document.querySelectorAll("#productGrid .customcol");

            let minVal = parseInt(inputMin.value);
            let maxVal = parseInt(inputMax.value);
            const maxPrice = 5000;

            // Update slider UI
            function updateSlider() {
                const minPercent = (minVal / maxPrice) * 100;
                const maxPercent = (maxVal / maxPrice) * 100;

                thumbMin.style.left = minPercent + '%';
                thumbMax.style.left = maxPercent + '%';

                sliderRange.style.left = minPercent + '%';
                sliderRange.style.width = (maxPercent - minPercent) + '%';

                inputMin.value = minVal;
                inputMax.value = maxVal;
            }

            // Unified filter: price + brand
            function filterProducts() {
                let selectedBrands = Array.from(document.querySelectorAll(".brand-filter:checked"))
                    .map(cb => cb.value);

                products.forEach(product => {
                    const price = parseFloat(product.dataset.price);
                    const brandId = product.getAttribute("data-brand-id");

                    let matchesPrice = price >= minVal && price <= maxVal;
                    let matchesBrand = (selectedBrands.length === 0 || selectedBrands.includes(brandId));

                    if (matchesPrice && matchesBrand) {
                        product.style.display = "block";
                    } else {
                        product.style.display = "none";
                    }
                });
            }

            // Unified drag function (mouse + touch)
            function dragThumb(thumb, isMin) {
                function move(e) {
                    e.preventDefault();
                    let clientX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;

                    const rect = sliderContainer.getBoundingClientRect();
                    let percent = ((clientX - rect.left) / rect.width) * 100;
                    percent = Math.min(Math.max(percent, 0), 100);

                    let value = Math.round((percent / 100) * maxPrice);

                    if (isMin) {
                        if (value > maxVal) value = maxVal;
                        minVal = value;
                    } else {
                        if (value < minVal) value = minVal;
                        maxVal = value;
                    }

                    updateSlider();
                    filterProducts();
                }

                function end() {
                    document.removeEventListener('mousemove', move);
                    document.removeEventListener('mouseup', end);
                    document.removeEventListener('touchmove', move);
                    document.removeEventListener('touchend', end);
                }

                document.addEventListener('mousemove', move);
                document.addEventListener('mouseup', end);
                document.addEventListener('touchmove', move, {
                    passive: false
                });
                document.addEventListener('touchend', end);
            }

            // Attach slider events
            thumbMin.addEventListener('mousedown', () => dragThumb(thumbMin, true));
            thumbMax.addEventListener('mousedown', () => dragThumb(thumbMax, false));
            thumbMin.addEventListener('touchstart', e => dragThumb(thumbMin, true), {
                passive: false
            });
            thumbMax.addEventListener('touchstart', e => dragThumb(thumbMax, false), {
                passive: false
            });

            // Input fields
            inputMin.addEventListener('input', () => {
                let val = parseInt(inputMin.value);
                if (val > maxVal) val = maxVal;
                if (val < 0) val = 0;
                minVal = val;
                updateSlider();
                filterProducts();
            });

            inputMax.addEventListener('input', () => {
                let val = parseInt(inputMax.value);
                if (val < minVal) val = minVal;
                if (val > maxPrice) val = maxPrice;
                maxVal = val;
                updateSlider();
                filterProducts();
            });

            // Brand filter events
            brandFilters.forEach(checkbox => {
                checkbox.addEventListener("change", filterProducts);
            });

            // Initialize
            updateSlider();
            filterProducts();
        });
    </script>
@endsection
