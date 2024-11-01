<?php

function wpic_display_custom_product_checkout_page_frontend(){
  
  $cart_item = wpic_get_session('cart_data');
  if(empty($cart_item)){
   wp_redirect(get_option( 'siteurl' ).'/?page_id='.get_option( 'Cart' ));
   exit;
  }
  
  wp_enqueue_script( 'checkoutjs',WPIC_CUSTOM_PRODUCT_URL.'/resource/checkout/js/checkout.js');
  wp_localize_script( 'checkoutjs', 'scpdAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
  wp_enqueue_style ( 'checkoutcss',WPIC_CUSTOM_PRODUCT_URL.'/resource/checkout/css/checkout.css');
  $checkout_output='';
  if(isset($_POST['checkout_submit'])){
    $checkoutdata = wpic_save_checkout_data();
    if(isset($_POST['checkout_gateway'])){
      $response = wpic_checkout_gateway_call(sanitize_text_field($_POST['checkout_gateway']));

      if($response['error']==0){
        $location = ''.get_option('site_url').'?page_id='.get_option('Payment Result').'&code=success';
        ob_clean();
        wp_redirect( $location ); 
        exit();
      }else{
        $checkout_output .='<div class="checkout_error">'.$response['message'].'</div>';
      }
    }
  }
  $checkout_output .= '<form name="wpic_checkout" id="wpic_checkout" method="post" action="" >';
  ob_start();
  wpic_get_checkout_sections();
  $checkout_output .= ob_get_contents();
  ob_end_clean();
  $checkout_output .= '<input type="hidden" name="checkout_submit" value="true"/></form>';
  return $checkout_output;
}

function wpic_get_checkout_sections(){
  $billdata = wpic_get_billing();
  if(empty($billdata)){
    $billdata['billing_first_name']='';
    $billdata['billing_last_name']='';
    $billdata['billing_email']='';
    $billdata['billing_address_1']='';
    $billdata['billing_address_2']='';
    $billdata['billing_city']='';
    $billdata['billing_zip']='';
    $billdata['billing_state']='';
    $billdata['billing_country']='';
    $billdata['billing_phone']='';
    $billdata['wpic_same_shipping']='';
  }
  $shipdata = wpic_get_shipping();
  if(empty($shipdata)){
    $shipdata['shipping_first_name']='';
    $shipdata['shipping_last_name']='';
    $shipdata['shipping_email']='';
    $shipdata['shipping_address_1']='';
    $shipdata['shipping_address_2']='';
    $shipdata['shipping_city']='';
    $shipdata['shipping_zip']='';
    $shipdata['shipping_state']='';
    $shipdata['shipping_country']='';
    $shipdata['shipping_phone']='';
  }
  ?>
  <div class="wpic_checkout">
    <ul class="wpic_checkout_steps">
      <li id="wpic_billing" class="wpic_checkout_section enable">
        <div class="wpic_checkout_title active">Billing Info</div>
        <div class="wpic_step" style="display:block;">
          <table>
            <tr>
              <td class="field_title">First Name <span class="wpic_required">*</span></td>
              <td>
                <input type="text" name="billing_first_name" id="billing_first_name" class="checkout_required" value="<?php echo $billdata['billing_first_name'];?>" />
              </td>
            </tr>
            <tr>
              <td class="field_title">Last Name <span class="wpic_required">*</span></td>
              <td><input type="text" name="billing_last_name" id="billing_last_name" class="checkout_required" value="<?php echo $billdata['billing_last_name'];?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Email <span class="wpic_required">*</span></td>
              <td><input type="text" name="billing_email" id="billing_email" class="checkout_required" value="<?php echo $billdata['billing_email'];?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Address 1 <span class="wpic_required">*</span></td>
              <td><input type="text" name="billing_address_1" id="billing_address_1" class="checkout_required" value="<?php echo $billdata['billing_address_1'];?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Address 2</td>
              <td><input type="text" name="billing_address_2" id="billing_address_2" value="<?php echo $billdata['billing_address_2'];?>" /></td>
            </tr>
            <tr>
              <td class="field_title">City <span class="wpic_required">*</span></td>
              <td><input type="text" name="billing_city" id="billing_city" class="checkout_required" value="<?php echo $billdata['billing_city'];?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Zip/Postal Code <span class="wpic_required">*</span></td>
              <td><input type="text" name="billing_zip" id="billing_zip" class="checkout_required" value="<?php echo $billdata['billing_zip'];?>" /></td>
            </tr>
            <tr>
              <td class="field_title">State <span class="wpic_required">*</span></td>
              <td><input type="text" name="billing_state" id="billing_state" class="checkout_required" value="<?php echo $billdata['billing_state'];?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Country <span class="wpic_required">*</span></td>
              <td>
                <?php $country_data  =  wpic_currency_table_query(); ?>
                <select name="billing_country" >
                  <?php
                  foreach($country_data as $cur_name){
                   echo '<option value="'.$cur_name->isocode.'" '.($billdata['billing_country']==$cur_name->isocode? 'selected="slected"':'').'>'.$cur_name->country.'</option>';
                  }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td class="field_title">Phone <span class="wpic_required">*</span></td>
              <td><input type="text" name="billing_phone" id="billing_phone" class="checkout_required" value="<?php echo $billdata['billing_phone'];?>" /></td>
            </tr>
          </table>
          <input type="checkbox" name="wpic_same_shipping" <?php if($billdata['wpic_same_shipping']){echo 'checked="checked"';}?> /> Ship to same address
          <a class="checkout_billing checkout_a_btn">Next</a>
          <div class="clear"></div>
        </div>
      </li>
      <li id="wpic_shipping" class="wpic_checkout_section">
        <div class="wpic_checkout_title">Shipping Info</div>
        <div class="wpic_step">
          <table>
            <tr>
              <td class="field_title">First Name <span class="wpic_required">*</span></td>
              <td><input type="text" name="shipping_first_name" id="shipping_first_name" class="checkout_required" value="<?php echo $shipdata['shipping_first_name']?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Last Name <span class="wpic_required">*</span></td>
              <td><input type="text" name="shipping_last_name" id="shipping_last_name" class="checkout_required" value="<?php echo $shipdata['shipping_last_name']?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Email</td>
              <td><input type="text" name="shipping_email" id="shipping_email" value="<?php echo $shipdata['shipping_email']?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Address 1 <span class="wpic_required">*</span></td>
              <td><input type="text" name="shipping_address_1" id="shipping_address_1" class="checkout_required" value="<?php echo $shipdata['shipping_address_1']?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Address 2</td>
              <td><input type="text" name="shipping_address_2" id="shipping_address_2" value="<?php echo $shipdata['shipping_address_2']?>" /></td>
            </tr>
            <tr>
              <td class="field_title">City <span class="wpic_required">*</span></td>
              <td><input type="text" name="shipping_city" id="shipping_city" class="checkout_required" value="<?php echo $shipdata['shipping_city']?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Zip/Postal Code <span class="wpic_required">*</span></td>
              <td><input type="text" name="shipping_zip" id="shipping_zip" class="checkout_required" value="<?php echo $shipdata['shipping_zip']?>" /></td>
            </tr>
            <tr>
              <td class="field_title">State <span class="wpic_required">*</span></td>
              <td><input type="text" name="shipping_state" id="shipping_state" class="checkout_required" value="<?php echo $shipdata['shipping_state']?>" /></td>
            </tr>
            <tr>
              <td class="field_title">Country <span class="wpic_required">*</span></td>
              <td>
                <?php $country_data  =  wpic_currency_table_query(); ?>
                <select name="shipping_country" id="shipping_country">
                  <?php
                  foreach($country_data as $cur_name){
                   echo '<option value="'.$cur_name->isocode.'" '.($shipdata['shipping_country']==$cur_name->isocode? 'selected="slected"':'').'>'.$cur_name->country.'</option>';
                  }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td class="field_title">Phone <span class="wpic_required">*</span></td>
              <td><input type="text" name="shipping_phone" id="shipping_phone" class="checkout_required" value="<?php echo $shipdata['shipping_phone']?>" /></td>
            </tr>
          </table>
          <a class="checkout_shipping checkout_a_btn" >Next</a>
          <div class="clear"></div>
        </div>
      </li>
      <li id="wpic_shipping_method" class="wpic_checkout_section">
        <div class="wpic_checkout_title">Shipping Method</div>
        <div class="wpic_step">
          <div id="wpic_ship_container"></div>
          <?php //wpic_shipping_frontend_form();?>
          <div id="wpic_shipping_error"></div>
          <a class="checkout_shipping_method checkout_a_btn">Next</a>
          <div class="clear"></div>
        </div>
      </li>
      <li id="wpic_payment_method" class="wpic_checkout_section">
        <div class="wpic_checkout_title">Payment Method</div>
        <div class="wpic_step">
          <?php wpic_gateway_frontend_form(); ?>
          <div id="wpic_payment_error"></div>
          <a class="checkout_payment_method checkout_a_btn">Next</a>
          <div class="clear"></div>
        </div>
      </li>
      <li id="wpic_overview" class="wpic_checkout_section">
        <div class="wpic_checkout_title">Overview</div>
        <div class="wpic_step">
          <div id="wpic_overview_content">   
          </div>
          <a class="checkout_submit checkout_a_btn">Checkout</a>
          <div class="clear"></div>
        </div>
      </li>
    </ul>
  </div>
  <?php
}

add_shortcode('wpic_checkout_page', 'wpic_display_custom_product_checkout_page_frontend');

function wpic_checkout_overview_content(){
  ?>
  <table class="wpic_overview_table">
    <tr>
      <th>Sn.</th>
      <th>Product Name</th>
      <th>SKU</th>
      <th>Quantity</th>
      <th>Price</th>
      <th>Sub Total</th>
    </tr>
    <?php
    $amount_data = wpic_get_total_amount();
    $cart_data = wpic_get_session('cart_data');
    $i = 1;
    foreach($cart_data as $key=>$cart_item){
      echo '<tr>';
      echo '<td>'.$i.'</td>';
      echo '<td width="200px">';
      echo $cart_item['prods']['title'];
      if(isset($cart_item['prods']['options']['attribute'])){
        foreach($cart_item['prods']['options']['attribute'] as $attr_key=>$attr_val){
          echo ', '.$attr_key.': '.$attr_val;
        }
      }
      echo '</td>';
      echo '<td>'.$cart_item['prods']['sku'].'</td>';
      echo '<td>'.$cart_item['prods']['quantity'].'</td>';
      echo '<td>'.wpic_get_display_price($cart_item['prods']['price']).'</td>';
      echo '<td>'.wpic_get_display_price(($cart_item['prods']['price']*$cart_item['prods']['quantity'])).'</td>';
      echo '</tr>';
      $i++;
    }
    ?>
    <tr class="wpic_overview_total_sec">
      <td colspan="5" class="wpic_overview_total_td">Total :</td>
      <td><?php echo wpic_get_display_price($amount_data['product_total']); ?></td>
    </tr>
    <tr class="wpic_overview_total_sec">
      <td colspan="5" class="wpic_overview_total_td">Shipping & Handling Fee :</td>
      <td><?php echo wpic_get_display_price($amount_data['shipping']); ?></td>
    </tr>
    <tr class="wpic_overview_total_sec">
      <td colspan="5" class="wpic_overview_total_td">Tax :</td>
      <td><?php echo wpic_get_display_price($amount_data['tax']); ?></td>
    </tr>
    <tr class="wpic_overview_total_sec">
      <td colspan="5" class="wpic_overview_total_td">Grand Total :</td>
      <td>
        <?php echo wpic_get_display_price($amount_data['grand_total']); ?>
      </td>
    </tr>
  </table>
  <?php
  die();
}
add_action( 'wp_ajax_wpic_checkout_overview', 'wpic_checkout_overview_content' );
add_action( 'wp_ajax_nopriv_wpic_checkout_overview', 'wpic_checkout_overview_content' );

function wpic_set_checkout_billing(){
  $request_data = wpic_sanitize_array($_POST['bill_data']);
  $checkout_session=(array)wpic_get_session('wpic_checkout');
  $checkout_session['wpic_billing'] = $request_data;
  wpic_set_session('wpic_checkout',$checkout_session);
  $response = array('response'=> '1');
  echo json_encode($response);
  die();
}
add_action( 'wp_ajax_wpic_checkout_billing', 'wpic_set_checkout_billing' );
add_action( 'wp_ajax_nopriv_wpic_checkout_billing', 'wpic_set_checkout_billing' );

function wpic_set_checkout_shipping(){
  $request_data = wpic_sanitize_array($_POST['ship_data']);
  $checkout_session=wpic_get_session('wpic_checkout');
  $checkout_session['wpic_shipping'] = $request_data;
  wpic_set_session('wpic_checkout',$checkout_session);
  $response = array('response'=> '1');
  echo json_encode($response);
  die();
}
add_action( 'wp_ajax_wpic_checkout_shipping', 'wpic_set_checkout_shipping' );
add_action( 'wp_ajax_nopriv_wpic_checkout_shipping', 'wpic_set_checkout_shipping' );

function wpic_get_total_amount(){
  $amount =array();
  $cart_amount=0;
  $cart_data = wpic_get_session('cart_data');
  $checkout_shipping_method = wpic_get_shipping_method();
  $shipping_rate = $checkout_shipping_method['rate'];
  $tax = get_option('wpic_base_tax');
  if ((!empty($cart_data))){
    foreach($cart_data as $cart_item){
      $product_qty = $cart_item['prods']['quantity'];
      $product_price = $cart_item['prods']['price'];
      $cart_amount=$cart_amount+($product_price*$product_qty);
    }
    $tax_amount = ($tax/100)*$cart_amount;
    $grand_total=$cart_amount+$shipping_rate+$tax_amount;
  }
  $amount = array(
        'product_total' => $cart_amount,
        'shipping'      => $shipping_rate,
        'tax'           => $tax_amount,
        'grand_total'   => $grand_total
    );
  return $amount;
}

function wpic_get_billing(){
  $checkout_session=wpic_get_session('wpic_checkout');
  return $checkout_session['wpic_billing'];
}

function wpic_get_shipping(){
  $checkout_session=wpic_get_session('wpic_checkout');
  return ((isset($checkout_session['wpic_shipping']))?$checkout_session['wpic_shipping']:'');
}

function wpic_get_shipping_method(){
  $checkout_session = wpic_get_session('wpic_checkout');
  return $checkout_session['active_shipping_method'];
}

function wpic_get_product_total_weight(){
  $weight=array();
  $product_weight =0;
  $cart_weight =0;
  $cart_data = wpic_get_session('cart_data');
  if ((!empty($cart_data))){
    foreach($cart_data as $cart_item){
      $product_qty = $cart_item['prods']['quantity'];
      $product_weight = $cart_item['prods']['weight'];
      $cart_weight=$cart_weight+($product_weight*$product_qty);
    }
  }
  $weight = array('product_weight'=>$cart_weight);
  return $weight;
}

function wpic_save_checkout_data(){
  global $wpdb;
  $checkout_billing = wpic_get_billing();
  $checkout_shipping = wpic_get_shipping();
  $checkout_shipping_method = wpic_get_shipping_method();
  $checkout_amount = wpic_get_total_amount();
  $checkout_payment_method = $_POST['checkout_gateway'];
  $wpic_table_prefix = wpic_get_custom_table_prefix();
  $wpdb->insert($wpdb->prefix.$wpic_table_prefix.'order',
          array(
              'user_id'=>'',
              'billing_name'      => $checkout_billing['billing_first_name'].' '.$checkout_billing['billing_last_name'],
              'billing_email'     => $checkout_billing['billing_email'],
              'billing_address1'  => $checkout_billing['billing_address_1'],
              'billing_address2'  => $checkout_billing['billing_address_2'],
              'billing_city'      => $checkout_billing['billing_city'],
              'billing_state'     => $checkout_billing['billing_state'],
              'billing_zip'       => $checkout_billing['billing_zip'],
              'billing_country'   => $checkout_billing['billing_country'],
              'billing_phone'     => $checkout_billing['billing_phone'],
              'shipping_name'     => $checkout_shipping['shipping_first_name'].' '.$checkout_shipping['shipping_last_name'],
              'shipping_email'    => $checkout_shipping['shipping_email'],
              'shipping_address1' => $checkout_shipping['shipping_address_1'],
              'shipping_address2' => $checkout_shipping['shipping_address_2'],
              'shipping_city'     => $checkout_shipping['shipping_city'],
              'shipping_state'    => $checkout_shipping['shipping_state'],
              'shipping_zip'      => $checkout_shipping['shipping_zip'],
              'shipping_country'  => $checkout_shipping['shipping_country'],
              'shipping_phone'    => $checkout_shipping['shipping_phone'],
              //'order_date'=>'',
              'order_total'       => $checkout_amount['grand_total'],
              'shipping_price'    => $checkout_shipping_method['rate'],
              'tax'               => $checkout_amount['tax'],
              'product_total'     => $checkout_amount['product_total'],
              'payment_method'    => $checkout_payment_method,
              'shipping_method'   => $checkout_shipping_method['provider'],
              'shipping_option'   => $checkout_shipping_method['shipping_type'],
              'order_status'=>1,
              'extra_charges'=>'',
              'note'=>'',
              'currency_code'=>''
          ),
          array(
              '%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%f','%f','%f','%f','%s','%s','%s','%d','%f','%s','%s'
          )
        );
  $order_id = $wpdb->insert_id;
  $cart_data = wpic_get_session('cart_data');
  foreach($cart_data as $cart_item){
    $prod_id = $cart_item['prods']['id'];
    $prod_sku = $cart_item['prods']['sku'];
    $prod_title = $cart_item['prods']['title'];
    $prod_qty= $cart_item['prods']['quantity'];
    $prod_price=$cart_item['prods']['price'];
    $prod_img=$cart_item['prods']['image'];
    if(isset($cart_item['customitem'])){
      $product_custom = $cart_item['customitem'];
      if($product_custom){
        foreach($product_custom as $key=>$prod_custom){
          copy($prod_custom['screenshot'],WPIC_UPLOADS__CUSTOM_IMAGES_PATH.basename($prod_custom['screenshot'],'.png').'.png');
          $img_url=WPIC_UPLOADS__CUSTOM_IMAGES_URL.basename($prod_custom['screenshot'],'.png').'.png';
          $prod_custom['screenshot'] = $img_url;
          $product_custom[$key]=$prod_custom;
        }
        $cart_item['customitem']=$product_custom;
      }

      $prod_item_val = serialize($cart_item['customitem']);
    }else{
      $prod_item_val = '';
    }
    
    if(isset($cart_item['prods']['options'])){
      $prod_var = serialize($cart_item['prods']['options']);
    }else{
      $prod_var = '';
    }
    
    $wpdb->insert( 
      $wpdb->prefix.$wpic_table_prefix.'order_item', 
      array( 
          'fk_order_id' => $order_id,
          'product_id' => $prod_id,
          'product_sku' => $prod_sku,
          'product_title' => $prod_title, 
          'product_qty' => $prod_qty, 
          'product_price' => $prod_price, 
          'product_attribute' => $prod_item_val,//serialize($prod_item_val), 
          'product_image' => $prod_img,
          'product_variation' => $prod_var
      ),
      array( '%d','%d','%s','%s','%d','%f','%s','%s','%s')
    );
  }
  $checkout_session=wpic_get_session('wpic_checkout');
  $checkout_session['order_data'] = array('order_id'=>$order_id);
  $checkout_session['order_amount']=$checkout_amount;
  wpic_set_session('wpic_checkout',$checkout_session);
  return array('order_id'=>$order_id);
}

function wpic_checkout_gateway_call($gatewayname){
  global $wpdb;
  $wpic_table_prefix = wpic_get_custom_table_prefix();
  $checkout_session=wpic_get_session('wpic_checkout');
  $order_data = $checkout_session['order_data'];
  $order_id = $order_data['order_id'];
  require_once WPIC_CUSTOM_PRODUCT_PATH.'gateway/'.$gatewayname.'.php';
  $gateway_name = 'Wpic_Gateway_'.$gatewayname;
  $gateway = new $gateway_name();
  $gateway_response = $gateway->gateway_action();
  if($gateway_response){
    $wpdb->update($wpdb->prefix.$wpic_table_prefix.'order',
            array('order_status'=>$gateway_response['status_code']),
            array('order_id'=>$order_id)
            );
    return $gateway_response;
  }else{
    return array('error'=>1,'message'=>'Something went wrong please consult with site admin.');
  }
}