<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title>SeverQuest - Admin-panel</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicons -->
  <link href="/img/favicon.png" rel="icon">
  <link href="/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="/admin-panel/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="/admin-panel/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="/admin-panel/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="/admin-panel/lib/gritter/css/jquery.gritter.css" />
  <!-- Custom styles for this template -->
  <link href="/admin-panel/css/style.css" rel="stylesheet">
  <link href="/admin-panel/css/style-responsive.css" rel="stylesheet">
  <script src="/admin-panel/lib/chart-master/Chart.js"></script>

  <!-- =======================================================
    Template Name: Dashio
    Template URL: https://templatemag.com/dashio-bootstrap-admin-template/
    Author: TemplateMag.com
    License: https://templatemag.com/license/
  ======================================================= -->
</head>

<body>
  <section id="app">
    <!-- **********************************************************************************************************************************************************
        TOP BAR CONTENT & NOTIFICATIONS
        *********************************************************************************************************************************************************** -->
    @include('admin.header')
    <!-- **********************************************************************************************************************************************************
        MAIN SIDEBAR MENU
        *********************************************************************************************************************************************************** -->
    @include('admin.sidebar')
    <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
    <section id="main-content">
      @yield('main')
    </section>
    <!-- **********************************************************************************************************************************************************
        FOOTER
        *********************************************************************************************************************************************************** -->
    @include('admin.footer')
    <notifications group="admin" />
  </section>
  <script src="{{ mix('js/app.js') }}"></script>
  <!--common script for all pages-->
  <script src="/admin-panel/lib/common-scripts.js"></script>
 
  <script src="https://cdn.tiny.cloud/1/r5dnrnvhdkqbpdrk8ihegv0bwyn4touy2fkvc8ko85phxls4/tinymce/5/tinymce.min.js"></script>
  <script>
      tinymce.init({
        selector: '#description',
        plugins : 'advlist autolink link image lists charmap print preview',
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        content_css: '{{ asset('css/.css') }}',
      });
      </script>
</body>

</html>
