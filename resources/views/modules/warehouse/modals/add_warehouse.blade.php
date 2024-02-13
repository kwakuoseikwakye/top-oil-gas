<!-- Modal-->
<div class="modal fade" id="add-warehouse-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Warehouse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-warehouse-form" enctype="multipart/form-data">
                    @csrf

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Warehouse <span class="text-danger">*</span></label>
                            <input type="text" name="wname" placeholder="John" class="form-control form-control-sm"
                                id="add-warehouse-fname" required>
                        </div>
                        <div class="col">
                            <label for="">Street Name <span class="text-danger">*</span></label>
                            <input type="text" name="streetname" class="form-control form-control-sm"
                                id="add-warehouse-streetname" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" placeholder="john@example.com"
                                class="form-control form-control-sm" id="add-warehouse-email" required>
                        </div>
                        <div class="col">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="text" min="10" name="phoneNumber" placeholder="0555555555"
                                class="form-control form-control-sm" id="add-warehouse-phone" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Region <span class="text-danger">*</span></label>
                            <select name="region" class="form-control select2" id="add-warehouse-region" required>
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
                            <input type="text" name="town" class="form-control form-control-sm" id="add-warehouse-town"
                                >
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">GPS Address </label>
                            <input type="text" name="gpsaddress" class="form-control form-control-sm"
                                id="add-warehouse-gps" >
                        </div>
                      
                        <div class="col">
                            <label for="">Landmark </label>
                            <input type="text" name="landmark" class="form-control form-control-sm"
                                id="add-warehouse-landmark" >
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="add-warehouse-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var addWarehouse = document.getElementById("add-warehouse-form");

    $(addWarehouse).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(addWarehouse)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to add warehouse?',
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
                fetch(`${APP_URL}/api/warehouse`, {
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
                        text: "Warehouse added  successfully",
                        icon: "success"
                    });
                    $("#add-warehouse-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    warehouseTable.ajax.reload(false, null);
                    addWarehouse.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Adding warehouse failed"
                        });
                    }
                })
            }
        })
    });

</script>
