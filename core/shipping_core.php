<?php

function wpic_shipping_list(){
  $shipping_path = WPIC_CUSTOM_PRODUCT_PATH .'shipping';
  $dir = opendir( $shipping_path );
  $i = 0;
  while ( ($file = readdir( $dir )) !== false ) {
    //if($file!='.' && $file!='..'){
    if (!is_dir($shipping_path.'/'.$file) ){
      $dirlist[$i] = $file;
      $i++;
    }
  }
  return $dirlist;
}

function wpic_admin_shipping_form(){
  $shipping_list = wpic_shipping_list();
  if($_POST){
    update_option('wpic_active_shipping',(isset($_POST['wpic_shipping_active'])?$_POST['wpic_shipping_active']:''));
    update_option('wpic_shipping_title',(isset($_POST['wpic_shipping_title'])?$_POST['wpic_shipping_title']:''));
  }
  $acitive_shipping = get_option('wpic_active_shipping');
  $shipping_title = get_option('wpic_shipping_title');
  $output = '';
  foreach($shipping_list as $dr){
    $shipping_name = 'Wpic_Shipping_'. basename($dr,'.php');
    require_once WPIC_CUSTOM_PRODUCT_PATH.'shipping/'.$dr;
    if(class_exists($shipping_name)){
      $shipping = new $shipping_name();
      do_action('wpic_shipping_admin_option_save_'.$shipping->shipping_unique);
      $output .='<div class="postbox closed" id="postexcerpt" style="margin-bottom:2px;">
                  <div title="Click to toggle" class="handlediv"><br></div>
                  <h3 class="hndle"><span class="gttitle">'.$shipping->shipping_name.'</span> '.(isset($acitive_shipping[$shipping->shipping_unique])?'<span class="active_ico"></span>':'').'</h3>
                  <div class="inside">
                    <table class="form-table">
                      <tr>
                        <th scope="row">Enable</th>
                        <td>
                          <input type="checkbox" name="wpic_shipping_active['.$shipping->shipping_unique.']" '.(isset($acitive_shipping[$shipping->shipping_unique]) ? 'checked="checked"':'').' /> 
                          Enable '.$shipping->shipping_name.'
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">Title</th>
                        <td>
                          <input type="text" name="wpic_shipping_title['.$shipping->shipping_unique.']" value="'.(isset($shipping_title[$shipping->shipping_unique])?$shipping_title[$shipping->shipping_unique]:'').'" />
                        </td>
                      </tr>
                      '.$shipping->shipping_admin_form().'
                    </table>
                  </div>
                </div>';
    }
  }
  return $output;
}

function wpic_shipping_frontend(){
  $active_shipping = get_option('wpic_active_shipping');
  $shipping_title = get_option('wpic_shipping_title');
  if($active_shipping){
    foreach($active_shipping as $key=>$val){
      if(file_exists(WPIC_CUSTOM_PRODUCT_PATH.'shipping/'.$key.'.php')){
        require_once WPIC_CUSTOM_PRODUCT_PATH.'shipping/'.$key.'.php';
        $shipping_name = 'Wpic_Shipping_'.$key;

        $shipping = new $shipping_name();
        echo '<div>';
        //echo '<input type="radio" name="checkout_shipping" class="checkout_shipping checkout_required" value="'.$shipping->shipping_unique.'" /> ';
        if($shipping_title[$shipping->shipping_unique]){
          echo $shipping_title[$shipping->shipping_unique];
        }else{
          echo $shipping->shipping_name;
        }
        echo '</div>';
        echo '<table class="wpic_shipping wpic_shipping_'.$shipping->shipping_unique.'">';
        echo wpic_generate_shipping_data($shipping->shipping_unique,$shipping->shipping_frontend_rate());
        echo '</table>';
      }
    }
  }else{
    echo 'There is no active shipping method available.';
  }
  die();
}

add_action( 'wp_ajax_wpic_shipping_methods', 'wpic_shipping_frontend' );
add_action( 'wp_ajax_nopriv_wpic_shipping_methods', 'wpic_shipping_frontend' );

function wpic_generate_shipping_data($provider,$rate){
  $checkout_session=wpic_get_session('wpic_checkout');
  $checkout_session['shipping_method'][$provider] = $rate;
  wpic_set_session('wpic_checkout',$checkout_session);
  foreach($rate as $key=>$val){
    echo '<tr><td><input type="radio" name="wpic_checkout_shipping_method" class="checkout_required" value="'.$provider.'_'.$key.'" /> '.wpic_get_display_price($val['rate']).' '.$val['shipping_type']. '<br /></td></tr>';
  }
}

function wpic_update_shipping_session(){
  if($_POST['ship_method']){
    $request_data = $_POST['ship_method'];
    $shipping_data = explode('_',$request_data['wpic_checkout_shipping_method']);
    $checkout_session=wpic_get_session('wpic_checkout');
    $shipping_method = $checkout_session['shipping_method'][$shipping_data[0]][$shipping_data[1]];
    $new_shipping_data = array(
                          'provider'=>$shipping_data[0],
                          'rate'=>$shipping_method['rate'],
                          'shipping_type'=>$shipping_method['shipping_type']
                        );
    $checkout_session['active_shipping_method']=$new_shipping_data;
    wpic_set_session('wpic_checkout',$checkout_session);
  }
  $response = array('response'=> '1');
  echo json_encode($response);
  die();
}
add_action( 'wp_ajax_wpic_shipping_method_update', 'wpic_update_shipping_session' );
add_action( 'wp_ajax_nopriv_wpic_shipping_method_update', 'wpic_update_shipping_session' );