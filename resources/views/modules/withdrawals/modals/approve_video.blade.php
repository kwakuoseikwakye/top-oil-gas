<!-- Modal-->
<div class="modal fade" id="approve-video-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="min-width:65%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Approve Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="approve-video-form">
                    @csrf
                    <input type="text" name="transid" id="approve-video-transid" required hidden>
                </form>
                <div class="embed-responsive embed-responsive-21by9 bg-dark">
                    <video autoplay loop controls id="parent-video-tag">
                        <source id="approved-video">
                    </video>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="approve-video-form"
                    class="btn btn-primary font-weight-bold">Approve</button>
            </div>
        </div>
    </div>
</div>
<script>
    var approveVideoForm = document.getElementById("approve-video-form");

    $(approveVideoForm).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(approveVideoForm)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to approve this video?',
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
                fetch(`${APP_URL}/api/celebRequests/approve_video`, {
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
                        text: "Video approved  successfully",
                        icon: "success"
                    });
                    $("#approve-video-modal").modal('hide');
                    requestTable.ajax.reload(false, null);
                    todayRequestTable.ajax.reload(false, null);
                    approveVideoForm.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Approving failed"
                        });
                    }
                })
            }
        })
    });

</script>