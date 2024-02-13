<!-- Modal-->
<div class="modal fade" id="edit-customer-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-customer-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="custno" id="edit-customer-number">
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstName" placeholder="John" class="form-control form-control-sm"
                                id="edit-customer-fname">
                        </div>
                        <div class="col">
                            <label for="">Middle Name</label>
                            <input type="text" name="middleName" class="form-control form-control-sm"
                                id="edit-customer-mname">
                        </div>
                        <div class="col">
                            <label for="">Surname <span class="text-danger">*</span></label>
                            <input type="text" name="lastName" placeholder="Doe" class="form-control form-control-sm"
                                id="edit-customer-lname" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control select2" id="edit-customer-gender" required>
                                <option value="m">Male</option>
                                <option value="f">Female</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" placeholder="john@example.com"
                                class="form-control form-control-sm" id="edit-customer-email" required>
                        </div>
                        <div class="col">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="text" min="10" name="phoneNumber" placeholder="0555555555"
                                class="form-control form-control-sm" id="edit-customer-phone" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Title <span class="text-danger">*</span></label>
                            <select name="title" class="form-control select2" id="edit-customer-title" required>
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                                <option value="Ms">Ms</option>
                                <option value="Dr">Dr</option>
                                <option value="Alhaji">Alhaji</option>
                                <option value="Hajia">Hajia</option>
                                <option value="Prof">Prof</option>
                            </select>
                        </div>


                        <div class="col">
                            <label for="">Date Of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dateOfBirth" placeholder="Doe" class="form-control form-control-sm"
                                id="edit-customer-dob" required>
                        </div>

                        <div class="col">
                            <label for="">Marital Status <span class="text-danger">*</span></label>
                            <select name="maritalStatus" class="form-control select2" id="edit-customer-marital"
                                required>
                                <option value="married">Married</option>
                                <option value="single">Single</option>
                                <option value="divorced">Divorced</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">GPS Address <span class="text-danger">*</span></label>
                            <input type="text" name="gpsaddress" class="form-control form-control-sm"
                                id="edit-customer-gps" required>
                        </div>
                        <div class="col">
                            <label for="">Street Name <span class="text-danger">*</span></label>
                            <input type="text" name="streetName" class="form-control form-control-sm"
                                id="edit-customer-streetname" required>
                        </div>
                        <div class="col">
                            <label for="">Landmark <span class="text-danger">*</span></label>
                            <input type="text" name="landmark" class="form-control form-control-sm"
                                id="edit-customer-landmark" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Region <span class="text-danger">*</span></label>
                            <select name="region" class="form-control select2" id="edit-customer-region" required>
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
                            <label for="">Town <span class="text-danger">*</span></label>
                            <input type="text" name="town" class="form-control form-control-sm" id="edit-customer-town"
                                required>
                        </div>
                        <div class="col">
                            <label for="">Home Address <span class="text-danger">*</span></label>
                            <input type="text" name="homeAddress" class="form-control form-control-sm"
                                id="edit-customer-address" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Occupation </label>
                            <input type="text" name="occupation" class="form-control form-control-sm"
                                id="edit-customer-occupation">
                        </div>
                        <div class="col">
                            <label for="">Longitude</label>
                            <input type="text" name="longitude" class="form-control form-control-sm"
                                id="edit-customer-long">
                        </div>

                        <div class="col">
                            <label for="">Latitude</label>
                            <input type="text" name="latitude" class="form-control form-control-sm"
                                id="edit-customer-lat" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">ID Type <span class="text-danger">*</span></label>
                            <select name="idType" class="form-control select2" id="edit-customer-idtype" required>
                                <option value="Voters Card">Voters Card</option>
                                <option value="Ghana Card">Ghana Card</option>
                                <option value="Passport">Passport</option>
                                <option value="Drivers Licence">Drivers Licence</option>
                                <option value="NHIS">NHIS</option>
                                <option value="SSNIT">SSNIT</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="">ID No. <span class="text-danger">*</span></label>
                            <input type="text" name="idNumber" id="edit-customer-idno"
                                class="form-control form-control-sm" required>
                        </div>
                        <div class="col">
                            <label for="">Upload ID Card</label>
                            <input type="file" accept="jpeg/png" name="idimage" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Place Of Birth</label>
                            <input type="text" name="placeOfBirth" placeholder="John"
                                class="form-control form-control-sm" id="edit-customer-pob">
                        </div>
                        <div class="col">
                            <label for="">Picture </label>
                            <input type="file" name="image" class="form-control form-control-sm">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="edit-customer-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var editCustomer = document.getElementById("edit-customer-form");

    $(editCustomer).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(editCustomer)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to edit customer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Submit'

        }).then((result) => {

            if (result.value) {
                Swal.fire({
                    text: "Saving changes...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/customer/update`, {
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
                        text: "Customer updated successfully",
                        icon: "success"
                    });
                    $("#edit-customer-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    customerTable.ajax.reload(false, null);
                    editCustomer.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Updating customer failed"
                        });
                    }
                })
            }
        })
    });

</script>
