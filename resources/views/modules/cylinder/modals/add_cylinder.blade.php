<!-- Modal-->
<div class="modal fade" id="add-cylinder-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Cylinder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-cylinder-form" enctype="multipart/form-data">
                    @csrf

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Owner<span class="text-danger">*</span></label>
                            <select name="owner" class="form-control" required id="add-cylinder-owner">
                                <option value="Petrocell">Petrocell</option>
                                <option value="Customer">Customer</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Cylinder Code <span class="text-danger">*</span></label>
                            <input type="text" name="cylcode" class="form-control form-control-sm"
                                required>
                        </div>
                        <div class="col">
                            <label for="">Barcode</label>
                            <input type="text" name="barcode" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Size  <span class="text-danger">*</span></label>
                            <input type="number" name="size" class="form-control form-control-sm"
                                id="add-cylinder-size">
                        </div>
                        <div class="col">
                            <label for="">Weight </label>
                            <input name="weight" type="number" step="0.2" class="form-control" id="add-cylinder-weight">

                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Amount</label>
                            <input type="number" step="0.2" name="amount" class="form-control form-control-sm"
                                id="add-cylinder-amount">
                        </div>
                        <div class="col">
                            <label for="">Image </label>
                            <input type="file" name="image[]" multiple class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Notes</label>
                            <textarea name="notes" id="add-cylinder-notes" class="form-control" cols="30" rows="7"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-warning font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="add-cylinder-form" class="btn btn-warning font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var addCylinder = document.getElementById("add-cylinder-form");

    $(addCylinder).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(addCylinder)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to add cylinder?',
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
                fetch(`${APP_URL}/api/cylinder`, {
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
                        text: "Cylinder added successfully",
                        icon: "success"
                    });
                    $("#add-cylinder-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    cylinderTable.ajax.reload(false, null);
                    addCylinder.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Adding cylinder failed"
                        });
                    }
                })
            }
        })
    });

</script>
