<?php

require("../class/class.main.php");

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $sitename ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="../dist/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../dist/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../dist/bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
 
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a class="logo">
      <span class="logo-mini"><?php echo $sitefooter ?></span>   
      <span class="logo-lg"><?php echo $sitename ?></span>
    </a>

    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
    </nav>
  </header>
 
	<aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li> 
		
		<?php
		if(basename($_SERVER['PHP_SELF']) == "parents.php")
		{
		?>	
			<li class="active"><a href="parents.php"><i class="fa fa-address-book"></i><span>Parents</span></a></li> 
		<?php
		}
		else
		{
		?>
			<li><a href="parents.php"><i class="fa fa-address-book"></i><span>Parents</span></a></li> 
		<?php
		}
		?>
		
		<?php
		if(basename($_SERVER['PHP_SELF']) == "slots.php")
		{
		?>		
			<li class="active"><a href="slots.php"><i class="fa fa-barcode"></i><span>Available Slots</span></a></li> 
		<?php
		}
		else
		{
		?>		
			<li><a href="slots.php"><i class="fa fa-barcode"></i><span>Available Slots</span></a></li> 
		<?php
		}
		?>
		
		<?php
		if(basename($_SERVER['PHP_SELF']) == "lecturers.php")
		{
		?>		
			<li class="active"><a href="lecturers.php"><i class="fa fa-male"></i><span>Lecturers</span></a></li> 
		<?php
		}
		else
		{
		?>		
			<li><a href="lecturers.php"><i class="fa fa-male"></i><span>Lecturers</span></a></li> 
		<?php
		}
		?>
		
		<?php
		if(basename($_SERVER['PHP_SELF']) == "courses.php")
		{
		?>		
			<li class="active"><a href="courses.php"><i class="fa fa-book"></i><span>Courses</span></a></li> 
		<?php
		}
		else
		{
		?>		
			<li><a href="courses.php"><i class="fa fa-book"></i><span>Courses</span></a></li>  
		<?php
		}
		?>
		
		<?php
		if(basename($_SERVER['PHP_SELF']) == "venues.php")
		{
		?>		
			<li class="active"><a href="venues.php"><i class="fa fa-location-arrow"></i><span>Venues</span></a></li> 
		<?php
		}
		else
		{
		?>		
			<li><a href="venues.php"><i class="fa fa-location-arrow"></i><span>Venues</span></a></li> 
		<?php
		}
		?>
		
		<?php
		if(basename($_SERVER['PHP_SELF']) == "timeslot.php")
		{
		?>		
			<li class="active"><a href="timeslot.php"><i class="fa fa-calendar"></i><span>Time Slots</span></a></li> 
		<?php
		}
		else
		{
		?>		
			<li><a href="timeslot.php"><i class="fa fa-calendar"></i><span>Time Slots</span></a></li> 
		<?php
		}
		?>
         
		<?php
		if(basename($_SERVER['PHP_SELF']) == "classes.php")
		{
		?>		
			<li class="active"><a href="classes.php"><i class="fa fa-graduation-cap"></i><span>Classes</span></a></li> 
		<?php
		}
		else
		{
		?>		
			<li><a href="classes.php"><i class="fa fa-graduation-cap"></i><span>Classes</span></a></li> 
		<?php
		}
		?>
		
		<?php
		if(basename($_SERVER['PHP_SELF']) == "student-letters.php")
		{
		?>		
			<li class="active"><a href="student-letters.php"><i class="fa fa-envelope"></i><span>Generate Letter</span></a></li> 
		<?php
		}
		else
		{
		?>		
			<li><a href="student-letters.php"><i class="fa fa-envelope"></i><span>Generate Letter</span></a></li> 
		<?php
		}
		?>
		
		
		
      </ul>
    </section>
  </aside>