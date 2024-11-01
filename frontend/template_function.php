<?php
/*
 * Wpicommerce product class
 */

class Wpic_product{
  
  public $pid;
  public $post;
  
  public function __construct($prod){
    $this->pid = $prod->ID;
    $this->post = $prod;
  }
  public function get_id() {
    return $this->pid;
  }
  
  public function get_price_html() {
    $actual_price = get_post_meta($this->pid,'_prod_actual_price',true);
    return wpic_get_display_price((float)$actual_price);
  }
  
  public function get_sku() {
    return get_post_meta($this->pid,'_prod_sku',true);
  }
  
  public function get_weight() {
    $weight = get_post_meta($this->pid,'_prod_weight',true);
    return $weight .' lbs';
  }
  
  public function get_thumbnail() {
    if ( has_post_thumbnail() ) {
      $thumbnail =  wp_get_attachment_image_src(get_post_thumbnail_id(),'large');
      return $thumbnail[0];
    }else{
      return WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg';
    }
  }
}

function wpic_setup_product_data(){
  global $product,$post;
  $product = new Wpic_product($post);
}

add_action('the_post','wpic_setup_product_data');

function wpic_minicart(){
  echo '<div class="wpicminicart"></div>';
  add_action('wp_footer','wpic_setup_minicart');
}

function wpic_setup_minicart(){
  ?>
  <script>
    jQuery(document).ready(function(){
      var data = {'action': 'wpic_minicart_action', 'myobj':'cart'};
      jQuery.post('<?php echo admin_url( 'admin-ajax.php' );?>', data, function(response) {
        jQuery('.wpicminicart').html(response);
      });
    });
  </script>
  <?php
}

//add_action('wp_footer','wpic_setup_minicart');

function wpic_minicart_fn(){
  $cartobj = wpic_get_session('cart_data');
  $total_price = 0;
  $total_qty = 0;
  if($cartobj){
    foreach($cartobj as $cd){
      $total_price = $total_price + ($cd['prods']['price']*$cd['prods']['quantity']);
      $total_qty = $total_qty + $cd['prods']['quantity'];
    }
  }
  
  echo '<a class="cart-contents button" href="'.get_option('siteurl').'/?page_id='.get_option('Cart').'" title="View your shopping cart">'.$total_qty.' items '. wpic_get_display_price($total_price).'</a>';
  exit;
}

add_action( 'wp_ajax_wpic_minicart_action', 'wpic_minicart_fn' );
add_action( 'wp_ajax_nopriv_wpic_minicart_action', 'wpic_minicart_fn' );

function wpic_product_add_to_cart($product_id=0,$quantity=1){
  $product_data=array();
  $cart_arr['prods'] = array();
  if($product_id){
    $product_data = get_post($product_id);
    $cart_arr['prods']['id']=$product_id;
    $cart_arr['prods']['title'] = $product_data->post_title;
    $cart_arr['prods']['price'] = get_post_meta($product_id,'_prod_actual_price',true);
    $cart_arr['prods']['sku'] = get_post_meta($product_id,'_prod_sku',true);
    $cart_arr['prods']['weight'] = get_post_meta($product_id,'_prod_weight',true);
    $cart_arr['prods']['quantity'] = $quantity;
    $img_url = get_post_meta($product_id,'_custom_product_image',true);
    if(isset($img_url[0]) && $img_url[0]!=''){
      $cart_arr['prods']['image'] = $img_url[0];
    }elseif(has_post_thumbnail()){
      $img_url = wp_get_attachment_image_src(get_post_thumbnail_id(),'large');
      $cart_arr['prods']['image']=$img_url[0];
    }else{
      $cart_arr['prods']['image']=WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg';
    }
    $cart_data = wpic_get_session('cart_data');
    if(empty($cart_data)){
      $cart_data=array();
      array_push($cart_data,$cart_arr);
      wpic_set_session('cart_data',$cart_data);
    }else{
      array_push($cart_data,$cart_arr);
      wpic_set_session('cart_data',$cart_data);
    }
    echo 1;
    exit;
  }
}

