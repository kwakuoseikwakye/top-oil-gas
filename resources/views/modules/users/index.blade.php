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
                    <div class="card-toolbar">

                        <!--begin::Button-->
                        <a data-toggle="modal" data-target="#add-user-modal" class="btn btn-primary font-weight-bolder">
                            Add Admin User</a>
                        <!--end::Button-->
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">All users</span>
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
@include('modules.users.modals.add_user')
@include('modules.users.modals.update_user')
@include('modules.users.modals.ass_priv')
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
                    return full['fname'] +' '+full['lname'];
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
            {
                data: null,
                defaultContent: `

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-info btn-sm assign-btn'>
                   <i class='fas fa-eye'></i>
                </button>

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-success btn-sm edit-btn'>
                   <i class='fas fa-edit'></i>
                </button>

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-danger btn-sm delete-btn'>
                   <i class='fas fa-trash'></i>
                </button>
                
                `
            },
            {
                data: "transid"
            },
        ],
        "columnDefs": [{
            "targets": [5],
            "visible": false
        }, ],
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

    var privTable = $('#priv-table').DataTable({
        dom: 'Bfrtip',
        // ajax: {
        //     url: `${APP_URL}/api/users/fetch_privileges`,
        //     type: "GET"

        // },
        ordering: false,
        order: [],
        processing: true,
        columns: [
            {
                data: "mod_id"
            },
            {
                data: "mod_name"
            },
            {
                data: "read"
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

    $("#user-table").on("click", ".edit-btn", function () {
        let data = userTable.row($(this).parents('tr')).data();

        $("#update-user-modal").modal("show");
        $("#update-user-transid").val(data.transid);
        $("#update-user-usertype").val(data.usertype).trigger('change');
        $("#update-user-phone").val(data.phone);
        $("#update-user-email").val(data.email);
        $("#update-user-fname").val(data.fname);
        $("#update-user-lname").val(data.lname);
    });

    $("#user-table").on("click", ".assign-btn", function () {
        let data = userTable.row($(this).parents('tr')).data();

        $("#assPrivModal").modal("show");
        privTable.ajax.url(`${APP_URL}/api/users/fetch_privileges/${data.email}`).load()
    });

    $("#user-table").on("click", ".delete-btn", function () {
        var data = userTable.row($(this).parents("tr")).data();
        var formdata = new FormData()
        formdata.append("transid", data.transid);
        formdata.append("createuser", "admin");

        Swal.fire({
            title: "",
            text: "Are you sure you want to delete user?",
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
                fetch(`${APP_URL}/api/users/delete`, {
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
                        text: "User deleted successfully",
                        icon: "success"
                    });
                    userTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Deleting user failed"
                        });
                    }
                })
            }
        })

    });
</script>


@endsection
