<?php

require("class/class.main.php");

if(!empty($_POST['value']))
{
	$message = $frontend->SearchResult($_POST['value']);
	echo $message;
}

if(!empty($_POST['search']))
{
	$message = $frontend->SearchSlots($_POST['search']);
	echo $message;
}

if(!empty($_POST['search_all']))
{
	$message = $frontend->SearchAllSlots($_POST['search_all']);
	echo $message;
}

?>