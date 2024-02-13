@extends('layout.main')
@section('pageName', 'Employees')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Employees
                        </h3>
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">Employees</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="trash-tab" data-toggle="tab" href="#trash">
                                <span class="nav-text">Trash</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">

                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="employees-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            {{-- <th>Gender</th> --}}
                                            <th>Email</th>
                                            <th>Phone</th>
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
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="trash-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            {{-- <th>Gender</th> --}}
                                            <th>Email</th>
                                            <th>Phone</th>
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
@include('modules.employees.modals.info')

<script>
    var employeesTable = $('#employees-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/employees`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "code"
            },
            {
                data: "name"
            },
            // {
            //     data: "gender"
            // },
            {
                data: "email"
            },
            {
                data: "phone"
            },
            {
                data: null,
                defaultContent: `

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-success btn-sm view-btn'>
                   <i class='fas fa-eye'></i>
                </button>
                `
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
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

    $("#employees-table").on("click", ".view-btn", function () {
        let data = employeesTable.row($(this).parents('tr')).data();

        $("#info-employees-modal").modal("show")
        document.getElementById("info-employees-name").innerHTML = data.name;
        $("#info-employees-image").attr("src", Boolean(data.picture) ? data.picture :
            '/avatar.png');
        document.getElementById("info-employees-number").innerHTML = data.code;
        document.getElementById("info-employees-gender").innerHTML = data.gender;
        document.getElementById("info-employees-occupation").innerHTML = data.occupation;
        document.getElementById("info-employees-marital-status").innerHTML = data.marital_status;
        document.getElementById("info-employees-dob").innerHTML = data.dob;
        document.getElementById("info-employees-pob").innerHTML = data.pob;
        document.getElementById("info-employees-phone").innerHTML = data.phone;
        document.getElementById("info-employees-email").innerHTML = data.email;
        document.getElementById("info-employees-region").innerHTML = data.region;
        document.getElementById("info-employees-town").innerHTML = data.town;
        document.getElementById("info-employees-streetname").innerHTML = data.streetname;
        document.getElementById("info-employees-landmark").innerHTML = data.landmark;
        document.getElementById("info-employees-gpsaddress").innerHTML = data.address;
        document.getElementById("info-employees-coordinates").innerHTML = `Lat: ${data.lat}, Long: ${data.long}`;
        document.getElementById("info-employees-id-type").innerHTML = data.idtype;
        document.getElementById("info-employees-id-number").innerHTML = data.idno;
        $("#info-employees-id-image").attr("src", Boolean(data.picture) ? data.picture :
            '/img/noimage.jpg');
    });

    var trashTable = $('#trash-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/employees/trash`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "code"
            },
            {
                data: "name"
            },
            // {
            //     data: "gender"
            // },
            {
                data: "email"
            },
            {
                data: "phone"
            },
            {
                data: null,
                defaultContent: `

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-success btn-sm view-btn'>
                   <i class='fas fa-eye'></i>
                </button>
                
                `
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
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

        $("#info-employees-modal").modal("show")
        document.getElementById("info-employees-name").innerHTML = data.name;
        $("#info-employees-image").attr("src", Boolean(data.picture) ? data.picture :
            '/avatar.png');
        document.getElementById("info-employees-number").innerHTML = data.code;
        document.getElementById("info-employees-gender").innerHTML = data.gender;
        document.getElementById("info-employees-occupation").innerHTML = data.occupation;
        document.getElementById("info-employees-marital-status").innerHTML = data.marital_status;
        document.getElementById("info-employees-dob").innerHTML = data.dob;
        document.getElementById("info-employees-pob").innerHTML = data.pob;
        document.getElementById("info-employees-phone").innerHTML = data.phone;
        document.getElementById("info-employees-email").innerHTML = data.email;
        document.getElementById("info-employees-region").innerHTML = data.region;
        document.getElementById("info-employees-town").innerHTML = data.town;
        document.getElementById("info-employees-streetname").innerHTML = data.streetname;
        document.getElementById("info-employees-landmark").innerHTML = data.landmark;
        document.getElementById("info-employees-gpsaddress").innerHTML = data.address;
        document.getElementById("info-employees-coordinates").innerHTML = `Lat: ${data.lat}, Long: ${data.long}`;
        document.getElementById("info-employees-id-type").innerHTML = data.idtype;
        document.getElementById("info-employees-id-number").innerHTML = data.idno;
        $("#info-employees-id-image").attr("src", Boolean(data.picture) ? data.picture :
            '/img/noimage.jpg');
    });

</script>


@endsection
