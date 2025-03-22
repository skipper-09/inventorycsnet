@extends('layouts.base')
@section('title', $title)

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        .fc-event {
            cursor: pointer;
            font-weight: bold;
        }

        .shift-1 {
            font-size: 1rem;
            color: #007bff !important;
            /* background-color: #007bff !important; */
            /* border-color: #0056b3 !important; */
        }

        .shift-2 {
            font-size: 1rem;
            color: #28a745 !important;
            /* border-color: #145523 !important; */
        }

        .shift-3 {
            font-size: 1rem;
            color: #dc3545 !important;
            /* border-color: #a71d2a !important; */
        }

        .shift-4 {
            font-size: 1rem;
            color: #ffc107 !important;
            /* border-color: #d39e00 !important; */
            /* color: #212529 !important; */
        }

        .offday {
            font-size: 1rem;
            color: #6c757d !important;
            /* border-color: #495057 !important; */
        }

        .fc-event.shift-1 {
            background-color: #007bff !important;
            border-color: #0056b3 !important;
            color: white !important;
        }

        .fc-event.shift-2 {
            background-color: #28a745 !important;
            border-color: #145523 !important;
            color: white !important;
        }

        .fc-event.shift-3 {
            background-color: #dc3545 !important;
            border-color: #a71d2a !important;
            color: white !important;
        }

        .fc-event.shift-4 {
            background-color: #ffc107 !important;
            border-color: #d39e00 !important;
            color: #212529 !important;
        }

        .fc-event.offday {
            background-color: #6c757d !important;
            border-color: #495057 !important;
            color: white !important;
        }

        .calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 15px 0;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 5px;
            border-radius: 3px;
        }

        .fc-day-today {
            background-color: rgba(0, 123, 255, 0.1) !important;
        }

        .employee-card {
            transition: all 0.3s ease;
        }

        .employee-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <!-- Employee List with Search -->
                <div class="card employee-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Karyawan</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#groupScheduleModal">
                            <i class="bx bx-group me-1"></i> Group Jadwal
                        </button>
                    </div>
                    <div class="card-body">
                        <label for="employee-select" class="form-label">Pilih Karyawan</label>
                        <select id="employee-select" class="form-control select2form">
                            <option value="" selected disabled>-- Select Employee --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                    data-department="{{ $employee->department->name }}">
                                    {{ $employee->name }} - ({{ $employee->department->name }})
                                </option>
                            @endforeach
                        </select>

                        <div class="mt-3" id="employee-info" style="display: none;">
                            <h6 id="selected-employee-name"></h6>
                            <p id="selected-employee-department"></p>
                        </div>
                    </div>
                </div>

                <!-- Shift List -->
                <div class="card employee-card mt-3">
                    <div class="card-header">
                        <h5>Shift Tersedia</h5>
                    </div>
                    <div class="card-body">
                        <div id="shift-list" class="list-group">
                            @foreach ($shift as $s)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $s->name }}</strong>
                                        <div class="text-muted small">{{ $s->shift_start }} - {{ $s->shift_end }}</div>
                                    </div>
                                    <span
                                        class="shift-{{ $loop->iteration }} fas fa-circle">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </div>
                            @endforeach
                            <div class="list-group-item d-flex justify-content-between align-items-center mt-2">
                                <div>
                                    <strong>OFF DAY</strong>
                                    <div class="text-muted small">Libur</div>
                                </div>
                                <span class="fas fa-circle offday">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card employee-card mt-3">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" id="add-schedule-btn" disabled>
                                <i class="bx bx-plus-circle me-1"></i> Tambah Jadwal
                            </button>
                            <button class="btn btn-info" id="export-excel-btn" disabled>
                                <i class="bx bx-export me-1"></i> Export Excel
                            </button>
                            <button class="btn btn-dark" id="set-weekday-offday-btn" disabled>
                                <i class="bx bx-calendar-x me-1"></i> Set Hari Libur
                            </button>
                            <button class="btn btn-danger" id="clear-month-btn" disabled>
                                <i class="bx bx-trash me-1"></i> Clear Bulan Ini
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Jadwal Kerja Karyawan</h5>
                        <button id="export-department-btn" class="btn btn-info">
                            <i class="bx bx-export"></i> Export Department Schedule
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="calendar-legend">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #007bff;"></div>
                                <span>PAGI</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #28a745;"></div>
                                <span>SIANG</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #dc3545;"></div>
                                <span>SORE</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #ffc107;"></div>
                                <span>MALAM</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #6c757d;"></div>
                                <span>OFFDAY</span>
                            </div>
                        </div>

                        <div id="calendar"></div>

                        <div class="alert alert-info mt-3" id="calendar-info" style="display: none;">
                            <i class="bx bx-info-circle me-2"></i>
                            <span>Klik pada tanggal untuk menambah jadwal atau klik pada jadwal yang sudah ada untuk
                                mengubah/menghapus jadwal.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Add Work Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="scheduleForm">
                        <div class="mb-3">
                            <label for="schedule-date-range" class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="schedule-date-range" required>
                        </div>

                        <div class="mb-3">
                            <label for="schedule-type" class="form-label">Schedule Type</label>
                            <select class="form-control" id="schedule-type" required>
                                <option value="work">Work Schedule</option>
                                <option value="offday">Off Day</option>
                            </select>
                        </div>

                        <div class="mb-3" id="shift-selector">
                            <label for="shift-id" class="form-label">Shift</label>
                            <select class="form-control" id="shift-id" required>
                                @foreach ($shift as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->shift_start }} -
                                        {{ $s->shift_end }})</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-schedule">Save Schedule</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Schedule Modal -->
    <div class="modal fade" id="groupScheduleModal" tabindex="-1" aria-labelledby="groupScheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupScheduleModalLabel">Create Group Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="groupScheduleForm">
                        <div class="mb-3">
                            <label for="department-id" class="form-label">Department</label>
                            <select class="form-control" id="department-id" required>
                                <option value="" selected disabled>-- Select Department --</option>
                                @foreach ($Departement as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="group-date-range" class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="group-date-range" required>
                        </div>

                        <div class="mb-3">
                            <label for="group-shift-id" class="form-label">Shift</label>
                            <select class="form-control" id="group-shift-id" required>
                                @foreach ($shift as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->shift_start }} -
                                        {{ $s->shift_end }})</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-group-schedule">Save Group Schedule</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Jadwal Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="event-details-content">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" id="change-to-offday">Set as Off Day</button>
                    <button type="button" class="btn btn-primary" id="change-to-work">Set as Work Day</button>
                    <button type="button" class="btn btn-danger" id="delete-event">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekday Offday Modal -->
    <div class="modal fade" id="weekdayOffdayModal" tabindex="-1" aria-labelledby="weekdayOffdayModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="weekdayOffdayModalLabel">Set Hari Libur Mingguan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="weekdayOffdayForm">
                        <div class="mb-3">
                            <label for="weekday-select" class="form-label">Pilih Hari</label>
                            <select class="form-control" id="weekday-select" required>
                                <option value="0">Minggu</option>
                                <option value="1">Senin</option>
                                <option value="2">Selasa</option>
                                <option value="3">Rabu</option>
                                <option value="4">Kamis</option>
                                <option value="5">Jumat</option>
                                <option value="6">Sabtu</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirm-weekday-offday">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Department Modal -->
    <div class="modal fade" id="exportDepartmentModal" tabindex="-1" role="dialog"
        aria-labelledby="exportDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportDepartmentModalLabel">Export Jadwal Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="exportDepartmentForm">
                        <div class="mb-3">
                            <label for="export-department-select">Departemen</label>
                            <select class="form-control" id="export-department-select" required>
                                <option value="">Pilih Departemen</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="export-period-type">Tipe Periode</label>
                            <select class="form-control" id="export-period-type">
                                <option value="custom">Kustom Tanggal</option>
                                <option value="month">Bulanan</option>
                                <option value="year">Tahunan</option>
                            </select>
                        </div>

                        <!-- Custom Date Range Fields -->
                        <div id="export-custom-date-fields">
                            <div class="mb-3">
                                <label for="export-date-range">
                                    Jangka Waktu
                                </label>
                                <input type="text" class="form-control" id="export-date-range" required>
                            </div>
                        </div>

                        <!-- Monthly Period Field -->
                        <div id="export-monthly-field" style="display: none;">
                            <div class="mb-3">
                                <label for="export-month-picker">Tahun dan Bulan</label>
                                <input type="month" class="form-control" id="export-month-picker">
                            </div>
                        </div>

                        <!-- Yearly Period Field -->
                        <div id="export-yearly-field" style="display: none;">
                            <div class="mb-3">
                                <label for="export-year-picker">Tahun</label>
                                <select class="form-control" id="export-year-picker">
                                    <!-- Will be populated via JavaScript -->
                                </select>
                            </div>
                        </div>

                        {{-- <div class="mb-3">
                            <label for="export-format-select">Export Format</label>
                            <select class="form-control" id="export-format-select">
                                <option value="xlsx">Excel (XLSX)</option>
                                <option value="csv">CSV</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div> --}}

                        <!-- Hidden fields to store the actual dates -->
                        <input type="hidden" id="export-start-date">
                        <input type="hidden" id="export-end-date">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="export-department-submit">Export</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
    <script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

    <script>
        let selectedEmployeeId = null;
        let selectedEmployeeName = '';
        let selectedEmployeeDepartment = '';
        let calendar = null;
        let currentEvent = null;

        // Check if we have stored employee data
        const storedEmployeeId = localStorage.getItem('selectedEmployeeId');
        const storedEmployeeName = localStorage.getItem('selectedEmployeeName');
        const storedEmployeeDepartment = localStorage.getItem('selectedEmployeeDepartment');

        if (storedEmployeeId && storedEmployeeName) {
            selectedEmployeeId = storedEmployeeId;
            selectedEmployeeName = storedEmployeeName;
            selectedEmployeeDepartment = storedEmployeeDepartment;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the calendar
            initializeCalendar();

            initExportDepartmentModal();

            // $('#exportDepartmentModal').on('shown.bs.modal', function() {
            //     // Reinitialize select2 when modal is shown
            //     $('#export-department-select').select2('destroy');
            //     initSelect2();
            // });

            // Initialize daterangepickers
            $('#schedule-date-range').daterangepicker({
                singleDatePicker: false,
                showDropdowns: true,
                minYear: 2020,
                maxYear: parseInt(moment().format('YYYY'), 10) + 2,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            $('#group-date-range').daterangepicker({
                singleDatePicker: false,
                showDropdowns: true,
                minYear: 2020,
                maxYear: parseInt(moment().format('YYYY'), 10) + 2,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            // Show schedule type toggle
            $('#schedule-type').on('change', function() {
                if ($(this).val() === 'work') {
                    $('#shift-selector').show();
                } else {
                    $('#shift-selector').hide();
                }
            });

            // Set previously selected employee if any
            if (selectedEmployeeId) {
                $('#employee-select').val(selectedEmployeeId).trigger('change');
                $('#employee-info').show();
                $('#selected-employee-name').text(selectedEmployeeName);
                $('#selected-employee-department').text(selectedEmployeeDepartment);
                enableButtons();
                $('#calendar-info').show();
                loadEmployeeSchedule();
            }

            // Handle employee selection
            $('#employee-select').on('change', function() {
                selectedEmployeeId = $(this).val();
                selectedEmployeeName = $('#employee-select option:selected').data('name');
                selectedEmployeeDepartment = $('#employee-select option:selected').data('department');

                localStorage.setItem('selectedEmployeeId', selectedEmployeeId);
                localStorage.setItem('selectedEmployeeName', selectedEmployeeName);
                localStorage.setItem('selectedEmployeeDepartment', selectedEmployeeDepartment);

                $('#employee-info').show();
                $('#selected-employee-name').text(selectedEmployeeName);
                $('#selected-employee-department').text(selectedEmployeeDepartment);
                enableButtons();
                $('#calendar-info').show();

                loadEmployeeSchedule();
            });

            // Add schedule button
            $('#add-schedule-btn').on('click', function() {
                // Set default date range to today
                const today = moment().format('YYYY-MM-DD');
                $('#schedule-date-range').data('daterangepicker').setStartDate(today);
                $('#schedule-date-range').data('daterangepicker').setEndDate(today);

                // Reset form
                $('#schedule-type').val('work').trigger('change');
                $('#scheduleModal').modal('show');
            });

            // Save schedule
            $('#save-schedule').on('click', function() {
                const dateRange = $('#schedule-date-range').val();
                const dates = dateRange.split(' - ');
                const startDate = dates[0];
                const endDate = dates.length > 1 ? dates[1] : startDate;
                const scheduleType = $('#schedule-type').val();
                const shiftId = $('#shift-id').val();

                if (scheduleType === 'work') {
                    saveBulkSchedule(startDate, endDate, shiftId);
                } else {
                    saveBulkOffday(startDate, endDate);
                }
            });

            // Save group schedule
            $('#save-group-schedule').on('click', function() {
                const departmentId = $('#department-id').val();
                const dateRange = $('#group-date-range').val();
                const shiftId = $('#group-shift-id').val();

                if (!departmentId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select a department'
                    });
                    return;
                }

                saveGroupSchedule(departmentId, dateRange, shiftId);
            });

            // Handle event detail actions
            $('#change-to-offday').on('click', function() {
                if (!currentEvent) return;

                createSingleOffday(moment(currentEvent.start).format('YYYY-MM-DD'));
                $('#eventDetailsModal').modal('hide');
            });

            $('#change-to-work').on('click', function() {
                if (!currentEvent) return;

                $('#schedule-date-range').data('daterangepicker').setStartDate(moment(currentEvent.start)
                    .format('YYYY-MM-DD'));
                $('#schedule-date-range').data('daterangepicker').setEndDate(moment(currentEvent.start)
                    .format('YYYY-MM-DD'));
                $('#schedule-type').val('work').trigger('change');
                $('#eventDetailsModal').modal('hide');
                $('#scheduleModal').modal('show');
            });

            $('#delete-event').on('click', function() {
                if (!currentEvent) return;

                deleteSchedule(moment(currentEvent.start).format('YYYY-MM-DD'));
                $('#eventDetailsModal').modal('hide');
            });

            // Export to Excel
            $('#export-excel-btn').on('click', function() {
                if (!selectedEmployeeId) return;

                const currentDate = moment();
                const startDate = moment(currentDate).startOf('month').format('YYYY-MM-DD');
                const endDate = moment(currentDate).endOf('month').format('YYYY-MM-DD');

                exportSchedule('excel', startDate, endDate);
            });

            $('#export-excel-btn').on('click', function() {
                if (!selectedEmployeeId) return;

                const currentDate = moment();
                const startDate = moment(currentDate).startOf('month').format('YYYY-MM-DD');
                const endDate = moment(currentDate).endOf('month').format('YYYY-MM-DD');

                exportSchedule('excel', startDate, endDate);
            });

            // Clear Month
            $('#clear-month-btn').on('click', function() {
                if (!selectedEmployeeId) return;

                const currentDate = moment();
                const startDate = moment(currentDate).startOf('month').format('YYYY-MM-DD');
                const endDate = moment(currentDate).endOf('month').format('YYYY-MM-DD');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `This will remove all schedules for ${moment(currentDate).format('MMMM YYYY')}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete schedules',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkDeleteSchedules(startDate, endDate);
                    }
                });
            });

            $('#set-weekday-offday-btn').on('click', function() {
                if (!selectedEmployeeId) return;

                if (selectedEmployeeId) {
                    $('#weekdayOffdayModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Please select an employee first'
                    });
                }
            });

            // Confirm weekday offday button
            $('#confirm-weekday-offday').on('click', function() {
                const weekday = $('#weekday-select').val();
                const currentDate = new Date();
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth() + 1; // JavaScript months are 0-based

                setWeekdayOffdays(selectedEmployeeId, year, month, weekday);
                $('#weekdayOffdayModal').modal('hide');
            });

            // Export Department button event
            $('#export-department-btn').on('click', function() {
                $('#exportDepartmentModal').modal('show');
            });

            // Export Department submit button
            $('#export-department-submit').on('click', function() {
                const departmentId = $('#export-department-select').val();
                const periodType = $('#export-period-type').val();
                // Set format directly to xlsx without reading from select
                const format = 'xlsx';
                let startDate, endDate;

                if (!departmentId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select a department'
                    });
                    return;
                }

                if (periodType === 'custom') {
                    const dateRange = $('#export-date-range').val();
                    const dates = dateRange.split(' - ');
                    startDate = dates[0];
                    endDate = dates.length > 1 ? dates[1] : startDate;
                } else if (periodType === 'month') {
                    const monthValue = $('#export-month-picker').val(); // Format: "YYYY-MM"
                    if (monthValue) {
                        const [year, month] = monthValue.split('-').map(Number);
                        // First day of selected month
                        startDate = moment(new Date(year, month - 1, 1)).format('YYYY-MM-DD');
                        // Last day of selected month
                        endDate = moment(new Date(year, month, 0)).format('YYYY-MM-DD');
                    }
                } else if (periodType === 'year') {
                    const year = $('#export-year-picker').val();
                    if (year) {
                        // First day of selected year
                        startDate = moment(new Date(year, 0, 1)).format('YYYY-MM-DD');
                        // Last day of selected year
                        endDate = moment(new Date(year, 11, 31)).format('YYYY-MM-DD');
                    }
                }

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select valid dates'
                    });
                    return;
                }

                // Call the export function
                exportDepartmentSchedule(departmentId, format, startDate, endDate);

                // Close the modal
                $('#exportDepartmentModal').modal('hide');
            });

            // Add these handlers for period type changes
            $('#export-period-type').on('change', function() {
                const periodType = $(this).val();

                // Hide all period fields first
                $('#export-custom-date-fields, #export-monthly-field, #export-yearly-field').hide();

                // Show the appropriate fields based on selection
                if (periodType === 'custom') {
                    $('#export-custom-date-fields').show();
                } else if (periodType === 'month') {
                    $('#export-monthly-field').show();
                    updateExportDatesFromMonth();
                } else if (periodType === 'year') {
                    $('#export-yearly-field').show();
                    updateExportDatesFromYear();
                }
            });

            $('#export-month-picker').on('change', function() {
                updateExportDatesFromMonth();
            });

            $('#export-year-picker').on('change', function() {
                updateExportDatesFromYear();
            });
        });

        function enableButtons() {
            $('#add-schedule-btn').prop('disabled', false);
            $('#export-excel-btn').prop('disabled', false);
            $('#clear-month-btn').prop('disabled', false);
            $('#set-weekday-offday-btn').prop('disabled', false); // Add this line
        }

        function initializeCalendar() {
            const calendarEl = document.getElementById('calendar');

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                height: 650,
                allDayDefault: true,
                selectable: true,
                selectMirror: true,
                editable: false,
                dayMaxEvents: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                select: function(info) {
                    if (!selectedEmployeeId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Select Employee',
                            text: 'Please select an employee first'
                        });
                        return;
                    }

                    // Set the date range in the modal
                    $('#schedule-date-range').data('daterangepicker').setStartDate(moment(info.start).format(
                        'YYYY-MM-DD'));
                    $('#schedule-date-range').data('daterangepicker').setEndDate(moment(info.end).subtract(1,
                        'days').format('YYYY-MM-DD'));

                    // Reset form
                    $('#schedule-type').val('work').trigger('change');
                    $('#scheduleModal').modal('show');
                },
                eventClick: function(info) {
                    currentEvent = info.event;
                    showEventDetails(info.event);
                }
            });

            calendar.render();
        }

        function loadEmployeeSchedule() {
            if (!selectedEmployeeId) return;

            // Get the current view's start and end date
            const view = calendar.view;
            const start = moment(view.activeStart).format('YYYY-MM-DD');
            const end = moment(view.activeEnd).format('YYYY-MM-DD');

            // Show loading indicator
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching schedule data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/master/workschedule/events/${selectedEmployeeId}?start=${start}&end=${end}`)
                .then(response => response.json())
                .then(data => {
                    // Remove all events
                    calendar.removeAllEvents();

                    // Add new events
                    data.forEach(event => {
                        calendar.addEvent({
                            id: `${event.id || event.start}`,
                            title: event.title,
                            start: event.start,
                            end: event.end,
                            allDay: true,
                            className: event.status === 'offday' ? 'offday' : getShiftClass(event
                                .shift_name),
                            extendedProps: {
                                status: event.status,
                                shift_name: event.shift_name,
                                shift_start: event.shift_start,
                                shift_end: event.shift_end,
                                shift_id: event.shift_id
                            }
                        });
                    });

                    Swal.close();
                })
                .catch(error => {
                    console.error('Error loading schedule:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load employee schedule'
                    });
                });
        }

        function showEventDetails(event) {
            const eventProps = event.extendedProps;
            const dateFormatted = moment(event.start).format('DD MMMM YYYY');

            let content = '';

            if (eventProps.status === 'offday') {
                content = `
                    <div class="text-center mb-3">
                        <span class="badge bg-secondary p-2">OFF DAY</span>
                    </div>
                    <div class="alert alert-dark">
                        <div class="alert-content">
                            <h4 class="alert-heading">Libur</h4>
                            <p>${selectedEmployeeName}</p>
                            <p class="mb-0">${dateFormatted}</p>
                        </div>
                    </div>
                `;

                $('#change-to-offday').hide();
                $('#change-to-work').show();
            } else {
                content = `
                    <div class="text-center mb-3">
                        <span class="badge ${getShiftClass(eventProps.shift_name)} p-2">${eventProps.shift_name}</span>
                    </div>
                    <div class="alert alert-dark">
                        <div class="alert-content">
                            <h4 class="alert-heading">Libur</h4>
                            <p>${selectedEmployeeName}</p>
                            <p>${moment(eventProps.shift_start).format('HH:mm')} - ${moment(eventProps.shift_end).format('HH:mm')}</p>
                            <p class="mb-0">${dateFormatted}</p>
                        </div>
                    </div>
                `;

                $('#change-to-offday').show();
                $('#change-to-work').hide();
            }

            $('#event-details-content').html(content);
            $('#eventDetailsModal').modal('show');
        }

        function getShiftClass(shiftName) {
            switch (shiftName) {
                case "PAGI":
                    return "shift-1 bg-primary text-white";
                case "SIANG":
                    return "shift-2 bg-success text-white";
                case "SORE":
                    return "shift-3 bg-danger text-white";
                case "MALAM":
                    return "shift-4 bg-warning text-dark";
                default:
                    return "";
            }
        }

        function createSingleSchedule(shiftId, date) {
            fetch(`/admin/master/workschedule/create-schedule`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        employee_id: selectedEmployeeId,
                        date: date,
                        shift_id: shiftId,
                        status: 'work',
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Jadwal berhasil ditambahkan',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#scheduleModal').modal('hide');
                            loadEmployeeSchedule();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add schedule'
                    });
                });
        }

        function saveBulkSchedule(startDate, endDate, shiftId) {
            fetch(`/admin/master/workschedule/create-bulk-schedule`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        employee_id: selectedEmployeeId,
                        start_date: startDate,
                        end_date: endDate,
                        status: 'work',
                        shift_id: shiftId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Jadwal kerja berhasil ditambahkan',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#scheduleModal').modal('hide');
                            loadEmployeeSchedule();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add schedules'
                    });
                });
        }

        function createSingleOffday(date) {
            fetch(`/admin/master/workschedule/create-offday`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        employee_id: selectedEmployeeId,
                        date: date,
                        status: 'offday',
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Offday berhasil ditambahkan',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            loadEmployeeSchedule();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add offday'
                    });
                });
        }

        function saveBulkOffday(startDate, endDate) {
            fetch(`/admin/master/workschedule/create-bulk-offday`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        employee_id: selectedEmployeeId,
                        start_date: startDate,
                        end_date: endDate,
                        status: 'offday'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Offdays berhasil ditambahkan',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#scheduleModal').modal('hide');
                            loadEmployeeSchedule();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add offdays'
                    });
                });
        }

        function saveGroupSchedule(departmentId, dateRange, shiftId) {
            const dates = dateRange.split(' - ');
            const startDate = dates[0];
            const endDate = dates.length > 1 ? dates[1] : startDate;

            // Show loading indicator
            Swal.fire({
                title: 'Processing...',
                text: 'Creating schedules for department',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/master/workschedule/create-group-schedule`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        department_id: departmentId,
                        start_date: startDate,
                        end_date: endDate,
                        shift_id: shiftId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Group schedules created successfully',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#groupScheduleModal').modal('hide');

                            // Reload employee schedule if the current employee belongs to the department
                            if (selectedEmployeeId && departmentId == $('#employee-select option:selected')
                                .data('department-id')) {
                                loadEmployeeSchedule();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to create group schedules'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to create group schedules'
                    });
                });
        }

        function deleteSchedule(date) {
            fetch(`/admin/master/workschedule/delete-schedule`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        employee_id: selectedEmployeeId,
                        date: date
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Jadwal berhasil dihapus',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            loadEmployeeSchedule();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete schedule'
                    });
                });
        }

        function bulkDeleteSchedules(startDate, endDate) {
            // Show loading indicator
            Swal.fire({
                title: 'Processing...',
                text: 'Deleting schedules',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/master/workschedule/delete-bulk-schedule`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        employee_id: selectedEmployeeId,
                        start_date: startDate,
                        end_date: endDate
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Jadwal berhasil dihapus',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            loadEmployeeSchedule();
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete schedules'
                    });
                });
        }

        function exportSchedule(format, startDate, endDate) {
            const url =
                `/admin/master/workschedule/export/${selectedEmployeeId}/${format}?start_date=${startDate}&end_date=${endDate}`;
            window.open(url, '_blank');
        }

        function exportDepartmentSchedule(departmentId, format, startDate, endDate) {
            const url =
                `/admin/master/workschedule/export-department/${departmentId}/${format}?start_date=${startDate}&end_date=${endDate}`;
            window.open(url, '_blank');
        }

        // Event listener untuk tombol "Konfirmasi"
        document.getElementById('confirm-weekday-offday').addEventListener('click', function() {
            // Ambil nilai weekday dari elemen <select>
            const weekday = document.getElementById('weekday-select').value;

            // Pastikan weekday terdefinisi
            if (weekday === undefined || weekday === null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Silakan pilih hari terlebih dahulu.'
                });
                return;
            }

            // Ambil tahun dan bulan dari FullCalendar
            const currentDate = calendar.getDate();
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth() + 1;


            // Panggil fungsi setWeekdayOffdays dengan parameter yang benar
            setWeekdayOffdays(selectedEmployeeId, year, month, parseInt(weekday));
        });

        // Fungsi setWeekdayOffdays
        function setWeekdayOffdays(employeeId, year, month, weekday) {
            const weekdayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin mengatur semua hari ${weekdayNames[weekday]} di bulan ${month} tahun ${year} sebagai hari libur?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Atur',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: `Setting offdays for ${weekdayNames[weekday]}`,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    console.log({
                        employee_id: employeeId,
                        year: year,
                        month: month,
                        weekday: weekday
                    });

                    fetch(`/admin/master/workschedule/set-weekday-offdays`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                employee_id: employeeId,
                                year: year,
                                month: month,
                                weekday: weekday
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();

                            if (data.success) {
                                Swal.fire({
                                    title: 'Success',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Refresh the calendar after setting off days
                                    loadEmployeeSchedule();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to set weekday offdays'
                            });
                        });
                }
            });
        }

        function updateButtonState(isEmployeeSelected) {
            $('#add-schedule-btn').prop('disabled', !isEmployeeSelected);
            $('#export-excel-btn').prop('disabled', !isEmployeeSelected);
            $('#clear-month-btn').prop('disabled', !isEmployeeSelected);
            $('#set-weekday-offday-btn').prop('disabled', !isEmployeeSelected); // Add this line
        }

        // Helper functions for date updates
        function updateExportDatesFromMonth() {
            const monthValue = $('#export-month-picker').val(); // Format: "YYYY-MM"
            if (monthValue) {
                const [year, month] = monthValue.split('-').map(Number);

                // First day of selected month
                const firstDay = new Date(year, month - 1, 1);

                // Last day of selected month
                const lastDay = new Date(year, month, 0);

                $('#export-start-date').val(moment(firstDay).format('YYYY-MM-DD'));
                $('#export-end-date').val(moment(lastDay).format('YYYY-MM-DD'));
            }
        }

        function updateExportDatesFromYear() {
            const year = $('#export-year-picker').val();
            if (year) {
                // First day of selected year
                const firstDay = new Date(year, 0, 1);

                // Last day of selected year
                const lastDay = new Date(year, 11, 31);

                $('#export-start-date').val(moment(firstDay).format('YYYY-MM-DD'));
                $('#export-end-date').val(moment(lastDay).format('YYYY-MM-DD'));
            }
        }

        function populateExportYearOptions() {
            const select = $('#export-year-picker');
            const currentYear = new Date().getFullYear();

            // Add options for the last 5 years and next 2 years
            for (let year = currentYear - 5; year <= currentYear + 2; year++) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                if (year === currentYear) {
                    option.selected = true;
                }
                select.append(option);
            }
        }

        // function initSelect2() {
        //     $('#export-department-select').select2({
        //         dropdownParent: $('#exportDepartmentModal'),
        //         width: '100%'
        //     });
        // }

        // Call this in your DOMContentLoaded handler to initialize
        function initExportDepartmentModal() {
            // Reset the form when modal is shown
            $('#exportDepartmentModal').on('show.bs.modal', function() {
                $('#export-department-select').val('').trigger('change');
                $('#export-period-type').val('custom').trigger('change');
            });
            // initSelect2();

            // Initialize the daterangepicker for export
            $('#export-date-range').daterangepicker({
                singleDatePicker: false,
                showDropdowns: true,
                minYear: 2020,
                maxYear: parseInt(moment().format('YYYY'), 10) + 2,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            // Set default dates to current month
            const today = moment();
            $('#export-date-range').data('daterangepicker').setStartDate(moment(today).startOf('month').format(
                'YYYY-MM-DD'));
            $('#export-date-range').data('daterangepicker').setEndDate(moment(today).endOf('month').format('YYYY-MM-DD'));

            // Set default month picker to current month
            $('#export-month-picker').val(moment().format('YYYY-MM'));

            // Populate year options
            populateExportYearOptions();

            // Set period type change handling
            $('#export-period-type').trigger('change');
        }

        // Handle calendar navigation to reload events
        document.addEventListener('fullcalendarViewChange', function(e) {
            if (selectedEmployeeId) {
                loadEmployeeSchedule();
            }
        });

        // Custom event when FullCalendar changes view
        window.addEventListener('resize', function() {
            if (calendar) {
                calendar.updateSize();
            }
        });

        // Dispatch a custom event when the prev/next/today buttons are clicked
        const observeCalendarNavigation = () => {
            const buttons = document.querySelectorAll('.fc-prev-button, .fc-next-button, .fc-today-button');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    setTimeout(() => {
                        document.dispatchEvent(new CustomEvent('fullcalendarViewChange'));
                    }, 100);
                });
            });
        };

        // Call after calendar is initialized
        setTimeout(observeCalendarNavigation, 500);
    </script>
@endpush
