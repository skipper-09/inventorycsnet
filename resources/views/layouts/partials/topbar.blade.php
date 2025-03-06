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
                {{-- <form class="app-search d-none d-lg-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search...">
                        <span class="fab fa-sistrix fs-17 align-middle"></span>
                    </div>
                </form> --}}
                <!--End App Search-->

                <!-- Start Notification -->
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm top-icon" id="page-header-notifications-dropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell align-middle"></i>
                        <span class="btn-marker"><i class="marker marker-dot text-danger"></i><span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-md dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3 bg-info">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-white m-0"><i class="far fa-bell me-2"></i> Notifications </h6>
                                </div>
                                <div class="col-auto">
                                    <a href="#!"
                                        class="badge bg-info-subtle text-info">{{ count($unreadNotifications) }}</a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 230px;">
                            @foreach ($unreadNotifications as $notification)
                                <a href="" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="avatar avatar-xs avatar-label-primary me-3">
                                            <span class="rounded fs-16">
                                                <i class="mdi mdi-file-document-outline"></i>
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <h6 class="mb-1">{{ $notification->data['data'] }}</h6>
                                            <div class="fs-12 text-muted">
                                                <p class="mb-0"><i
                                                        class="mdi mdi-clock-outline"></i>{{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <i class="mdi mdi-chevron-right align-middle ms-2"></i>
                                    </div>
                                </a>
                            @endforeach

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
                    <button type="button" class="btn btn-sm top-icon p-0" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if (Auth::user()->picture == null)
                            <img class="rounded avatar-2xs p-0" src="{{ asset('assets/images/users/avatar-1.png') }}"
                                alt="Header Avatar">
                        @else
                            <img class="rounded avatar-2xs p-0"
                                src="{{ asset('storage/images/user/' . Auth::user()->picture) }}" alt="Header Avatar">
                        @endif
                    </button>
                    <div
                        class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-animated overflow-hidden py-0">
                        <div class="card border-0">
                            <div class="card-header bg-primary rounded-0">
                                <div class="rich-list-item w-50 p-0">
                                    <div class="rich-list-prepend">
                                        <div class="avatar avatar-label-light avatar-circle">
                                            <div class="avatar-display"><i class="fa fa-user-alt"></i></div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <h3 class="rich-list-title text-white">{{ Auth::user()->name }}</h3>
                                        <span class="rich-list-subtitle text-white">{{ Auth::user()->email }}</span>
                                    </div>
                                    {{-- <div class="rich-list-append"><span class="badge badge-label-light fs-6">6+</span></div> --}}
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <a href="{{ route('setting.profile', Auth::user()->id) }}"
                                    class="text-reset notification-item">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs avatar-label-primary me-3">
                                            <span class="rounded fs-16">
                                                <i class="fas fa-user-alt"></i>
                                            </span>
                                        </div>
                                        <div class="flex">
                                            <h6 class="mb-1">Profile</h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="card-footer card-footer-bordered rounded-0"><a
                                    href="{{ route('auth.signout') }}" class="btn btn-label-danger">Sign out</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Profile -->
            </div>
        </div>
        <!-- End menu -->
    </div>
</header>
