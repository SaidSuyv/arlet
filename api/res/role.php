<?php

require 'arlet.php';
require 'auth.php';

class cRole extends cArlet
{
  private $cAuth;
  private $cDatabase;
  
  public function __construct()
  {
    $this->cAuth = new cAuth();
    $this->cDatabase = new cDatabase("arlet_digysoft");
  }

  public function f_get_all()
  {
    if( $this->cAuth->f_check_token() )
    {
      $q = $this->cDatabase->execute(
        "SELECT id AS 'value' , name AS 'text' FROM role WHERE b_archive = 0;"
      );
      return json_encode( $q , true );
    }
    else $this->cAuth->set_error();
  }
}