@extends('front.layouts.app')

@section('content')
    <section id="productDetails" class="py-5">
        @if (!empty($product))
            @php
                $productImage = $product->product_image;
            @endphp
            <div class="container">
                <div class="row align-items-start">

                    <div class="row">
                        <!-- Product Images (Fixed) -->
                        <div class="col-lg-6 mb-4" style="top:20px; height:auto;">
                            {{-- Main Image --}}
                            <div class="main-image mb-3">
                                <img id="mainProductImg"
                                    src="{{ asset(optional($productImage->first())->image ? 'uploads/products/large/' . $productImage->first()->image : 'admin-assets/img/default-150x150.png') }}"
                                    alt="{{ $product->title }}" class="img-fluid rounded shadow">
                            </div>

                            {{-- Preview Images --}}
                            <div class="preview-images d-flex gap-2">
                                @foreach ($productImage as $key => $image)
                                    <img src="{{ asset(optional($image)->image ? 'uploads/products/large/' . $image->image : 'admin-assets/img/default-150x150.png') }}"
                                        alt="Preview {{ $key + 1 }}"
                                        class="preview-img {{ $loop->first ? 'active' : '' }}"
                                        style="cursor:pointer; width:60px; height:60px; object-fit:cover;">
                                @endforeach
                            </div>
                        </div>

                        <!-- Product Info (Scrollable) -->
                        <div class="col-lg-6" style="">
                            <h2 class="fw-bold mb-2" style="margin-bottom: 10px;">
                                {{ $product->title }}
                            </h2>

                            <div class="mb-3">
                                <span class="sku">
                                    SKU: <span style="color: #000;">{{ strtoupper($product->sku) }}</span>
                                </span>
                            </div>

                            <div class="mb-3">
                                <span class="fs-4 fw-bold text-danger" style="font-size:1.5rem; color:red;">
                                    Tk {{ number_format($product->price, 2) }}
                                </span>

                                @if (!empty($product->compare_price))
                                    <span class="text-muted text-decoration-line-through ms-2"
                                        style="text-decoration: line-through; margin-left: 10px;">
                                        Tk {{ number_format($product->compare_price, 2) }}
                                    </span>

                                    <span class="badge bg-success ms-2"
                                        style="background: green; color: #fff; margin-left: 10px; padding: 5px 8px; font-size: 0.9rem;">
                                        Save Tk {{ number_format($product->compare_price - $product->price, 2) }}
                                    </span>
                                @endif
                            </div>


                            <!-- Action Buttons -->
                            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:20px;">
                                <button onclick="addToCart({{ $product->id }})" data-bs-toggle="offcanvas"
                                    data-bs-target="#cartSidebar"
                                    style="width:100%; padding:12px; background:#000000; color:#ff7f2a; font-weight:500; border:2px solid transparent; border-radius:5px; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                                    onmouseover="this.style.border='2px solid #000000'; this.style.background='white';"
                                    onmouseout="this.style.border='2px solid transparent'; this.style.background='#000000';">
                                    Add to Cart
                                </button>

                                <a href="{{ route('front.sing.checkout', $product->slug) }}"><button
                                        style="width:100%; padding:12px; background:#ff9800; color:#000; font-weight:500; border:2px solid transparent; border-radius:5px; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                                        onmouseover="this.style.border='2px solid #ff9800'; this.style.background='white';"
                                        onmouseout="this.style.border='2px solid transparent'; this.style.background='#ff9800';">
                                        🛒 ক্যাশ অন ডেলিভারিতে অর্ডার করুন
                                    </button></a>

                                <button
                                    style="width:100%; padding:12px; background:#ffeb3b; color:#000; font-weight:500; border:2px solid transparent; border-radius:5px; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                                    onmouseover="this.style.border='2px solid #ffeb3b'; this.style.background='white';"
                                    onmouseout="this.style.border='2px solid transparent'; this.style.background='#ffeb3b';">
                                    💳 Pay Online
                                </button>

                                <a href="https://m.me/61572962902378" target="_blank" style="text-decoration:none;">
                                    <button
                                        style="width:100%; padding:12px; background:#000; color:#fff; font-weight:500; border:2px solid transparent; border-radius:5px; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                                        onmouseover="this.style.border='2px solid #000'; this.style.background='white'; this.style.color='#000';"
                                        onmouseout="this.style.border='2px solid transparent'; this.style.background='#000'; this.style.color='#fff';">
                                        <img src="https://cdn-icons-png.flaticon.com/512/124/124010.png"
                                            style="width:20px; height:20px; margin-right:8px;"> Chat with us
                                    </button>
                                </a>



                                <a href="https://wa.me/8801711258826" target="_blank" style="text-decoration:none;">
                                    <button
                                        style="width:100%; padding:12px; background:#000; color:#25D366; font-weight:500; border:2px solid transparent; border-radius:5px; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                                        onmouseover="this.style.border='2px solid #25D366'; this.style.background='white';"
                                        onmouseout="this.style.border='2px solid transparent'; this.style.background='#000';">
                                        <img src="https://cdn-icons-png.flaticon.com/512/124/124034.png"
                                            style="width:20px; height:20px; margin-right:8px;"> WhatsApp Us
                                    </button>
                                </a>

                            </div>

                            <!-- Description Collapse -->
                            <div style="margin-bottom:20px; border-bottom:2px solid rgb(255,139,75);">
                                <button type="button" onclick="toggleDesc()"
                                    style="background:none; border:none; font-size:18px; font-weight:600; color:#000; padding:12px 0; width:100%; text-align:left; display:flex; justify-content:space-between; align-items:center; cursor:pointer;">
                                    <h4 style="margin:0;">Description</h4>
                                    <span id="descCaret" style="transition: transform 0.3s;">&#9660;</span>
                                </button>
                                <div id="descCollapse" style="display:none; padding:15px 0; transition: all 0.3s ease;">
                                    {!! $product->description !!}
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="contact-info" style="margin-top:20px;">
                                <p style="font-size:1.5rem; margin-bottom:5px;">আমাদের যে কোন পণ্য অর্ডার করতে কল বা
                                    WhatsApp করুন:</p>
                                <p style="font-size:medium; font-weight:bold; margin-bottom:5px;">📞 +880 1711-258826</p>
                                <p style="font-size:medium; font-weight:bold;">হট লাইন: +880 1711-258826</p>
                            </div>
                        </div>
                    </div>




                </div>
            </div>
        @endif
    </section>

    <section id="features" class="py-5">
        <div class="features-grid">
            <div class="feature-item text-center p-4">
                <img src="https://cdn-icons-png.flaticon.com/512/833/833314.png" alt="Secure Payment"
                    class="feature-icon mb-3">
                <h5 class="fw-bold mb-2">নিরাপদ পেমেন্ট</h5>
                <p class="text-muted mb-0">বিভিন্ন পেমেন্ট পদ্ধতি থেকে বেছে নিন</p>
            </div>
            <div class="feature-item text-center p-4">
                <img src="https://cdn-icons-png.flaticon.com/512/2769/2769339.png" alt="Green Delivery"
                    class="feature-icon mb-3">
                <h5 class="fw-bold mb-2">দ্রুত ডেলিভারি</h5>
                <p class="text-muted mb-0">৩-৫ দিনের মধ্যে আপনার পণ্য পৌছে যাবে</p>
            </div>
            <div class="feature-item text-center p-4">
                <img src="https://cdn-icons-png.flaticon.com/512/4586/4586104.png" alt="Natural Product"
                    class="feature-icon mb-3">
                <h5 class="fw-bold mb-2">টেকসই প্যাকেজিং</h5>
                <p class="text-muted mb-0">পণ্যটি নিরাপদে ও অক্ষতভাবে পৌঁছে দিতে আমরা প্রতিশ্রুতিবদ্ধ</p>
            </div>
        </div>
    </section>

    <section id="relatedProducts" class="py-5">
        <div class="container">
            <h3 class="fw-bold text-center mb-4">You Might Also Like</h3>
            <div class="row g-4 justify-content-center">
                <!-- Product Items -->
                @if (!empty($extra_products))
                    @foreach ($extra_products as $extra_product)
                        @php
                            // Get the first product image
                            // dd($extra_product->id, $product->id);
                            $productImage = $extra_product->product_image->first();
                        @endphp

                        @if ($extra_product->id == $product->id)
                        @else
                            <div class="col-6 col-md-4 col-lg-2 custom-col">
                                <div class="product-card p-3 text-center d-flex flex-column"
                                    style="height: 100%; border:1px solid #ddd; border-radius:8px; position:relative;">

                                    <a href="{{ route('Product_details.home', $extra_product->slug) }}">
                                        @if (!empty($productImage) && !empty($productImage->image))
                                            <img style="width: 100%; max-width:300px;"
                                                src="{{ asset('uploads/products/large/' . $productImage->image) }}"
                                                alt="{{ $extra_product->title }}" class="img-thumbnail mb-3">
                                        @else
                                            <img style="width: 100%; max-width:300px;"
                                                src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                                alt="{{ $extra_product->title }}" class="img-thumbnail mb-3">
                                        @endif
                                    </a>

                                    <p class="product-title mb-2">
                                        {{ \Illuminate\Support\Str::limit($extra_product->title, 10) }}</p>
                                    <p class="product-title mb-3">৳{{ number_format($extra_product->price, 2) }}</p>

                                    @if (!empty($extra_product->compare_price))
                                        <p class="product-title mb-3" style="text-decoration:line-through;">
                                            TK: {{ number_format($extra_product->compare_price, 2) }}
                                        </p>
                                    @endif

                                    <!-- Quick Add button fixed at bottom -->
                                    <a href="javascript:void(0)" onclick="addToCart({{ $extra_product->id }})"
                                        class="mt-auto w-100">
                                        <button class="btn btn-sm btn-primary quick-add-btn w-100"
                                            data-bs-toggle="offcanvas" data-bs-target="#cartSidebar">
                                            Quick Add
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </section>

@endsection

@section('customJs')
    <script>
        // Select all preview images
        const previewImages = document.querySelectorAll('.preview-img');
        const mainImage = document.getElementById('mainProductImg');

        previewImages.forEach(img => {
            img.addEventListener('click', function() {
                // Change main image src
                mainImage.src = this.src;

                // Update active class
                previewImages.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>


    {{-- for description toogle --}}
    <script>
        function toggleDesc() {
            const desc = document.getElementById('descCollapse');
            const caret = document.getElementById('descCaret');
            if (desc.style.display === 'none' || desc.style.display === '') {
                desc.style.display = 'block';
                caret.style.transform = 'rotate(180deg)';
            } else {
                desc.style.display = 'none';
                caret.style.transform = 'rotate(0deg)';
            }
        }
    </script>
@endsection
