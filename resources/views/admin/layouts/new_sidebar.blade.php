@php
    // Route-based active detection. Using Laravel's route names keeps the
    // sidebar correct on refresh, Back/Forward, direct URL access and new tabs.
    $routeIs = fn (...$patterns) => request()->routeIs(...$patterns);

    $active = [
        'dashboard' => $routeIs('admin.dashboard'),

        'categories' => $routeIs('categories.*'),
        'sub_categories' => $routeIs('sub-categories.*'),
        'brands' => $routeIs('brands.*'),

        'products_parent' => $routeIs('products.*', 'new_products.*', 'update_product.*', 'new_product.*', 'get_product_list.*', 'image_edit.*', 'image.store', 'galery_image.*'),
        'products_list' => $routeIs('products.index', 'new_products.edit', 'update_product.*', 'new_product.destroy'),
        'products_create' => $routeIs('products.create', 'new_product.store'),
        'products_images' => $routeIs('get_product_list.*', 'image_edit.*', 'image.store', 'galery_image.*'),

        'units' => $routeIs('units.*'),
        'variations' => $routeIs('variation.*', 'variations.*'),
        'pricing' => $routeIs('admin.pricing.*'),

        'orders_parent' => $routeIs('orders.*'),
        'orders_list' => $routeIs('orders.index', 'orders.details', 'orders.delete', 'orders.changeStatus', 'orders.address_update', 'orders.order_update', 'orders.update_full', 'orders.paymentStatus'),
        'orders_create' => $routeIs('orders.create', 'orders.store'),

        'pre_orders' => $routeIs('admin.pre_orders.*'),

        'coupons_parent' => $routeIs('coupon.*'),
        'coupons_list' => $routeIs('coupon.index', 'coupon.edit', 'coupon.update', 'coupon.delete'),
        'coupons_create' => $routeIs('coupon.create', 'coupon.store'),

        'shipping' => $routeIs('shipping.*'),
        'suppliers' => $routeIs('supply.*'),

        'purchase_parent' => $routeIs('purchase.index', 'purchase.create', 'purchase.store', 'purchase.edit', 'purchase.update', 'purchase.search'),
        'purchase_list' => $routeIs('purchase.index', 'purchase.edit', 'purchase.update', 'purchase.search'),
        'purchase_create' => $routeIs('purchase.create', 'purchase.store'),

        'purchase_return_parent' => $routeIs('return.index', 'purchase.return_store', 'purchase.return_list', 'purchase.return_view'),
        'purchase_return_new' => $routeIs('return.index', 'purchase.return_store'),
        'purchase_return_list' => $routeIs('purchase.return_list', 'purchase.return_view'),

        'banners' => $routeIs('banner.*', 'banners.*'),
        'advertise' => $routeIs('advertise.*', 'advertisements.*'),
        'users' => $routeIs('users.*'),

        'pages_parent' => $routeIs('admin.display.*', 'admin.store_*'),
        'pages_about' => $routeIs('admin.display.aboutus', 'admin.store_who_we_are', 'admin.store_our_mission', 'admin.store_our_vission'),
        'pages_return' => $routeIs('admin.display.return', 'admin.store_return'),
        'pages_refund' => $routeIs('admin.display.refund', 'admin.store_refund'),

        'reports_dashboard' => $routeIs('admin.reports.index'),
        'reports_parent' => $routeIs('sales.*', 'item_sales.*', 'admin.reports.product_performance', 'admin.reports.profit_loss', 'admin.reports.orders', 'admin.reports.customers', 'admin.reports.payments', 'admin.reports.inventory', 'admin.reports.purchases', 'admin.reports.purchase_returns', 'admin.reports.coupons', 'admin.reports.shipping'),
        'reports_sales' => $routeIs('sales.*'),
        'reports_item_sales' => $routeIs('item_sales.*'),
        'reports_product_performance' => $routeIs('admin.reports.product_performance'),
        'reports_profit_loss' => $routeIs('admin.reports.profit_loss'),
        'reports_orders' => $routeIs('admin.reports.orders'),
        'reports_customers' => $routeIs('admin.reports.customers'),
        'reports_payments' => $routeIs('admin.reports.payments'),
        'reports_inventory' => $routeIs('admin.reports.inventory'),
        'reports_purchases' => $routeIs('admin.reports.purchases'),
        'reports_purchase_returns' => $routeIs('admin.reports.purchase_returns'),
        'reports_coupons' => $routeIs('admin.reports.coupons'),
        'reports_shipping' => $routeIs('admin.reports.shipping'),

        'settings' => $routeIs('admin.settings*'),
    ];
@endphp

