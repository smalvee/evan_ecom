 <?php
 
 use App\Models\SubCategory;
 ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta name="description" content="Fastkart">
     <meta name="keywords" content="Fastkart">
     <meta name="author" content="Fastkart">
     <link rel="icon" href="{{ asset('new-front-assets/images/favicon/8.jpeg') }}" type="image/x-icon">
     <title>Smart Choices, Better Living</title>

     <!-- Google font -->
     <link rel="preconnect" href="https://fonts.gstatic.com">
     <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700;800;900&display=swap"
         rel="stylesheet">
     <link rel="stylesheet"
         href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">

     <!-- bootstrap css -->
     <link id="rtl-link" rel="stylesheet" type="text/css"
         href=" {{ asset('new-front-assets/css/vendors/bootstrap.css') }}">

     <!-- wow css -->
     <link rel="stylesheet" href=" {{ asset('new-front-assets/css/animate.min.css') }}">

     <!-- Iconly css -->
     <link rel="stylesheet" type="text/css" href=" {{ asset('new-front-assets/css/bulk-style.css') }}">
     {{-- <link rel="stylesheet" type="text/css" href=" {{ asset('new-front-assets/css/vendors/animate.css') }}"> --}}

     <!-- Template css -->
     <link id="color-link" rel="stylesheet" type="text/css" href=" {{ asset('new-front-assets/css/style.css') }}">
     <meta name="csrf-token" content="{{ csrf_token() }}">
  
  

  
  

     <style>
         .nav-link.remove-dropdown::before {
             display: none !important;
         }

         .navbar-nav .nav-link:hover,
         .navbar-nav .nav-link:focus {
             background-color: #6262a6;
             color: #ffffff !important;
             border-color: #ddd;
         }


         .navbar-nav .nav-link:hover {
             border-color: #ddd;
             background-color: #6262a6;
         }




         .navbar-nav .nav-item {
             margin: 4px;
         }

         .search-item {
             cursor: pointer;
         }

         .search-item:hover {
             background-color: #0d6efd;
             color: white;
         }

         .cart-icon-wrapper {
             position: relative;
             display: inline-block;
         }

         .cart-badge {
             position: absolute;
             top: 4px;
             right: 30px;
             background: #ff4c3b;
             color: #fff;
             font-size: 11px;
             font-weight: 600;
             width: 20px;
             height: 20px;
             line-height: 18px;
             border-radius: 25%;
             text-align: center;
             z-index: 10;
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
fbq('init', '2261244381019011');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=2261244381019011&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->


 </head>

 <body class="theme-color-1">
     <!-- Loader Start -->
     {{-- <div class="fullpage-loader">
         <span></span>
         <span></span>
         <span></span>
         <span></span>
         <span></span>
         <span></span>
     </div> --}}
     <!-- Loader End -->

     <!-- Header Start -->
     <header class="pb-md-4 pb-0">
         <div class="top-nav top-header sticky-header">
             <div class="container-fluid-lg">
                 <div class="row">
                     <div class="col-12">
                         <div class="navbar-top">
                             <button class="navbar-toggler d-xl-none d-inline navbar-menu-button" type="button"
                                 data-bs-toggle="offcanvas" data-bs-target="#primaryMenu">
                                 <span class="navbar-toggler-icon">
                                     <i class="fa-solid fa-bars"></i>
                                 </span>
                             </button>
                              <a href="{{ route('front.home') }}" class="web-logo nav-logo">
                                  <img src="{{ $settings['site_logo_url'] ?? asset('new-front-assets/images/logo/8.png') }}"
                                      class="img-fluid blur-up lazyload" alt="Evan Store">
                              </a>

                             <div class="middle-box">
                                 <div class="location-box">
                                     <button class="btn location-button">
                                         <span class="location-arrow">
                                             <i data-feather="map-pin"></i>
                                         </span>
                                         <span class="locat-name">Bangladesh</span>
                                         {{-- <i class="fa-solid fa-angle-down"></i> --}}
                                     </button>
                                 </div>


                                 <div class="search-box">
                                     <div class="input-group">
                                         <input type="search" class="form-control" placeholder="I'm searching for..."
                                             id="searchInput">
                                         <button data-bs-toggle="modal" data-bs-target="#exampleModal"
                                             class="btn search-button-2" type="button" id="button-addon2">
                                             <i data-feather="search"></i>
                                         </button>
                                     </div>
                                 </div>
                             </div>


                             <div class="rightside-box">
                                 {{-- <div class="search-full">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i data-feather="search" class="font-light"></i>
                                        </span>
                                        <input type="text" class="form-control search-type"
                                            placeholder="Search here..">
                                        <span class="input-group-text close-search">
                                            <i data-feather="x" class="font-light"></i>
                                        </span>
                                    </div>
                                </div> --}}
                                 <ul class="right-side-menu">
                                     <li class="right-side">
                                         <div class="delivery-login-box">
                                             <div class="delivery-icon">
                                                 <div class="search-box" data-bs-toggle="modal"
                                                     data-bs-target="#exampleModal">
                                                     <i data-feather="search"></i>
                                                 </div>
                                             </div>
                                         </div>
                                     </li>
                                      @if (!empty($settings['site_phone']))
                                          <li class="right-side">
                                              <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['site_phone']) }}"
                                                  class="delivery-login-box">
                                                  <div class="delivery-icon">
                                                      <i data-feather="phone-call"></i>
                                                  </div>
                                                  <div class="delivery-detail">
                                                      <h6>24/7 Delivery</h6>
                                                      <h5>{{ $settings['site_phone'] }}</h5>
                                                  </div>
                                              </a>
                                          </li>
                                      @endif
                                     <li class="right-side">
                                         <a href="#" class="btn p-0 position-relative header-wishlist">
                                             <i data-feather="heart"></i>
                                         </a>
                                     </li>

                                     {{-- Cart  --}}

                                     <li class="right-side">
                                         <div class="onhover-dropdown header-badge">
                                             <button type="button" class="btn p-0 position-relative header-wishlist">
                                                 <i data-feather="shopping-cart"></i>
                                                 <span id="cart-count"
                                                     class="position-absolute top-0 start-100 translate-middle badge">{{ count($cartContent) }}
                                                     <span class="visually-hidden">unread messages</span>
                                                 </span>
                                             </button>

                                             <div class="onhover-div" id="cart-sidebar">
                                                 @include('front.layouts.card-sidebar', [
                                                     'cartContent' => $cartContent,
                                                 ])
                                             </div>
                                         </div>
                                     </li>



                                     <li class="right-side onhover-dropdown">
                                         <div class="delivery-login-box">
                                             <div class="delivery-icon">
                                                 <i data-feather="user"></i>
                                             </div>
                                             <div class="delivery-detail">
                                                 <h6>Hello,</h6>
                                                 <h5>My Account</h5>
                                             </div>
                                         </div>

                                         <div class="onhover-div onhover-div-login">
                                             <ul class="user-box-name">
                                                 <li class="product-box-contain">
                                                     <i></i>
                                                     <a href="{{ route('account.userLogin') }}">Log In</a>
                                                 </li>

                                                 <li class="product-box-contain">
                                                     <a href="{{ route('account.register') }}">Register</a>
                                                 </li>
                                                 {{-- 
                                                <li class="product-box-contain">
                                                    <a href="forgot.html">Forgot Password</a>
                                                </li> --}}
                                             </ul>
                                         </div>

                                     </li>
                                 </ul>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

         <div class="container-fluid-lg">
             <div class="row">
                 <div class="col-12">
                     <div class="header-nav">
                         <div class="header-nav-middle">
                             <div class="main-nav navbar navbar-expand-xl navbar-light navbar-sticky">
                                 <div class="offcanvas offcanvas-collapse order-xl-2" id="primaryMenu">
                                     <div class="offcanvas-header navbar-shadow">
                                         <h5>Menu</h5>
                                         <button class="btn-close lead" type="button"
                                             data-bs-dismiss="offcanvas"></button>
                                     </div>
                                     <div class="offcanvas-body">
                                         <ul class="navbar-nav">
                                            <li class="nav-item dropdown"><a class="nav-link remove-dropdown" style="font-size: 15px"
                                                             href="{{ route('product_shop.home', 'hot-products') }}">HotProducts</a></li>
                                             @if (!empty($categories))
                                                 @foreach ($categories as $category)
                                                     <li class="nav-item dropdown">
                                                         <a class="nav-link remove-dropdown" style="font-size: 15px"
                                                             href="{{ route('product_shop.home', $category->slug) }}">{{ $category->name }}</a>
                                                         <ul class="dropdown-menu">
                                                             @php
                                                                 $sub_categories = SubCategory::where(
                                                                     'category_id',
                                                                     $category->id,
                                                                 )->get();
                                                             @endphp

                                                             @if (!empty($sub_categories))
                                                                 @foreach ($sub_categories as $sub_category)
                                                                     <li>
                                                                         <a class="dropdown-item"
                                                                             href="{{ route('product_shop_sub_cat.home', $sub_category->slug) }}">{{ $sub_category->name }}</a>
                                                                     </li>
                                                                 @endforeach
                                                             @endif
                                                         </ul>
                                                     </li>
                                                 @endforeach
                                             @endif
                                         </ul>
                                     </div>
                                     <hr>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </header>
     <!-- Header End -->

     <!-- mobile fix menu start -->
     @include('front.layouts.new_mobile_menue')
     <!-- mobile fix menu end -->

     @yield('content')

     <!-- Newsletter Section End -->

     <!-- Footer Section Start -->
     <footer class="section-t-space footer-section-2 footer-color-3">
         <div class="container-fluid-lg">
             <div class="main-footer">
                 <div class="row g-md-4 gy-sm-5">
                     <div class="col-xxl-3 col-xl-4 col-sm-6">
                          <a href="{{ route('front.home') }}" class="foot-logo theme-logo">
                              <img src="{{ $settings['site_logo_url'] ?? asset('new-front-assets/images/logo/8.png') }}"
                                  class="img-fluid blur-up lazyload" alt="Evan Store">
                          </a>
                         <p class="information-text information-text-2">it is a long established fact that a reader
                             will
                             be distracted by the readable content.</p>
                          @if (!empty($socialLinks))
                              <ul class="social-icon">
                                  @foreach ($socialLinks as $social)
                                      <li class="light-bg">
                                          <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                              class="footer-link-color" title="{{ $social['label'] }}">
                                              <i class="{{ $social['icon'] }}"></i>
                                          </a>
                                      </li>
                                  @endforeach
                              </ul>
                          @endif
                     </div>

                     <div class="col-xxl-2 col-xl-4 col-sm-6">
                         <div class="footer-title">
                             <h4 class="text-white">About Evan Store</h4>
                         </div>
                         <ul class="footer-list footer-contact footer-list-light">
                             <li>
                                 <a href="{{ route('front.aboutus') }}" class="light-text">About Us</a>
                             </li>
                             <li>
                                 <a href="#" class="light-text">Contact Us</a>
                             </li>
                             {{-- <li>
                                 <a href="#" class="light-text">Terms & Conditions</a>
                             </li> --}}
                             <li>
                                 <a href="{{ route('front.return') }}" class="light-text">Return Policy</a>
                             </li>
                             {{-- <li>
                                 <a href="blog-list.html" class="light-text">Latest Blog</a>
                             </li> --}}
                         </ul>
                     </div>

                     <div class="col-xxl-2 col-xl-4 col-sm-6">
                         <div class="footer-title">
                             <h4 class="text-white">Useful Link</h4>
                         </div>
                         <ul class="footer-list footer-list-light footer-contact">
                             <li>
                                 <a href="{{ route('account.userLogin') }}" class="light-text">Your Order</a>
                             </li>
                             <li>
                                 <a href="{{ route('account.userLogin') }}" class="light-text">Your Account</a>
                             </li>
                             <li>
                                 <a href="{{ route('account.userLogin') }}" class="light-text">Track Orders</a>
                             </li>

                         </ul>
                     </div>

                     <div class="col-xxl-2 col-xl-4 col-sm-6">
                         <div class="footer-title">
                             <h4 class="text-white">FAQ</h4>
                         </div>
                         <ul class="footer-list footer-list-light footer-contact">
                             <li>
                                 <a href="" class="light-text">How to make order</a>
                             </li>
                             <li>
                                 <a href="" class="light-text">How to apply Coupon</a>
                             </li>
                             {{-- <li>
                                 <a href="" class="light-text">Brand New Bags</a>
                             </li> --}}

                         </ul>
                     </div>

                     <div class="col-xxl-3 col-xl-4 col-sm-6">
                         <div class="footer-title">
                             <h4 class="text-white">Store information</h4>
                         </div>
                          <ul class="footer-address footer-contact">
                              @if (!empty($settings['site_address']))
                                  <li>
                                      <a href="javascript:void(0)" class="light-text">
                                          <div class="inform-box flex-start-box">
                                              <i data-feather="map-pin"></i>
                                              <p>{!! nl2br(e($settings['site_address'])) !!}</p>
                                          </div>
                                      </a>
                                  </li>
                              @endif

                              @if (!empty($settings['site_phone']))
                                  <li>
                                      <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['site_phone']) }}"
                                          class="light-text">
                                          <div class="inform-box">
                                              <i data-feather="phone"></i>
                                              <p>Call us: {{ $settings['site_phone'] }}</p>
                                          </div>
                                      </a>
                                  </li>
                              @endif

                              @if (!empty($settings['site_email']))
                                  <li>
                                      <a href="mailto:{{ $settings['site_email'] }}" class="light-text">
                                          <div class="inform-box">
                                              <i data-feather="mail"></i>
                                              <p>Email Us: {{ $settings['site_email'] }}</p>
                                          </div>
                                      </a>
                                  </li>
                              @endif
                          </ul>
                     </div>
                 </div>
             </div>

             <div class="sub-footer sub-footer-lite section-b-space section-t-space">
                 <div class="left-footer">
                     <p class="light-text">2025 Copyright By Evans Store</p>
                 </div>

                 {{-- <ul class="payment-box">
                     <li>
                         <img src="../assets/images/icon/paymant/visa.png" class="blur-up lazyload" alt="">
                     </li>
                     <li>
                         <img src="../assets/images/icon/paymant/discover.png" alt=""
                             class="blur-up lazyload">
                     </li>
                     <li>
                         <img src="../assets/images/icon/paymant/american.png" alt=""
                             class="blur-up lazyload">
                     </li>
                     <li>
                         <img src="../assets/images/icon/paymant/master-card.png" alt=""
                             class="blur-up lazyload">
                     </li>
                     <li>
                         <img src="../assets/images/icon/paymant/giro-pay.png" alt=""
                             class="blur-up lazyload">
                     </li>
                 </ul> --}}
             </div>
         </div>
     </footer>
     <!-- Footer Section End -->

     {{-- Search Modal --}}
     <!-- Small modal -->
     <!-- Modal -->
     <!-- Modal -->
     <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-xl">
             <div class="modal-content">
                 <div class="modal-header">
                     <h1 class="modal-title fs-5" id="exampleModalLabel">Search Products</h1>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>

                 <!-- Body (Search Input) -->
                 <div class="modal-body">
                     <div style="position: relative; width: 100%;">
                         <input id="searchInput_modal" type="text" class="form-control"
                             placeholder="Search products..." style="padding-left: 35px;">
                         <span
                             style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #888;">🔍</span>
                     </div>
                 </div>

                 <!-- Footer (Search Results) -->
                 <div class="modal-footer" id="searchResults"
                     style="text-align:left; display:block; max-height:300px; overflow-y:auto;">
                     <p class="text-muted">Type to search products...</p>
                 </div>
             </div>
         </div>
     </div>

     <!-- Items section Start -->
     <div class="button-item">
         <button class="item-btn btn text-white">
             <i class="iconly-Bag-2 icli"></i>
         </button>
     </div>
     <div class="item-section">
         <button class="close-button">
             <i class="fas fa-times"></i>
         </button>
         <a href="{{ route('front.cart') }}" class="btn btn-sm cart-button">   <h6>
             <i class="iconly-Bag-2 icli"></i>
             <span id="cart-pop-up-count">{{ count($cartContent) }} Items</span>
         </h6></a>
      


         <div id="card-pop-up">
             @include('front.layouts.cart-pop-up', [
                 'cartContent' => $cartContent,
             ])
         </div>

     </div>
     <!-- Items section End -->






     <!-- Tap to top and theme setting button start -->
     <div class="theme-option">
         <div class="back-to-top">
             <a id="back-to-top" href="#">
                 <i class="fas fa-chevron-up"></i>
             </a>
         </div>
     </div>
     <!-- Tap to top and theme setting button end -->

     <!-- Bg overlay Start -->
     <div class="bg-overlay"></div>
     <!-- Bg overlay End -->

     <!-- latest jquery-->
     <script src=" {{ asset('new-front-assets/js/jquery-3.6.0.min.js') }}"></script>

     <!-- jquery ui-->
     <script src=" {{ asset('new-front-assets/js/jquery-ui.min.js') }}"></script>

     <!-- sidebar open js -->
     <script src="{{ asset('new-front-assets/js/filter-sidebar.js') }}"></script>

     <!-- Bootstrap js-->
     <script src=" {{ asset('new-front-assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
     <script src=" {{ asset('new-front-assets/js/bootstrap/bootstrap-notify.min.js') }}"></script>
     <script src=" {{ asset('new-front-assets/js/bootstrap/popper.min.js') }}"></script>

     <!-- feather icon js-->
     <script src=" {{ asset('new-front-assets/js/feather/feather.min.js') }}"></script>
     <script src=" {{ asset('new-front-assets/js/feather/feather-icon.js') }}"></script>

     <!-- Lazyload Js -->
     <script src=" {{ asset('new-front-assets/js/lazysizes.min.js') }}"></script>

     <!-- Slick js-->
     <script src=" {{ asset('new-front-assets/js/slick/slick.js') }}"></script>
     <script src=" {{ asset('new-front-assets/js/slick/slick-animation.min.js') }}"></script>
     <script src=" {{ asset('new-front-assets/js/slick/custom_slick.js') }}"></script>

     <!-- Price Range Js -->
     <script src="{{ asset('new-front-assets/js/ion.rangeSlider.min.js') }}"></script>

     <!-- sidebar open js -->
     <script src="{{ asset('new-front-assets/js/filter-sidebar.js') }}"></script>

     <!-- Auto Height Js -->
     <script src="{{ asset('new-front-assets/js/auto-height.js') }}"></script>

     <!-- Timer Js -->
     <script src="{{ asset('new-front-assets/js/timer1.js') }}"></script>

     <!-- Fly Cart Js -->
     <script src="{{ asset('new-front-assets/js/fly-cart.js') }}"></script>

     <!-- Quantity js -->
     <script src="{{ asset('new-front-assets/js/quantity-2.js') }}"></script>

     <!-- WOW js -->
     <script src="{{ asset('new-front-assets/js/wow.min.js') }}"></script>
     <script src="{{ asset('new-front-assets/js/custom-wow.js') }}"></script>

     <!-- script js -->
     <script src=" {{ asset('new-front-assets/js/script.js') }}"></script>

     <!-- theme setting js -->
     <script src=" {{ asset('new-front-assets/js/theme-setting.js') }}"></script>
     @php
         $all_products = \App\Models\NewProduct::get();
     @endphp


     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script>
         function addToCart(button, productId) {
             let qty = $(button)
                 .closest('.price-qty')
                 .find('.qty-input')
                 .val();

             qty = parseInt(qty);
             qty = qty > 0 ? qty : 1; // default to 1

             $.ajax({
                 url: '/add-to-cart',
                 type: 'POST',
                 data: {
                     id: productId,
                     qty: qty,
                     _token: '{{ csrf_token() }}'
                 },
                 success: function(response) {
                     if (response.status) {
                         $('#cart-sidebar').html(response.cartView);
                         $('#card-pop-up').html(response.pop_uo_cartView);
                         $('#cart-count').text(response.cartCount);
                         $('#cart-pop-up-count').text(response.cartCount);
                         $('#cart-mob-nav-count').text(response.cartCount);

                         Swal.fire({
                             icon: 'success',
                             title: response.message,
                             timer: 1500,
                             showConfirmButton: false
                         });
                     } else {
                         Swal.fire({
                             icon: 'warning',
                             title: response.message,
                             timer: 1500,
                             showConfirmButton: false
                         });
                     }
                 },
                 error: function(xhr) {
                     Swal.fire({
                         icon: 'error',
                         title: 'Something went wrong!',
                         text: xhr.responseJSON?.message || 'Error adding product to cart',
                     });
                 }
             });
         }
     </script>

     <script>
         $(document).on('click', '.close_button', function() {
             let rowId = $(this).data('rowid');

             $.ajax({
                 url: '{{ route('front.deleteItem.sidecart') }}', // route for deleteItem
                 type: 'POST',
                 data: {
                     rowId: rowId,
                     _token: '{{ csrf_token() }}'
                 },
                 success: function(response) {
                     if (response.status) {
                         // Update cart sidebar and count dynamically
                         $('#cart-sidebar').html(response.cartView);
                         $('#card-pop-up').html(response.pop_uo_cartView);
                         $('#cart-count').text(response.cartCount);
                         $('#cart-pop-up-count').text(response.cartCount);
                         $('#cart-mob-nav-count').text(response.cartCount);

                         Swal.fire({
                             icon: 'success',
                             title: response.message,
                             showConfirmButton: false,
                             timer: 1500
                         });
                     } else {
                         Swal.fire({
                             icon: 'error',
                             title: response.message,
                             showConfirmButton: false,
                             timer: 1500
                         });
                     }
                 },
                 error: function(xhr) {
                     Swal.fire({
                         icon: 'error',
                         title: 'Something went wrong!',
                         text: xhr.responseJSON?.message || 'Error removing item',
                     });
                 }
             });
         });
     </script>
     <script>
         // Wait until DOM is ready
         document.addEventListener('DOMContentLoaded', function() {
             var searchInput = document.getElementById('searchInput');
             var exampleModal = new bootstrap.Modal(document.getElementById('exampleModal'));

             // Open modal when input is clicked
             searchInput.addEventListener('click', function() {
                 exampleModal.show();
             });
         });
     </script>





     @php
         $products = DB::select("
            SELECT 
                p.id AS product_id,
                p.name AS title,
                p.slug,
                v.selling_price AS price,
                v.compare_price,
                img.image
            FROM new_products p

            /* FIRST VARIANT PER PRODUCT */
            LEFT JOIN product_variants v 
                ON v.id = (
                    SELECT pv.id
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.id ASC
                    LIMIT 1
                )

            /* FIRST IMAGE PER VARIANT */
            LEFT JOIN product_images img 
                ON img.id = (
                    SELECT pi.id
                    FROM product_images pi
                    WHERE pi.product_id = v.id
                    ORDER BY pi.sort_order ASC, pi.id ASC
                    LIMIT 1
                )

            WHERE p.status = 1
        ");

         $allProducts = collect($products)
             ->map(function ($p) {
                 return [
                     'id' => $p->product_id,
                     'title' => $p->title,
                     'slug' => $p->slug,
                     'price' => $p->price ?? 0,
                     'compare_price' => $p->compare_price,
                     'image' => $p->image
                         ? asset('uploads/products/large/' . $p->image)
                         : asset('admin-assets/img/default-150x150.png'),
                 ];
             })
             ->toArray();

     @endphp

     <script>
         const allProducts = @json($allProducts);
         const searchInput = document.getElementById("searchInput_modal");
         const searchResults = document.getElementById("searchResults");

         // Debounce function to improve performance
         function debounce(func, delay) {
             let timeout;
             return function(...args) {
                 clearTimeout(timeout);
                 timeout = setTimeout(() => func.apply(this, args), delay);
             };
         }

         function renderResults(query) {
             searchResults.innerHTML = ""; // Clear previous results

             if (!query) {
                 searchResults.innerHTML = `<p class="text-muted">Type to search products...</p>`;
                 return;
             }

             const filtered = allProducts.filter(p => p.title.toLowerCase().includes(query.toLowerCase()));

             if (filtered.length === 0) {
                 searchResults.innerHTML = `<p class="text-danger">No products found</p>`;
                 return;
             }

             const row = document.createElement('div');
             row.className = 'row g-3';

             filtered.forEach(p => {
                 const col = document.createElement('div');
                 col.className = 'col-6 col-md-4 col-lg-2';
                 const productUrl = "{{ route('Product_details.home', ':slug') }}".replace(':slug', p.slug);

                 col.innerHTML = `
                <div class="card h-100 text-center shadow-sm">
                    <a href="${productUrl}">
                        <img src="${p.image}" class="card-img-top" style="height:150px; object-fit:cover;">
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
            `;

                 row.appendChild(col);
             });

             searchResults.appendChild(row);
         }

         const debouncedRender = debounce(function() {
             renderResults(this.value);
         }, 300); // 300ms debounce

         searchInput.addEventListener('keyup', debouncedRender);
     </script>

     <script>
         $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
         });
     </script>

     @yield('customJs')
 </body>

 </html>
