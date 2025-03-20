<?php

try
{
	require '../res/user.php';
	$cApi = new cUser();
	echo $cApi->f_autologin();
}
catch(Exception $e)
{
	echo $e->getMessage();
}
