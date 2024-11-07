<!-- Modal-->
<div class="modal fade" id="add-customer-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-customer-form" enctype="multipart/form-data">
                    @csrf

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="fname" placeholder="John" class="form-control form-control-sm"
                                id="add-customer-fname">
                        </div>
                        <div class="col">
                            <label for="">Surname <span class="text-danger">*</span></label>
                            <input type="text" name="lname" placeholder="Doe" class="form-control form-control-sm"
                                id="add-customer-lname" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="text" min="10" name="phone" placeholder="0555555555"
                                class="form-control form-control-sm" id="add-customer-phone" required>
                        </div>
                        <div class="col">
                            <label for="">Picture </label>
                            <input type="file" name="image" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">ID Type </label>
                            <select name="id_type" class="form-control form-control-sm select-input" id="add-customer-idtype">
                                <option value="Ghana Card">Ghana Card</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="">ID No. </label>
                            <input type="text" name="id_no" id="add-customer-idno"
                                class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Upload ID Card</label>
                            <input type="file" accept="jpeg/png" name="idimage" class="form-control form-control-sm">
                        </div>
                        <div class="col">
                            <label for="">Agent Code</label>
                            <input type="text" name="agent_code" class="form-control form-control-sm">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="add-customer-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    

</script>
