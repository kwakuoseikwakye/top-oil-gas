@extends('layout.main')
@section('pageName', 'Users')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Users
                        </h3>
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">All users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="logs-tab" data-toggle="tab" href="#logs">
                                <span class="nav-text">Logs</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">
                        {{-- Approve Staff List --}}
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="user-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Usertype</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="logs" role="tabpanel" aria-labelledby="logs-tab">
                            <div class="row">
                                <div class="col">
                                    <label for="">Users</label>
                                    <select id="user" class="form-control select2">
                                        <option value="all">All</option>
                                        @foreach ($users as $item)
                                        <option value="{{$item->userid}}">{{$item->fname}} {{$item->lname}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="">From</label>
                                    <input type="date" value="{{ date('Y-m-01') }}" id="dateFrom"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <label for="">To</label>
                                    <input type="date" value="{{ date('2050-m-d') }}" id="dateTo"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                        id="filter"><i class="fa fa-filter"></i> FILTER</button>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="logs-table">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Username</th>
                                            <th>Module</th>
                                            <th>Action</th>
                                            <th>Activity</th>
                                            <th>IP Address</th>
                                            <th>Longitude</th>
                                            <th>Latitude</th>
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

<script>
    var userTable = $('#user-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/users`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                "data": null,
                "render": function (data, type, full) {
                    return full['fname'] + ' ' + full['lname'];
                }
            },
            {
                data: "email"
            },
            {
                data: "phone"
            },
            {
                data: "usertype"
            },
        ],
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
                    columns: [0, 1, 2, 3, 4]
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

    var dateFrom = $('#dateFrom').val();
    var dateTo = $('#dateTo').val();
    var user = $('#user').val();

    var logsTable = $('#logs-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/logs/${user}/${dateFrom}/${dateTo}`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "createdate"
            },
            {
                data: "username"
            },
            {
                data: "module"
            },
            {
                data: "action"
            },
            {
                data: "activity"
            },
            {
                data: "ipaddress"
            },
            {
                data: "longitude"
            },
            {
                data: "latitude"
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

    // FILTER  
    $('#filter').click(function () {
        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        var user = $('#user').val();

        logsTable.ajax.url(
                `${APP_URL}/api/logs/${user}/${dateFrom}/${dateTo}`
            )
            .load();
    })

</script>


@endsection
