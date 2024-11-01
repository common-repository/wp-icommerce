<?php

function wpic_success_page(){
  if(isset($_GET['code'])){
    if($_GET['code']=='success'){
      $checkout_session=wpic_get_session('wpic_checkout');
      if(!empty($checkout_session)){
        $order_data=$checkout_session['order_data'];
        $billing_data = $checkout_session['wpic_billing'];
        $amount_data = $checkout_session['order_amount'];
        if(!empty($order_data)){
          $admin_email = stripslashes(get_option( 'wpic_admin_email' ));
          $admin_email_subject = "You have an order. Transaction Id ".$order_data['order_id']."";
          $admin_message = 'You have a order. Transaction Id <a href="'.admin_url().'edit.php?post_type=wpic_product&page=wpic_order_page&order_id='.$order_data['order_id'].'">'.$order_data['order_id'].'</a>';
          wp_mail($admin_email, $admin_email_subject, $admin_message);
          
        }
        if(!empty($billing_data) && !empty($amount_data) && !empty($order_data)){
          $user_email = stripslashes($billing_data['billing_email']);
          $user_email_subject = "Thank you for purchase Item. Your transaction Id ".$order_data['order_id']."";
          $user_message = 'Dear '.$billing_data['billing_first_name'].' '.$billing_data['billing_last_name'].', <br> Thank you for your purchase. Your Total cost is '.wpic_get_display_price($amount_data['grand_total']).'.<br> Your Transaction Id is '.$order_data['order_id'].'. <br> For any kind of information plz contact at '.get_option( 'admin_email' ).'<br> Best Regurds,<br><a href="'.get_option('siteurl').'">'.get_option('blogname').'</a>';
          wp_mail($user_email, $user_email_subject, $user_message);
        }
        
        $thanks_msg = 'Thank you for your purchase.<br />Your order id is '.$order_data['order_id'].'<br /> Order amount '.wpic_get_display_price($amount_data['grand_total']);
        wpic_session_end();
        return $thanks_msg;
      }else{
        return 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.';
      }     
    }
  }
  return 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.';
}
add_shortcode('wpic_payment_result_page', 'wpic_success_page');