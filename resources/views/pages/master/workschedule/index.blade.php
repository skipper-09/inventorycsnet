@extends('layouts.base')
@section('title', $title)

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* Ensure event text is always visible */
        .fc-event-title {
            color: #fff !important;  /* Always white text */
            font-weight: bold;
        }
        .fc-event-time {
            color: #000000 !important;  /* Always white text */
            font-weight: bold;
        }

        /* Custom colors for each shift */
        .shift-1 {
            background-color: #007bff;  /* Blue */
        }

        .shift-2 {
            background-color: #28a745;  /* Green */
        }

        .shift-3 {
            background-color: #dc3545;  /* Red */
        }

        .shift-4 {
            background-color: #ffc107;  /* Yellow */
        }

        /* Additional styling for offdays */
        .fc-offday {
            background-color: #6c757d !important; /* Gray */
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
                        <select id="employee-select" class="form-control select2form">
                            <option value="" disabled selected>-- Select Employee --</option>
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
                <!-- FullCalendar -->
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

        <!-- Bulk Set Schedule Modal -->
        <div class="modal fade" id="bulkScheduleModal" tabindex="-1" aria-labelledby="bulkScheduleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkScheduleModalLabel">Bulk Set Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="bulk-shift">Pilih Shift:</label>
                            <select id="bulk-shift" class="form-control select2form">
                                <!-- Dynamic shift options will go here -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="bulk-set-schedule">Set Jadwal</button>
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
        let selectedEmployeeName = ''; // Variable to store selected employee's name
        let selectedDates = []; // To hold the selected dates for bulk scheduling

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize FullCalendar
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                events: function(info, successCallback, failureCallback) {
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
                select: function(info) {
                    // Push the selected date range into the selectedDates array
                    selectedDates = [];
                    for (let date = info.start; date <= info.end; date.setDate(date.getDate() + 1)) {
                        selectedDates.push(date.toISOString().split('T')[0]);
                    }

                    // Show the bulk scheduling modal
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

            // Handle employee selection and calendar refresh
            $('#employee-select').on('change', function() {
                selectedEmployeeId = $(this).val();
                selectedEmployeeName = $('#employee-select option:selected').data('name');

                // Refresh the calendar to show the selected employee's schedule
                if (selectedEmployeeId) {
                    calendar.refetchEvents(); // This will reload the events for the selected employee
                }
            });

            // Show Bulk Schedule Modal
            $('#bulk-set-schedule').on('click', function() {
                const shiftId = $('#bulk-shift').val();

                if (shiftId && selectedDates.length > 0) {
                    createBulkSchedule(shiftId);
                }

                // Close modal and refresh calendar after bulk schedule
                $('#bulkScheduleModal').modal('hide');
                calendar.refetchEvents();
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

        function createBulkSchedule(shiftId) {
            selectedDates.forEach(date => {
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
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire('Success', 'Jadwal telah ditambahkan', 'success');
                          // Refresh events after successful schedule
                          calendar.refetchEvents();
                      }
                  });
            });
        }

        function createBulkOffday() {
            selectedDates.forEach(date => {
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
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire('Success', 'Offday telah ditambahkan', 'success');
                          // Refresh events after successful offday creation
                          calendar.refetchEvents();
                      }
                  });
            });
        }
    </script>
@endpush
