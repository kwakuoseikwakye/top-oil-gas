<!-- Modal-->
<div class="modal fade" id="update-cylinder-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Cylinder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-cylinder-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="transid" id="update-cylinder-number">

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Owner<span class="text-danger">*</span></label>
                            <select name="owner" class="form-control select2" id="update-cylinder-owner" required>
                                <option value="Topoil">Topoil</option>
                                <option value="Customer">Customer</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Cylinder Code <span class="text-danger">*</span></label>
                            <input type="text" name="cylcode" class="form-control form-control-sm"
                                id="update-cylinder-code" required>
                        </div>
                        <div class="col">
                            <label for="">Barcode</label>
                            <input type="text" name="barcode" class="form-control form-control-sm"
                                id="update-cylinder-barcode">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Size  <span class="text-danger">*</span></label>
                            <input type="text" name="size" class="form-control form-control-sm"
                                id="update-cylinder-size">
                        </div>
                        <div class="col">
                            <label for="">Weight</label>
                            <select name="weight" id="update-cylinder-weight" class="form-control select2">
                                @foreach ($weights as $item)
                                <option value="{{$item->id}}">{{$item->weight}} - GHS {{$item->amount}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Amount </label>
                            <input type="number" step="0.2" name="amount" class="form-control form-control-sm"
                                id="update-cylinder-amount" >
                        </div>
                        <div class="col">
                            <label for="">Image </label>
                            <input type="file" name="image[]" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Notes</label>
                            <textarea name="notes" id="update-cylinder-notes" class="form-control" cols="30" rows="7"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="update-cylinder-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var updateCylinder = document.getElementById("update-cylinder-form");

    $(updateCylinder).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(updateCylinder)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to update cylinder?',
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
                fetch(`${APP_URL}/api/cylinder/update`, {
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
                        text: "Cylinder updated successfully",
                        icon: "success"
                    });
                    $("#update-cylinder-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    cylinderTable.ajax.reload(false, null);
                    updateCylinder.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Updating cylinder failed"
                        });
                    }
                })
            }
        })
    });

</script>
