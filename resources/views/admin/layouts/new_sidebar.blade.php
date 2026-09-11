<div class="sidebar-wrapper">
    <div id="sidebarEffect"></div>
    <div>
        <div class="logo-wrapper logo-wrapper-center">
            <a href="{{ route('admin.dashboard') }}" data-bs-original-title="" title="">
                {{-- <img class="img-fluid for-white" src="{{ asset('new-admin-assets/images/logo/logo.png') }} "
                    alt="logo"> --}}
                <h2 style="color: white" class="img-fluid for-white">Evan Store</h2>
            </a>
            <div class="back-btn">
                <i class="fa fa-angle-left"></i>
            </div>
            <div class="toggle-sidebar">
                <i class="ri-apps-line status_toggle middle sidebar-toggle"></i>
            </div>
        </div>
        <div class="logo-icon-wrapper">
            <a href="{{ route('admin.dashboard') }}">
                {{-- <img class="img-fluid main-logo main-white" src="{{ asset('new-admin-assets/images/logo/logo.png') }}  "
                    alt="logo">
                <img class="img-fluid main-logo main-dark"
                    src="{{ asset('new-admin-assets/images/logo/logo-white.png') }}  " alt="logo"> --}}
                <h2 style="color: white" class="img-fluid for-white">Evan Store</h2>
            </a>
        </div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow">
                <i data-feather="arrow-left"></i>
            </div>

            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"></li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.dashboard') }}">
                            <i class="ri-home-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('supply.index') }}">
                            <i class="ri-home-line"></i>
                            <span>Suppliers</span>
                        </a>
                    </li> --}}


                    <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Contacts</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('supply.index') }}">Suppliers</a>
                            </li>

                            {{-- <li>
                                <a href="add-new-product.html">Customer</a>
                            </li> --}}
                        </ul>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('users.index') }}">
                            <i class="ri-user-3-line"></i>
                            <span>Users</span>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Product</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('products.index') }}">List Products</a>
                            </li>
                            <li>
                                <a href="{{ route('get_product_list.index') }}">Product Image</a>
                            </li>

                            <li>
                                <a href="{{ route('products.create') }}">Add Product</a>
                            </li>

                            <li>
                                <a href="{{ route('variation.index') }}">Variations</a>
                            </li>

                            <li>
                                <a href="{{ route('units.index') }}">Units</a>
                            </li>

                            <li>
                                <a href="{{ route('categories.index') }}">Categories</a>
                            </li>

                            <li>
                                <a href="{{ route('sub-categories.index') }}">Sub Categories</a>
                            </li>

                            <li>
                                <a href="{{ route('brands.index') }}">Brands</a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Purchase</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('purchase.index') }}">List Purchase</a>
                            </li>

                            <li>
                                <a href="{{ route('purchase.create') }}">Add Purchase</a>
                            </li>
                             <li>
                                <a href="{{ route('return.index') }}">Purchase Return</a>
                            </li>
                            <li>
                                <a href="{{ route('purchase.return_list') }}">Return List</a>
                            </li>
                           
                        </ul>
                    </li>

                    <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Pages</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('admin.display.aboutus') }}">About Us</a>
                            </li>

                            <li>
                                <a href="{{ route('admin.display.return') }}">Return Policy</a>
                            </li>

                            <li>
                                <a href="{{ route('admin.display.refund') }}">Refund Policy</a>
                            </li>
                        </ul>

                    </li>

                      <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Orders</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('orders.index') }}">Order List</a>
                            </li>

                            <li>
                                <a href="{{ route('orders.create') }}">Create Order</a>
                            </li>
                        </ul>

                    </li>
                    <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Coupons</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('coupon.index') }}">Coupon List</a>
                            </li>
                            <li>
                                <a href="{{ route('coupon.create') }}">Create Coupon</a>
                            </li>

                            {{-- <li>
                                <a href="add-new-product.html">Customer</a>
                            </li> --}}
                        </ul>
                    </li>
                   

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('shipping.create') }}">
                            <i class="ri-truck-line"></i>
                            <span>Shipping</span>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('banner.index') }}">
                            <i class="ri-image-line"></i>
                            <span>Banner</span>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('advertise.index') }}">
                            <i class="ri-home-line"></i>
                            <span>Advertise</span>
                        </a>
                    </li>

                   

                    <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Report</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('sales.index') }}">Sales Report</a>
                            </li>
                            <li>
                                <a href="{{ route('item_sales.index') }}">Item Wise Sales Report</a>
                            </li>

                            {{-- <li>
                                <a href="add-new-product.html">Customer</a>
                            </li> --}}
                        </ul>
                    </li>

                    {{-- <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Purchase</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="products.html">List Purchase</a>
                            </li>

                            <li>
                                <a href="add-new-product.html">Add Purchase</a>
                            </li>
                        </ul>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-list-check-2"></i>
                            <span>Category</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="category.html">Category List</a>
                            </li>

                            <li>
                                <a href="add-new-category.html">Add New Category</a>
                            </li>
                        </ul>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-list-settings-line"></i>
                            <span>Attributes</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="attributes.html">Attributes</a>
                            </li>

                            <li>
                                <a href="add-new-attributes.html">Add Attributes</a>
                            </li>
                        </ul>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-user-3-line"></i>
                            <span>Users</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="all-users.html">All users</a>
                            </li>
                            <li>
                                <a href="add-new-user.html">Add new user</a>
                            </li>
                        </ul>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-user-3-line"></i>
                            <span>Roles</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="role.html">All roles</a>
                            </li>
                            <li>
                                <a href="create-role.html">Create Role</a>
                            </li>
                        </ul>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="media.html">
                            <i class="ri-price-tag-3-line"></i>
                            <span>Media</span>
                        </a>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-archive-line"></i>
                            <span>Orders</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="order-list.html">Order List</a>
                            </li>
                            <li>
                                <a href="order-detail.html">Order Detail</a>
                            </li>
                            <li>
                                <a href="order-tracking.html">Order Tracking</a>
                            </li>
                        </ul>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-focus-3-line"></i>
                            <span>Localization</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="translation.html">Translation</a>
                            </li>

                            <li>
                                <a href="currency-rates.html">Currency Rates</a>
                            </li>
                        </ul>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-price-tag-3-line"></i>
                            <span>Coupons</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="coupon-list.html">Coupon List</a>
                            </li>

                            <li>
                                <a href="create-coupon.html">Create Coupon</a>
                            </li>
                        </ul>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="taxes.html">
                            <i class="ri-price-tag-3-line"></i>
                            <span>Tax</span>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="product-review.html">
                            <i class="ri-star-line"></i>
                            <span>Product Review</span>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="support-ticket.html">
                            <i class="ri-phone-line"></i>
                            <span>Support Ticket</span>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <a class="linear-icon-link sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-settings-line"></i>
                            <span>Settings</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="profile-setting.html">Profile Setting</a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="reports.html">
                            <i class="ri-file-chart-line"></i>
                            <span>Reports</span>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="list-page.html">
                            <i class="ri-list-check"></i>
                            <span>List Page</span>
                        </a>
                    </li> --}}
                </ul>
            </div>

            <div class="right-arrow" id="right-arrow">
                <i data-feather="arrow-right"></i>
            </div>
        </nav>
    </div>
</div>