<div class="sidebar-wrapper">
    <div id="sidebarEffect"></div>
    <div>
        <div class="logo-wrapper logo-wrapper-center">
            <a href="{{ route('admin.dashboard') }}">
                <span class="brand-mark">E</span>
                <span class="brand-name">Evan Store</span>
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
                <span class="brand-mark">E</span>
            </a>
        </div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow">
                <i data-feather="arrow-left"></i>
            </div>

            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"></li>

                    {{-- Main --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Main</span>
                    </li>
                    <li class="sidebar-list {{ $active['dashboard'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['dashboard'] ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <i class="ri-dashboard-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Catalog --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Catalog</span>
                    </li>
                    <li class="sidebar-list {{ $active['categories'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['categories'] ? 'active' : '' }}"
                            href="{{ route('categories.index') }}">
                            <i class="ri-price-tag-3-line"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="sidebar-list {{ $active['sub_categories'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['sub_categories'] ? 'active' : '' }}"
                            href="{{ route('sub-categories.index') }}">
                            <i class="ri-list-settings-line"></i>
                            <span>Sub Categories</span>
                        </a>
                    </li>
                    <li class="sidebar-list {{ $active['brands'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['brands'] ? 'active' : '' }}"
                            href="{{ route('brands.index') }}">
                            <i class="ri-medal-line"></i>
                            <span>Brands</span>
                        </a>
                    </li>
                    <li class="sidebar-list {{ $active['products_parent'] ? 'open active' : '' }}">
                        <a class="sidebar-link sidebar-title has-submenu {{ $active['products_parent'] ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Products</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a class="{{ $active['products_list'] ? 'active' : '' }}"
                                    href="{{ route('products.index') }}">List Products</a>
                            </li>
                            <li>
                                <a class="{{ $active['products_create'] ? 'active' : '' }}"
                                    href="{{ route('products.create') }}">Add Product</a>
                            </li>
                            <li>
                                <a class="{{ $active['products_images'] ? 'active' : '' }}"
                                    href="{{ route('get_product_list.index') }}">Product Images</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list {{ $active['units'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['units'] ? 'active' : '' }}"
                            href="{{ route('units.index') }}">
                            <i class="ri-scales-3-line"></i>
                            <span>Units</span>
                        </a>
                    </li>
                    <li class="sidebar-list {{ $active['variations'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['variations'] ? 'active' : '' }}"
                            href="{{ route('variation.index') }}">
                            <i class="ri-shuffle-line"></i>
                            <span>Variations</span>
                        </a>
                    </li>
                    <li class="sidebar-list {{ $active['pricing'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['pricing'] ? 'active' : '' }}"
                            href="{{ route('admin.pricing.index') }}">
                            <i class="ri-price-tag-3-line"></i>
                            <span>Pricing</span>
                        </a>
                    </li>

                    {{-- Sales --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Sales</span>
                    </li>
                    <li class="sidebar-list {{ $active['orders_parent'] ? 'open active' : '' }}">
                        <a class="sidebar-link sidebar-title has-submenu {{ $active['orders_parent'] ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <i class="ri-shopping-bag-3-line"></i>
                            <span>Orders</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a class="{{ $active['orders_list'] ? 'active' : '' }}"
                                    href="{{ route('orders.index') }}">Order List</a>
                            </li>
                            <li>
                                <a class="{{ $active['orders_create'] ? 'active' : '' }}"
                                    href="{{ route('orders.create') }}">Create Order</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list {{ $active['pre_orders'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['pre_orders'] ? 'active' : '' }}"
                            href="{{ route('admin.pre_orders.index') }}">
                            <i class="ri-calendar-check-line"></i>
                            <span>Pre Orders</span>
                        </a>
                    </li>
                    <li class="sidebar-list {{ $active['coupons_parent'] ? 'open active' : '' }}">
                        <a class="sidebar-link sidebar-title has-submenu {{ $active['coupons_parent'] ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <i class="ri-coupon-3-line"></i>
                            <span>Coupons</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a class="{{ $active['coupons_list'] ? 'active' : '' }}"
                                    href="{{ route('coupon.index') }}">Coupon List</a>
                            </li>
                            <li>
                                <a class="{{ $active['coupons_create'] ? 'active' : '' }}"
                                    href="{{ route('coupon.create') }}">Create Coupon</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list {{ $active['shipping'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['shipping'] ? 'active' : '' }}"
                            href="{{ route('shipping.create') }}">
                            <i class="ri-truck-line"></i>
                            <span>Shipping</span>
                        </a>
                    </li>

                    {{-- Purchasing --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Purchasing</span>
                    </li>
                    <li class="sidebar-list {{ $active['suppliers'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['suppliers'] ? 'active' : '' }}"
                            href="{{ route('supply.index') }}">
                            <i class="ri-contacts-line"></i>
                            <span>Suppliers</span>
                        </a>
                    </li>
                    <li class="sidebar-list {{ $active['purchase_parent'] ? 'open active' : '' }}">
                        <a class="sidebar-link sidebar-title has-submenu {{ $active['purchase_parent'] ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <i class="ri-shopping-cart-2-line"></i>
                            <span>Purchase</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a class="{{ $active['purchase_list'] ? 'active' : '' }}"
                                    href="{{ route('purchase.index') }}">List Purchase</a>
                            </li>
                            <li>
                                <a class="{{ $active['purchase_create'] ? 'active' : '' }}"
                                    href="{{ route('purchase.create') }}">Add Purchase</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list {{ $active['purchase_return_parent'] ? 'open active' : '' }}">
                        <a class="sidebar-link sidebar-title has-submenu {{ $active['purchase_return_parent'] ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <i class="ri-arrow-go-back-line"></i>
                            <span>Purchase Return</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a class="{{ $active['purchase_return_new'] ? 'active' : '' }}"
                                    href="{{ route('return.index') }}">New Return</a>
                            </li>
                            <li>
                                <a class="{{ $active['purchase_return_list'] ? 'active' : '' }}"
                                    href="{{ route('purchase.return_list') }}">Return List</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Marketing --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Marketing</span>
                    </li>
                    <li class="sidebar-list {{ $active['banners'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['banners'] ? 'active' : '' }}"
                            href="{{ route('banner.index') }}">
                            <i class="ri-image-line"></i>
                            <span>Banners</span>
                        </a>
                    </li>
                    <li class="sidebar-list {{ $active['advertise'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['advertise'] ? 'active' : '' }}"
                            href="{{ route('advertise.index') }}">
                            <i class="ri-advertisement-line"></i>
                            <span>Advertisement</span>
                        </a>
                    </li>

                    {{-- Customers --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Customers</span>
                    </li>
                    <li class="sidebar-list {{ $active['users'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['users'] ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                            <i class="ri-user-3-line"></i>
                            <span>Users</span>
                        </a>
                    </li>

                    {{-- Content --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Content</span>
                    </li>
                    <li class="sidebar-list {{ $active['pages_parent'] ? 'open active' : '' }}">
                        <a class="sidebar-link sidebar-title has-submenu {{ $active['pages_parent'] ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <i class="ri-file-text-line"></i>
                            <span>Pages</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a class="{{ $active['pages_about'] ? 'active' : '' }}"
                                    href="{{ route('admin.display.aboutus') }}">About Us</a>
                            </li>
                            <li>
                                <a class="{{ $active['pages_return'] ? 'active' : '' }}"
                                    href="{{ route('admin.display.return') }}">Return Policy</a>
                            </li>
                            <li>
                                <a class="{{ $active['pages_refund'] ? 'active' : '' }}"
                                    href="{{ route('admin.display.refund') }}">Refund Policy</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Reports --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Reports</span>
                    </li>
                    <li class="sidebar-list {{ $active['reports_dashboard'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['reports_dashboard'] ? 'active' : '' }}"
                            href="{{ route('admin.reports.index') }}">
                            <i class="ri-dashboard-3-line"></i>
                            <span>Reports Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-list {{ $active['reports_parent'] ? 'open active' : '' }}">
                        <a class="sidebar-link sidebar-title has-submenu {{ $active['reports_parent'] ? 'active' : '' }}"
                            href="javascript:void(0)">
                            <i class="ri-file-chart-line"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a class="{{ $active['reports_sales'] ? 'active' : '' }}"
                                    href="{{ route('sales.index') }}">Sales Report</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_item_sales'] ? 'active' : '' }}"
                                    href="{{ route('item_sales.index') }}">Product Sales</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_product_performance'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.product_performance') }}">Product Performance</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_profit_loss'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.profit_loss') }}">Profit &amp; Loss</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_orders'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.orders') }}">Order Report</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_customers'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.customers') }}">Customer Report</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_payments'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.payments') }}">Payment Report</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_inventory'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.inventory') }}">Inventory Report</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_purchases'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.purchases') }}">Purchase Report</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_purchase_returns'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.purchase_returns') }}">Purchase Return</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_coupons'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.coupons') }}">Coupon Report</a>
                            </li>
                            <li>
                                <a class="{{ $active['reports_shipping'] ? 'active' : '' }}"
                                    href="{{ route('admin.reports.shipping') }}">Shipping Report</a>
                            </li>
                        </ul>
                    </li>

                    {{-- System --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">System</span>
                    </li>
                    <li class="sidebar-list {{ $active['settings'] ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav {{ $active['settings'] ? 'active' : '' }}"
                            href="{{ route('admin.settings') }}">
                            <i class="ri-settings-3-line"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="right-arrow" id="right-arrow">
                <i data-feather="arrow-right"></i>
            </div>
        </nav>
    </div>
</div>

<script>
    // Capture the server-rendered active link BEFORE the theme's sidebar-menu.js
    // runs (it strips `.active` and its own path matching compares against the
    // absolute href, which fails). syncActiveSidebar() re-applies this after.
    window.__sidebarActiveHref = (function() {
        var child = document.querySelector('.sidebar-wrapper .sidebar-submenu a.active');
        var top = document.querySelector('.sidebar-wrapper a.sidebar-link.active:not(.has-submenu)');
        var el = child || top;
        return el ? el.getAttribute('href') : '';
    })();
</script>
