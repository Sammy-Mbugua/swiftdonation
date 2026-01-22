<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Global Header -->
    @include("$theme_dir.includes.global._head")
    <!-- End Global Header -->

    <!-- Main Header -->
    @include("$theme_dir.includes.head")
    <!-- End Main Header -->
</head>

<body class="index-page">

    <header id="header" class="header  d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="{{ url('') }}" class="logo d-flex align-items-center me-auto">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <!-- <img src="assets/img/logo.png" alt=""> -->
                <h1 class="sitename">Swift Oak Donations </h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ url('/account/home/dashboard') }}" class="active">Dashboard</a></li>
                    @auth
                        <li><a href="{{ url('/account/dashboard') }}">profile</a></li>
                    @else
                        <li><a href="#contact">Contact</a></li>
                    @endauth

                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
            @auth
                <a class="cta-btn" href="{{ url('/logout') }}">logout</a>
            @else
                <a class="cta-btn" href="{{ url($links->loginview) }}">Login</a>
            @endauth




        </div>
    </header>

    <!-- Main Page -->
    @yield('content')
    <!-- End Main Page -->


    <footer id="footer" class="footer dark-background">

        <div class="container copyright text-center mt-0">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Swift Oak Donations</strong> <span>All Rights
                    Reserved</span></p>
            <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you've purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
                {{-- Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a href=“https://themewagon.com>ThemeWagon --}}
            </div>
        </div>

    </footer>

    <!-- Global Footer -->
    @include("$theme_dir.includes.global._footer")
    <!-- End Global Footer -->

    <!-- Main Footer -->
    @include("$theme_dir.includes.footer")
    <!-- End Main Footer -->
</body>

</html>
