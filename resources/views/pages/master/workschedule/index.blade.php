@extends('layouts.base')
@section('title', $title)

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* Ensure event text is always visible */
        .fc-event-title {
            color: #fff !important;
            /* Always white text */
            font-weight: bold;
        }

        .fc-event-time {
            color: #000000 !important;
            /* Always white text */
            font-weight: bold;
        }

        /* Custom colors for each shift */
        .shift-1 {
            background-color: #007bff;
            /* Blue */
        }

        .shift-2 {
            background-color: #28a745;
            /* Green */
        }

        .shift-3 {
            background-color: #dc3545;
            /* Red */
        }

        .shift-4 {
            background-color: #ffc107;
            /* Yellow */
        }

        /* Additional styling for offdays */
        .fc-offday {
            background-color: #6c757d !important;
            /* Gray */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!-- Employee List with Search -->
                <div class="card">
                    <div class="card-header">
                        <h5>Karyawan</h5>
                    </div>
                    <div class="card-body">
                        <label for="employee-select" class="form-label">Pilih Karyawan</label>
                        <select id="employee-select" class="form-control select2form">
                            <option value="" selected disabled>-- Select Employee --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                    data-department="{{ $employee->department->name }}">
                                    {{ $employee->name }} - ({{ $employee->department->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Jadwal Kerja</h5>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.js"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

    <script>


        let selectedEmployeeId = null;
        let selectedEmployeeName = '';

        const storedEmployeeId = localStorage.getItem('selectedEmployeeId');
        const storedEmployeeName = localStorage.getItem('selectedEmployeeName');

        if (storedEmployeeId !== null && storedEmployeeName !== null) {
            selectedEmployeeId = storedEmployeeId;
            selectedEmployeeName = storedEmployeeName;
        } else {
            selectedEmployeeId = null;
            selectedEmployeeName = '';
        }

        let selectedDates = [];

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize FullCalendar
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                events: function (info, successCallback, failureCallback) {
                    if (selectedEmployeeId) {
                        fetch(`/admin/master/workschedule/events/${selectedEmployeeId}?start=${info.startStr}&end=${info.endStr}`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(event => {
                                    switch (event.shift_name) {
                                        case "PAGI":
                                            event.className = 'shift-1';  // Blue
                                            break;
                                        case "SIANG":
                                            event.className = 'shift-2';  // Green
                                            break;
                                        case "SORE":
                                            event.className = 'shift-3';  // Red
                                            break;
                                        case "MALAM":
                                            event.className = 'shift-4';  // Yellow
                                            break;
                                        default:
                                            event.className = 'fc-offday';  // Gray for offday
                                    }
                                });
                                successCallback(data);
                            })
                            .catch(error => failureCallback(error));
                    }
                },
                select: function (info) {
                    // Push the selected date range into the selectedDates array
                    selectedDates = [];
                    if (info.startStr === info.endStr) {
                        selectedDates.push(info.startStr);  // Only add the single selected date
                    } else {
                        // Otherwise, we are dealing with a range of dates
                        for (let date = info.start; date <= info.end; date.setDate(date.getDate() + 1)) {
                            selectedDates.push(date.toISOString().split('T')[0]);
                        }
                    }

                    if (selectedEmployeeId) {
                        fetch('/admin/master/workschedule/shifts')
                            .then(response => response.json())
                            .then(shifts => {
                                let shiftOptions = '';
                                shifts.forEach(shift => {
                                    shiftOptions += `<option value="${shift.id}">${shift.name} (From ${shift.shift_start} to ${shift.shift_end})</option>`;
                                });

                                Swal.fire({
                                    title: 'Set Jadwal Kerja untuk Beberapa Tanggal',
                                    html: `
                                                                <p>Apakah Anda ingin menambahkan jadwal atau offday untuk ${selectedEmployeeName} pada tanggal yang dipilih?</p>
                                                                <label for="shift">Pilih Shift:</label>
                                                                <select id="shift" class="form-control select2form">${shiftOptions}</select>
                                                            `,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonText: 'Set Jadwal',
                                    cancelButtonText: 'Offday',
                                }).then(result => {
                                    const shiftId = document.getElementById('shift').value;
                                    if (result.isConfirmed) {
                                        createBulkSchedule(shiftId);
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        createBulkOffday();
                                    }
                                });
                            });
                    }
                },
            });
            calendar.render();

            $('#employee-select').on('change', function () {
                selectedEmployeeId = $(this).val();
                selectedEmployeeName = $('#employee-select option:selected').data('name');

                localStorage.setItem('selectedEmployeeId', selectedEmployeeId);
                localStorage.setItem('selectedEmployeeName', selectedEmployeeName);
                if (selectedEmployeeId) {
                    calendar.refetchEvents();
                }
            });


            // Load shifts into bulk schedule modal
            fetch('/admin/master/workschedule/shifts')
                .then(response => response.json())
                .then(shifts => {
                    let shiftOptions = '';
                    shifts.forEach(shift => {
                        shiftOptions += `<option value="${shift.id}">${shift.name} (From ${shift.shift_start} to ${shift.shift_end})</option>`;
                    });
                    $('#bulk-shift').html(shiftOptions);
                });
        });

        // Function for creating a single schedule
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
                            text: 'Jadwal untuk tanggal tersebut telah ditambahkan',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
        }

        // Function for creating bulk schedules
        function createBulkSchedule(shiftId) {
            if (selectedDates.length > 2) {
                const startDate = selectedDates[1];
                const endDate = selectedDates[selectedDates.length - 1];

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
                                text: 'Jadwal telah ditambahkan untuk beberapa tanggal',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
            } else {
                // For single date scheduling
                createSingleSchedule(shiftId, selectedDates[1]);
            }
        }

        // remove localStorage
        // window.addEventListener('beforeunload', function () {
        //     localStorage.removeItem('selectedEmployeeId');
        //     localStorage.removeItem('selectedEmployeeName');
        // });



        // Function for creating a single offdays
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
                            text: 'Offday untuk tanggal tersebut telah ditambahkan',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
        }

        // Function for creating bulk offdays
        function createBulkOffday() {
            if (selectedDates.length > 2) {
                const startDate = selectedDates[1];
                const endDate = selectedDates[selectedDates.length - 1];

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
                        status: 'offday',
                    })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success',
                                text: 'Offday telah ditambahkan untuk beberapa tanggal',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
            } else {
                createSingleOffday(selectedDates[1]);
            }
        }



    </script>

@endpush