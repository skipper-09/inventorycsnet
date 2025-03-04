<div class="sidebar-left">

    <div data-simplebar class="h-100">

        <!--- Sidebar-menu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="left-menu list-unstyled" id="side-menu">
                @can('read-dashboard')
                    <li>
                        <a href="{{ route('dashboard') }}" class="">
                            <i class="fas fa-desktop"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endcan

                @canany(['read-branch', 'read-unit-product', 'read-product', 'read-zone', 'read-zone-odp',
                    'read-product-role', 'read-task-template'])
                    <li class="menu-title">MASTER</li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow ">
                            <i class=" fas fa-archive"></i>
                            <span>Master Data</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            @can('read-branch')
                                <li><a href="{{ route('branch') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Data Cabang</a></li>
                            @endcan
                            @can('read-unit-product')
                                <li><a href="{{ route('unitproduk') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Unit Produk</a></li>
                            @endcan
                            @can('read-product')
                                <li><a href="{{ route('produk') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Produk</a></li>
                            @endcan
                            @can('read-zone')
                                <li><a href="{{ route('zone') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Jalur</a></li>
                            @endcan
                            @can('read-zone-odp')
                                <li><a href="{{ route('odp') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>ODP</a></li>
                            @endcan
                            @can('read-product-role')
                                <li><a href="{{ route('productrole') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Barang Per Role</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['read-task-template', 'read-task', 'read-assignment', 'read-assigmentdata'])
                    <li>
                        <a href="javascript: void(0);" class="has-arrow ">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Task Master</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            @can('read-task')
                                <li><a href="{{ route('taskdata') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Data Task</a></li>
                            @endcan
                            @can('read-task-template')
                                <li><a href="{{ route('tasktemplate') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Task Template</a></li>
                            @endcan
                            @can('read-assignment')
                                <li><a href="{{ route('assignment') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Penugasan</a></li>
                            @endcan
                            @can('read-assigmentdata')
                                <li><a href="{{ route('assigmentdata') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Tugas Karyawan</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['read-deduction', 'read-deduction-type', 'read-allowance', 'read-allowance-type',
                    'read-position', 'read-department', 'read-employee', 'read-salary'])
                    <li>
                        <a href="javascript: void(0);" class="has-arrow ">
                            <i class="fas fa-user-tie"></i>
                            <span>Data Karyawan</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">

                            @can('read-deduction')
                                <li><a href="{{ route('deduction') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Deduksi</a></li>
                            @endcan
                            @can('read-deduction-type')
                                <li><a href="{{ route('deductiontype') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Tipe Deduksi</a></li>
                            @endcan
                            @can('read-allowance')
                                <li><a href="{{ route('allowance') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Tunjangan</a></li>
                            @endcan
                            @can('read-allowance-type')
                                <li><a href="{{ route('allowancetype') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Tipe Tunjangan</a></li>
                            @endcan
                            @can('read-salary')
                                <li><a href="{{ route('salary') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Gaji Karyawan</a></li>
                            @endcan
                            @can('read-position')
                                <li><a href="{{ route('position') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Jabatan</a></li>
                            @endcan
                            @can('read-department')
                                <li><a href="{{ route('department') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Departemen</a></li>
                            @endcan
                            @can('read-employee')
                                <li><a href="{{ route('employee') }}"><i
                                            class="mdi mdi-checkbox-blank-circle align-middle"></i>Karyawan</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                {{-- 
                <li>
                    <a href="{{ route('formbuilder') }}" class="">
                        <i class="fas fa-user-alt"></i>
                        <span>Form Template</span>
                    </a>
                </li> --}}

                {{-- <li>
                    <a href="{{ route('customer') }}" class="">
                        <i class="fas fa-user-alt"></i>
                        <span>Customer</span>
                    </a>
                </li> --}}

                {{-- @canany(['read-leave'])
                    <li class="menu-title">Pengajuan</li>
                    @can('read-leave')
                        <li>
                            <a href="{{ route('leave') }}" class="">
                                <i class="fas fa-file-contract"></i>
                                <span>Cuti</span>
                            </a>
                        </li>
                    @endcan
                @endcanany --}}
                
                <li class="menu-title">Laporan</li>
                @canany(['read-leave-report', 'read-task-report', 'read-customer', 'read-activity-report'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-user-tie"></i>
                        <span>Laporan Karyawan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('read-leave-report')
                            <li><a href="{{ route('leavereport') }}"><i
                                        class="mdi mdi-checkbox-blank-circle align-middle"></i>Cuti Karyawan</a></li>
                        @endcan
                        @can('read-task-report')
                            <li><a href="{{ route('taskreport') }}"><i
                                        class="mdi mdi-checkbox-blank-circle align-middle"></i>Tugas Karyawan</a></li>
                        @endcan
                        @can('read-customer')
                            <li><a href="{{ route('customer') }}"><i
                                        class="mdi mdi-checkbox-blank-circle align-middle"></i>Pelanggan</a></li>
                        @endcan
                        @can('read-activity-report')
                            <li><a href="{{ route('activityreport') }}"><i
                                        class="mdi mdi-checkbox-blank-circle align-middle"></i>Aktivitas</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['read-transaction-product', 'read-transfer-product'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-warehouse"></i>
                        <span>Laporan Barang</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('read-transaction-product')
                            <li><a href="{{ route('report.transaction-product') }}"><i
                                        class="mdi mdi-checkbox-blank-circle align-middle"></i>Penggunaan Barang</a></li>
                        @endcan
                        @can('read-transfer-product')
                            <li><a href="{{ route('transfer') }}"><i
                                        class="mdi mdi-checkbox-blank-circle align-middle"></i>Pemindahan Barang</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany

                @canany(['read-user', 'read-role', 'read-setting', 'read-activity-log'])
                    <li class="menu-title">SETTINGS</li>
                    @can('read-user')
                        <li>
                            <a href="{{ route('user') }}" class="">
                                <i class="fas fa-users"></i>
                                <span>
                                    User Management
                                </span>
                            </a>
                        </li>
                    @endcan
                    @can('read-role')
                        <li>
                            <a href="{{ route('role') }}" class="">
                                <i class="fas fa-user-tag"></i>
                                <span>Hak Akses</span>
                            </a>
                        </li>
                    @endcan
                    @can('read-activity-log')
                        <li>
                            <a href="{{ route('activitylog') }}" class="">
                                <i class="fas fa-user-tag"></i>
                                <span>Log Aktivitas</span>
                            </a>
                        </li>
                    @endcan
                    @can('read-setting')
                        <li>
                            <a href="{{ route('setting') }}" class="">
                                <i class="fas fa-cog"></i>
                                <span>Setting Aplikasi</span>
                            </a>
                        </li>
                    @endcan
                @endcanany
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
