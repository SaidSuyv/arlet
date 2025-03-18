<?php

require 'arlet.php';

class cProduct extends cArlet
{
  private $cAuth;

  public function __construct()
  {
    $this->cAuth = new cAuth();
    $this->cDatabase = new cDatabase("arlet_digysoft");
  }

  public function f_add_product($data)
  {
    if( $this->cAuth->f_check_token() )
    {
      $params = [
        "code",
        "ptype",
        "ipath",
        "desc",
        "cprice",
        "profit",
        "csale",
        "whprice",
        "whminq",
        "flg_inv",
        "stock",
        "min_st",
        "xpdate",
        "sunat_unit",
        "sunat_pcode",
        "igv_per",
        "icbper_am"
      ];
      if( $this->check_params( $data , $params ) )
      {
        $q = $this->cDatabase->execute(
          ""
        );
      }
      else $this->set_error("Server Error: P001");
    }
    else $this->set_error("Authentication failed");
  }

}