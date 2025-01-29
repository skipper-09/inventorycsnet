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
                    'read-product-role'])
                    <li class="menu-title">MASTER</li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow ">
                            <i class="fa fa-palette"></i>
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
                
                <li>
                    <a href="{{ route('customer') }}" class="">
                        <i class="fas fa-user-alt"></i>
                        <span>Customer</span>
                    </a>
                </li>

                @canany(['read-transfer-product', 'read-work-product'])
                    <li class="menu-title">Transaksi</li>

                    @can('read-transfer-product')
                        <li>
                            <a href="{{ route('transfer') }}" class="">
                                <i class="fas fa-cart-plus"></i>
                                <span>Transfer Barang</span>
                            </a>
                        </li>
                    @endcan
                    @can('read-work-product')
                        <li>
                            <a href="{{ route('workproduct') }}" class="">
                                <i class="fas fa-cart-arrow-down"></i>
                                <span>Pengeluaran Barang</span>
                            </a>
                        </li>
                    @endcan
                @endcanany

                @canany(['read-transaction-product'])
                    <li class="menu-title">Laporan</li>
                    @can('read-transaction-product')
                        <li>
                            <a href="{{ route('report.transaction-product') }}" class="">
                                <i class="fas fa-box"></i>
                                <span>Transaksi Barang</span>
                            </a>
                        </li>
                    @endcan
                @endcanany
                {{-- <li class="menu-title">Laporan</li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow "><i class="fa fa-chart-pie align-middle"></i>
                        Apexcharts</a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="charts-apex-line.html"><i class="mdi mdi-checkbox-blank-circle"></i> Line</a></li>
                        <li><a href="charts-apex-area.html"><i class="mdi mdi-checkbox-blank-circle"></i> Area</a></li>
                        <li><a href="charts-apex-column.html"><i class="mdi mdi-checkbox-blank-circle"></i> Column</a>
                        </li>
                        <li><a href="charts-apex-bar.html"><i class="mdi mdi-checkbox-blank-circle"></i> Bar</a></li>
                        <li><a href="charts-apex-mixed.html"><i class="mdi mdi-checkbox-blank-circle"></i>
                                Mixed/Combo</a></li>
                        <li><a href="charts-apex-range.html"><i class="mdi mdi-checkbox-blank-circle"></i> Range
                                Area</a></li>
                    </ul>
                </li> --}}

                @canany(['read-user', 'read-role', 'read-setting'])
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
                                <span>Role</span>
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
