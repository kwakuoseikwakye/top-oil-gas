@section('user_profile')

<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
    <!--begin::Header-->
    <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
        <h3 class="font-weight-bold m-0">User Profile
            {{-- <small class="text-muted font-size-sm ml-2">12 messages</small></h3> --}}
            <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
                <i class="ki ki-close icon-xs text-muted"></i>
            </a>
    </div>
    <!--end::Header-->
    <!--begin::Content-->
    <div class="offcanvas-content pr-5 mr-n5">
        <!--begin::Header-->
        <div class="d-flex align-items-center mt-5">
            <div class="symbol symbol-100 mr-5">
                {{-- <div class="symbol-label" style="background-image:url('<?= Auth::user()->company->image?>')"></div> --}}
                <i class="symbol-badge bg-success"></i>
            </div>
            <div class="d-flex flex-column">
                <a href="#"
                    class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary"></a>
                {{-- <div class="text-muted mt-1">{{Auth::user()->company->name}}</div> --}}
                <div class="navi mt-2">
                    <a data-toggle="modal" data-target="#password-modal" class="btn btn-sm btn-light-primary">
                        <span class="menu-text">Change Password</span>
                        <i class="menu-arrow"></i>
                    </a>
                     <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a style="cursor:pointer" onclick="event.preventDefault(); document.forms['logout-form'].submit()" class="mt-3 btn btn-sm btn-light-primary font-weight-bolder py-2 px-5"
                            value="Sign Out">Sign Out</a>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Separator-->
        <div class="separator separator-dashed mt-8 mb-5"></div>
        <div class="separator separator-dashed my-7"></div>
    </div>
</div>

@show
