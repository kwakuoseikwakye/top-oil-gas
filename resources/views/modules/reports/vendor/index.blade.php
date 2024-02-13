@extends('layout.main')
@section('pageName', 'Operators')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
   
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Operators
                        </h3>
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">Active Operators</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="trash-tab" data-toggle="tab" href="#trash">
                                <span class="nav-text">Trash</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">
                        {{-- Approve Staff List --}}
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%" class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="vendor-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%" class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="trash-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
@include('modules.vendor.modals.info')
<script>
    var vendorTable = $('#vendor-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/vendor`,
            type: "GET"

        },
        ordering :false,
        order : [],
        processing: true,
        columns: [{
                data: "name"
            },
            {
                data: "gender"
            },
            {
                data: "phone"
            },
            {
                data: "email"
            },

            {
                data: null,
                defaultContent: `

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-primary btn-sm view-btn'>
                    <i class='fas fa-eye'></i>
                </button>
                `
            },
        ],
        // "columnDefs": [{
        //         "targets": [6],
        //         "visible": false
        //     },
        // ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-warning btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
        ]
    });

    $("#vendor-table").on("click", ".view-btn", function () {
        let data = vendorTable.row($(this).parents('tr')).data();

        $("#info-vendor-modal").modal("show")
        document.getElementById("info-vendor-name").innerHTML = data.name;
        $("#info-vendor-image").attr("src", Boolean(data.picture) ? data.picture :
            '/avatar.png');
        document.getElementById("info-vendor-number").innerHTML = data.code;
        document.getElementById("info-vendor-gender").innerHTML = data.gender;
        document.getElementById("info-vendor-phone").innerHTML = data.phone;
        document.getElementById("info-vendor-email").innerHTML = data.email;
        document.getElementById("info-vendor-region").innerHTML = data.region;
        document.getElementById("info-vendor-town").innerHTML = data.town;
        document.getElementById("info-vendor-streetname").innerHTML = data.streetname;
        document.getElementById("info-vendor-landmark").innerHTML = data.landmark;
        document.getElementById("info-vendor-gpsaddress").innerHTML = data.gpsaddress;
        document.getElementById("info-vendor-coordinates").innerHTML = `Lat: ${data.lat}, Long: ${data.long}`;
        document.getElementById("info-vendor-id-type").innerHTML = data.idtype;
        document.getElementById("info-vendor-id-number").innerHTML = data.idno;
        $("#info-vendor-id-image").attr("src", Boolean(data.picture) ? data.picture :
            '/img/noimage.jpg');
    });

    var trashTable = $('#trash-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/vendor/trash`,
            type: "GET"

        },
        ordering :false,
        order : [],
        processing: true,
        columns: [{
                data: "name"
            },
            {
                data: "gender"
            },
            {
                data: "phone"
            },
            {
                data: "email"
            },

            {
                data: null,
                defaultContent: `

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-primary btn-sm view-btn'>
                    <i class='fas fa-eye'></i>
                </button>
                `
            },
        ],
        // "columnDefs": [{
        //         "targets": [6],
        //         "visible": false
        //     },
        // ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-warning btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
        ]
    });

    $("#trash-table").on("click", ".view-btn", function () {
        let data = trashTable.row($(this).parents('tr')).data();

        $("#info-vendor-modal").modal("show")
        document.getElementById("info-vendor-name").innerHTML = data.name;
        $("#info-vendor-image").attr("src", Boolean(data.picture) ? data.picture :
            '/avatar.png');
        document.getElementById("info-vendor-number").innerHTML = data.code;
        document.getElementById("info-vendor-gender").innerHTML = data.gender;
        document.getElementById("info-vendor-phone").innerHTML = data.phone;
        document.getElementById("info-vendor-email").innerHTML = data.email;
        document.getElementById("info-vendor-region").innerHTML = data.region;
        document.getElementById("info-vendor-town").innerHTML = data.town;
        document.getElementById("info-vendor-streetname").innerHTML = data.streetname;
        document.getElementById("info-vendor-landmark").innerHTML = data.landmark;
        document.getElementById("info-vendor-gpsaddress").innerHTML = data.gpsaddress;
        document.getElementById("info-vendor-coordinates").innerHTML = `Lat: ${data.lat}, Long: ${data.long}`;
        document.getElementById("info-vendor-id-type").innerHTML = data.idtype;
        document.getElementById("info-vendor-id-number").innerHTML = data.idno;
        $("#info-vendor-id-image").attr("src", Boolean(data.picture) ? data.picture :
            '/img/noimage.jpg');
    });
</script>


@endsection