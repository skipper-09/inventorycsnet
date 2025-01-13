<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>@yield('title') | PT Cahaya Solusindo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Inventory Apps PT Cahaya Solusindo" name="description" />
    <meta content="Csnet Developer" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">


    @include('layouts.partials.css')
    @stack('css')

</head>

<body>
    <div class="container-fluid overflow-hidden">
        <div class="row align-items-center justify-content-center min-vh-100">
            @yield('content')
        </div>
    </div>

    <!-- JAVASCRIPT -->
    @include('layouts.partials.js')
    @stack('js')
</body>

</html>