@extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- Select2 CSS -->
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit {{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('employee') }}">{{ $title }}</a></li>
                            <li class="breadcrumb-item active">Edit {{ $title }}</li>
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
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('employee.update', ['id' => $employee->id]) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Personal Information -->
                                    <div class="col-md-6">
                                        <h5 class="mb-4">Informasi Pribadi</h5>

                                        <div class="mb-3">
                                            <label class="form-label required">Nama Lengkap</label>
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $employee->name) }}">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">NIK</label>
                                            <input type="text" name="nik"
                                                class="form-control @error('nik') is-invalid @enderror"
                                                value="{{ old('nik', $employee->nik) }}">
                                            @error('nik')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">Tanggal Lahir</label>
                                            <input type="date" name="date_of_birth"
                                                class="form-control @error('date_of_birth') is-invalid @enderror"
                                                value="{{ old('date_of_birth', $employee->date_of_birth) }}">
                                            @error('date_of_birth')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">Jenis Kelamin</label>
                                            <select name="gender"
                                                class="form-control @error('gender') is-invalid @enderror">
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="male"
                                                    {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>
                                                    Laki-laki</option>
                                                <option value="female"
                                                    {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">Alamat</label>
                                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $employee->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">Nomor Telepon</label>
                                            <input type="text" name="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', $employee->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">Email</label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $employee->email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Kartu Identitas</label>
                                            <input type="file" name="identity_card"
                                                class="form-control @error('identity_card') is-invalid @enderror">
                                            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                                            @if ($employee->identity_card)
                                                <div class="mt-2">
                                                    <small class="text-muted">File saat ini:
                                                        {{ $employee->identity_card }}</small>
                                                </div>
                                            @endif
                                            @error('identity_card')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Employment Information -->
                                    <div class="col-md-6">
                                        <h5 class="mb-4">Informasi Kepegawaian</h5>

                                        <div class="mb-3">
                                            <label class="form-label required">Perusahaan</label>
                                            <select name="company_id"
                                                class="form-control select2form @error('company_id') is-invalid @enderror">
                                                <option value="">Pilih Perusahaan</option>
                                                @foreach ($company as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('company_id', $employee->company_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('company_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">Departemen</label>
                                            <select name="department_id"
                                                class="form-control select2form @error('department_id') is-invalid @enderror">
                                                <option value="">Pilih Departemen</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}"
                                                        {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                                        {{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('department_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">Jabatan</label>
                                            <select name="position_id"
                                                class="form-control select2form @error('position_id') is-invalid @enderror">
                                                <option value="">Pilih Jabatan</option>
                                                @foreach ($positions as $position)
                                                    <option value="{{ $position->id }}"
                                                        {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                                                        {{ $position->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('position_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <hr class="my-4">

                                        <h5 class="mb-4">Informasi Akun</h5>

                                        <div class="mb-3">
                                            <label class="form-label">Foto Profil</label>
                                            <input type="file" name="picture"
                                                class="form-control @error('picture') is-invalid @enderror">
                                            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                                            @if ($employee->user && $employee->user->picture)
                                                <div class="mt-2">
                                                    <small class="text-muted">File saat ini:
                                                        {{ $employee->user->picture }}</small>
                                                </div>
                                            @endif
                                            @error('picture')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">Username</label>
                                            <input type="text" name="username"
                                                class="form-control @error('username') is-invalid @enderror"
                                                value="{{ old('username', $employee->user->username ?? '') }}">
                                            @error('username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror">
                                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah
                                                password</small>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label required">Role</label>
                                            <select name="role"
                                                class="form-control select2form @error('role') is-invalid @enderror">
                                                <option value="">Pilih Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ old('role', $employee->user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('employee') }}" class="btn btn-secondary ms-2">Kembali</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Select2 JS -->
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
@endpush
