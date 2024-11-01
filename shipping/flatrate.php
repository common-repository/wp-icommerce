<?php

class Wpic_Shipping_flatrate{
  //public static $unique;
  
  public function __construct(){
    $this->shipping_unique = 'flatrate';
    $this->shipping_name = 'Flat Rate';
    add_action('wpic_shipping_admin_option_save_'.$this->shipping_unique,array($this,'shipping_admin_option_save'));
  }
  
  public function shipping_admin_form(){
    $admin_html = '
        <tr>
          <th scope="row">Fee</th>
          <td><input type="text" name="wpic_shipping_flatrate_fee" value="'.get_option('wpic_shipping_flatrate_fee').'" /></td>
        </tr>
        ';
    return $admin_html;
  }
  
  public function shipping_admin_option_save(){
    if(isset($_POST['wpic_shipping_flatrate_fee'])!=null){
      update_option('wpic_shipping_flatrate_fee',$_POST['wpic_shipping_flatrate_fee']);
    }
  }
  
  public function shipping_action(){
    
  }
  
  public function shipping_frontend_rate(){
    $rate = array(1=>array('rate'=> (float)get_option('wpic_shipping_flatrate_fee'), 'shipping_type'=>'fixed'));
    return $rate;
  }
}