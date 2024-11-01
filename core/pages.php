<?php

function wpic_create_custom_page(){
  wpic_create_post_page('Products','[wpcustomproduct]');
  wpic_create_post_page('Cart','[wpic_cart_page]');
  wpic_create_post_page('Checkout','[wpic_checkout_page]');
  wpic_create_post_page('Payment Result','[wpic_payment_result_page]');
}

function wpic_create_post_page($title,$content){  
  global $user_ID;
  $new_page_title = $title;
  $new_page_content = $content;
  $new_page_template = '';
  $page_check = get_page_by_title($new_page_title);
  $new_page = array(
          'post_type' => 'page',
          'post_title' => $new_page_title,
          'post_content' => $new_page_content,
          'post_status' => 'publish',
          'comment_status' => 'closed',          
          'post_author' => $user_ID
  );
  if(!isset($page_check->ID)){
    $new_page_id = wp_insert_post($new_page);
    update_option( $title, $new_page_id );
  }
}

add_action( 'init', 'wpic_create_custom_page' );