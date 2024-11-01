<?php

function wpic_display_custom_product_cart_page(){
  $product_data = wpic_sanitize_array($_POST['product_data']);
  $product_custom = $product_data['customitem'];
  if(is_array($product_custom)){
    foreach($product_custom as $key=>$prod_custom){
      $prod_img_data = base64_decode(str_replace('data:image/png;base64,', '', $prod_custom['screenshot']));
      $img_name = $prod_custom['imgid'].'_'.time().rand();
      file_put_contents(WPIC_UPLOADS__TMP_PATH . $img_name . ".png", $prod_img_data);
      $img_url = WPIC_UPLOADS__TMP_URL . $img_name . ".png";
      $prod_custom['screenshot'] = $img_url;
      $product_custom[$key]=$prod_custom;
    }
    $product_data['customitem']=$product_custom;
  }
  $cart_data = wpic_get_session('cart_data');
  if(empty($cart_data)){
    $cart_data=array();
    array_push($cart_data,$product_data);
    wpic_set_session('cart_data',$cart_data);
  }else{
    array_push($cart_data,$product_data);
    wpic_set_session('cart_data',$cart_data);
  }
  echo 1;
  exit;
}

function wpic_display_custom_product_cart_page_frontend(){
  $cart_output = '';
  $cart_data = wpic_get_session('cart_data');
  if(isset($_GET['clear_cart'])){
    $cart_data = 0;
    wpic_set_session('cart_data',$cart_data);
  }
  
  if(isset($_GET['delete_prod'])){
    unset($cart_data[intval($_GET['delete_prod'])]);        
    wpic_set_session('cart_data',$cart_data);
  }
  
  if(isset($_POST['btnupdatecheckout'])){
    foreach($cart_data as $k=>$v){
      $new_prod_quantity = intval($_POST['prod_qty_'.$k]);
      if($new_prod_quantity){
        $cart_data[$k]['prods']['quantity']=$new_prod_quantity;
      }
    }
    wpic_set_session('cart_data',$cart_data);
  }
  $cart_data = wpic_get_session('cart_data');
  if(!empty($cart_data)){
    $cart_output .= '<div class="wpic_cart_container">';
    $cart_output .= '<form name="frmcart" id="frmcart" method="post" action="?page_id='.get_option( 'Cart' ).'&update_prod=1">';
    $cart_output .= '<table>';
    $cart_output .= '<tr>';
    $cart_output .= '<th valign="top">Sn.</th>';
    $cart_output .= '<th valign="top">Name</th>';
    $cart_output .= '<th>Image</td>';
    $cart_output .= '<th valign="top">Quantity</th>';
    $cart_output .= '<th valign="top">Unit Price</th>';
    $cart_output .= '<th valign="top">Sub Total</th>';
    $cart_output .= '<th valign="top">&nbsp;</th>';
    $cart_output .= '</tr>';
    $i=1;
    $total_amount = 0;
    foreach($cart_data as $key=>$cart_item){
      $cart_output .= '<tr>';
      $cart_output .= '<td>'.$i.'</td>';
      $cart_output .= '<td>';
      $cart_output .= '<a href="'.get_permalink($cart_item['prods']['id']).'">'.$cart_item['prods']['title'].'</a>';
      $cart_output .= '<br /><strong>SKU: </strong>'.(isset($cart_item['prods']['options']['wpic_variation_sku'])?$cart_item['prods']['options']['wpic_variation_sku']:$cart_item['prods']['sku']);
      if(isset($cart_item['prods']['options']['attribute'])){
        foreach($cart_item['prods']['options']['attribute'] as $attr_key => $attr_val){
          $cart_output .= '<br /><strong>'.$attr_key.'</strong> : '.$attr_val ;
        }
      }
      $cart_output .= '</td>';
      $img_output = '';
      if(isset($cart_item['customitem'])){
        foreach($cart_item['customitem'] as $cart_img){
          $img_output .= '<img src="'.$cart_img['screenshot'].'" width="60px" height="60px" />';
        }
      }else{
        if($cart_item['prods']['image']){
          $img_output .= '<img src="'.$cart_item['prods']['image'].'" width="60px" height="60px" />';
        }else{
          $img_output .= '<img src="'.WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg" width="60px" height="60px" />';
        }
      }
      $cart_output .= '<td>'.$img_output.'</td>';
      $cart_output .= '<td><input type="text" style="width:50px;" name="prod_qty_'.$key.'" id="prod_qty_'.$key.'" value="'.$cart_item['prods']['quantity'].'" /></td>';
      $cart_output .= '<td>'.wpic_get_display_price($cart_item['prods']['price']).'</td>';
      $cart_output .= '<td>'.wpic_get_display_price($cart_item['prods']['price']*$cart_item['prods']['quantity']).'</td>';
      $cart_output .= '<td><a href="'.get_option('siteurl').'?page_id='.get_option( 'Cart' ).'&delete_prod='.$key.'"><img style="cursor:pointer;" src="'.WPIC_CUSTOM_PRODUCT_URL.'/resource/images/erase.png" width="16px"></a></td>';
      $cart_output .= '</tr>';
      $total_amount = $total_amount + ($cart_item['prods']['price']*$cart_item['prods']['quantity']);
      $i++;
    }
    $cart_output .= '<tr>';
    $cart_output .= '<td colspan="5" class="wpic_cart_total_cell">Total</td>';
    $cart_output .= '<td style="border-right:none;">'.wpic_get_display_price($total_amount).'</td>';
    $cart_output .= '<td style="border-left:none;"></td>';
    $cart_output .= '</tr>';
    $cart_output .= '<tr>';
    $cart_output .= '<td colspan="7" class="wpic_cart_btns">';
    $cart_output .= '<button type="button" class="wpic_clear_cart_btn" onclick="window.location.href=\''.get_option('site_url').'?page_id='.get_option( 'Cart' ).'&clear_cart=1\'">Clear Cart</button>';
    $cart_output .= '<button type="submit" name="btnupdatecheckout" value="btnupdatecheckout" class="wpic_submit_cart_btn">Update Cart</button>';
    $cart_output .= '<button type="button" class="wpic_cart_checkout_btn" onclick="window.location.href=\''.get_option('siteurl').'?page_id='.get_option( 'Checkout' ).'&checkout=1\'">Checkout</button>';
    $cart_output .= '</td>';
    $cart_output .= '</tr>';
    
    $cart_output .='</table></form></div>';
  }else{
    $cart_output .= '<div> Your Cart is empty. Please <a href="'.get_option('siteurl').'?page_id='.get_option( 'Products' ).'">continue</a> shopping</div>';
  }
  return $cart_output;
  
}
add_action( 'wp_ajax_nopriv_wpic_display_custom_product_cart_page','wpic_display_custom_product_cart_page' );
add_action( 'wp_ajax_wpic_display_custom_product_cart_page', 'wpic_display_custom_product_cart_page' );

add_shortcode('wpic_cart_page', 'wpic_display_custom_product_cart_page_frontend');