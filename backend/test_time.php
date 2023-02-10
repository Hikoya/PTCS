<?php
	
	date_default_timezone_set('Asia/Singapore');

	$now = date("Y-m-d");
	$start = "09:30";
	
	$check_time = $now . " " . $start;
	$check_unix = strtotime($check_time);
	
	$now_time = time();
	
	$time = "2018-06-21 10:45";
	$time_unix = strtotime($time);
	
	echo $check_time; 
	echo " ";
	echo $time_unix;
	echo " ";
	echo $now_time;
	
	if( (int)$now_time > (int)$check_unix )
	{
		echo " LATER ";
	}
	else{
		echo " EARLIER ";
	}

?>