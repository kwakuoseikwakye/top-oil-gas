@extends('layout.main')
@section('pageName', 'One Time Password')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">One Time Password
                        </h3>
                    </div>
                </div>

                <div class="card-body">
                    <form class="md-float-material form-material" id="sendOTPForm">
                        <input type="text" name="modID" hidden value="{{ session('modID') }}">
                        <input type="text" name="modName" hidden value="{{ session('modName') }}">
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="text-center"><i
                                                class="icofont icofont-lock text-primary f-80"></i></h3>
                                    </div>
                                </div>

                                <div class="form-group form-primary">
                                    <label class="float-label">Enter OTP</label>
                                    <input type="text" name="otp" autofocus class="form-control" required="">
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" form="sendOTPForm"
                                            class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20"><i
                                                class="icofont icofont-lock"></i> Continue </button>
                                    </div>
                                </div>
                                <p class="text-inverse text-right"><a href="#" id="resend-otp-btn" data-mod-id="{{ session('modID') }}" data-mod-name="{{ session('modName') }}">Resend Code </a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>

<script>
    const sendOTPForm = document.forms["sendOTPForm"];

    $(sendOTPForm).submit(function(e) {
        e.preventDefault();

        var formdata = new FormData(sendOTPForm)
        formdata.append("createuser", CREATEUSER);

        Swal.fire({
            text: "Verifying",
            showConfirmButton: false,
            allowEscapeKey: false,
            allowOutsideClick: false
        });
        fetch(`${APP_URL}/otp/verify`, {
            method: "POST",
            body: formdata,
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
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
                text: "OTP verified",
                icon: "success",
                showConfirmButton: false,
                timer: 2000,
            });
            sendOTPForm.reset();
            window.location.href = data.data.url;
        }).catch(function(err) {
            if (err) {
                Swal.fire({
                    icon: "error",
                    text: "Oops! An error occured while adding record, please contact admin ):"
                });
            }
        })
    });

    document.getElementById("resend-otp-btn").addEventListener("click", function(e) {
        e.preventDefault();
        
        Swal.fire({
            text: "Resending OTP",
            showConfirmButton: false,
            allowEscapeKey: false,
            allowOutsideClick: false
        });

        let formdata = new FormData();
        formdata.append("modID", e.target.dataset.modId);
        formdata.append("modName", e.target.dataset.modName);

        fetch(`${APP_URL}/otp/resend`, {
            method: "POST",
            body: formdata,
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
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
                text: data.msg,
                icon: "success",
                showConfirmButton: false,
                timer: 2000,
            });
        }).catch(function(err) {
            if (err) {
                Swal.fire({
                    icon: "error",
                    text: "Oops! An error occured resending OTP, please contact admin ):"
                });
            }
        })
    });

</script>

@endsection
