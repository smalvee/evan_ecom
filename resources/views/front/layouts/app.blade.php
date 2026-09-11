<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@500&display=swap" rel="stylesheet">


    <link rel="icon" type="image/png" href="{{ asset('front-assets/images/favicon.png') }}">



    <link rel="stylesheet" href="{{ asset('front-assets/css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sharm International</title>
    <style>
        .active-custom1 {
            background-color: #FC8934 !important;
            color: #fff !important;
            /* keep text readable */
            border-color: #FC8934 !important;
        }

        .active-custom2 {
            background-color: #ffffff !important;
            color: #fff !important;
            /* keep text readable */
            border-color: #ffffff !important;
        }

        /* Default two-column on mobile */
        #productGrid .customcol {
            flex: 0 0 50%;
            /* 2 per row */
            max-width: 50%;
        }

        #productGrid.one-col .customcol {
            flex: 0 0 100%;
            /* 1 per row */
            max-width: 100%;
        }

        #productGrid.two-col .customcol {
            flex: 0 0 50%;
            /* 2 per row */
            max-width: 50%;
        }

        /* One column view (mobile toggle) */
        #productGrid.one-col .customcol {
            flex: 0 0 100%;
            max-width: 100%;
        }

        /* Two column view (mobile toggle) */
        #productGrid.two-col .customcol {
            flex: 0 0 50%;
            max-width: 50%;
        }

        /* Scroll to Top Button */
        #scrollToTopBtn {
            position: fixed;
            bottom: 80px;
            right: 40px;
            z-index: 100;
            background-color: #fc8934;
            color: white;
            border: none;
            outline: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            display: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        #scrollToTopBtn:hover {
            background-color: #e77b2d;
            transform: translateY(-2px);
        }
    </style>
    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '813485278270631');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=813485278270631&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
</head>

