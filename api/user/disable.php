<?php

require "../res/user.php";

$cApi = new cUser();

$input = json_decode( file_get_contents("php://input") , true );
echo $cApi->f_availability($input,1);
