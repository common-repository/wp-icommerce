<?php

class Wpic_Gateway_paypal{
  //public static $unique;
  
  public function __construct(){
    $this->gateway_unique = 'paypal';
    $this->gateway_name = 'Paypal';
    $this->gateway_logo = WPIC_CUSTOM_PRODUCT_URL.'/resource/gateway/paypalcc.jpg';
    add_action('wpic_gateway_admin_option_save_'.$this->gateway_unique,array($this,'gateway_admin_option_save'));
  }
  
  public function gateway_admin_form(){
    $admin_html = '
        <tr>
          <th scope="row">Paypal Email</th>
          <td><input type="text" name="wpic_gateway_paypal_email" value="'.get_option('wpic_gateway_paypal_email').'" /></td>
        </tr>
        <tr>
          <th scope="row">Paypal API Username</th>
          <td><input type="text" name="wpic_gateway_paypal_api_username" value="'.get_option('wpic_gateway_paypal_api_username').'" /></td>
        </tr>
        <tr>
          <th scope="row">Paypal API Password</th>
          <td><input type="text" name="wpic_gateway_paypal_api_password" value="'.get_option('wpic_gateway_paypal_api_password').'" /></td>
        </tr>
        <tr>
          <th scope="row">Paypal API Signature</th>
          <td><input type="text" name="wpic_gateway_paypal_api_signature" value="'.get_option('wpic_gateway_paypal_api_signature').'" /></td>
        </tr>
        <tr>
          <th scope="row">Paypal Mode</th>
          <td>
            <select name="wpic_gateway_paypal_mode">
              <option value="sandbox" '.(get_option('wpic_gateway_paypal_mode')=='sandbox'? 'selected="selected"':'').'>Sandbox</option>
              <option value="live" '.(get_option('wpic_gateway_paypal_mode')=='live'? 'selected="selected"':'').'>Live</option>
            </select>
          </td>
        </tr>
        ';
    return $admin_html;
  }
  
  public function gateway_admin_option_save(){
    if(isset($_POST['wpic_gateway_paypal_email'])){
      update_option('wpic_gateway_paypal_email',$_POST['wpic_gateway_paypal_email']);
    }
    if(isset($_POST['wpic_gateway_paypal_api_username'])){
      update_option('wpic_gateway_paypal_api_username',$_POST['wpic_gateway_paypal_api_username']);
    }
    if(isset($_POST['wpic_gateway_paypal_api_password'])){
      update_option('wpic_gateway_paypal_api_password',$_POST['wpic_gateway_paypal_api_password']);
    }
    if(isset($_POST['wpic_gateway_paypal_api_signature'])){
      update_option('wpic_gateway_paypal_api_signature',$_POST['wpic_gateway_paypal_api_signature']);
    }
    if(isset($_POST['wpic_gateway_paypal_mode'])){
      update_option('wpic_gateway_paypal_mode',$_POST['wpic_gateway_paypal_mode']);
    }
  }
  
  public function gateway_frontend_form(){
    $current_year = date( 'Y' );
    $year='';
    for ( $i = 0; $i < 10; $i++ ) {
      $year .= "<option value='" . $current_year . "'>" . $current_year . "</option>\r\n";
      $current_year++;
    }
    $gateway_output = '
      <tr>
        <td>First Name</td>
        <td><input type="text" name="first_name" /></td>
      </tr>
      <tr>
        <td>Last Name</td>
        <td><input type="text" name="last_name" /></td>
      </tr>
      <tr>
        <td>Card Type</td>
        <td>
          <select name="card_type">
            <option value="Visa">Visa</option>
            <option value="Mastercard">Mastercard</option>
            <option value="Discover">Discover</option>
            <option value="Amex">Amex</option>
          </select>  
        </td>
      </tr>
      <tr>
        <td>Card No.</td>
        <td><input type="text" name="card_no" /></td>
      </tr>
      <tr>
        <td>Card Expiry</td>
        <td>
          <select name="card_expiry_month" >
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
          </select>
          <select name="card_expiry_year" >'.$year.'</select>
        </td>
      </tr>
      <tr>
        <td>Card CVV.</td>
        <td><input type="text" name="card_cvv" /></td>
      </tr>
      ';
    return $gateway_output;
  }
  
  public function gateway_action(){
    global $API_Endpoint,$version,$API_UserName,$API_Password,$API_Signature,$nvp_Header;
    $checkout_session=wpic_get_session('wpic_checkout');
    define('USE_PROXY',FALSE);
    define('PROXY_HOST', '127.0.0.1');
    define('PROXY_PORT', '808');
    $API_UserName=get_option( 'wpic_gateway_paypal_api_username' );
    $API_Password=get_option( 'wpic_gateway_paypal_api_password' );
    $API_Signature=get_option( 'wpic_gateway_paypal_api_signature' );
		$paypal_mode=get_option( 'wpic_gateway_paypal_mode' );
		if($paypal_mode=='sandbox'){
      $API_Endpoint ='https://api-3t.sandbox.paypal.com/nvp';
		}
		else{
      $API_Endpoint ='https://api-3t.paypal.com/nvp';
		}
    $version='53.0';
    include (WPIC_CUSTOM_PRODUCT_PATH.'gateway/paypal/CallerService.php');
    
    $paymentType =urlencode('Sale');
    $firstName =urlencode( $_POST['first_name']);
    $lastName =urlencode( $_POST['last_name']);
    $creditCardType =urlencode( $_POST['card_type']);
    $creditCardNumber = urlencode($_POST['card_no']);
    
    $expDateMonth =urlencode($_POST['card_expiry_month']);

    //Month must be padded with leading zero
    $padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);

    $expDateYear =urlencode($_POST['card_expiry_year']);
    
    $cvv2Number = urlencode($_POST['card_cvv']);
    
    $orderamount = $checkout_session['order_amount'];
    $grand_total = $orderamount['grand_total'];
    $amount = urlencode($grand_total);
    $currencyCode=urlencode('USD');

    $nvpstr="&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber&EXPDATE=".$padDateMonth.$expDateYear."&CVV2=$cvv2Number&FIRSTNAME=$firstName&COUNTRYCODE=US&CURRENCYCODE=$currencyCode";
    
    $resArray=hash_call("doDirectPayment",$nvpstr);

    $ack = strtoupper($resArray["ACK"]);

    if($ack!="SUCCESS")  {
      $_SESSION['reshash']=$resArray;
      $response['error'] = 1;
      $response['status_code'] =1;
      $response['message'] = 'You have error processing the payment data';
    }
    if($ack=="SUCCESS")  {             
      $response['error'] = 0;
      $response['status_code'] =3;
      $response['message'] = 'payment successful';
    }
    return $response;
  }
}