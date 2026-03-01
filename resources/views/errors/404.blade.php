<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, user-scalable=0">
  <title>Page Not Found</title>    
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{asset('default/error/css/style.css')}}"/>
</head>
<body>
	<section class="error_section">
      <p class="error_section_subtitle">This page you are looking for does not exist !</p>
      <h1 class="error_title">
        <p>404</p>
        404
      </h1>
      <a href="<?php echo url('/'); ?>" class="btn">BACK TO HOMEPAGE</a>
    </section>
	<script  src="{{asset('default/error/js/script.js')}}"></script>
</body>
</html>