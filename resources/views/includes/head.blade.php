@section('head')
<meta charset="utf-8" />
<title>TopOil &middot; @yield('pageName')</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description"
    content="Metronic admin dashboard live demo. Check out all the features of the admin panel. A large number of settings, additional services and widgets." />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="canonical" href="https://keenthemes.com/metronic" />
<!--begin::Fonts-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<!--end::Fonts-->
<!--begin::Page Vendors Styles(used by this page)-->
<link href="{{asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css')}}" rel="stylesheet" type="text/css" />
<!--end::Page Vendors Styles-->
<!--begin::Global Theme Styles(used by all pages)-->
<link href="{{asset('assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/custom/prismjs/prismjs.bundle.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
<!--end::Global Theme Styles-->
<!--begin::Layout Themes(used by all pages)-->
<link href="{{asset('assets/css/themes/layout/header/base/light.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/css/themes/layout/header/menu/light.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/css/themes/layout/brand/dark.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/css/themes/layout/aside/dark.css')}}" rel="stylesheet" type="text/css" />
<!--end::Layout Themes-->
<script type="text/javascript" src="{{asset('assets/js/jquery.min.js')}}"></script>

{{-- <link rel="shortcut icon" href="{{asset('img/logo.jpeg')}}" /> --}}
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
    integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/b-html5-1.6.2/b-print-1.6.2/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.4/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.0/sl-1.3.1/datatables.min.css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript"
    src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/b-html5-1.6.2/b-print-1.6.2/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.4/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.0/sl-1.3.1/datatables.min.js">
</script>

{{-- Select 2css --}}
<link rel="stylesheet" href="{{ asset('assets/select2.min.css') }}">


<script src="{{asset('js/app.js')}}"></script>


<style>
    .select2-container {
        height: 35px !important;
    }

    .select2-selection {
        height: 35px !important;
    }

</style>
<script>
    var APP_URL = "{{config('app.url')}}";
    var CREATEUSER = "{{Auth::user()->userid}}";
    $(document).ready(function () {
        $(".select2").select2({
            width: '100%',
            selectOnClose: true
        });
    });

</script>
@show
