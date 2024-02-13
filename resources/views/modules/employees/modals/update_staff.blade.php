<!-- Modal-->
<div class="modal fade" id="update-staff-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-staff-form" enctype="multipart/form-data">
                    @csrf

                    <input type="text" name="transid" id="update-staff-transid" required hidden>
                    <input type="text" name="staff" id="update-staff-number" required hidden>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstName" placeholder="John" class="form-control form-control-sm"
                                id="update-staff-fname">
                        </div>
                        <div class="col">
                            <label for="">Middle Name</label>
                            <input type="text" name="middleName" class="form-control form-control-sm"
                                id="update-staff-mname">
                        </div>
                        <div class="col">
                            <label for="">Surname <span class="text-danger">*</span></label>
                            <input type="text" name="lastName" placeholder="Doe" class="form-control form-control-sm"
                                id="update-staff-lname" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control select2" id="update-staff-gender" required>
                                <option value="m">Male</option>
                                <option value="f">Female</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" placeholder="john@example.com"
                                class="form-control form-control-sm" id="update-staff-email" required>
                        </div>
                        <div class="col">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="text" min="10" name="phoneNumber" placeholder="0555555555"
                                class="form-control form-control-sm" id="update-staff-phone" required>
                        </div>
                    </div>

                    

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">GPS Address </label>
                            <input type="text" name="gpsaddress" class="form-control form-control-sm"
                                id="update-staff-gps" >
                        </div>
                        <div class="col">
                            <label for="">Street Name </label>
                            <input type="text" name="streetName" class="form-control form-control-sm"
                                id="update-staff-streetname" >
                        </div>
                        <div class="col">
                            <label for="">Landmark </label>
                            <input type="text" name="landmark" class="form-control form-control-sm"
                                id="update-staff-landmark" >
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Region </label>
                            <select name="region" class="form-control select2" id="update-staff-region" >
                                <option value="Oti Region">Oti Region</option>
                                <option value="Bono East Region">Bono East</option>
                                <option value="Ahafo Region">Ahafo</option>
                                <option value="Bono Region">Bono</option>
                                <option value="North East Region">North East</option>
                                <option value="Savannah Region">Savannah</option>
                                <option value="Western North Region">Western North</option>
                                <option value="Western Region">Western</option>
                                <option value="Volta Region">Volta</option>
                                <option value="Greater Accra Region">Greater Accra</option>
                                <option value="Eastern Region">Eastern</option>
                                <option value="Ashanti Region">Ashanti</option>
                                <option value="Central Region">Central</option>
                                <option value="Northern Region">Northern</option>
                                <option value="Upper East Region">Upper East</option>
                                <option value="Upper West Region">Upper West</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Town </label>
                            <input type="text" name="town" class="form-control form-control-sm" id="update-staff-town"
                                >
                        </div>
                        <div class="col">
                            <label for="">Employment Date</label>
                            <input type="date" name="empdate" class="form-control form-control-sm"
                                id="update-staff-address" >
                        </div>
                    </div>

                    <div class="row mt-3">

                        <div class="col">
                            <label for="">Date Of Birth </label>
                            <input type="date" name="dateOfBirth" class="form-control form-control-sm"
                                id="update-staff-dob" required>
                        </div>

                        <div class="col">
                            <label for="">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-control select2" id="update-staff-role"
                                required>
                              @foreach ($role as $item)
                                  <option value="{{$item->description}}">{{$item->description}}</option>
                              @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="update-staff-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var updateStaff = document.getElementById("update-staff-form");

    $(updateStaff).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(updateStaff)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to add employee?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Submit'

        }).then((result) => {

            if (result.value) {
                Swal.fire({
                    text: "Updating...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/employees/update`, {
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
                        text: "Employee updated  successfully",
                        icon: "success"
                    });
                    $("#update-staff-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    employeesTable.ajax.reload(false, null);
                    updateStaff.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Adding customer failed"
                        });
                    }
                })
            }
        })
    });

</script>
