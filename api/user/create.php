<?php

require '../res/user.php';

$cApi = new cUser();

$input = $_POST;
echo $cApi->f_create( $input );