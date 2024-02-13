<div class="modal fade" id="return-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Return Cylinder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="return-form">
                    @csrf

                    <input type="text" name="transid" class="form-control" id="return-transid" hidden return>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Cylinder <span class="text-danger">*</span></label>
                            <select name="cylinder" class="form-control select2" id="return-cylinder" required>
                                @foreach ($cylinders as $item)
                                <option value="{{$item->cylcode}}">Barcode({{$item->barcode}}) -
                                    CylCode({{$item->cylcode}}) -
                                    Size({{$item->size}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Vendor <span class="text-danger">*</span></label>
                            <select name="vendor" class="form-control select2" id="return-vendor" required>
                                @foreach ($vendor as $item)
                                <option value="{{$item->vendor_no}}">{{$item->fname}} {{$item->mname}}
                                    {{$item->lname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Size <span class="text-danger">*</span></label>
                            <input type="number" min="1" name="size" class="form-control form-control-sm"
                                id="return-size" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Empty/Full <span class="text-danger">*</span></label>
                            <select name="emptyFull" class="form-control select2" id="return-full" required>
                                <option value="">--Select--</option>
                                <option value="empty">Empty</option>
                                <option value="full">Full</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Returned To <span class="text-danger">*</span></label>
                            <select name="staff" class="form-control select2" id="return-staff" required>
                                <option value="">--Select--</option>
                                @foreach ($staff as $item)
                                <option value="{{$item->wcode}}">{{$item->wname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="return-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    $("#add-celeb-cat").on("select2:select", function (e) {
        let selectedCat = e.params.data.id;

    })
    var returnCylinder = document.getElementById("return-form");

    $(returnCylinder).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(returnCylinder)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to return cylinder?',
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
                fetch(`${APP_URL}/api/warehouse/return_cylinder`, {
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
                        text: "Cylinder returned  successfully",
                        icon: "success"
                    });
                    $("#return-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    returnTable.ajax.reload(false, null);
                    dispatchTable.ajax.reload(false, null);
                    returnCylinder.reset();

                }).catch(function (err) {
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
