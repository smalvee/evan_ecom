<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset('admin-assets/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SHOP</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Category -->
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-folder"></i>
                        <p>Category</p>
                    </a>
                </li>

                <!-- Sub Category -->
                <li class="nav-item">
                    <a href="{{ route('sub-categories.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>Sub Category</p>
                    </a>
                </li>

                <!-- Brands -->
                <li class="nav-item">
                    <a href="{{ route('brands.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Brands</p>
                    </a>
                </li>

                <!-- Banner -->
                <li class="nav-item">
                    <a href="{{ route('banner.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Banner</p>
                    </a>
                </li>

                <!-- Products -->
                <li class="nav-item">
                    <a href="{{ route('products.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Products</p>
                    </a>
                </li>

                <!-- Shipping -->
                <li class="nav-item">
                    <a href="{{ route('shipping.create') }}" class="nav-link">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Shipping</p>
                    </a>
                </li>

                <!-- Orders -->
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-shopping-bag"></i>
                        <p>Orders</p>
                    </a>
                </li>

                <!-- Discount -->
                <li class="nav-item">
                    <a href="{{ route('coupon.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-percent"></i>
                        <p>Discount</p>
                    </a>
                </li>

                <!-- Users -->
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>

                <!-- Pages Sub-menu -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Pages
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.display.aboutus') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>About Us</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.display.refund') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Refund Policy</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.display.return') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Return Policy</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Report Section -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('sales.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sales Report</p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('admin.display.refund') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Refund Policy</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.display.return') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Return Policy</p>
                            </a>
                        </li> --}}
                    </ul>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<!-- Custom Script to toggle Pages Sub-menu -->
