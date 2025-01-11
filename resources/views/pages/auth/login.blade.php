@extends('layouts.auth')

@section('title', $title)

@section('content')
    <div class="col-10 col-md-6 col-lg-4 col-xxl-3">
        <div class="card mb-0">
            <div class="card-body">
                <div class="text-center">
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
                    <h4 class="mt-2">Welcome Back !</h4>
                    <p class="text-muted">Sign in to continue to {{ Setting('name') }}.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-label-danger alert-dismissible fade show mb-4" role="alert">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="ri-error-warning-fill fs-16 align-middle me-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong class="d-block mb-1">Please check the following errors:</strong>
                                <div class="text-muted">
                                    @foreach ($errors->all() as $error)
                                        <div class="mb-1">{{ $error }}</div>
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                <div class="p-2 mt-5">
                    <form method="POST" action="{{ route('auth.signin') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" id="username" required
                                placeholder="Enter username" value="{{ old('username') }}">
                        </div>
                        <div class="mb-3">
                            <label for="userpassword" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="userpassword" required
                                placeholder="Enter password">
                        </div>
                        <div class="mb-sm-5">
                            <div class="form-check float-sm-start">
                                <input type="checkbox" class="form-check-input" id="customControlInline">
                                <label class="form-check-label" for="customControlInline">Remember me</label>
                            </div>
                            <div class="float-sm-end">
                                <a href="{{ route('resetpassword') }}" class="text-muted"><i class="mdi mdi-lock me-1"></i> Forgot
                                    your password?</a>
                            </div>
                        </div>

                        <div class="pt-3 text-center">
                            <button class="btn btn-primary w-xl waves-effect waves-light" type="submit">Log In</button>
                        </div>
                    </form>
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
