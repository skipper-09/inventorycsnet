<div class="sidebar-left">

    <div data-simplebar class="h-100">

        <!--- Sidebar-menu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="left-menu list-unstyled" id="side-menu">
                <li>
                    <a href="{{ route('dashboard') }}" class="">
                        <i class="fas fa-desktop"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-title">MASTER</li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow ">
                        <i class="fa fa-palette"></i>
                        <span>Master Data</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('unitproduk') }}"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Unit Produk</a></li>
                        <li><a href="{{ route('produk') }}"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Produk</a></li>
                    </ul>
                </li>

                <li class="menu-title">Laporan</li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow "><i class="fa fa-chart-pie align-middle"></i> Apexcharts</a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="charts-apex-line.html"><i class="mdi mdi-checkbox-blank-circle"></i> Line</a></li>
                        <li><a href="charts-apex-area.html"><i class="mdi mdi-checkbox-blank-circle"></i> Area</a></li>
                        <li><a href="charts-apex-column.html"><i class="mdi mdi-checkbox-blank-circle"></i> Column</a></li>
                        <li><a href="charts-apex-bar.html"><i class="mdi mdi-checkbox-blank-circle"></i> Bar</a></li>
                        <li><a href="charts-apex-mixed.html"><i class="mdi mdi-checkbox-blank-circle"></i> Mixed/Combo</a></li>
                        <li><a href="charts-apex-range.html"><i class="mdi mdi-checkbox-blank-circle"></i> Range Area</a></li>
                    </ul>
                </li>

                <li class="menu-title">SETTINGS</li>

                <li>
                    <a href="apps-chat.html" class="">
                        <i class="fas fa-comment"></i>
                        <span>Chat</span>
                    </a>
                </li>

                <li>
                    <a href="apps-kanban.html" class="">
                        <i class="fas fa-grip-horizontal"></i>
                        <span>Kanban Board</span>
                    </a>
                </li>

                <li>
                    <a href="apps-contact.html" class="">
                        <i class="fas fa-id-badge"></i>
                        <span>Contacts</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>