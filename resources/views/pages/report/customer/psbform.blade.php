<form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <input type="hidden" name="type" value="{{ $type ?? 'psb' }}">
        
        <div class="mb-3">
            <label class="form-label w-100" for="branch_id">Piih Cabang</label>
            <select name="branch_id" id="branch_id"
                class="form-control select2form @error('branch_id') is-invalid @enderror">
                <option value="">Pilih Cabang</option>
                @foreach ($branch as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
            @error('branch_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="validationCustom01" class="form-label required">Nama Customer</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                id="validationCustom01">
            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label w-100" for="zone_id">Piih Jalur</label>
            <select name="zone_id" id="zone_id"
                class="form-control select2form @error('zone_id') is-invalid @enderror">
                <option value="">Pilih Jalur</option>
                @foreach ($zone as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
            @error('zone_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label w-100" for="odp_id">Piih Odp</label>
            <select name="odp_id" id="odp_id" class="form-control select2form">
                <option value="">Pilih Odp</option>
            </select>

            <div id="custom-odp-container" class="mt-2" style="display:none;">
                <label for="custom_odp" class="form-label">Masukkan ODP (Bila Tidak Ada
                    ODP)</label>
                <input type="text" id="custom_odp" name="odp_id"
                    placeholder="Isi ODP jika tidak tersedia pada pilihan"
                    class="form-control @error('odp_id') is-invalid @enderror">
            </div>
        </div>

        <div class="mb-3">
            <label for="validationCustom01" class="form-label required">No HP</label>
            <input type="text" inputmode="numeric" name="phone"
                class="form-control @error('phone') is-invalid @enderror">
            @error('phone')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <input type="hidden" name="purpose" value="psb">
        @error('purpose')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
        <div class="col-6 mb-2">
            <button class="btn btn-primary" type="button" id="get-location-btn">Get Lokasi
                Client</button>
        </div>
        <div class=" d-flex justify-between gap-3">
            <div class="mb-3">
                <label class="form-label" for="latitude">Latitude</label>
                <input type="text" id="latitude" name="latitude" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label" for="longitude">Longitude</label>
                <input type="text" id="longitude" name="longitude" class="form-control">
            </div>
        </div>
        <div class="mb-3">
            <label for="validationCustom01" class="form-label required">Alamat</label>
            <textarea name="address" class="form-control @error('address') is-invalid @enderror" cols="30"></textarea>
            @error('address')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label w-100" for="tecnition">Piih Teknisi</label>
            <select name="tecnition[]" class="form-control select2form" multiple>
                <option value="">Pilih Teknisi</option>
                @foreach ($technitian as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
                @error('tecnition')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </select>
        </div>

        <hr class="text-bg-info">
        <div class="col-md-6">
            <button type="button" class="btn btn-primary btn-sm mb-3" id="addRow">Tambah
                Barang</button>
        </div>

        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>SN Modem</th>
                            <th>Jumlah</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <div>
        <button class="btn btn-primary" type="submit">Submit</button>
    </div>
</form>
