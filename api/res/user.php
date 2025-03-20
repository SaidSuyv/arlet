<?php

require 'arlet.php';
require 'sql_str.php';
require 'auth.php';

class cUser extends cArlet
{
  private $cAuth;
  private $cCrypto;
  private $cDatabase;

  public function __construct()
  {
    $this->cAuth = new cAuth();
    $this->cCrypto = new cCrypto();
    $this->cDatabase = new cDatabase("arlet_digysoft");
  }

  public function f_login($data)
  {
    $params = ['u','p'];

    if( $this->check_params( $data , $params ) )
    {
      if( $this->cAuth->f_internal_login( $data["u"] , $data["p"] ) )
      {
        // Obtain important data for login procedure
        $id = $this->cAuth->f_get_id();

        if( $id == null )
          $this->set_error("SERVER ERROR: There was an unexpected server error. Please try again later.");

        $n = $this->cDatabase->execute(
          "SELECT b_new AS 'n' , IF( f_role = 1 , 1 , 0 ) AS 'a' FROM user WHERE id = :id;",
          [ "id" => $id ],
          "one"
        );

        // Generates token and sends it as a cookie
        $t = $this->cAuth->f_generate_token($data);
        $this->cAuth->f_generate_cookie("arlet_token",$t);

        // Returns login data
        return json_encode( $n , true );
      }
      else $this->set_error("Login failed. Please check credentials");
    }
    else die();
  }

  public function f_autologin()
  {
    if( $this->cAuth->f_check_token() )
    {
      $id = $this->cAuth->f_get_id();
      $company = $this->cAuth->f_get_company();

      if( $id == null || $company == null )
        return $this->set_error("SERVER ERROR: There was an unexpected error in the server. Please try again later.");

      $n = $this->cDatabase->execute(
        "SELECT b_new AS 'n' , IF( f_role = 1 , 1 , 0 ) AS 'a' FROM user WHERE id = :id AND f_company = :company;",
        [ "id" => $id , "company" => $company ],
        "one"
      );

      return json_encode( $n , true );
    }
    else return $this->cAuth->set_error();
  }

  public function f_get()
  {
    if( $this->cAuth->f_check_token() )
    {
      $company = $this->cAuth->f_get_company();
      if( $company == null )
        $this->set_error("SERVER ERROR: There was an unexpected error in the server. Please try again later.");

      $r = $this->cDatabase->execute(
        "SELECT u.id AS 'uid', u.name AS 'name', u.lastname AS 'lastname', u.username AS 'username', u.email AS 'email' , r.name AS 'role' , u.b_archive AS 'archived' FROM user u LEFT JOIN role r on u.f_role = r.id WHERE f_company = :company;",
        [ "company" => $company ]
      );

      $active = [];
      $inactive= [];

      foreach($r as &$user)
      {
        $user['name'] = $this->cCrypto->decrypt($user["name"]);
        $user['lastname'] = $this->cCrypto->decrypt($user["lastname"]);
        $user['email'] = $this->cCrypto->decrypt($user["email"]);

        $isArchived = $user["archived"];

        unset($user["archived"]);


        if( $isArchived )
          array_push( $inactive , $user );
        else
          array_push( $active , $user );
      }

      $return = [
        "n_active" => count($active),
        "n_inactive" => count($inactive),
        "active" => $active,
        "inactive" => $inactive
      ];
      return json_encode( $return , true );
    }
    else $this->cAuth->set_error();
  }

  public function f_create($data)
  {
    $param = ["name","lastname","username","email","pwd","role"];
    if( $this->check_params($data,$param) )
    {
      if( $this->cAuth->f_check_token() )
      {
        $company = $this->cAuth->f_get_company();
        $q = $this->cDatabase->execute(
          "SELECT true FROM user WHERE username = :u AND f_company = :c;",
          [ "u" => $data["username"] , "c" => $company ],
          "one"
        );

        if( $q != false )
          return $this->set_error("User already exists");

        if( $company == null )
          $this->set_error("SERVER ERROR: There was an unexpected error in the server. Please try again later.");

        foreach($data as $d => &$v)
        {
          switch( $d )
          {
            case "username":
            case "role":
            case "pfp_image_data":
              break;
            default:
              $v = $this->cCrypto->encrypt($v);
          }
        }

        if( isset($_FILES["image"]) )
        {
          if( !isset($data["pfp_image_data"]) )
            return $this->set_error("Incomplete upload data");

          $img = $_FILES["image"];

          if( $img["error"] !== UPLOAD_ERR_OK )
            return $this->set_error("Unexpected error uploading");

          $dir = $_SERVER["DOCUMENT_ROOT"] . "/cdn/";
          $tmp = $img["tmp_name"];
          $ext = pathinfo($img["name"],PATHINFO_EXTENSION);

          $typ = mime_content_type($tmp);
          if( strpos( $typ , "image" ) === false )
            return $this->set_error("Unexpected error");

          $final = $dir . time() . "_" . uniqid() . ".$ext";

          if( move_uploaded_file( $tmp , $final ) )
            $data["pfp_image_url"] = $final;
          else
            return $this->set_error("Couldn't upload image");
        }
        else
        {
          $data["pfp_image_data"] = null;
          $data["pfp_image_url"] = null;
        }

        $q = $this->cDatabase->execute(
          USER_INSERTION,
          array_merge(
            $data,
            [ "company" => $company ]
          )
        );

        return true;
      }
      else $this->cAuth->set_error();
    }
    else die();
  }

  public function f_availability($ids,$available)
  {
    if( $this->cAuth->f_check_token() )
    {
      if( !empty($ids) && is_numeric($available) )
      {
        $placeholders = implode(",",array_fill(0,count($ids),"?"));
        $cDB = $this->cDatabase->getObj();
        $q = $cDB->prepare("UPDATE user SET b_archive = $available WHERE id IN ($placeholders);");
        $q->execute($ids); 
        return true;
      }
      else return json_encode(["error"=> true, "message"=>"No Users"],true);
    }
    else $this->cAuth->set_error();
  }
}
