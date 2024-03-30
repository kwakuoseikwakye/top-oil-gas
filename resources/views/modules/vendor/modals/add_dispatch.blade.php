<div class="modal fade" id="add-dispatch-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Dispatch Cylinder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-dispatch-form" enctype="multipart/form-data">
                    @csrf

                    <input type="text" name="order_id" id="dispatch-cylinder-orderid" required hidden>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Vendor <span class="text-danger">*</span></label>
                            <select name="vendor" class="form-control select2" id="add-dispatch-vendor" required>
                                <option value="">--Select--</option>
                                @foreach ($vends as $item)
                                    <option value="{{ $item->vendor_no }}">{{ $item->fname }} {{ $item->mname }}
                                        {{ $item->lname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">Close</button>
                <button type="submit" form="add-dispatch-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#add-dispatch-modal').on('shown.bs.modal', function() {
        $('#dispatch-customer-cyl').focus();
    })
    var addDispatch = document.getElementById("add-dispatch-form");

    $(addDispatch).submit(function(e) {
        e.preventDefault();

        var formdata = new FormData(addDispatch)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want vendor to dispatch cylinder?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Submit'

        }).then((result) => {

            if (result.value) {
                Swal.fire({
                    text: "Processing...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/warehouse/add_dispatch`, {
                    method: "POST",
                    body: formdata,
                }).then(function(res) {
                    return res.json()
                }).then(function(data) {
                    if (!data.ok) {
                        Swal.fire({
                            text: data.msg,
                            icon: "error"
                        });
                        return;
                    }
                    Swal.fire({
                        text: "Cylinder dispatched  successfully",
                        icon: "success"
                    });
                    // $("#add-dispatch-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    dispatchTable.ajax.reload(false, null);
                    addDispatch.reset();

                }).catch(function(err) {
                    if (err) {
                        Swal.fire({
                            text: "Dispatch failed"
                        });
                    }
                })
            }
        })
    });
</script>
