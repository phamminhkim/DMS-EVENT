<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DMS Event</title>

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    {{-- +st: kim --}}
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

        .custom-nav-link {
            background: gold;
            color: blue;
        }

        .custom-nav-link:hover {
            background: transparent;
            color: white;
            font-weight: 500;

        }

        .custom-navbar {
            background-image: url('/bgnavbar.jpg');
            background-size: cover;
        }

        .custom-navbar-version-2 {
            position: sticky !important;
            top: 0;
            z-index: 15;
            background: white;
        }

        .sidepanel {
            width: 0;
            position: fixed;
            z-index: 1;
            height: auto;
            top: 0;
            right: 0;
            background-color: white;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }

        .sidepanel a {

            text-decoration: none;

            color: #818181;
            display: block;
            transition: 0.3s;
        }



        .sidepanel .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
        }

        .openbtn {
            cursor: pointer;
            /* background: linear-gradient(45deg, black, #0000009e); */
            transition: 0.3s all;
            letter-spacing: 1px;
            font-size: 12px;
            background: linear-gradient(45deg, rgb(247, 247, 247), rgb(247, 247, 247));
        }

        .text-color-menu{
            font-weight: 700;
            color: #44107a;
        }
        .icon-showroom {
            font-size: 30px;
            background: -webkit-linear-gradient(#830c0c, #140de0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 3px;
        }

        .icon-list-customer {
            font-size: 30px;
            background: -webkit-linear-gradient(#ffa407, #eb8b07, #ff3b00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 3px;
        }

        .icon-register {
            font-size: 30px;
            background: -webkit-linear-gradient(#ffa407, #eb8b07, #ff3b00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 3px;
        }

        .icon-approved {
            font-size: 30px;
            background: -webkit-linear-gradient(#b1b1b1, #00d51c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 3px;
        }

        .icon-list-program {
            font-size: 30px;
            background: -webkit-linear-gradient(#b1b1b1, #686868);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 3px;
        }

        .icon-program-admin {
            font-size: 30px;
            background: -webkit-linear-gradient(#f2ff04, #f3ff07);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 3px;
        }

        .icon-supervisor {
            font-size: 30px;
            background: -webkit-linear-gradient(#ffa704, #ffee07);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 3px;
        }

        .link-showroom {
            border-radius: 10px;

            letter-spacing: 2px;
            color: midnightblue;
            background: rgb(242, 242, 242);
            font-size: 10px;
        }

        .link-showroom:hover {

            text-decoration: none;
            background-color: rgb(219, 231, 242);
        }

        .link-menu {
            position: absolute;
            top: 11px;
            font-size: 25px;
            color: gray;
            font-weight: 700;
            left: -11px;
            letter-spacing: 2px;
            text-transform: uppercase;


        }


        .link-menu-admin {
            font-size: 18px;
            color: gray;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .text-program-stage {
            text-transform: uppercase;
            background-image: linear-gradient(-225deg,
                    #231557 0%,
                    #44107a 29%,
                    #ff1361 67%,
                    #fff800 100%);
            background-size: auto auto;
            background-clip: border-box;
            background-size: 200% auto;
            color: #fff;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textclip 2s linear infinite;
            display: inline-block;

        }

        @keyframes textclip {
            to {
                background-position: 200% center;
            }
        }

        .noti-pending {
            font-size: 11px;
            position: absolute;
            right: 5px;
            top: 4px;
        }
    </style>
    <script>
        function openNav() {
            document.getElementById("mySidepanel").style.width = "100%";
        }

        function closeNav() {
            document.getElementById("mySidepanel").style.width = "0";
        }

        function getSubmissionCount() {
            const token = window.Laravel.access_token;
            var userRoles = document.getElementById('submission-count').getAttribute('data-roles');
            if (userRoles == 'GSBH') {
                fetch('/api/submission-count-is-pending', {
                        headers: {
                            'Authorization': `Bearer ${token}`,

                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count;

                        if (count > 0) {
                            document.getElementById('submission-count').innerText = count;
                            document.getElementById('submission-count-noti').innerText = count;
                        }

                    })
                    .catch(error => {
                        console.error('Lỗi khi lấy số lượng ở ký duyệt:', error);
                    });
            }
        }

        function getSubmissionLevel_2_Count() {
            const token = window.Laravel.access_token;

            var userRoles = document.getElementById('submission-count-level2').getAttribute('data-roles');
            if (userRoles == 'QLGS') {
                fetch('/api/submission-count-is-pending-level2', {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const count_level2 = data.count;
                        console.log(count_level2)
                        if (count_level2 > 0) {
                            document.getElementById('submission-count-level2').innerText = count_level2;
                            document.getElementById('submission-count-level2-noti').innerText = count_level2;
                        }

                    })
                    .catch(error => {
                        console.error('Lỗi khi lấy số lượng ký duyệt level 2:', error);
                    });
            }
        }
        //   document.addEventListener('DOMContentLoaded', getSubmissionCount);
        //     document.addEventListener('DOMContentLoaded', getSubmissionLevel_2_Count);
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('submission-count') && document.getElementById('submission-count')
                .getAttribute('data-roles') === 'GSBH') {
                getSubmissionCount();
            }

            if (document.getElementById('submission-count-level2') && document.getElementById(
                    'submission-count-level2').getAttribute('data-roles') === 'QLGS') {
                getSubmissionLevel_2_Count();
            }
        });
        try {

            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'access_token' => $access_token,
                'current_user' => Auth::user(),
            ]) !!};
        } catch (err) {

        }
    </script>

    {{-- +en --}}

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light custom-navbar-version-2 shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand mt-1" href="{{ url('/') }}">
                    <img src="{{ asset('/logo.png') }}" alt="Logo">
                </a>
                <div id="mySidepanel" class="sidepanel">
                    <span class="link-menu text-uppercase ml-3">Danh sách menu </span>
                    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
                    <div class="row mt-2">
                        <div class="col-md-4 mb-3">
                            <span class="link-menu-admin text-uppercase ml-3 mt-2">Nhân viên bán hàng</span>
                            <a class="mt-1 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold link-showroom"
                                href="{{ url('/') }}">
                                <i class="fas fa-camera-retro mr-2 p-2 icon-showroom"></i>Showroom
                            </a>
                            <a class="mt-2 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold link-showroom"
                                href="{{ url('/customer') }}">
                                <i class="far fa-address-book mr-2 p-2 icon-list-customer"
                                    style="width: 48.98px;"></i>Danh sách khách hàng
                            </a>
                            <a class="mt-2 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold link-showroom"
                                href="{{ url('/submission-register') }}">
                                <i class="fas fa-registered mr-2 p-2 icon-register"></i>Đăng ký khách hàng
                            </a>


                        </div>
                        @if (Auth::check() && Auth::user()->roles === 'GSBH')
                            <div class="col-md-4 mb-3">
                                <span class="link-menu-admin text-uppercase ml-3 mt-2">Giám sát chương trình</span>
                                <a class="mt-2 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold link-showroom"
                                    href="{{ url('/approved') }}" style="position: relative;">
                                    <i class="fas fa-marker mr-2 p-2 icon-approved"></i>Ký duyệt
                                    <span class="badge badge-danger noti-pending" id="submission-count"
                                        data-roles="{{ Auth::user()->roles }}" onload="getSubmissionCount();"
                                        style="font-size: 11px"></span>
                                </a>
                            </div>
                        @endif
                        @if (Auth::check() && Auth::user()->roles === 'QLGS')
                            <div class="col-md-4 mb-3">
                                <span class="link-menu-admin text-uppercase ml-3">Quản lý giám sát</span>
                                <a class="mt-2 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold link-showroom"
                                    href="{{ url('/program-stages') }}">
                                    <i class="fas fa-lock-open mr-2 p-2 icon-list-program" style="width: 50.41px;"></i>
                                    <span class="text-program-stage">Mở đợt đánh giá</span>
                                </a>
                                <a class="mt-2 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold link-showroom"
                                    href="{{ url('/program') }}">
                                    <i class="fab fa-elementor mr-2 p-2 icon-list-program"
                                        style="width: 50.41px;"></i>Danh
                                    sách chương trình
                                </a>
                                <a class="mt-2 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold link-showroom"
                                    href="{{ url('/program-admin') }}">
                                    <i class="fas fa-star-half-alt mr-2 p-2 icon-program-admin"></i>Quản lý chương trình
                                </a>
                                <a class="mt-2 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold link-showroom"
                                    href="{{ url('/suppervision') }}" style="position: relative;">
                                    <i class="fas fa-flag  mr-2 p-2 icon-supervisor"></i>Quản lý giám sát
                                    <span class="badge badge-danger noti-pending" id="submission-count-level2"
                                        data-roles="{{ Auth::user()->roles }}" onload="getSubmissionLevel_2_Count();"
                                        style="font-size: 11px"></span>
                                </a>
                            </div>
                        @endif
                        <div class="col-md-4 mb-3">
                            <span class="link-menu-admin text-uppercase ml-3">Tra cứu</span>
                            <a class="mt-2 p-1 mr-1 ml-3 mr-4 d-flex align-items-center text-uppercase font-weight-bold link-showroom"
                                href="{{ url('/lookup-customer') }}">
                                <i class="fas fa-address-book mr-2 p-2 icon-list-program"
                                    style="width: 50.41px;"></i>Khách hàng
                            </a>
                        </div>
                    </div>
                    <div class="form-group d-block d-sm-none">
                        <ul class="navbar-nav">
                            <!-- Authentication Links -->
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#"
                                        role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>

                            @endguest
                        </ul>
                    </div>
                </div>

                {{-- <button class="navbar-toggler mt-1" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}" style="color:rgba(255, 255, 255, 0.75);">
                    <span class="navbar-toggler-icon"></span>
                </button> --}}


                <button class="openbtn mr-2 mt-1 btn border-0 d-block d-sm-none" onclick="openNav()">
                    <i class="fas fa-th-large font-italic" style="color:#00d0ff"></i> 
                    <span class="text-color-menu"> Menu </span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item" style="position: relative;">
                                <button class="openbtn mr-2 btn" onclick="openNav()">
                                    <i class="fas fa-th-large font-italic" style="color:#00d0ff"></i> 
                                    <span class="text-color-menu"> Menu </span>
                                </button>
                                <span class="badge badge-danger noti-pending" id="submission-count-noti"
                                    data-roles="{{ Auth::user()->roles }}" onload="getSubmissionCount();"
                                    style="font-size: 11px"></span>
                                <span class="badge badge-danger noti-pending" id="submission-count-level2-noti"
                                    data-roles="{{ Auth::user()->roles }}" onload="getSubmissionLevel_2_Count();"
                                    style="font-size: 11px"></span>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>

                        @endguest
                    </ul>
                </div>
            </div>

        </nav>


        <main class="main-content">
            @yield('content')
        </main>
    </div>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="plugins/toastr/toastr.min.js"></script>
</body>

</html>
@yield('script')
