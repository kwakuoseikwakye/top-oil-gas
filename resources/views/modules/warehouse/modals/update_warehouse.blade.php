<!-- Modal-->
<div class="modal fade" id="update-warehouse-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Warehouse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-warehouse-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="wcode" id="update-warehouse-code">
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Warehouse <span class="text-danger">*</span></label>
                            <input type="text" name="wname" placeholder="John" class="form-control form-control-sm"
                                id="update-warehouse-name">
                        </div>
                        <div class="col">
                            <label for="">Street Name <span class="text-danger">*</span></label>
                            <input type="text" name="streetname" class="form-control form-control-sm"
                                id="update-warehouse-streetname" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" placeholder="john@example.com"
                                class="form-control form-control-sm" id="update-warehouse-email" required>
                        </div>
                        <div class="col">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="text" min="10" name="phoneNumber" placeholder="0555555555"
                                class="form-control form-control-sm" id="update-warehouse-phone" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Region <span class="text-danger">*</span></label>
                            <select name="region" class="form-control select2" id="update-warehouse-region" required>
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
                            <input type="text" name="town" class="form-control form-control-sm" id="update-warehouse-town"
                                required>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">GPS Address <span class="text-danger">*</span></label>
                            <input type="text" name="gpsaddress" class="form-control form-control-sm"
                                id="update-warehouse-gpsaddress" required>
                        </div>
                      
                        <div class="col">
                            <label for="">Landmark <span class="text-danger">*</span></label>
                            <input type="text" name="landmark" class="form-control form-control-sm"
                                id="update-warehouse-landmark" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="update-warehouse-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var updateWarehouse = document.getElementById("update-warehouse-form");

    $(updateWarehouse).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(updateWarehouse)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to update warehouse?',
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
                fetch(`${APP_URL}/api/warehouse/update`, {
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
                        text: "Warehouse updated  successfully",
                        icon: "success"
                    });
                    $("#update-warehouse-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    warehouseTable.ajax.reload(false, null);
                    updateWarehouse.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Updating warehouse failed"
                        });
                    }
                })
            }
        })
    });

</script>
