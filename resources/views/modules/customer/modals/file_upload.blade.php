<div class="modal fade" id="file-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title">Upload Customer Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="import-form" enctype="multipart/form-data">
                    @csrf
                    <div class="col mb-2">
                        <span class="text-danger font-weight-bold">Please download excel before upload</span>
                    </div>
                    <div class="row">
                        <div class="col-12" id="load-feed"></div>
                    </div>
                    <div class="col mt-3">
                        <label for="">Choose Excel File</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="col mt-3">
                        <a href="{{asset('excel/customer_list.xlsx')}}" download="" class="btn btn-block btn-success" name="submit" form="import-form" type="submit"><i
                                class="fa fa-download"></i>Download Excel File</a>
                    </div>
                    <br>
                    {{-- <a class="btn btn-warning" href="{{ route('export') }}">Export User Data</a> --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
                <button type="reset" form="import-form" class="btn btn-light btn-sm">Reset</button>
                <button type="submit" form="import-form" class="btn btn-primary btn-sm">Upload</button>
            </div>
        </div>
    </div>
</div>
<script>
    var importForm = document.getElementById("import-form");
    // var spin = document.getElementById("spin");
    const loadFeeds = document.getElementById("load-feed");

    $(importForm).submit(function (e) {
        e.preventDefault();

        // spin.style.display = "block";
        loadFeeds.innerHTML = "<span class='text-info'>Uploading please wait....</span>"

        var formdata = new FormData(importForm)
        fetch(`${APP_URL}/import_customer`, {
            method: "POST",
            body: formdata,
        }).then(function (res) {
            return res.json()
        }).then(function (data) {
            // spin.style.display = "none";

            if (!data.ok) {
                Swal.fire({
                    title: data.msg,
                    text: data.fatal,
                    icon: "error"
                });
                return;
            }
            loadFeeds.innerHTML =
                "<p class='alert alert-info p-1'>Upload Successful</p>";
            // studentTable.ajax.reload(false, null);
            setTimeout(() => {
                loadFeeds.innerHTML = null;
            }, 2000);
            setTimeout(() => {
                $("#file-modal").modal('hide');
            }, 1000);

        }).catch(function (err) {
            if (err) {
                Swal.fire({
                    text: "Importing failed",
                    icon: "error"
                });
            }
        })
    });

</script>
