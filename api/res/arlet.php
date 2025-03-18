<?php

class cArlet
{
  protected function check_params($var , $params)
  {
    if( !isset($var) )
      return false;

    foreach( $params as $param )
    {
      if( !array_key_exists( $param , $var ) )
        return false;
    }

    return true;
  }

  protected function set_error($message)
  {
    echo json_encode( [ "error" => true , "message" => $message ] , true );
    die();
  }
}