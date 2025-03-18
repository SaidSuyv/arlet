<?php

/**
 *************************************
 *                                   *
 *      COMPANY REGISTRATION         *
 *                                   *
 *************************************
*/
$company_insertion = 
  'INSERT INTO company '.
  'values ('.
    ':id,'.
    'NULL,'.
    ':trade_name,'.
    ':tax_type,'.
    ':country_currency,'.
    ':localization,'.
    ':fiscal_address,'.
    ':annex_facility,'.
    ':email,'.
    ':company_name,'.
    ':ruc,'.
    ':certificate_path,'.
    ':certificate_pwd,'.
    ':scnd_sunat_user,'.
    ':scnd_sunat_pwd,'.
    ':sunat_server,'.
    ':consulting_website);';
define(
  "COMPANY_INSERTION",
  $company_insertion
);

/**
 *************************************
 *                                   *
 *          USER CREATION            *
 *                                   *
 *************************************
*/
$user_insertion =
  'INSERT INTO user('.
    'pfp_image_data,'.
    'pfp_image_url,'.
    'name,'.
    'lastname,'.
    'email,'.
    'username,'.
    'pwd,'.
    'f_company,'.
    'f_role) '.
  'VALUES ('.
    ':pfp_image_data,'.
    ':pfp_image_url,'.
    ':name,'.
    ':lastname,'.
    ':email,'.
    ':username,'.
    ':pwd,'.
    ':company,'.
    ':role);';
define(
  "USER_INSERTION",
  $user_insertion
);

/**
 *************************************
 *                                   *
 *        PRODUCT CREATION           *
 *                                   *
 *************************************
*/
$product_insertion = 
  'INSERT INTO products VALUES('.
    ':code,'.
    ':ptype,'.
    ':ipath,'.
    ':desc,'.
    ':cprice,'.
    ':profit,'.
    ':csale,'.
    ':whprice,'.
    ':whminq,'.
    ':flg_inv,'.
    ':stock,'.
    ':min_st,'.
    ':xpdate,'.
    ':sunat_unit,'.
    ':sunat_pcode,'.
    ':igv_per,'.
    ':icbper_am,'.
  ') where ';