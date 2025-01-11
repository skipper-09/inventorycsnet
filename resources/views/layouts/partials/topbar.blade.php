<header id="page-topbar">
    <div class="navbar-header">

        <!-- Logo -->

        <!-- Start Navbar-Brand -->
        <div class="navbar-logo-box">
            <a href="{{ route('dashboard') }}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ asset('/storage/' . Setting('logo')) }}" alt="logo-sm-dark" height="20">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('/storage/' . Setting('logo')) }}" alt="logo-dark" height="18">
                </span>
            </a>

            <a href="{{ route('dashboard') }}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ asset('/storage/' . Setting('logo')) }}" alt="logo-sm-light" height="20">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('/storage/' . Setting('logo')) }}" alt="logo-light" height="18">
                </span>
            </a>

            <button type="button" class="btn btn-sm top-icon sidebar-btn" id="sidebar-btn">
                <i class="mdi mdi-menu-open align-middle fs-19"></i>
            </button>
        </div>
        <!-- End navbar brand -->

        <!-- Start menu -->
        <div class="d-flex justify-content-between menu-sm px-3 ms-auto">
            <div class="d-flex align-items-center gap-2">
               
            </div>

            <div class="d-flex align-items-center gap-2">
                <!--Start App Search-->
                <form class="app-search d-none d-lg-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search...">
                        <span class="fab fa-sistrix fs-17 align-middle"></span>
                    </div>
                </form>
                <!--End App Search-->

                <!-- Start Notification -->
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm top-icon" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell align-middle"></i>
                        <span class="btn-marker"><i class="marker marker-dot text-danger"></i><span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-md dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3 bg-info">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-white m-0"><i class="far fa-bell me-2"></i> Notifications </h6>
                                </div>
                                <div class="col-auto">
                                    <a href="#!" class="badge bg-info-subtle text-info"> 8+</a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 230px;">
                            <a href="" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar avatar-xs avatar-label-primary me-3">
                                        <span class="rounded fs-16">
                                            <i class="mdi mdi-file-document-outline"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-1">New report has been recived</h6>
                                        <div class="fs-12 text-muted">
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 3 min ago</p>
                                        </div>
                                    </div>
                                    <i class="mdi mdi-chevron-right align-middle ms-2"></i>
                                </div>
                            </a>
                            <a href="" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar avatar-xs avatar-label-success me-3">
                                        <span class="rounded fs-16">
                                            <i class="mdi mdi-cart-variant"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-1">Last order was completed</h6>
                                        <div class="fs-12 text-muted">
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 1 hour ago</p>
                                        </div>
                                    </div>
                                    <i class="mdi mdi-chevron-right align-middle ms-2"></i>
                                </div>
                            </a>
                            <a href="" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar avatar-xs avatar-label-danger me-3">
                                        <span class="rounded fs-16">
                                            <i class="mdi mdi-account-group"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-1">Completed meeting canceled</h6>
                                        <div class="fs-12 text-muted">
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 5 hour ago</p>
                                        </div>
                                    </div>
                                    <i class="mdi mdi-chevron-right align-middle ms-2"></i>
                                </div>
                            </a>
                            <a href="" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar avatar-xs avatar-label-warning me-3">
                                        <span class="rounded fs-16">
                                            <i class="mdi mdi-send-outline"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-1">New feedback received</h6>
                                        <div class="fs-12 text-muted">
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 6 hour ago</p>
                                        </div>
                                    </div>
                                    <i class="mdi mdi-chevron-right align-middle ms-2"></i>
                                </div>
                            </a>
                            <a href="" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar avatar-xs avatar-label-secondary me-3">
                                        <span class="rounded fs-16">
                                            <i class="mdi mdi-download-box"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-1">New update was available</h6>
                                        <div class="fs-12 text-muted">
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 1 day ago</p>
                                        </div>
                                    </div>
                                    <i class="mdi mdi-chevron-right align-middle ms-2"></i>
                                </div>
                            </a>
                            <a href="" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar avatar-xs avatar-label-info me-3">
                                        <span class="rounded fs-16">
                                            <i class="mdi mdi-hexagram-outline"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-1">Your password was changed</h6>
                                        <div class="fs-12 text-muted">
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 2 day ago</p>
                                        </div>
                                    </div>
                                    <i class="mdi mdi-chevron-right align-middle ms-2"></i>
                                </div>
                            </a>
                        </div>
                        <div class="p-2 border-top">
                            <div class="d-grid">
                                <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                    <i class="mdi mdi-arrow-right-circle me-1"></i> View More..
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Notification -->
                <!-- Start Profile -->
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm top-icon p-0" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if (Auth::user()->picture == null)
                        <img class="rounded avatar-2xs p-0" src="{{ asset('assets/images/users/avatar-1.png') }}" alt="Header Avatar">
                        @else
                        <img class="rounded avatar-2xs p-0" src="{{ asset('storage/images/user/' . Auth::user()->picture) }}" alt="Header Avatar">
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-wide dropdown-menu-end dropdown-menu-animated overflow-hidden py-0">
                        <div class="card border-0">
                            <div class="card-header bg-primary rounded-0">
                                <div class="rich-list-item w-100 p-0">
                                    <div class="rich-list-prepend">
                                        <div class="avatar avatar-label-light avatar-circle">
                                            <div class="avatar-display"><i class="fa fa-user-alt"></i></div>
                                        </div>
                                    </div>
                                    <div class="rich-list-content">
                                        <h3 class="rich-list-title text-white">{{ Auth::user()->name }}</h3>
                                        <span class="rich-list-subtitle text-white">{{ Auth::user()->email }}</span>
                                    </div>
                                    {{-- <div class="rich-list-append"><span class="badge badge-label-light fs-6">6+</span></div> --}}
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="grid-nav grid-nav-flush grid-nav-action grid-nav-no-rounded">
                                    <div class="grid-nav-row">
                                        <a href="{{ route('setting.profile', ['id'=>Auth()->user()->id]) }}" class="grid-nav-item">
                                            <div class="grid-nav-icon"><i class="far fa-address-card"></i></div>
                                            <span class="grid-nav-content">Profile</span>
                                        </a>
                                        <a href="#!" class="grid-nav-item">
                                            <div class="grid-nav-icon"><i class="far fa-comments"></i></div>
                                            <span class="grid-nav-content">Messages</span>
                                        </a>
                                        <a href="#!" class="grid-nav-item">
                                            <div class="grid-nav-icon"><i class="far fa-clone"></i></div>
                                            <span class="grid-nav-content">Activities</span>
                                        </a>
                                    </div>
                                    <div class="grid-nav-row">
                                        <a href="#!" class="grid-nav-item">
                                            <div class="grid-nav-icon"><i class="far fa-calendar-check"></i></div>
                                            <span class="grid-nav-content">Tasks</span>
                                        </a>
                                        <a href="#!" class="grid-nav-item">
                                            <div class="grid-nav-icon"><i class="far fa-sticky-note"></i></div>
                                            <span class="grid-nav-content">Notes</span>
                                        </a>
                                        <a href="#!" class="grid-nav-item">
                                            <div class="grid-nav-icon"><i class="far fa-bell"></i></div>
                                            <span class="grid-nav-content">Notification</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer card-footer-bordered rounded-0"><a href="{{ route('auth.signout') }}" class="btn btn-label-danger">Sign out</a></div>
                        </div>
                    </div>
                </div>
                <!-- End Profile -->
            </div>
        </div>
        <!-- End menu -->
    </div>
</header>