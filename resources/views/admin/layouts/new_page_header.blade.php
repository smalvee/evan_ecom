<div class="page-header">
    <div class="header-wrapper">
        <div class="d-flex align-items-center gap-3">
            <div class="toggle-sidebar" role="button" aria-label="Toggle sidebar">
                <i class="ri-menu-line status_toggle middle sidebar-toggle"></i>
            </div>
        </div>

        <div class="nav-right pull-right right-header">
            <ul class="nav-menus">
                <li class="profile-nav onhover-dropdown pe-0 me-0">
                    <div class="media profile-media">
                        <img class="user-profile rounded-circle"
                            src="{{ asset('new-admin-assets/images/users/4.jpg') }}" alt="Admin">
                        <div class="user-name-hide media-body">
                            <span>Admin</span>
                            <p class="mb-0">Administrator<i class="middle ri-arrow-down-s-line"></i></p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li>
                            <a data-bs-toggle="modal" data-bs-target="#staticBackdrop" href="javascript:void(0)">
                                <i data-feather="log-out"></i>
                                <span>Log out</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
