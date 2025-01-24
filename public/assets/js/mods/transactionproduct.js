// Wait for document ready
$(document).ready(function () {
    // Initialize Select2 first
    $(".select2").select2({
        width: "100%",
        dropdownParent: $("body"),
    });

    // Sweet Alert configuration
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: !1,
    });

    var route = $("#scroll-sidebar-datatable").data("route");

    // Initialize DataTable
    var table = $("#scroll-sidebar-datatable").DataTable({
        scrollY: "350px",
        scrollCollapse: true,
        paging: true,
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>",
            },
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination");
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: route,
            data: function (d) {
                d.transaksi = $("#FilterTransaction").val(); // Filter for transaction
                d.created_at = $("#created_at").val(); // Filter tanggal
            },
        },
        columns: [
            {
                data: "DT_RowIndex",
                searchable: false,
                width: "10px",
                class: "text-center",
            },
            {
                data: "created_at",
                name: "created_at",
            },
            {
                data: "transaksi",
                name: "transaksi",
            },
            {
                data: "pelanggan",
                name: "pelanggan",
            },
            {
                data: "products",
                name: "products",
            },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });

    // Handle transaction filter change
    $("#FilterTransaction").on("change", function () {
        $('#created_at').val('').trigger('change.select2'); // reset created_at
        table.ajax.reload(null, false); // Reload table with updated filter
    });

    // Handle date filter change
    $("#created_at").on("change", function () {
        $('#FilterTransaction').val('').trigger('change.select2'); // reset transaksi
        table.ajax.reload(null, false); // Reload table with updated filter
    });

    document.getElementById("export-button").addEventListener("click", function () {
        let baseUrl = $("#export-button").data("route");
        let transaksi = document.getElementById("FilterTransaction").value;
        let createdAt = document.getElementById("created_at").value;

        if (!transaksi && !createdAt) {
            Swal.fire("Perhatian", "Silakan pilih minimal satu filter sebelum ekspor.", "warning");
            return;
        }

        let exportUrl = `${baseUrl}?transaksi=${transaksi}&created_at=${createdAt}`;
        window.location.href = exportUrl; // Redirect ke URL ekspor
    });
});