function wpic_get_product_variation($prod_id){
  $product_attributes=get_post_meta($prod_id,'_wpic_product_attribute',true);
  if(!$product_attributes){
    return false;
  }
  $product_variation=get_post_meta($prod_id,'_wpic_product_variation',true);
  if(!$product_variation){
    return false;
  }
  $varcount = count($product_attributes);
  $prdvr=array();
  foreach($product_variation as $var){
    foreach($var['attribute'] as $k=>$v){
        $prdvr[$k][]=$v;
    }
  }
  echo '<table class="wpic_prod_var">';
  $cnt = 0;
  foreach($prdvr as $key=>$val){
    echo '<tr><td>';
    echo ucfirst($product_attributes[$key]['name']).' : ';
    echo ' </td><td>';
    echo '<select id="var_ddl_'.$cnt.'" class="var_ddl" att_name="'.$key.'" uid="'.$cnt.'" autocomplete="off" >';
    echo '<option value="">Choose an option</option>';
    foreach(array_unique($val) as $vl){
      if($cnt==0){
        if($vl){
          echo '<option value="'.$vl.'">'.$vl.'</option>';
        }
      }
    }
    echo '</select>';
    echo '</td></tr>';
    $cnt++;
  }
  //echo '<tr><td></td><td><div class="wpic_var_msg"></div></td></tr>';
  echo '</table><div class="wpic_var_msg"></div><input type="hidden" id="is_variation" value="yes" name="is_variation" />';
  ?>
  <script>
    var vararr = <?php echo json_encode($prdvr);?>;
    var varcnt = <?php echo $varcount; ?>;
    var prod_main_variation = <?php echo json_encode($product_variation);?>;
    jQuery(document).ready(function(){
      jQuery('.wpic_tab_prod_variation').on('change','.var_ddl',function(){
        var uid = parseInt(jQuery(this).attr('uid'));
        var val = jQuery(this).val();
        var att_name = jQuery(this).attr('att_name');
        var varindx=arrsearch(val,att_name);
        var next_uid = uid+1;
        var next_att_name = jQuery('#var_ddl_'+next_uid).attr('att_name');
        if(next_att_name){
          for(var j=0;j<varcnt;j++){
            if(j>uid){
              jQuery('#var_ddl_'+j).empty();
              jQuery('#var_ddl_'+j).append('<option value="">Choose an option</option>');
            }
          }
          var i=0;
          for(i=0;i<varindx.length;i++){
            if(vararr[next_att_name][varindx[i]]){
              jQuery('#var_ddl_'+next_uid).append('<option value="'+vararr[next_att_name][varindx[i]]+'">'+vararr[next_att_name][varindx[i]]+'</option>');
            }else{
              jQuery('#var_ddl_'+next_uid).empty();
              jQuery('#var_ddl_'+next_uid).append('<option value="-1">Choose an option</option>');
            }
          }
        }else{
          var k=0;
          var varmtch={};
          for(k=0;k<varcnt;k++){
            varmtch[jQuery('#var_ddl_'+k).attr('att_name')]=jQuery('#var_ddl_'+k).val();
          }
          var new_varmtch = Object.keys(varmtch).map(function(k) { return varmtch[k] });
          var r=-1;
          for(k=0;k<prod_main_variation.length;k++){
            var newarr= Object.keys(prod_main_variation[k]['attribute']).map(function(ks) { return prod_main_variation[k]['attribute'][ks] });
            if(JSON.stringify(new_varmtch)==JSON.stringify(newarr)){
              if(prod_main_variation[k]['wpic_variation_sale_price']>0){
                product.price=prod_main_variation[k]['wpic_variation_sale_price'];
                product.options = prod_main_variation[k];
                var sp = parseFloat(product.price);
                var rp = parseFloat(prod_main_variation[k]['wpic_variation_price']);
                jQuery('.wpic_tab_prod_prc').html('<span class="wpic_ac_price"><span class="wpic_prod_currency">'+currency_code+' </span><span class="wpic_product_price">'+rp.toFixed(2)+'</span></span> <span class="wpic_sl_price"><span class="wpic_prod_currency">'+currency_code+' </span><span class="wpic_product_price">'+sp.toFixed(2)+'</span></span>');
              }else{
                product.price=prod_main_variation[k]['wpic_variation_price'];
                product.options = prod_main_variation[k];
                var rp = parseFloat(product.price);
                //jQuery('.wpic_product_price').html(rp.toFixed(2));
                jQuery('.wpic_tab_prod_prc').html('<span class="wpic_prod_currency">'+currency_code+' </span><span class="wpic_product_price">'+rp.toFixed(2)+'</span>');
              }
            }
          }
        }
      });
      
      function arrsearch(val,indxnm){
        var found =[];
        var i=0;
        var cnts =0;
        for(i=0;i<vararr[indxnm].length;i++){
          if(vararr[indxnm][i]==val){
            found.push(i);
            cnts++;
          }
        }
        if(cnts>0){
          return found;
        }else{
          return -1;
        }
      } 
    });
  </script>
  <?php
}

function wpic_array_isearch($str, $array){
  $found = array();
  foreach ($array as $k => $v)
    if (strtolower($v) == strtolower($str)) $found[] = $k;
  return $found;
}


/*
 * wpic_is_product_page
 */

if(!function_exists('wpic_is_product_page')){
  function wpic_is_product_page(){
    return ( is_post_type_archive( 'wpic_product' ) || is_page(get_option('Products')) ) ? true : false;
  }
}

/*
 * wpic_is_product
 */
if(!function_exists('wpic_is_product')){
  function wpic_is_product(){
    return is_singular( array( 'wpic_product' ) );
  }
}

/*
 * wpic_is_product
 */
if(!function_exists('wpic_is_product_category')){
  function wpic_is_product_category($cat=''){
    return is_tax( 'wpic_category', $cat );
  }
}

/*
 * wpic_is_cart
 */
if(!function_exists('wpic_is_cart')){
  function wpic_is_cart(){
    return is_page(get_option('Cart'));
  }
}

/*
 * wpic_is_checkout
 */
if(!function_exists('wpic_is_checkout')){
  function wpic_is_checkout(){
    return is_page(get_option('Checkout'));
  }
}

/*
 * wpic_is_checkout
 */
if(!function_exists('wpic_is_success_page')){
  function wpic_is_success_page(){
    return is_page(get_option('Payment Result'));
  }
}