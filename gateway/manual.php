<?php

class Wpic_Gateway_manual{
  //public static $unique;
  
  public function __construct(){
    $this->gateway_unique = 'manual';
    $this->gateway_name = 'Manual Payment';
    add_action('wpic_gateway_admin_option_save_'.$this->gateway_unique,array($this,'gateway_admin_option_save'));
  }
  
  public function gateway_admin_form(){
    $admin_html = '
        <tr>
          <th scope="row">Manual Payment Description</th>
          <td><textarea name="wpic_gateway_manual_desc" cols="30" rows="5">'.get_option('wpic_gateway_manual_desc').'</textarea></td>
        </tr>';
    return $admin_html;
  }
  
  public function gateway_admin_option_save(){
    if(isset($_POST['wpic_gateway_manual_desc'])!=null){
      update_option('wpic_gateway_manual_desc',$_POST['wpic_gateway_manual_desc']);
    }
  }
  
  public function gateway_frontend_form(){
    $gateway_output = '<tr><td>';
    $gateway_output .= get_option('wpic_gateway_manual_desc');
    $gateway_output .='</td></tr>';
    return $gateway_output;
  }
  
  public function gateway_action(){
    $response['error'] = 0;
    $response['status_code'] =1;
    $response['message'] = 'payment successful';
    return $response;
  }
  
}