<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>@yield('title') | {{ Setting('name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Inventory Apps PT Cahaya Solusindo" name="description" />
    <meta content="Csnet Developer" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">


    @include('layouts.partials.css')
    @stack('css')

</head>

<body>

    <div id="layout-wrapper">
        <!-- Start topbar -->
        @include('layouts.partials.topbar')
        <!-- End topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        @include('layouts.partials.sidebar')
        <!-- Left Sidebar End -->


        <!-- Start right Content here -->
        <div class="main-content">
            <div class="page-content">
                @yield('content')
            </div>
            <!-- End Page-content -->

            @include('layouts.partials.footer')

        </div>
        <!-- end main content-->
    </div>
    <!-- end layout-wrapper -->


    @include('layouts.partials.customsetting')



    <!-- JAVASCRIPT -->
    @include('layouts.partials.js')
    @stack('js')
</body>

</html>