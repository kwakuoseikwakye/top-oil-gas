@extends('layout.main')
@section('pageName', 'Messaging Centre')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-2">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Message Centre</h5>
                <!--end::Page Title-->

            </div>
            <!--end::Info-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">

                <!--begin::Daterange-->
                <a href="#" class="btn btn-sm btn-light font-weight-bold mr-2" id="kt_dashboard_daterangepicker"
                    data-toggle="tooltip" title="Select dashboard daterange" data-placement="left">
                    <span class="text-muted font-size-base font-weight-bold mr-2"
                        id="kt_dashboard_daterangepicker_title">Today</span>
                    <span class="text-primary font-size-base font-weight-bolder"
                        id="kt_dashboard_daterangepicker_date">Aug 16</span>
                </a>
                <!--end::Daterange-->
                <!--begin::Dropdowns-->
                <div class="dropdown dropdown-inline" data-toggle="tooltip" title="Quick actions" data-placement="left">
                    <a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <span class="svg-icon svg-icon-success svg-icon-lg">
                            <!--begin::Svg Icon | path:assets/media/svg/icons/Files/File-plus.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path
                                        d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M11,14 L9,14 C8.44771525,14 8,13.5522847 8,13 C8,12.4477153 8.44771525,12 9,12 L11,12 L11,10 C11,9.44771525 11.4477153,9 12,9 C12.5522847,9 13,9.44771525 13,10 L13,12 L15,12 C15.5522847,12 16,12.4477153 16,13 C16,13.5522847 15.5522847,14 15,14 L13,14 L13,16 C13,16.5522847 12.5522847,17 12,17 C11.4477153,17 11,16.5522847 11,16 L11,14 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                    </a>
                    <div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-right py-3">
                        <!--begin::Navigation-->
                        <ul class="navi navi-hover py-5">
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="flaticon2-drop"></i>
                                    </span>
                                    <span class="navi-text">New Group</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="flaticon2-list-3"></i>
                                    </span>
                                    <span class="navi-text">Contacts</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="flaticon2-rocket-1"></i>
                                    </span>
                                    <span class="navi-text">Groups</span>
                                    <span class="navi-link-badge">
                                        <span class="label label-light-primary label-inline font-weight-bold">new</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="flaticon2-bell-2"></i>
                                    </span>
                                    <span class="navi-text">Calls</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="flaticon2-gear"></i>
                                    </span>
                                    <span class="navi-text">Settings</span>
                                </a>
                            </li>
                            <li class="navi-separator my-3"></li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="flaticon2-magnifier-tool"></i>
                                    </span>
                                    <span class="navi-text">Help</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="flaticon2-bell-2"></i>
                                    </span>
                                    <span class="navi-text">Privacy</span>
                                    <span class="navi-link-badge">
                                        <span class="label label-light-danger label-rounded font-weight-bold">5</span>
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <!--end::Navigation-->
                    </div>
                </div>
                <!--end::Dropdowns-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <!--end::Subheader-->


    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Send Bulk Message
                            {{-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> --}}
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                {{-- <div class="card-header">
                                    <h4 class="card-title">Send notifications to 24/7 Doctor Online Users </h4>
                                </div> --}}
                                <div class="card-body">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active text-info" id="notifications-tab"
                                                data-toggle="pill" href="#notifications" role="tab"
                                                aria-controls="profile" aria-selected="false">Notifications</a>
                                        </li>

                                        {{-- <li class="nav-item" role="presentation">
                                            <a class="nav-link text-info " id="home-tab" data-toggle="tab"
                                                href="#video-ads-tab" role="tab" aria-controls="home"
                                                aria-selected="true">Video Ads</a>
                                        </li> --}}
                                    </ul>

                                    <div class="tab-content" id="myTabContent">
                                        {{-- Notifications tab --}}
                                        <div class="tab-pane fade show active" id="notifications" role="tabpanel"
                                            aria-labelledby="notifications-tab">

                                            <div class="row shadow-sm bg-light border border-default p-2 rounded">
                                                <!-- Notification types -->
                                                <div class="col-6">
                                                    <h5 class="border-bottom">Select Notification Type</h5>
                                                    <!-- SMS notification -->
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            id="sms-notification-type" required form="notification-form"
                                                            name="notificationType" value="sms">
                                                        <label class="form-check-label"
                                                            for="sms-notification-type">SMS</label>
                                                    </div>

                                                    <!-- Push notification -->
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            id="push-notification-type" title="Send push notifications"
                                                            required form="notification-form" name="notificationType"
                                                            value="push">
                                                        <label class="form-check-label"
                                                            for="push-notification-type">Push
                                                            Notification</label>
                                                    </div>
                                                </div>

                                                <!-- Notification recipients -->
                                                <div class="col-6 border-left">
                                                    <div class="h5 border-bottom">
                                                        Select Recipients
                                                        <small id="notification-recipients-error"
                                                            class="font-weight-bold p-0 mx-2 mb-2">
                                                            <!-- If no recipient is selected, an error message is inserted here -->
                                                        </small>
                                                    </div>

                                                    <div id="recipients-holder" class="p-1 rounded">
                                                        <!-- All Doctors -->
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="all-talents-checkbox" form="notification-form"
                                                                name="notificationRecipient" value="doctors">
                                                            <label class="form-check-label"
                                                                for="all-talents-checkbox">All
                                                                Talents</label>
                                                        </div>

                                                        <!-- All patients -->
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="all-fans-checkbox" form="notification-form"
                                                                name="notificationRecipient" value="patients">
                                                            <label class="form-check-label"
                                                                for="all-fans-checkbox">All
                                                                Fans</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>

                                            <!-- Notification content -->
                                            <div class="row">
                                                <div class="col-3 mx-auto p-3 border border-default rounded shadow">
                                                    <h5 class="border-bottom">Notification Content</h5>
                                                    <br>
                                                    <form id="notification-form">
                                                        <!-- Notification title -->
                                                        <div class="form-group" id="notification-title-holder" hidden>
                                                            <label for="push-notification-title">Push notification
                                                                title:</label>
                                                            <input placeholder="eg. Upcoming free health screening"
                                                                type="text" class="form-control"
                                                                name="notificationTitle" id="push-notification-title">
                                                        </div>

                                                        <!-- Notification body -->
                                                        <div class="form-group">
                                                            <label for="notification-body">Notification body:</label>
                                                            <textarea required
                                                                placeholder="Enter the content of the notification here"
                                                                rows="8" col="5" class="form-control"
                                                                name="notificationBody"></textarea>
                                                        </div>

                                                        <button class="btn btn-sm btn-block btn-primary">Send</button>
                                                    </form>
                                                </div>
                                                <!-- Notification content -->

                                                <!-- Previous Notifications -->
                                                <div class="col-8 p-3 border border-default mx-auto">
                                                    <h5 class="border-bottom">Previous Notifications</h5>
                                                    <br>
                                                    <div class="table">
                                                        <table class="table table-stripped"
                                                            id="previous-notifications-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Type</th>
                                                                    <th>Recipients</th>
                                                                    <th>Date sent</th>
                                                                    <th>Title</th>
                                                                    <th style="width: 150px;">Body</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!-- Previous Notifications -->
                                            </div>
                                        </div>


                                        {{-- Video ads tab --}}
                                        <div class="tab-pane fade" id="video-ads-tab" role="tabpanel"
                                            aria-labelledby="home-tab">

                                            <div class="row">
                                                <div class="col">
                                                    <!-- Under construction notice -->
                                                    <p class="alert alert-warning">Under construction</p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col p-3 rounded border border-default bg-light shadow-sm"
                                                    style="background:#f5f8ff">
                                                    <form id="video-ad-form">
                                                        <div class="form-row">
                                                            <div class="form-group col-md-5">
                                                                <label for="video-description">Video description</label>
                                                                <input type="text" required name="promo_title"
                                                                    class="form-control" id="video-description"
                                                                    placeholder="eg. August Promo video">
                                                            </div>
                                                            <div class="form-group col-md-5">
                                                                <label for="video-file">Video file</label>
                                                                <input required type="file" class="form-control"
                                                                    name="promo_video" id="video-file"
                                                                    placeholder="Video file">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                                <label>upload status here</label>
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-primary btn-block">Upload</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <br>
                                            <br>

                                            <div class="row">
                                                <div class="col">
                                                    <div class="table-responsive">
                                                        <table class="datatable table table-stripped" width='100%'
                                                            id="video-ads-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>File name</th>
                                                                    <th>Upload Date</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                {{-- Data is fetched here using ajax --}}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
