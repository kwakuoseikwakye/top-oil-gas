@section('head')
    <meta charset="utf-8" />
    <title>TopOil &middot; @yield('pageName')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Metronic admin dashboard live demo. Check out all the features of the admin panel. A large number of settings, additional services and widgets." />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    {{-- <link rel="canonical" href="https://keenthemes.com/metronic" /> --}}
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Page Vendors Styles(used by this page)-->
    {{-- <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet"
        type="text/css" /> --}}
    <!--end::Page Vendors Styles-->
    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles-->
    <!--begin::Layout Themes(used by all pages)-->
    <link href="{{ asset('assets/css/themes/layout/header/base/light.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/themes/layout/header/menu/light.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/themes/layout/brand/dark.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/themes/layout/aside/dark.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Layout Themes-->
    <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>

    {{-- <link rel="shortcut icon" href="{{asset('img/logo.jpeg')}}" /> --}}
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/datatables/dt.css') }}" />
    {{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script> --}}
    <script type="text/javascript" src="{{ asset('assets/datatables/dt.js') }}"></script>
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.0/css/bootstrap.min.css" integrity="sha512-NZ19NrT58XPK5sXqXnnvtf9T5kLXSzGQlVZL9taZWeTBtXoN3xIfTdxbkQh6QSoJfJgpojRqMfhyqBAAEeiXcA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    {{-- Select 2css --}}
    <link rel="stylesheet" href="{{ asset('bootstrap-select.min.css') }}">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0/dist/css/bootstrap-select.min.css" rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta/css/bootstrap-select.min.css" integrity="sha512-S/noEArtFzz8UzFxmVcWfUy5f1zE6cmk7ny8spHuuhSuq9mPUZONHtO+U1Bna9tRn6kAo3mRIsWSsqWbWyhUQQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>


    <style>
        .select2-container {
            height: 35px !important;
        }

        .select2-selection {
            height: 35px !important;
        }
    </style>
    <script>
        var APP_URL = "{{ config('app.url') }}";
        var CREATEUSER = "{{ Auth::user()->userid }}";
        // $(document).ready(function () {
        //     $(".select-input").select2({
        //         width: '100%',
        //         selectOnClose: true
        //     });
        // });
    </script>
   


@show
