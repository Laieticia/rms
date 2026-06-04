<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RestaurantMS') - Gestion Restaurant</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{url('admin/assets/images/favicon.ico')}}" />
    <link rel="stylesheet" href="{{url('admin/assets/css/backend-plugin.min.css')}}">
    <link rel="stylesheet" href="{{url('admin/assets/css/backend.css?v=1.0.0')}}">
    <link rel="stylesheet" href="{{url('admin/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{url('admin/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{url('admin/assets/vendor/remixicon/fonts/remixicon.css')}}">
    @stack('styles')
</head>

<body class="  ">
    <!-- loader Start -->
    <!-- <div id="loading">
        <div id="loading-center">
        </div>
    </div> -->
    <!-- loader END -->
    <!-- Wrapper Start -->
    <div class="wrapper">

        
        @include("layouts.menu")  
        @include("layouts.header")  
        @yield("content")
        
    </div>
    <!-- Wrapper End-->
    @include("layouts.footer") 
    <!-- Backend Bundle JavaScript -->
    <script src="{{url('admin/assets/js/backend-bundle.min.js')}}"></script>

    <!-- Table Treeview JavaScript -->
    <script src="{{url('admin/assets/js/table-treeview.js')}}"></script>

    <!-- Chart Custom JavaScript -->
    <script src="{{url('admin/assets/js/customizer.js')}}"></script>

    <!-- Chart Custom JavaScript -->
    <script async src="{{url('admin/assets/js/chart-custom.js')}}"></script>

    <!-- app JavaScript -->
    <script src="{{url('admin/assets/js/app.js')}}"></script>

    @stack('scripts')
</body>

</html>