@endsection


@section("page-scripts")
@parent
<script>
    // Handle previous notifications datatable
    var previousNotificationsTable = $("#previous-notifications-table").DataTable({
        dom: "Bfrtip",
        buttons: [],
        ordering: true,
        order: [],
        ajax: {
            url: `${APP_URL}/api/messaging/previous_notifications`
        },
        columns: [{
                data: "type"
            },
            {
                data: "recipient"
            },
            {
                data: "date_sent"
            },
            {
                data: "title"
            },
            {
                data: "body"
            },
        ],
    });

    let notificationForm = document.forms["notification-form"];
    let notificationTitleHolder = document.getElementById("notification-title-holder");

    (function () {
        notificationForm.notificationType.forEach(type => {
            type.addEventListener("change", function (e) {
                switch (type.value) {
                    case "push":
                        notificationTitleHolder.hidden = false;
                        notificationForm.notificationTitle.hidden = false;
                        notificationForm.notificationTitle.required = true;
                        break;
                    default:
                        notificationTitleHolder.hidden = true;
                        notificationForm.notificationTitle.hidden = true;
                        notificationForm.notificationTitle.required = false;
                        break;
                }
            });
        });
    })();

    notificationForm.addEventListener("submit", function (e) {
        e.preventDefault();

        // // Determine the selected recipients
        let selectedRecipients = [];
        let recipients = document.querySelectorAll("input[name='notificationRecipient']")
        recipients.forEach((recipient, index) => {
            if (recipient.checked) {
                selectedRecipients.push(recipient.value);
            }
        });

        // Make sure at least one recipient group is selected
        let recipientsHolder = document.getElementById("recipients-holder");
        let recipientsError = document.getElementById("notification-recipients-error");
        if (selectedRecipients.length === 0) {
            recipientsError.innerText = `At least select one recipient group`;
            recipientsError.classList.add("alert", "alert-danger");
            recipientsHolder.classList.add("border", "border-danger");
            return;
        } else {
            recipientsError.innerText = null;
            recipientsError.classList.remove("alert", "alert-danger");
            recipientsHolder.classList.remove("border", "border-danger");
        }

        let formdata = new FormData();
        formdata.append("notificationType", this.notificationType.value);
        formdata.append("notificationRecipients", JSON.stringify(selectedRecipients));
        formdata.append("notificationBody", this.notificationBody.value);
        formdata.append("notificationTitle", this.notificationTitle.value);
        formdata.append("createuser", `${CREATEUSER}`);

        fetch(`${APP_URL}/api/messaging/send_notification`, {
                method: "POST",
                body: formdata,
            }).then(res => res.json())
            .then(data => {
                previousNotificationsTable.ajax.reload(false, null);
            })

        Swal.fire({
            title: "",
            text: "The notification will be delivered to all recipients",
            timer: 2800,
            icon: "success",
            showConfirmButton: false,
        });
    });

    // Handle upload of video file
    /*
    let videoAdForm = document.forms["video-ad-form"];
    videoAdForm.addEventListener("submit", function(e) {
        e.preventDefault();
        let formdata = new FormData(videoAdForm);
        formdata.append("promo_video", videoAdForm.promo_video.files[0]);
        formdata.append("promo_title", videoAdForm.promo_title.value.trim());
        fetch(`${APP_URL}/api/messaging/upload_video`, {
            method: "POST",
            body: formdata,
            headers: {
                "Content-Type": "multipart/form-data"
            }
        }).then(function(res) {
            return res.json();
        }).then(function(data) {
            console.log(data);
        });
    });
*/

</script>


@endsection
