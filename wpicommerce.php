<?php
/*
Plugin Name: wpicommerce
Plugin URI: http://www.solvercircle.com
Description: WP iCommerce is a complete ecommerce solution with built-in Products Custom Design facility.  
Version: 1.1.1
Author: SolverCircle
Author URI: http://www.solvercircle.com
*/

define('WPIC_CUSTOM_PRODUCT_URL', plugins_url('',__FILE__));
define('WPIC_CUSTOM_PRODUCT_PATH',plugin_dir_path( __FILE__ ));
$upload = wp_upload_dir();

//Upload Path
$get_upload_dir=$upload['basedir'].'/wpic_uploads/';
define('WPIC_UPLOADS__BASE_PATH',$get_upload_dir);
define('WPIC_UPLOADS__TMP_PATH',$get_upload_dir.'tmp/');
define('WPIC_UPLOADS__CUSTOM_IMAGES_PATH',$get_upload_dir.'custom_images/');
define('WPIC_UPLOADS__USER_UPLOADED_IMAGES_PATH',$get_upload_dir.'user_uploaded_images/');

//Upload URL
$get_upload_url=$upload['baseurl'].'/wpic_uploads/';
define('WPIC_UPLOADS__BASE_URL',$get_upload_url);
define('WPIC_UPLOADS__TMP_URL',$get_upload_url.'tmp/');
define('WPIC_UPLOADS__CUSTOM_IMAGES_URL',$get_upload_url.'custom_images/');
define('WPIC_UPLOADS__USER_UPLOADED_IMAGES_URL',$get_upload_url.'user_uploaded_images/');

include('core/core.php');
include('core/init.php');
include('core/pages.php');
include('admin/product/product.php');
include('admin/logo/logo.php');
include('admin/order/order.php');
include('admin/settings/settings.php');

include ('frontend/products.php');
include ('frontend/cart.php');
include ('frontend/checkout.php');
include ('frontend/success.php');
include ('frontend/template_function.php');


function wpic_add_admin_additional_script(){
  wp_enqueue_script( 'thickbox');
  wp_enqueue_style ( 'thickbox');
  wp_enqueue_media();

  wp_enqueue_script( 'post' );
  wp_enqueue_style ( 'wpic_admin_style',plugins_url( '/resource/admin/css/admin.css', __FILE__ ));
  wp_enqueue_script( 'jquery-no-conflict.js', plugins_url( '/resource/js/jquery-no-conflict.js', __FILE__ ) );
  wp_enqueue_script( 'wpic-admin-js', plugins_url( '/resource/admin/js/wpic-admin.js', __FILE__ ) );
  wp_localize_script( 'wpic-admin-js', 'wpicAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}

function wpic_add_frontend_additional_script(){
	wp_enqueue_style( 'custom.css', plugins_url( '/resource/css/custom.css', __FILE__ ) );
  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'fabricjs', plugins_url( '/resource/js/fabric-1.4.0.min.js', __FILE__ ) );
	wp_enqueue_script( 'jscolor.js', plugins_url( '/resource/js/jscolor/jscolor.js', __FILE__ ) );
	wp_enqueue_script( 'jquery.form.js', plugins_url( '/resource/js/jquery.form.js', __FILE__ ) );
	wp_enqueue_script( 'jquery-ui-core' );	        
	wp_enqueue_script( 'jquery-ui-accordion' );
  wp_enqueue_style( 'wpic_frontend.css', plugins_url( '/resource/css/frontend.css', __FILE__ ) );
}
function wpic_load_custom_wp_admin_style() {
  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'jquery-ui-core' );
  wp_enqueue_script( 'jquery-ui-dialog' );
  wp_enqueue_script( 'jquery-ui-tabs' );
}


register_activation_hook(__FILE__, 'wpic_custom_product_plugin_install');
register_deactivation_hook(__FILE__, 'wpic_custom_product_plugin_uninstall');
register_activation_hook(__FILE__, '_wpic_create_db_table');
