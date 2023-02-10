<?php

require("class/class.main.php");
if(!empty($_POST['id']))
{
	$result = $frontend->CheckSlot($_POST['id']);
	echo json_encode($result);
}

if(!empty($_POST['slot']) && !empty($_POST['parent']) )
{
	$result = $frontend->AssignSlotToParent($_POST['slot'] , $_POST['parent']);
	echo json_encode($result);
}


?>