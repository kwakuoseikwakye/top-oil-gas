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
                    <div class="card-toolbar">

                        <!--begin::Button-->
                        <a data-toggle="modal" data-target="#add-vendor-modal"
                            class="btn btn-primary font-weight-bolder">
                            Add Vendor</a>
                        <!--end::Button-->
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">Active Vendors</span>
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
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
@include('modules.vendor.modals.add_vendor')
@include('modules.vendor.modals.update_vendor')
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

    $("#vendor-table").on("click", ".edit-btn", function () {
        let data = vendorTable.row($(this).parents('tr')).data();

        $("#edit-vendor-modal").modal("show");
        $("#edit-vendor-number").val(data.code);
        $("#edit-vendor-fname").val(data.fname);
        $("#edit-vendor-mname").val(data.mname);
        $("#edit-vendor-lname").val(data.lname);
        $("#edit-vendor-town").val(data.town);
        $("#edit-vendor-gender").val(data.gender_lower).trigger('change');
        $("#edit-vendor-email").val(data.email);
        $("#edit-vendor-phone").val(data.phone);
        $("#edit-vendor-gpsaddress").val(data.gpsaddress);
        $("#edit-vendor-streetname").val(data.streetname);
        $("#edit-vendor-landmark").val(data.landmark);
        $("#edit-vendor-region").val(data.region).trigger('change');
        $("#edit-vendor-long").val(data.long);
        $("#edit-vendor-lat").val(data.lat);
        $("#edit-vendor-idtype").val(data.idtype).trigger('change');
        $("#edit-vendor-idno").val(data.idno);
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

    $("#vendor-table").on("click", ".delete-btn", function () {
        var data = vendorTable.row($(this).parents("tr")).data();
        var formdata = new FormData()
        formdata.append("transid", data.transid);
        formdata.append("createuser", "admin");

        data.transid

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
                fetch(`${APP_URL}/api/vendor/delete`, {
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
                        text: "Vendor deleted successfully",
                        icon: "success"
                    });
                    vendorTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Deleting vendor failed"
                        });
                    }
                })
            }
        })

    });
</script>


@endsection