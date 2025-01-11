@extends('layouts.auth')

@section('title', $title)

@section('content')
    <div class="col-10 col-md-6 col-lg-4 col-xxl-3">
        <div class="card mb-0">
            <div class="card-body">
                <div class="my-3">
                    <a href="{{ route('login') }}" class="text-muted">
                        <i class="mdi mdi-arrow-left"></i> Kembali ke Login
                    </a>
                </div>
                <div class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('/storage/' . Setting('logo')) }}" alt="logo-sm-dark" height="20">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('/storage/' . Setting('logo')) }}" alt="logo-dark" height="18">
                    </span>
                </div>
                <div class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('/storage/' . Setting('logo')) }}" alt="logo-sm-light" height="20">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('/storage/' . Setting('logo')) }}" alt="logo-light" height="18">
                    </span>
                </div>
                <h4 class="text-danger mt-4">Reset Password</h4>
                <p class="text-muted">Untuk mereset password Anda, harap hubungi admin kami.</p>

                <div class="mt-4">
                    <a href="mailto:admin@domain.com" class="btn btn-primary">
                        Hubungi Admin
                    </a>
                </div>
                <div class="mt-5 text-center">
                    <p>©
                        <script>
                            document.write(new Date().getFullYear())
                        </script> {{ Setting('name') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
