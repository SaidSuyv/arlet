<?php

$input = json_decode( file_get_contents("php://input") , true );

if( !isset($input) )
  die();

require '../res/user.php';

$cAPI = new cUser();

echo $cAPI->f_login($input);