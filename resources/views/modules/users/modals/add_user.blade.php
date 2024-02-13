<!-- Modal-->
<div class="modal fade" id="add-user-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-user-form" enctype="multipart/form-data">
                    @csrf

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="fname" placeholder="John" class="form-control form-control-sm"
                                id="add-user-fname" required>
                        </div>
                    
                        <div class="col">
                            <label for="">Surname <span class="text-danger">*</span></label>
                            <input type="text" name="lname" placeholder="Doe" class="form-control form-control-sm"
                                id="add-user-lname" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" placeholder="john@example.com"
                                class="form-control form-control-sm" id="add-user-email" required>
                        </div>
                        <div class="col">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="text" min="10" name="phone" placeholder="0555555555"
                                class="form-control form-control-sm" id="add-user-phone" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Usertype <span class="text-danger">*</span></label>
                            <select name="usertype" class="form-control select2" id="add-user-usertype" required>
                                <option value="admin">Administrator</option>
                                <option value="warehouse">Warehouse</option>
                                <option value="staff">Employee/Staff</option>
                                <option value="vendor">Vendor</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Password</label>
                            <input type="text" name="password" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Picture</label>
                            <input type="file" name="image" class="form-control form-control-sm">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="add-user-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var addUser = document.getElementById("add-user-form");

$(addUser).submit(function (e) {
    e.preventDefault();

    var formdata = new FormData(addUser)
    formdata.append("createuser", CREATEUSER);
    Swal.fire({
        title: 'Are you sure you want to add user?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Submit'

    }).then((result) => {

        if (result.value) {
            Swal.fire({
                text: "Adding...",
                showConfirmButton: false,
                allowEscapeKey: false,
                allowOutsideClick: false
            });
            fetch(`${APP_URL}/api/users`, {
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
                    text: "User added  successfully",
                    icon: "success"
                });
                $("#add-user-modal").modal('hide');
                $("select").val(null).trigger('change');
                userTable.ajax.reload(false, null);
                addUser.reset();

            }).catch(function (err) {
                if (err) {
                    Swal.fire({
                        text: "Adding user failed"
                    });
                }
            })
        }
    })
});
</script>