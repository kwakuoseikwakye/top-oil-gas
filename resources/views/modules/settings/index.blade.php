@extends('layout.main')
@section('pageName', 'Settings')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Settings
                        </h3>
                    </div>
                    <div class="card-toolbar">

                        <!--begin::Button-->
                        {{-- <a data-toggle="modal" data-target="#add-Settings-modal" class="btn btn-primary font-weight-bolder">
                            Add Settings</a> --}}
                        <!--end::Button-->
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">Locations</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="size-tab" data-toggle="tab" href="#size">
                                <span class="nav-text">Cylinder Size</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">

                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="location-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Location Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="size" role="tabpanel" aria-labelledby="size-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="cylinder-size-table">
                                    <thead>
                                        <tr>
                                            <th>Cylinder Size</th>
                                            <th>Action</th>
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
@include('modules.settings.modals.add_location')
@include('modules.settings.modals.update_location')
@include('modules.settings.modals.add_cylinder_size')
@include('modules.settings.modals.update_cylinder_size')

<script>
    var locationTable = $('#location-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/settings/location`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "code"
            },
            {
                data: "desc"
            },
            {
                data: "action"
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1]
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
            {
                text: "Add Location",
                attr: {
                    class: "ml-2 btn-success btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    $('#add-location-modal').modal('show')
                }
            },
        ]
    });

    $("#location-table").on("click", ".loc-delete-btn", function () {
        var data = locationTable.row($(this).parents("tr")).data();
        var formdata = new FormData()
        formdata.append("transid", data.transid);
        formdata.append("createuser", CREATEUSER);

        Swal.fire({
            title: "",
            text: "Are you sure you want to remove this record?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete"

        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    text: "Deleting...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/settings/delete_location`, {
                    method: "POST",
                    body: formdata,
                }).then(function (res) {
                    return res.json()
                }).then(function (data) {
                    if (!data.ok) {
                        Swal.fire({
                            text: data.msg,
                            icon: "error"
                        });
                        return;
                    }
                    Swal.fire({
                        text: "Location deleted successfully",
                        icon: "success"
                    });
                    locationTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "An error occurred in the form"
                        });
                    }
                })
            }
        })

    });

    $("#location-table").on("click", ".loc-edit-btn", function () {
        let data = locationTable.row($(this).parents('tr')).data();

        $("#update-location-modal").modal("show");
        $("#update-location-code").val(data.code);
        $("#update-location-desc").val(data.desc);
    });

    var cylinderSizeTable = $('#cylinder-size-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/settings/cylinder_size`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "desc"
            },
            {
                data: "action"
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1]
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
            {
                text: "Add cylinder size",
                attr: {
                    class: "ml-2 btn-success btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    $('#add-cylinder-size-modal').modal('show')
                }
            },
        ]
    });

    $("#cylinder-size-table").on("click", ".size-edit-btn", function () {
        let data = cylinderSizeTable.row($(this).parents('tr')).data();

        $("#update-cylinder-size-modal").modal("show");
        $("#update-cylinder-size-transid").val(data.transid);
        $("#update-cylinder-size-desc").val(data.desc);
    });

    $("#cylinder-size-table").on("click", ".size-delete-btn", function () {
        var data = cylinderSizeTable.row($(this).parents("tr")).data();
        var formdata = new FormData()
        formdata.append("transid", data.transid);
        formdata.append("createuser", CREATEUSER);

        Swal.fire({
            title: "",
            text: "Are you sure you want to remove this record?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete"

        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    text: "Deleting...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/settings/delete_cylinder_size`, {
                    method: "POST",
                    body: formdata,
                }).then(function (res) {
                    return res.json()
                }).then(function (data) {
                    if (!data.ok) {
                        Swal.fire({
                            text: data.msg,
                            icon: "error"
                        });
                        return;
                    }
                    Swal.fire({
                        text: "Cylinder size deleted successfully",
                        icon: "success"
                    });
                    cylinderSizeTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "An error occurred in the form"
                        });
                    }
                })
            }
        })

    });

</script>


@endsection
