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
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.dashboard') }}">
                            <i class="ri-dashboard-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Catalog --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Catalog</span>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('categories.index') }}">
                            <i class="ri-price-tag-3-line"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('sub-categories.index') }}">
                            <i class="ri-list-settings-line"></i>
                            <span>Sub Categories</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('brands.index') }}">
                            <i class="ri-medal-line"></i>
                            <span>Brands</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-store-3-line"></i>
                            <span>Products</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('products.index') }}">List Products</a>
                            </li>
                            <li>
                                <a href="{{ route('products.create') }}">Add Product</a>
                            </li>
                            <li>
                                <a href="{{ route('get_product_list.index') }}">Product Images</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('units.index') }}">
                            <i class="ri-scales-3-line"></i>
                            <span>Units</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('variation.index') }}">
                            <i class="ri-shuffle-line"></i>
                            <span>Variations</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.pricing.index') }}">
                            <i class="ri-price-tag-3-line"></i>
                            <span>Pricing</span>
                        </a>
                    </li>

                    {{-- Sales --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Sales</span>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-shopping-bag-3-line"></i>
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
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.pre_orders.index') }}">
                            <i class="ri-calendar-check-line"></i>
                            <span>Pre Orders</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-coupon-3-line"></i>
                            <span>Coupons</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('coupon.index') }}">Coupon List</a>
                            </li>
                            <li>
                                <a href="{{ route('coupon.create') }}">Create Coupon</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('shipping.create') }}">
                            <i class="ri-truck-line"></i>
                            <span>Shipping</span>
                        </a>
                    </li>

                    {{-- Purchasing --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Purchasing</span>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('supply.index') }}">
                            <i class="ri-contacts-line"></i>
                            <span>Suppliers</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-shopping-cart-2-line"></i>
                            <span>Purchase</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('purchase.index') }}">List Purchase</a>
                            </li>
                            <li>
                                <a href="{{ route('purchase.create') }}">Add Purchase</a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-arrow-go-back-line"></i>
                            <span>Purchase Return</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('return.index') }}">New Return</a>
                            </li>
                            <li>
                                <a href="{{ route('purchase.return_list') }}">Return List</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Marketing --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Marketing</span>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('banner.index') }}">
                            <i class="ri-image-line"></i>
                            <span>Banners</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('advertise.index') }}">
                            <i class="ri-advertisement-line"></i>
                            <span>Advertisement</span>
                        </a>
                    </li>

                    {{-- Customers --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Customers</span>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('users.index') }}">
                            <i class="ri-user-3-line"></i>
                            <span>Users</span>
                        </a>
                    </li>

                    {{-- Content --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Content</span>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-file-text-line"></i>
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

                    {{-- Reports --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">Reports</span>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.reports.index') }}">
                            <i class="ri-dashboard-3-line"></i>
                            <span>Reports Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <i class="ri-file-chart-line"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('sales.index') }}">Sales Report</a>
                            </li>
                            <li>
                                <a href="{{ route('item_sales.index') }}">Product Sales</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.product_performance') }}">Product Performance</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.profit_loss') }}">Profit &amp; Loss</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.orders') }}">Order Report</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.customers') }}">Customer Report</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.payments') }}">Payment Report</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.inventory') }}">Inventory Report</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.purchases') }}">Purchase Report</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.purchase_returns') }}">Purchase Return</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.coupons') }}">Coupon Report</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.shipping') }}">Shipping Report</a>
                            </li>
                        </ul>
                    </li>

                    {{-- System --}}
                    <li class="sidebar-list sidebar-group">
                        <span class="sidebar-group-label">System</span>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.settings') }}">
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
