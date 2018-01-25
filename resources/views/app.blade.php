<!DOCTYPE html>
<html ng-app="app"  ng-model-options="{ debounce: 250 }">

@include('partials._head')

<body class="fixed-left theme-{{ config('app.theme') }}">

<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">

        <!-- LOGO -->
        <div class="topbar-left">
            <a class="logo">
                <span style="font-size: 0.8em;">{{ trans('general.short_name') }}</span>
                <i class="zmdi zmdi-layers"></i>
                <i class="ti-menu"></i>
            </a>
        </div>

        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">

                <!-- Page title -->
                <ul class="nav navbar-nav navbar-left">
                    <li>
                        <button class="button-menu-mobile open-left">
                            <i class="zmdi zmdi-menu"></i>
                        </button>
                    </li>
                    <li>
                        {{--<h4 class="page-title">{{ trans('general.dashboard') }}</h4>--}}
                    </li>
                </ul>
            </div><!-- end container -->
        </div><!-- end navbar -->
    </div>
    <!-- Top Bar End -->


    <!-- ========== Left Sidebar Start ========== -->
    <div class="left side-menu">
        <div class="sidebar-inner slimscrollleft">

            <!-- User -->
            @include('partials._user_box')
            <!-- End User -->

            <!--- Sidebar -->
            @include('nav.sidebar')
            <!-- Sidebar -->

            <div class="clearfix"></div>
        </div>
    </div>
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container">

                <div class="row">
                    @yield('content')
                </div>
                <!-- End row -->

            </div> <!-- container -->

        </div> <!-- content -->

        <!-- @include('partials._footer') --> <!-- This is removed to keep page in all browser height without scroll! IMPORTANT check response -->

    </div>
    <!-- End content-page -->

</div>
<!-- END wrapper -->
@include('partials._javascript')

</body>
</html>
