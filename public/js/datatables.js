(function () {
    "use strict";

    if (typeof window.jQuery === "undefined" || typeof window.DataTable === "undefined") {
        console.error("DataTables gagal dimuat. Pastikan jQuery dimuat sebelum DataTables.");
        return;
    }

    var language = {
        emptyTable: "Belum ada data.",
        info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data",
        infoFiltered: "(disaring dari _MAX_ data)",
        lengthMenu: "Tampilkan _MENU_",
        loadingRecords: "Memuat data...",
        processing: "Memuat data...",
        search: "Cari:",
        searchPlaceholder: "Ketik untuk mencari...",
        zeroRecords: "Data tidak ditemukan.",
        paginate: { first: "Awal", last: "Akhir", next: "Berikutnya", previous: "Sebelumnya" },
    };

    function parseConfiguration(table) {
        var script = table.parentElement.querySelector("[data-datatable-config]");
        return script ? JSON.parse(script.textContent) : {};
    }

    document.querySelectorAll("[data-datatable]").forEach(function (table) {
        var configuration = parseConfiguration(table);
        var filterForm = table.dataset.filterForm ? document.querySelector(table.dataset.filterForm) : null;
        var dataTable = new DataTable(table, {
            ajax: {
                url: table.dataset.source,
                data: function (data) {
                    if (!filterForm) {
                        return;
                    }
                    new FormData(filterForm).forEach(function (value, key) {
                        data[key] = value;
                    });
                },
            },
            columns: configuration.columns || [],
            columnDefs: configuration.columnDefs || [],
            order: configuration.order || [[0, "desc"]],
            pageLength: Number(table.dataset.pageLength || 25),
            lengthMenu: [10, 25, 50, 100],
            processing: true,
            serverSide: true,
            searchDelay: 350,
            stateSave: false,
            autoWidth: false,
            language: language,
            layout: {
                topStart: "pageLength",
                topEnd: "search",
                bottomStart: "info",
                bottomEnd: "paging",
            },
        });

        table._dataTable = dataTable;

        if (filterForm) {
            filterForm.addEventListener("submit", function (event) {
                event.preventDefault();
                dataTable.ajax.reload();
            });
            filterForm.querySelectorAll("select,input[type=date]").forEach(function (field) {
                field.addEventListener("change", function () { dataTable.ajax.reload(); });
            });
            filterForm.querySelectorAll("[data-datatable-reset]").forEach(function (button) {
                button.addEventListener("click", function () {
                    filterForm.reset();
                    dataTable.search("");
                    dataTable.ajax.reload();
                });
            });
        }

        table.addEventListener("datatable:reload", function () { dataTable.ajax.reload(null, false); });
    });
})();
