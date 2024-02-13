<div class="modal fade" id="edit_personal_details" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Personal Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div id="profile-form-feedback"></div>
                </div>
                <form id="profile-update-form" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="admin_code" value="{{Auth::user()->transid }}" hidden>
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="fname" value="{{Auth::user()->fname }}" class="form-control"
                                    placeholder="John" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lname" value="{{Auth::user()->lname }}" class="form-control"
                                    placeholder="Doe" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Username</label>
                                <div class="">
                                    <input type="text" value="{{Auth::user()->username }}" class="form-control"
                                        name="oname" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" value="{{Auth::user()->email }}" name="email"
                                    placeholder="johndoe@example.com" readonly>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Mobile</label>
                                <input type="text" placeholder="023XXXXXXX" value="{{Auth::user()->phone}}" name="phone"
                                    class="form-control">
                            </div>
                        </div>

                        @csrf
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Profile Photo</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                    </div>
                </form>
                <button type="submit" form="profile-update-form" class="btn btn-outline-primary btn-block shadow">Save Changes</button>
            </div>
        </div>
    </div>


    <script>
        var adminProfileForm = document.getElementById("profile-update-form");
        const profileFeedBack = document.getElementById("profile-form-feedback");
        profileFeedBack.innerHTML = null;
        $(adminProfileForm).submit(function (e) {

            let profileFormData = new FormData(adminProfileForm);
            profileFormData.append('createuser', CREATEUSER);
            profileFeedBack.innerHTML = null;

            e.preventDefault();

            profileFeedBack.innerHTML = `
            <p class="text-info mt-2">
                <i class="fa fa-exclamation-triangle mr-1 ml-1"></i>
                Processing please wait...
            </p>`;
            fetch(`${APP_URL}/api/dashboard/profile_update`, {
                method: "POST",
                body: profileFormData,
                
            }).then(function (res) {
                return res.json()
            }).then(function (data) {
                if (!data.ok) {
                    profileFeedBack.innerHTML = `
            <p class="text-danger mt-2">
                <i class="fa fa-exclamation-triangle mr-1 ml-1"></i>
                ${data.msg}
            </p>`;
                    return;
                }
                profileFeedBack.innerHTML = `
            <p class="text-success mt-2">
                <i class="fa fa-check mr-1 ml-1"></i>
                Account updated successfully 
            </p>`;
                setTimeout(() => {
            window.location.reload(true)
                    profileFeedBack.innerHTML = null;
                }, 3000);
                return;

            });

        });

    </script>
