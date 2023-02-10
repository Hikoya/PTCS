<?php
	
	require_once("../class/class.main.php");
	
	if(!empty($_POST['classes']))
	{
		if($functions->DeleteClass($_POST['classes']))
			return true;
		else
			return false;
	}
	
	if(!empty($_POST['course']))
	{
		if($functions->DeleteCourse($_POST['course']))
			return true;
		else
			return false;
	}
	
	if(!empty($_POST['lecturer']))
	{
		if($functions->DeleteLecturer($_POST['lecturer']))
			return true;
		else
			return false;
	}
	
	if(!empty($_POST['venue']))
	{
		if($functions->DeleteVenue($_POST['venue']))
			return true;
		else
			return false;
	}
	
	if(!empty($_POST['timeslot']))
	{
		if($functions->DeleteTimeSlot($_POST['timeslot']))
			return true;
		else
			return false;
	}
	
	if(!empty($_POST['parent']))
	{
		if($functions->DeleteParent($_POST['parent']))
			return true;
		else
			return false;
	}
	
	if(!empty($_POST['slot']))
	{
		if($functions->DeleteSlot($_POST['slot']))
			return true;
		else
			return false;
	}
	
?>