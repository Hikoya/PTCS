<?php
/*
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	*/
	
	$uri = 'https://';
	
	$uri .= $_SERVER['HTTP_HOST'];
	header('Location: '.$uri.'/qrcodesystem/backend/classes.php');
	exit;
?>
Something is wrong with the XAMPP installation :-(
