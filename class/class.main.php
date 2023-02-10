<?php

require_once("class.cred.php");
require_once("class.functions.php");
require_once("class.frontend.php");

$functions = new Functions();

$functions->InitDB($dbname, $username, $password, $servername);
$functions->InitDBTable($parents, $slots, $lecturers, $courses, $classes, $venues, $time, $timeinterval);
$functions->InitPath($pdf_path,$excel_path, $composer_path);
$functions->InitNumRecords($num_of_records);

$frontend = new FrontEnd();

$frontend->InitDB($dbname, $username, $password, $servername);
$frontend->InitDBTable($parents, $slots, $lecturers, $courses, $classes, $venues, $time);
$frontend->InitRealTime($real_time);
	
?>