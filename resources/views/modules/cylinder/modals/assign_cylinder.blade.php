<!-- Modal-->
<div class="modal fade" id="assign-cylinder-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Assign Cylinder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="assign-cylinder-form" enctype="multipart/form-data">
                    @csrf

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Customer<span class="text-danger">*</span></label>
                            <select name="customerNo" class="form-control select2" id="assign-customer-cust" required>
                                @foreach ($customer as $item)
                                <option value="{{$item->custno}}">{{$item->fname}} {{$item->mname}} {{$item->lname}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Cylinder<span class="text-danger">*</span></label>
                            <select name="cylinderNo" class="form-control select2" id="assign-customer-cylinder"
                                required>
                                @foreach ($cylinder as $item)
                                <option value="{{$item->transid}}">{{$item->cylcode}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Vendor <span class="text-danger">*</span></label>
                            <select name="vendorNo" class="form-control select2" id="assign-customer-vendor" required>
                                @foreach ($vendor as $item)
                                <option value="{{$item->vendor_no}}">{{$item->fname}} {{$item->mname}} {{$item->lname}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="assign-cylinder-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var assignCylinder = document.getElementById("assign-cylinder-form");

    $(assignCylinder).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(assignCylinder)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to assign cylinder?',
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
                fetch(`${APP_URL}/api/cylinder/assign`, {
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
                        text: "Cylinder assigned successfully",
                        icon: "success"
                    });
                    $("#assign-cylinder-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    custCylinderTable.ajax.reload(false, null);
                    assignCylinder.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Assigning cylinder failed"
                        });
                    }
                })
            }
        })
    });

</script>
