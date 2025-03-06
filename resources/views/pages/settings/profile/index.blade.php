@extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <style>
        .profile-container {
            position: relative;
        }

        .profile-image-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f1f1f1;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .required:after {
            content: " *";
            color: red;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .tab-content {
            padding: 20px 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
    </div>

    <div class="container-fluid">
        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-xl-12">
                    @if (session('status'))
                        <div class="alert alert-{{ session('status') == 'Success!' ? 'success' : 'danger' }} alert-dismissible fade show"
                            role="alert">
                            <strong>{{ session('status') }}</strong> {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 col-lg-3">
                                    <div class="profile-image-container">
                                        <img src="{{ $profile->picture ? asset('storage/images/user/' . $profile->picture) : asset('assets/images/default.png') }}"
                                            alt="Profile" class="profile-image">
                                    </div>
                                    <div class="text-center mb-4">
                                        <h5>{{ $profile->name }}</h5>
                                        <p class="text-muted">
                                            @if ($isEmployee && $profile->employee)
                                                {{ $profile->employee->position->name ?? 'Position not set' }} -
                                                {{ $profile->employee->department->name ?? 'Department not set' }}
                                            @else
                                                @foreach ($profile->getRoleNames() as $role)
                                                    <span class="badge bg-primary">{{ ucfirst($role) }}</span>
                                                @endforeach
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-8 col-lg-9">
                                    <ul class="nav nav-tabs" id="profileTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#profile" type="button" role="tab"
                                                aria-controls="profile" aria-selected="true">Profile</button>
                                        </li>
                                        @if ($isEmployee && $profile->employee)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="employee-tab" data-bs-toggle="tab"
                                                    data-bs-target="#employee" type="button" role="tab"
                                                    aria-controls="employee" aria-selected="false">Employee Details</button>
                                            </li>
                                        @endif
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="password-tab" data-bs-toggle="tab"
                                                data-bs-target="#password" type="button" role="tab"
                                                aria-controls="password" aria-selected="false">Change Password</button>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="profileTabContent">
                                        <!-- Basic Profile Tab -->
                                        <div class="tab-pane fade show active" id="profile" role="tabpanel"
                                            aria-labelledby="profile-tab">
                                            <!-- Profile Employee Details -->
                                            @if ($isEmployee && $profile->employee)
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-4">
                                                        <div class="mb-3">
                                                            <label for="position" class="form-label">Position</label>
                                                            <p class="form-control-plaintext">
                                                                {{ $profile->employee->position->name ?? 'Not set' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-4">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label">Department</label>
                                                            <p class="form-control-plaintext">
                                                                {{ $profile->employee->department->name ?? 'Not set' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-4">
                                                        <div class="mb-3">
                                                            <label for="salary" class="form-label">Salary</label>
                                                            <p class="form-control-plaintext">
                                                                {{ $salary ? number_format($salary->basic_salary_amount, 0, ',', '.') : 'Not set' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif


                                            <!-- Profile Form -->
                                            <form action="{{ route('setting.profile.update', ['id' => $profile->id]) }}"
                                                method="POST" enctype="multipart/form-data" class="needs-validation"
                                                novalidate>
                                                @csrf
                                                @method('PUT')

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="picture" class="form-label">Gambar Profil</label>
                                                            <input type="file" name="picture" id="picture"
                                                                class="form-control @error('picture') is-invalid @enderror"
                                                                accept="image/*" onchange="previewImage(this)">
                                                            <small class="text-muted">Format yang diterima: JPEG, PNG, JPG,
                                                                GIF. Ukuran maksimal: 2MB</small>
                                                            @error('picture')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                            <div class="preview-container">
                                                                <img id="imagePreview"
                                                                    src="{{ $profile->picture ? asset('storage/images/user/' . $profile->picture) : '#' }}"
                                                                    alt="Preview" class="image-preview"
                                                                    style="display: {{ $profile->picture ? 'block' : 'none' }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label required">Nama</label>
                                                            <input type="text" name="name"
                                                                value="{{ $profile->name }}"
                                                                class="form-control @error('name') is-invalid @enderror"
                                                                id="name" required>
                                                            @error('name')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="username"
                                                                class="form-label required">Username</label>
                                                            <input type="text" name="username"
                                                                value="{{ $profile->username }}"
                                                                class="form-control @error('username') is-invalid @enderror"
                                                                id="username" required>
                                                            @error('username')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="email"
                                                                class="form-label required">Email</label>
                                                            <input type="email" name="email"
                                                                value="{{ $profile->email }}"
                                                                class="form-control @error('email') is-invalid @enderror"
                                                                id="email" required>
                                                            @error('email')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mt-3">
                                                        <button type="submit" class="btn btn-primary">Simpan
                                                            Perubahan</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Employee Details Tab (Only for Employee Role) -->
                                        @if ($isEmployee && $profile->employee)
                                            <div class="tab-pane fade" id="employee" role="tabpanel"
                                                aria-labelledby="employee-tab">
                                                <form
                                                    action="{{ route('setting.profile.update', ['id' => $profile->id]) }}"
                                                    method="POST" class="needs-validation" novalidate>
                                                    @csrf
                                                    @method('PUT')

                                                    <!-- Include basic user info fields because they're required by validation -->
                                                    <input type="hidden" name="name" value="{{ $profile->name }}">
                                                    <input type="hidden" name="username"
                                                        value="{{ $profile->username }}">
                                                    <input type="hidden" name="email" value="{{ $profile->email }}">

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="nik" class="form-label">NIK</label>
                                                                <input type="text" name="nik"
                                                                    value="{{ $profile->employee->nik ?? '' }}"
                                                                    class="form-control @error('nik') is-invalid @enderror"
                                                                    id="nik">

                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="phone" class="form-label">Nomor
                                                                    Telepon</label>
                                                                <input type="text" name="phone"
                                                                    value="{{ $profile->employee->phone ?? '' }}"
                                                                    class="form-control @error('phone') is-invalid @enderror"
                                                                    id="phone">

                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="date_of_birth" class="form-label">Tanggal
                                                                    Lahir</label>
                                                                <input type="date" name="date_of_birth"
                                                                    value="{{ $profile->employee->date_of_birth ?? '' }}"
                                                                    class="form-control @error('date_of_birth') is-invalid @enderror"
                                                                    id="date_of_birth">

                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="gender" class="form-label">Jenis
                                                                    Kelamin</label>
                                                                <select name="gender" id="gender"
                                                                    class="form-control @error('gender') is-invalid @enderror">
                                                                    <option value="">Pilih Jenis Kelamin</option>
                                                                    <option value="male"
                                                                        {{ ($profile->employee->gender ?? '') == 'male' ? 'selected' : '' }}>
                                                                        Laki-laki</option>
                                                                    <option value="female"
                                                                        {{ ($profile->employee->gender ?? '') == 'female' ? 'selected' : '' }}>
                                                                        Perempuan</option>
                                                                </select>

                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="address" class="form-label">Alamat</label>
                                                                <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ $profile->employee->address ?? '' }}</textarea>

                                                            </div>
                                                        </div>

                                                        <div class="col-12 mt-3">
                                                            <button type="submit" class="btn btn-primary">Simpan
                                                                Perubahan</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif

                                        <!-- Password Change Tab -->
                                        <div class="tab-pane fade" id="password" role="tabpanel"
                                            aria-labelledby="password-tab">
                                            <form action="{{ route('setting.profile.update', ['id' => $profile->id]) }}"
                                                method="POST" class="needs-validation" novalidate>
                                                @csrf
                                                @method('PUT')

                                                <!-- Include basic user info fields because they're required by validation -->
                                                <input type="hidden" name="name" value="{{ $profile->name }}">
                                                <input type="hidden" name="username" value="{{ $profile->username }}">
                                                <input type="hidden" name="email" value="{{ $profile->email }}">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="password" class="form-label required">Kata Sandi
                                                                Baru</label>
                                                            <div class="input-group">
                                                                <input type="password" name="password"
                                                                    class="form-control @error('password') is-invalid @enderror"
                                                                    id="password" required>
                                                                <button class="btn btn-outline-secondary" type="button"
                                                                    id="togglePassword">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </div>
                                                            <small class="text-muted">Minimal 8 karakter, harus mengandung
                                                                huruf besar, huruf kecil, dan angka</small>
                                                            @error('password')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="password_confirmation"
                                                                class="form-label required">Konfirmasi Kata Sandi</label>
                                                            <div class="input-group">
                                                                <input type="password" name="password_confirmation"
                                                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                                                    id="password_confirmation" required>
                                                                <button class="btn btn-outline-secondary" type="button"
                                                                    id="toggleConfirmPassword">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </div>
                                                            @error('password_confirmation')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mt-3">
                                                        <button type="submit" class="btn btn-primary">Ubah Kata
                                                            Sandi</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                // Don't reset the preview if no new file is selected
                if (!preview.src.includes('storage/images/user')) {
                    preview.src = '#';
                    preview.style.display = 'none';
                }
            }
        }

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password_confirmation');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
@endpush
