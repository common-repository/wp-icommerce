<?php

function wpic_gateway_list(){
  $gateway_path = WPIC_CUSTOM_PRODUCT_PATH .'gateway';
  $dir = opendir( $gateway_path );
  $i = 0;
  while ( ($file = readdir( $dir )) !== false ) {
    //if($file!='.' && $file!='..' && !stristr( $file, "~" ) && !is_dir($file)){
    if (!is_dir($gateway_path.'/'.$file) ){
      $dirlist[$i] = $file;
      $i++;
    }
  }
  
  return $dirlist;
}

function wpic_admin_gateway_form(){
  $gateway_list = wpic_gateway_list();
  if($_POST){
    update_option('wpic_active_gateway',(isset($_POST['wpic_gateway_active'])?$_POST['wpic_gateway_active']:''));
    update_option('wpic_gateway_title',(isset($_POST['wpic_gateway_title'])?$_POST['wpic_gateway_title']:''));
  }
  $acitive_gateway = get_option('wpic_active_gateway');
  $gateway_title = get_option('wpic_gateway_title');
  $output = '';
  foreach($gateway_list as $dr){
    $gateway_name = 'Wpic_Gateway_'. basename($dr,'.php');
    require_once WPIC_CUSTOM_PRODUCT_PATH.'gateway/'.$dr;
    if(class_exists($gateway_name)){
      $gateway = new $gateway_name();
      if($gateway->gateway_unique){
        do_action('wpic_gateway_admin_option_save_'.$gateway->gateway_unique);
        $output .='<div class="postbox closed" id="postexcerpt" style="margin-bottom:2px;">
                    <div title="Click to toggle" class="handlediv"><br></div>
                    <h3 class="hndle"><span class="gttitle">'.$gateway->gateway_name.'</span> '.(isset($acitive_gateway[$gateway->gateway_unique]) ? '<span class="active_ico"></span>':'').'</h3>
                    <div class="inside">
                      <table class="form-table">
                        <tr>
                          <th scope="row">Enable</th>
                          <td>
                            <input type="checkbox" name="wpic_gateway_active['.$gateway->gateway_unique.']" '.(isset($acitive_gateway[$gateway->gateway_unique]) ? 'checked="checked"':'').' /> 
                            Enable '.$gateway->gateway_name.'
                          </td>
                        </tr>
                        <tr>
                          <th scope="row">Title</th>
                          <td>
                            <input type="text" name="wpic_gateway_title['.$gateway->gateway_unique.']" value="'.(isset($gateway_title[$gateway->gateway_unique])?$gateway_title[$gateway->gateway_unique]:'').'" />
                          </td>
                        </tr>
                        '.$gateway->gateway_admin_form().'
                      </table>
                    </div>
                  </div>';
      }
    }
  }
  return $output;
}

function wpic_gateway_frontend_form(){
  $active_gateway = get_option('wpic_active_gateway');
  $gateway_title = get_option('wpic_gateway_title');
  if($active_gateway){
    foreach($active_gateway as $key=>$val){
      if(file_exists(WPIC_CUSTOM_PRODUCT_PATH.'gateway/'.$key.'.php')){
        require_once WPIC_CUSTOM_PRODUCT_PATH.'gateway/'.$key.'.php';
        $gateway_name = 'Wpic_Gateway_'.$key;

        $gateway = new $gateway_name();
        echo '<div>';
        echo '<input type="radio" name="checkout_gateway" class="checkout_gateway checkout_required" value="'.$gateway->gateway_unique.'" autocomplete="off" /> ';
        if($gateway_title[$gateway->gateway_unique]){
          echo $gateway_title[$gateway->gateway_unique];
        }else{
          echo $gateway->gateway_name;
        }
        echo '</div>';
        echo '<table class="wpic_gateway wpic_gateway_'.$gateway->gateway_unique.'">';
        echo $gateway->gateway_frontend_form();
        echo '</table>';
      }
    }
  }else{
    echo 'There is no active payment gateway available.';
  }
}