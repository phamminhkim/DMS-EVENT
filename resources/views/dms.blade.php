<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>DMS</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
    <!-- overlayScrollbars -->
    <!-- flag-icon-css -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="plugins/toastr/toastr.css">


    {{-- <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous"> --}}
    <style>
        .dot {

            height: 6px;
            width: 6px;
            background-color: #dee2e6;
            border-radius: 50%;
            display: inline-block;
            margin-left: 10px;
            margin-right: 10px;

        }

        .active .dot {
            height: 6px;
            width: 6px;

            border-radius: 50%;
            display: inline-block;
            margin-left: 10px;
            margin-right: 8px;
            background-color: #3C8DBC;
        }

        .main-sidebar.sidebar-light-lightblue {

            background-color: white;
        }

        .main-header {
            border-bottom: 1px solid #c6c8c9;

        }

        .bosung-menu {
            background-color: #3C8DBC !important;
            color: white !important;
        }

        .show-hotline {
            display: block;
        }

        .hide-hotline {
            display: none;
        }



        .btn-function_top {
            border-radius: 50%;
            background-color: #fff;
            border-color: #fff;
            box-shadow: 1px 1px 10px #aaa !important;
            color: black !important;
            padding-top: 12px;
            width: 50px;
            height: 50px;
        }

        .toast-success {
            background-color: #51A351;
        }

        .os-theme-light .os-scrollbar .os-scrollbar-track .os-scrollbar-handle:hover {
            background: gray;
        }

    </style>
    @yield('css')
</head>

<body>
    {{-- <div class="flex-center position-ref full-height"> --}}
    <div>
        <div class="wrapper" id="app">
            <dms-submission></dms-submission>
        </div>
    </div>
    </div>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="plugins/toastr/toastr.min.js"></script>

</body>

</html>
@yield('script')
