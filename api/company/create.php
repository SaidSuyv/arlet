<?php

if( $_SERVER['REQUEST_METHOD'] != "POST" ) die();

$input = json_decode( file_get_contents("php://input") , true );

if( !isset($input) )
  die();

require '../res/company.php';

$cAPI = new cCompany();

$isDev = isset($_GET['dev']);

if( $isDev )
{
  $input = [
    "trade_name" => "demo",
    "tax_type" => 18,
    "country_currency" => 188,
    "localization" => "none/none/none",
    "fiscal_address" => "none",
    "annex_facility" => "none",
    "email" => "demo@demo.com",
    "company_name" => "demo",
    "ruc" => "12345678910",
    "certificate_path" => "null",
    "certificate_pwd" => "null",
    "scnd_sunat_user" => "null",
    "scnd_sunat_pwd" => "null",
    "sunat_server" => "null",
    "consulting_website" => "none"
  ];
}

echo $cAPI->f_register_company($input);