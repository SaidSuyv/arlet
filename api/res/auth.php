<?php

require $_SERVER['DOCUMENT_ROOT'].'/global/crypto.php';
require $_SERVER['DOCUMENT_ROOT'].'/global/database.php';

class cAuth
{
  private $cCrypto;
  private $cDatabase;

  private $nCurrentUserId;
  private $nCurrentCompanyId;
  
  public function __construct()
  {
    $this->cCrypto = new cCrypto();
    $this->cDatabase = new cDatabase("arlet_digysoft");
    $this->nCurrentUserId = null;
    $this->nCurrentCompanyId = null;
  }

  public function f_get_token( $token )
  {
    $str = $this->cCrypto->decrypt( $token );
    $d = explode( "|" , $str );
    $r = [
      "u" => $this->cCrypto->decrypt( $d[0] ),
      "p" => $this->cCrypto->decrypt( $d[1] ),
      "time" => $d[2]
    ];
    return $r;
  }

  public function f_generate_token( $data = [] )
  {
    $expiration = strtotime("+5 hours");

    $arr = [
      "user"  => $this->cCrypto->encrypt( $data['u'] ) ,
      "pwd"   => $this->cCrypto->encrypt( $data['p'] ) ,
      "time"  => $expiration
    ];

    $str = implode( "|" , $arr );
    $token = $this->cCrypto->encrypt( $str );
    return $token;
  }

  public function f_generate_cookie($name,$value)
  {
    // expiration = now + 5 hours
    $expiration = time() + (3600 * 5);

    setcookie(
      $name,
      $value,
      [
        "expires" => $expiration,
        "path" => "/",
        "secure" => true,
        "httponly" => true,
        "samesite" => "Strict"
      ]
    );
  }

  private function f_user_exists($user)
  {
    $p = [ "u" => $user ];
    $q = $this->cDatabase->execute(
      "SELECT id , f_company AS 'company' , pwd FROM user WHERE username = :u",
      $p,
      "one"
    );
    return $q;
  }

  public function f_internal_login( $user , $pass )
  {

    $user_data = $this->f_user_exists($user);

    if( $user_data == false ) return false;

    $pwd = $this->cCrypto->decrypt( $user_data['pwd'] );
    if( $pass === $pwd )
    {
      $this->nCurrentUserId = $user_data['id'];
      $this->nCurrentCompanyId = $user_data['company'];
      return true;
    }

    return false;

  }

  public function f_get_id()
  {
    if( $this->nCurrentUserId != null ) return $this->nCurrentUserId;
    else return null;
  }

  public function f_get_company()
  {
    if( $this->nCurrentCompanyId != null ) return $this->nCurrentCompanyId;
    else return null;
  }

  public function f_check_token()
  {
    if( isset($_COOKIE['arlet_token']) )
    {
      $t = $_COOKIE["arlet_token"];
      $td = $this->f_get_token( $t );

      if( !$this->f_internal_login( $td["u"] , $td["p"] ) )
        return false;

      $expired = time() > (int)$td["time"];
      if( $expired )
      {
        $nt = $this->f_generate_cookie( $td );
        $this->f_generate_cookie( "arlet_token" , $nt );
      }

      return true;
    }
    else return false;
  }

  public function set_error()
  {
    echo json_encode(["auth"=>false],true);
    die();
  }
}