<body>
    <!-- Announcement Section -->
    <section class="announcement-bar py-2">
        <div class="container text-center">
            <p class="mb-0 text-white fw-bold">
                আমাদের যে কোন পণ্য অর্ডার করতে কল বা WhatsApp করুন:
                <i class="bi bi-telephone-fill me-2"></i>
                <a href="https://wa.me/8801711258826" target="_blank" class="text-white text-decoration-none">
                    +880 1711-258826
                </a>
                |
                হট লাইন:
                <i class="bi bi-telephone-fill me-2"></i>
                <a href="tel:+8801711258826" class="text-white text-decoration-none">
                    +880 1711-258826
                </a>
            </p>

        </div>
    </section>


    <!-- Top Nav Row (inside container-fluid px-5) -->
    @include('front.layouts.nav')




    @yield('content')






    {{-- cart side bar  --}}

    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartSidebar">
        @include('front.layouts.cart-sidebar', ['cartContent' => $cartContent ?? []])
    </div>








    <!-- Footer Section -->
    <!-- Footer Section -->
    <div class="">
        <footer class="pt-5 pb-4">
            <div class="container-fluid px-5">
                <div class="row text-start align-items-start">

                    <!-- 1st Column (40%) -->
                    <div class="col-lg-5 col-md-6 mb-4">
                        <a href="{{ route('front.home') }}" class="d-block mb-3">
                            <img src="{{ asset('front-assets/images/logo.jpg') }}" alt="Sharm International Ltd."
                                style="width: 220px;">
                        </a>
                        <p style="text-align: justify">
                            <strong>Sharm International Ltd.: Your Trusted Destination for Premium
                                Cosmetics</strong><br><br> Sharm International Ltd. is a leading cosmetics brand in
                            Bangladesh, offering a wide range of high-quality beauty and skincare products. From
                            nourishing face creams and rejuvenating serums to vibrant lipsticks, eye shadows, and
                            everyday essentials, we are dedicated to enhancing your natural beauty with safe and
                            effective products.<br><br> Our mission is to provide customers with authentic, trendy, and
                            dermatologist-approved cosmetics at affordable prices. With a strong focus on quality and
                            customer satisfaction, Sharm International Ltd. is your one-stop shop for all things beauty
                            — helping you look and feel confident every day.
                        </p>
                    </div>

                    <!-- 2nd Column (20%) -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5 class="fw-bold mb-3">Company</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('front.aboutus') }}" class="text-dark text-decoration-none">About
                                    Us</a></li>
                            <li><a href="{{ route('front.return') }}" class="text-dark text-decoration-none">Return
                                    Policy</a></li>
                            <li><a href="{{ route('front.refund') }}" class="text-dark text-decoration-none">Refund
                                    Policy</a></li>
                        </ul>
                    </div>

                    <!-- 3rd Column (20%) -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5 class="fw-bold mb-3">Quick Help</h5>
                        <ul class="list-unstyled">
                            {{-- <li><a href="#" class="text-dark text-decoration-none">FAQs</a></li>
                            <li><a href="#" class="text-dark text-decoration-none">Support</a></li> --}}
                            <li><a href="{{ route('front.trackOrderPage') }}"
                                    class="text-dark text-decoration-none">Track Order</a></li>
                        </ul>
                    </div>

                    <!-- 4th Column (20%, adjusted as col-3 for grid) -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="fw-bold mb-3">Contact</h5>
                        <p><b>Novel House (4th Floor), 137, Shantinagar, Dhaka, Bangladesh</b></p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <section class="announcement-bar py-3" style="background: #fc8934;">
        <div class="container text-center text-white">
            <p class="mb-1 fw-bold">
                © Sharm International 2025
            </p>
            <p class="mb-0" style="font-size: 0.9rem;">
                Design & Developed by
                <a href="https://softcoit.com" target="_blank"
                    style="color: #111; text-decoration: none; font-weight: 600;">
                    <strong>Softco IT Ltd.</strong>
                </a>
            </p>
        </div>
    </section>


    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" title="Go to top">
        <i class="fas fa-arrow-up"></i>
    </button>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('front-assets/js/script.js') }}"></script>
    <script type="text/javascript">
        // Scroll to Top Button
        window.onscroll = function() {
            scrollFunction()
        };

        function scrollFunction() {
            const btn = document.getElementById("scrollToTopBtn");
            if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                btn.style.display = "block";
            } else {
                btn.style.display = "none";
            }
        }

        document.getElementById("scrollToTopBtn").addEventListener("click", function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        // Prevent mobile browsers from pushing fixed nav off-screen
        window.addEventListener("scroll", function() {
            const nav = document.querySelector(".mobile-bottom-nav");
            nav.style.bottom = "0";
        });



        // Add to cart 
        // Add to cart 
        // Add to cart 
        function addToCart(id) {
            $.ajax({
                url: '{{ route('front.addToCart') }}',
                type: 'post',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === true) {
                        // Update sidebar
                        $('#cartSidebar').html(response.cartView);

                        // Update all cart badges (desktop + mobile)
                        if (response.cartCount && response.cartCount > 0) {
                            if ($('.cart-badge').length) {
                                $('.cart-badge').text(response.cartCount);
                            } else {
                                // Desktop badge
                                $('.nav-icon.d-lg-inline-block').append(
                                    '<span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background-color:#fc8934; color:#fff; min-width:18px; min-height:18px; font-size:0.7rem;">' +
                                    response.cartCount + '</span>'
                                );
                                // Mobile badge
                                $('.mobile-bottom-nav button').append(
                                    '<span class="cart-badge position-absolute top-0 translate-middle badge rounded-pill" style="background-color:#fc8934; color:#fff; font-size:0.7rem; min-width:18px; min-height:18px;">' +
                                    response.cartCount + '</span>'
                                );
                            }
                        } else {
                            $('.cart-badge').remove(); // remove all badges if cart empty
                        }

                        // Show offcanvas sidebar
                        let cartSidebar = new bootstrap.Offcanvas(document.getElementById('cartSidebar'));
                        cartSidebar.show();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    </script>

    <script>
        // Increase qty
        $(document).on('click', '.plus', function() {
            var qtyElement = $(this).parent().prev(); // Qty Input
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue < 10) {
                qtyElement.val(qtyValue + 1);

                var rowId = $(this).data('id');
                var newQty = qtyElement.val();
                updateCart(rowId, newQty);
            }
        });

        // Decrease qty
        $(document).on('click', '.minus', function() {
            var qtyElement = $(this).parent().next();
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue > 1) {
                qtyElement.val(qtyValue - 1);

                var rowId = $(this).data('id');
                var newQty = qtyElement.val();
                updateCart(rowId, newQty);
            }
        });


        function updateCart(rowId, qty) {
            $.ajax({
                url: '{{ route('front.updateCart') }}',
                type: 'post',
                data: {
                    rowId: rowId,
                    qty: qty
                },
                dataType: 'json',
                success: function(response) {
                    $('#cartSidebar').html(response.cartView);
                }
            });
        }


        function deleteItems(rowId) {
            if (confirm("Are you sure you want to delete?")) {
                $.ajax({
                    url: '{{ route('front.deleteItem.sidecart') }}',
                    type: 'post',
                    data: {
                        rowId: rowId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Reload only sidebar (no page reload)
                        $('#cartSidebar').html(response.cartView);

                        // Update all cart badges
                        if (response.cartCount && response.cartCount > 0) {
                            if ($('.cart-badge').length) {
                                $('.cart-badge').text(response.cartCount);
                            } else {
                                // If badge doesn't exist yet (optional)
                                $('.nav-icon, .mobile-bottom-nav button').append(

                                );
                            }
                        } else {
                            // Remove badge if cart is empty
                            $('.cart-badge').remove();
                        }

                        // Show offcanvas sidebar
                        let cartSidebar = new bootstrap.Offcanvas(document.getElementById('cartSidebar'));
                        cartSidebar.show();

                    }
                });
            }
        }
    </script>

    @php
        $allProducts = $products
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'price' => $p->price,
                    'compare_price' => $p->compare_price,
                    'image' => $p->product_image->first()->image ?? null,
                ];
            })
            ->toArray();
    @endphp

    <script>
        const allProducts = @json($allProducts);
        const searchInput = document.getElementById("searchInput");
        const searchResults = document.getElementById("searchResults");

        searchInput.addEventListener("keyup", function() {
            let query = this.value.toLowerCase();
            searchResults.innerHTML = ""; // clear old results

            if (query.length === 0) {
                searchResults.innerHTML = `<p class="text-muted"></p>`;
                return;
            }

            // Filter products by title
            let filtered = allProducts.filter(p => p.title.toLowerCase().includes(query));

            if (filtered.length === 0) {
                searchResults.innerHTML = `<p class="text-danger">No products found</p>`;
                return;
            }

            // Start grid container
            let html = `<div class="row g-3">`;

            filtered.forEach(p => {
                let productUrl = "{{ route('Product_details.home', ':slug') }}".replace(':slug', p.slug);

                html += `
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 text-center shadow-sm">
                        <a href="${productUrl}">
                            <img src="${p.image ? '/uploads/products/large/' + p.image : '/admin-assets/img/default-150x150.png'}" 
                                 class="card-img-top" style="height:150px; object-fit:cover;">
                        </a>
                        <div class="card-body p-2">
                            <a href="${productUrl}" style="text-decoration:none; color:black;">
                                <h6 class="card-title mb-1" title="${p.title}">${p.title}</h6>
                            </a>
                            <p class="mb-1 fw-semibold">
                                TK: ${Number(p.price).toFixed(2)}
                                ${p.compare_price ? `<span class="text-muted text-decoration-line-through ms-1">TK:${Number(p.compare_price).toFixed(2)}</span>` : ""}
                            </p>
                        </div>
                    </div>
                </div>
            `;
            });

            html += `</div>`; // close row
            searchResults.innerHTML = html;
        });
    </script>


    {{-- controll row vidibility   --}}
    <script>
        function setGrid(type) {
            if (window.innerWidth < 576) {
                const items = document.querySelectorAll("#productGrid .grid-item");

                // Reset grid
                items.forEach(item => {
                    item.classList.remove("col-12", "col-6");
                    if (type === "two") {
                        item.classList.add("col-6"); // 2 per row
                    } else {
                        item.classList.add("col-12"); // 1 per row
                    }
                });

                // Reset both buttons
                document.getElementById("btnTwo").classList.remove("active-custom1");
                document.getElementById("btnTwo").classList.add("active-custom2");

                document.getElementById("btnOne").classList.remove("active-custom1");
                document.getElementById("btnOne").classList.add("active-custom2");

                // Apply active style
                if (type === "two") {
                    document.getElementById("btnTwo").classList.add("active-custom1");
                    document.getElementById("btnTwo").classList.remove("active-custom2");
                } else {
                    document.getElementById("btnOne").classList.add("active-custom1");
                    document.getElementById("btnOne").classList.remove("active-custom2");
                }

            }
        }

        // Default mobile view: 2 per row
        if (window.innerWidth < 576) {
            setGrid("two");
        }
    </script>


    @yield('customJs')

</body>

</html>
