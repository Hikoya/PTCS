<?php

	$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"]."/qrcodesystem/credentials/config.ini");
	
	$servername = $config['servername'];
	$username = $config['username'];
	$password = $config['password'];
	$dbname = $config['dbname'];
	
	$parents = $config['parents'];
	$slots = $config['slots'];
	$lecturers = $config['lecturers'];
	$courses = $config['courses'];
	$classes = $config['classes'];
	$venues = $config['venues'];
	$time = $config['time'];
	
	$sitename = $config['sitename'];
	$sitefooter = $config['sitefooter'];
	$sitewelcome = $config['sitewelcome'];
	
	$timeinterval = $config['timeinterval'];
	
	$pdf_path = $config['pdf_path'];
	$excel_path = $config['excel_path'];
	$composer_path = $config['composer_path'];
	$student_letter_path = $config['student_letter_path'];
	$student_reinstated_letter_path = $config['student_reinstated_letter_path'];
	$student_oos_letter_path = $config['student_oos_letter_path'];
	
	$create_if_notexist = $config['create_if_notexist'];
	$append_mode = $config['append_mode'];
	$real_time = $config['real_time'];
	
	$num_of_records = $config['num_of_records'];
?>