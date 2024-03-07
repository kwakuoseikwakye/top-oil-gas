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
                    <div class="card-toolbar">

                        <!--begin::Button-->
                        <a data-toggle="modal" data-target="#add-staff-modal"
                            class="btn btn-primary font-weight-bolder">
                            Add Employee</a>
                        <!--end::Button-->
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">Active Employees</span>
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
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
@include('modules.employees.modals.add_staff')
@include('modules.employees.modals.update_staff')
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
                rel='tooltip' class='btn btn-success btn-sm edit-btn'>
                   <i class='fas fa-edit'></i>
                </button>

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-danger btn-sm delete-btn'>
                   <i class='fas fa-trash'></i>
                </button>
                
                `
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
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

    $("#employees-table").on("click", ".edit-btn", function () {
        let data = employeesTable.row($(this).parents('tr')).data();

        $("#update-staff-modal").modal("show");
        $("#update-staff-transid").val(data.transid);
        $("#update-staff-number").val(data.code);
        $("#update-staff-fname").val(data.fname);
        $("#update-staff-mname").val(data.mname);
        $("#update-staff-lname").val(data.lname);
        $("#update-staff-town").val(data.town);
        $("#update-staff-email").val(data.email);
        $("#update-staff-phone").val(data.phone);
        $("#update-staff-dob").val(data.dob);
        $("#update-staff-role").val(data.role);
        $("#update-staff-gps").val(data.gps);
        $("#update-staff-streetname").val(data.streetname);
        $("#update-staff-landmark").val(data.landmark);
        $("#update-staff-region").val(data.region).trigger('change');
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

    $("#employees-table").on("click", ".delete-btn", function () {
        var data = employeesTable.row($(this).parents("tr")).data();
        var formdata = new FormData()
        formdata.append("transid", data.transid);
        formdata.append("createuser", "admin");

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
                fetch(`${APP_URL}/api/employees/delete`, {
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
                        text: "Employee deleted successfully",
                        icon: "success"
                    });
                    employeesTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Deleting employees failed"
                        });
                    }
                })
            }
        })

    });

</script>


@endsection
