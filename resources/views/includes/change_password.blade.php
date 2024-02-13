<!-- Modal-->
<div class="modal fade" id="password-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="password-reset-form">
                    @csrf
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control form-control-sm" autocomplete="current-password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control form-control-sm" autocomplete="new-password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control form-control-sm" autocomplete="old-password" required>
                    </div>
                   
                    {{-- Feedback --}}
                    <div>
                        <div id="change-password-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary shadow" form="password-reset-form" type="reset">Clear</button>
                    <button class="btn btn-outline-primary shadow" form="password-reset-form" type="submit">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    const USERID = "{{Auth::user()->transid}}";
    const feedbackHolder = document.getElementById("change-password-feedback");
    feedbackHolder.innerHTML = null;

    const passwordResetForm = document.forms["password-reset-form"];
    passwordResetForm.addEventListener("submit", function(e) {
        let resetForm = new FormData(passwordResetForm);
        resetForm.append('userid',USERID);
        feedbackHolder.innerHTML = null;
        e.preventDefault();
        let currentPassword = this.current_password.value.trim();
        let newPassword = this.new_password.value.trim();
        let confirmPassword = this.confirm_password.value.trim();
        if (
            currentPassword.length < 1 ||
            newPassword.length < 1 ||
            confirmPassword.length < 1
        ) {
            feedbackHolder.innerHTML = `
            <p class="text-danger mt-2">
                <i class="fa fa-exclamation-triangle mr-1 ml-1"></i>
                Please complete all fields
            </p>`;
            return;
        }

        if (newPassword !== confirmPassword) {
            feedbackHolder.innerHTML = `
            <p class="text-danger mt-2">
                <i class="fa fa-exclamation-triangle mr-1 ml-1"></i>
                Your passwords do not match
            </p>`;
            return;
        }

        feedbackHolder.innerHTML = `
            <p class="text-info mt-2">
                <i class="fa fa-exclamation-triangle mr-1 ml-1"></i>
                Processing please wait...
            </p>`;

        fetch(`${APP_URL}/api/reset_password`, {
            method: "POST",
            body: resetForm,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        }).then(function(res) {
            return res.json();
        }).then(function(payload) {
            if (!payload.ok) {
                feedbackHolder.innerHTML = `
                <p class="text-danger mt-2">
                    <i class="fa fa-exclamation-triangle mr-1 ml-1"></i>
                    ${payload.msg}
                </p>`;
                return;
            }

            feedbackHolder.innerHTML = `
            <p class="text-success mt-2">
                <i class="fa fa-check mr-1 ml-1"></i>
                Password successfully changed
            </p>`;
            passwordResetForm.reset();
            setTimeout(() => {
                feedbackHolder.innerHTML = null;
            }, 3000);
            return;

        }).catch(function(err) {
            console.log(err);
            feedbackHolder.innerHTML = `
            <p class="text-danger mt-2">
                <i class="fa fa-exclamation-triangle mr-1 ml-1"></i>
                An internal error occured.
            </p>`;
            return;
        });
    });
</script>
