<?php

include('gateway_core.php');
include('shipping_core.php');

function wpic_set_email_content_type(){
	return "text/html";
}

function wpic_ob_start_function(){
  ob_start();
}

function wpic_session(){
    if(!session_id()) {
        session_start();
    }
}

function wpic_get_custom_table_prefix(){
	global $plugin_custom_table_prefix;
	$plugin_custom_table_prefix = "wpic_";
	return $plugin_custom_table_prefix;
}

function wpic_session_end(){
  session_destroy();
}

function wpic_get_display_price($price)
{
	global $wpdb;
	$currency_symbol='';
	$custom_table_prefix = wpic_get_custom_table_prefix();
	$currency_info = "Select * from ".$wpdb->prefix.$custom_table_prefix."currency_list where code = '".get_option( 'wpic_base_currency' )."' ORDER BY country ASC limit 1";
	$currency_table_info = $wpdb->get_results($currency_info,ARRAY_A);
  if($currency_table_info){
    $currency_symbol = $currency_table_info[0]['symbol'];
    $currency_code = $currency_table_info[0]['code'];
    if($currency_symbol==''){
      $currency_symbol = $currency_code;
    }
  }
	$str_price=number_format($price,2,'.',',');
	$str_price='<span class="wpic_prod_currency">'.$currency_symbol.' </span><span class="wpic_product_price">'.$str_price.'</span>';
	return $str_price;
}

function wpic_currency_table_query (){
	global $wpdb;
	$custom_table_prefix = wpic_get_custom_table_prefix();
	$currency_rst = "Select * from ".$wpdb->prefix.$custom_table_prefix."currency_list ORDER BY country ASC";
	$currency_table_rst = $wpdb->get_results($currency_rst);
	$currency_data = $currency_table_rst;
	return $currency_data;
}

function wpic_set_session($session_name, $session_val){
  $_SESSION[$session_name]=$session_val;
  return 1;
}
function wpic_get_session($session_name){
  return (isset($_SESSION[$session_name])?$_SESSION[$session_name]:0);
}

function wpic_get_country_name_by_country_code($country_code){
  global $wpdb;
  $custom_table_prefix = wpic_get_custom_table_prefix();
  $sql = "SELECT * FROM ".$wpdb->prefix.$custom_table_prefix."currency_list WHERE isocode = '".$country_code."'";
  $result = $wpdb->get_row($sql);
  return $result->country;
}

function wpic_get_alphanumeric_string($string=''){
  return preg_replace("/[^A-Za-z0-9 ]/", '', $string);
}

function wpic_sanitize_array ($data = array()) {
	if (!is_array($data) || !count($data)) {
		return array();
	}
	foreach ($data as $k => $v) {
		if (!is_array($v) && !is_object($v)) {
			$data[$k] = htmlspecialchars(trim($v));
		}
		if (is_array($v)) {
			$data[$k] = wpic_sanitize_array($v);
		}
	}
	return $data;
}

add_action( 'admin_enqueue_scripts', 'wpic_add_admin_additional_script' );
add_action( 'wp_enqueue_scripts', 'wpic_add_frontend_additional_script' );
add_action('init', 'wpic_ob_start_function');
add_action('init', 'wpic_create_upload_dir');

add_action('init', 'wpic_session', 1);
add_action('wp_logout', 'wpic_session_end');
add_action('wp_login', 'wpic_session_end');


add_filter( 'wp_mail_content_type','wpic_set_email_content_type' );
add_action( 'admin_enqueue_scripts', 'wpic_load_custom_wp_admin_style' );
