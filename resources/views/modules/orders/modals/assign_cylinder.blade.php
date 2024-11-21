<!-- Modal-->
<div class="modal fade" id="assign-cylinder-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Assign Cylinder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="assign-cylinder-form">
                    @csrf

                    <input type="text" hidden name="order_id" id="assign-cylinder-transid" required >
                    <input type="text" hidden name="custno" id="assign-cylinder-custno" required >
                    <input type="text" hidden name="order_number" id="assign-cylinder-orderid" required >
                    <div class="row">
                        <div class="col">
                            <label for="">Cylinder</label>
                            <select name="cylinder_code[]" multiple class="form-control selectpicker" data-live-search="true" id="assign-customer-cylinder" required>
                                <option value="">--Select--</option>

                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">Close</button>
                <button type="submit" form="assign-cylinder-form"
                    class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
