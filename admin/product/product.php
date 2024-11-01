<?php

function wpic_product_post_type(){
	register_post_type( 'wpic_product',
		array(
			'labels' => array(
				'name' => __( 'Products' ),
				'singular_name' => __( 'Product' ),
        'add_new_item' => __('Add New Product'),
        'edit_item' => __('Edit Product')
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'wpic_products'),
      'menu_icon'   => 'dashicons-cart',
			'supports' => array('title','editor','thumbnail')
		)
	);
  flush_rewrite_rules();
}

function wpic_product_category_init() {
	register_taxonomy(
	  'wpic_category',
	  'wpic_product',
	  array(
	   'label' => __( 'Category' ),
	   'rewrite' => array( 'slug' => 'wpic_category' ),
	   'hierarchical' => true,
     //'show_admin_column' => true,
	  )
	 );
  flush_rewrite_rules();
}

function wpic_product_custom_meta_box() {
  add_meta_box('wpic_prod_opt', 'Product Options', 'wpic_product_custom_meta_box_content_new', 'wpic_product', 'normal', 'high' );

}

function wpic_product_custom_meta_box_content_new(){
  global $post;
  ?>
  <script>
    var wpic_prod_id = <?php echo $post->ID; ?>;
  </script>
  <div class="wpic_product_options">
    <ul id="wpic_tab" class="wpic_tab">
      <li class="current" data-tab="wpic_tab_general">General</li>
      <li data-tab="wpic_tab_images">Images</li>
      <li data-tab="wpic_tab_attributes">Attributes</li>
      <li data-tab="wpic_tab_variation">Variations</li>
    </ul>
    <div class="wpic_tab_contant_area">
      <div class="wpic_loader"><img src="<?php echo WPIC_CUSTOM_PRODUCT_URL.'/resource/admin/img/loader.svg'?>" /></div>
      <div id="wpic_tab_general" class="wpic_tab_content current">
        <?php wpic_display_general_option($post->ID);?>
      </div>
      <div id="wpic_tab_images" class="wpic_tab_content">
        <?php wpic_display_images($post->ID);?>
      </div>
      <div id="wpic_tab_attributes" class="wpic_tab_content">
        <?php wpic_display_attribute_data($post->ID); ?>
      </div>
      <div id="wpic_tab_variation" class="wpic_tab_content">
        <?php wpic_display_variation($post->ID);?>
      </div>
    </div>
    <div class="clear"></div>
  </div>
  <?php
}

function wpic_display_general_option($prod_id){
  $product_actual_price=get_post_meta($prod_id,'_prod_actual_price',true);
  $product_sale_price=get_post_meta($prod_id,'_prod_sale_price',true);
  $pro_sku=get_post_meta($prod_id,'_prod_sku',true);
  $prod_weight=get_post_meta($prod_id,'_prod_weight',true);
  $prod_design_panel=get_post_meta($prod_id,'_prod_design_panel',true);
  ?>
  <p class="form_field">
    <label for="_prod_actual_price">Product Price</label>
    <input type="text" name="_prod_actual_price" id="prod_actual_price" value="<?php echo ($product_actual_price?$product_actual_price:0);?>" />
  </p>
  <p class="form_field">
    <label for="_prod_sale_price">Sale Price</label>
    <input type="text" name="_prod_sale_price" id="prod_sale_price" value="<?php echo ($product_sale_price?$product_sale_price:0);?>" />
  </p>
  <hr>
  <p class="form_field">
    <label for="_prod_sku">SKU</label>
    <input type="text" name="_prod_sku" id="prod_sku" value="<?php echo $pro_sku;?>" />
  </p>
  <p class="form_field">
    <label for="_prod_weight">Weight</label>
    <input type="text" name="_prod_weight" id="prod_weight" value="<?php echo ($prod_weight?$prod_weight:0);?>" />(lbs)
  </p>
  <hr>
  <p>
    <label for="_prod_design_panel">Design Panel</label>
    <input type="checkbox" name="_prod_design_panel" <?php echo ($prod_design_panel?'checked="checked"':'');?> /> Enable
  </p>
  <?php
}

function wpic_display_images($prod_id){
  $front_img=get_post_meta($prod_id,'_custom_product_image',true);
  ?>
  <script type="text/javascript">
    jQuery(document).ready(function($){
      var custom_uploader;
      jQuery('.img_upload_button').click(function(e) {
        var trgfiledid = jQuery(this).data('customid');
        e.preventDefault();
        /*if (custom_uploader) {
          custom_uploader.open();
          return;
        }*/
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#custom_product_image_'+trgfiledid).val(attachment.url);
        });
        custom_uploader.open();
      });
    });
  </script>
  
  <p class="form_field">
    <label for="_custom_product_image_1">Image</label>
    <input type="text" class="code regular-text" name="_custom_product_image[1]" id="custom_product_image_1" value="<?php echo (isset($front_img[1])?$front_img[1]:'');?>" />
    <input id="upload_img_button_1" class="img_upload_button button" type="button" data-customid="1" value="Upload Image"  />
  </p>
  <?php
  if(!empty($front_img[1])){
  ?>
  <div style="text-align:center;"><img src="<?php echo $front_img[1];?>" width="120" height="120" style="margin:auto;" /></div>
  <?php
  }
}

function wpic_display_attribute_data($prod_id){
  $attr_data = get_post_meta($prod_id,'_wpic_product_attribute',true);
  ?>
  <div class="wpic_attribute_section">
    <?php
    if($attr_data){
      foreach($attr_data as $k=>$v){
        ?>
        <div class="wpic_attr_container closed">
          <h3><div class="wpic_attr_title"><?php echo $v['name'];?></div>
            <button class="button wpic_remove_attributes" type="button">Remove</button>
            <div class="wpic_toggle handlediv"></div>
            <div class="clear"></div>
          </h3>
          <div class="wpicattr">
            <table>
              <tr>
                <td >
                  <strong>Attribute Name:</strong><br />
                  <input type="text" class="wpic_attribute_name" name="wpic_attribute_name[]" value="<?php echo $v['name'];?>" />
                </td>
                <td class="wpic_attr_vals">
                  <strong>Attribute Values:</strong><br />
                  <textarea cols="40" name="wpic_attribute_value[]"><?php echo $v['value']; ?></textarea>
                  <p class="description">Enter "," comma spearated text for attribute value.</p>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <?php
      }
    }
    ?>
    <button class="button button-primary wpic_add_attribute" type="button">Add Attribute</button>
    <button class="button wpic_save_attributes" type="button">Save Attributes</button>
  </div>
  <?php
}

add_action('wp_ajax_wpic_save_attr_data','wpic_save_attr_data');

function wpic_save_attr_data(){
  $attrdata = $_POST['attr_data'];
  $prod_id = $_POST['postid'];
  parse_str($attrdata,$data);
  $attr_array = array();
  for($i=0; $i<count($data['wpic_attribute_name']); $i++){
    if($data['wpic_attribute_name'][$i]){
      $attrname = wpic_get_alphanumeric_string($data['wpic_attribute_name'][$i]);
      $attrslug = str_replace(' ','-',strtolower($attrname));
      $attr_array[ $attrslug]=  array('name'=>$attrname, 'value'=>$data['wpic_attribute_value'][$i]);
    }
  }
  update_post_meta($prod_id,'_wpic_product_attribute',$attr_array);
  print_r($attr_array);
  wp_die();
}

function wpic_display_variation($prod_id){
  $var_data = get_post_meta($prod_id,'_wpic_product_variation',true);
  $attr_data = get_post_meta($prod_id,'_wpic_product_attribute',true);
  ?>
  <div class="wpic_variation_section">
    <div class="wpic_message"></div>
    <?php
    if($var_data){
      foreach($var_data as $key=>$val){
        echo '<div class="wpic_var_container closed">';
        echo '<h3>';
        echo '<div class="wpic_var_title">';
        foreach($attr_data as $k=>$v){
          echo '<select name="attribute_'.$k.'[]" autocomplete="off" ><option value="">Choose '.$v['name'].'</option>';
          $vals = explode(',',$v['value']);
          foreach($vals as $vls){
            $attrvals = wpic_get_alphanumeric_string($vls);
            $attrvals = str_replace(' ','-',$attrvals);
            echo '<option value="'.$attrvals.'" '.(($val['attribute'][$k]==$attrvals)?'selected="selected"':'').'>'.$vls.'</option>';
          }
          echo '</select>';
        }
        echo '</div>';
        echo '<button class="button wpic_remove_variation" type="button">Remove</button>';
        echo '<div class="wpic_toggle handlediv"></div>';
        echo '<div class="clear"></div>';
        echo '</h3>';
        echo '<div class="wpicvariation">';
        ?>
        <table>
          <tr>
            <td>SKU</td>
            <td>

              <input type="text" name="wpic_variation_sku[]" value="<?php echo $val['wpic_variation_sku']; ?>" />
              <p class="description">Enter SKU for this variation or use parents SKU. </p>
            </td>
          </tr>
          <tr>
            <td>Price</td>
            <td><input type="text" name="wpic_variation_price[]" value="<?php echo $val['wpic_variation_price']; ?>" /></td>
          </tr>
          <tr>
            <td>Sale Price</td>
            <td><input type="text" name="wpic_variation_sale_price[]" value="<?php echo $val['wpic_variation_sale_price']; ?>" /></td>
          </tr>
          <tr>
            <td>Weight</td>
            <td><input type="text" name="wpic_variation_weight[]" value="<?php echo $val['wpic_variation_weight']; ?>" /></td>
          </tr>
          <!--<tr>
            <td>Status</td>
            <td>
              <input type="checkbox" name="wpic_variation_status[]" <?php //echo (isset($val['wpic_variation_status'])?'checked="checked"':'');?> value="yes" /> Enable 
            </td>
          </tr>-->
        </table>
        <input type="hidden" name="wpic_variation_status[]" value="yes" />
        <?php
        echo '</div>';
        echo '</div>';
      }
    }
    ?>
    <button class="button button-primary wpic_add_variation" type="button">Add Variation</button>
    <button class="button wpic_save_variation" type="button">Save Variations</button>
    
  </div>
  <?php
}

add_action('wp_ajax_wpic_get_attr_data','wpic_get_attr_data');

function wpic_get_attr_data(){
  $prod_id = $_POST['postid'];
  $attr_data = get_post_meta($prod_id,'_wpic_product_attribute',true);
  $pro_sku=get_post_meta($prod_id,'_prod_sku',true);
  if($attr_data){
    echo '<div class="wpic_var_container closed">';
    echo '<h3>';
    echo '<div class="wpic_var_title">';
    foreach($attr_data as $key=>$val){
      echo '<select name="attribute_'.$key.'[]" ><option value="">Choose '.$val['name'].'</option>';
      $vals = explode(',',$val['value']);
      foreach($vals as $vls){
        $attrvals = wpic_get_alphanumeric_string($vls);
        $attrvals = str_replace(' ','-',$attrvals);
        echo '<option value="'.$attrvals.'">'.$vls.'</option>';
      }
      echo '</select>';
    }
    echo '</div>';
    echo '<button class="button wpic_remove_variation" type="button">Remove</button>';
    echo '<div class="wpic_toggle handlediv"></div>';
    echo '<div class="clear"></div>';
    echo '</h3>';
    echo '<div class="wpicvariation">';
    ?>
    <table>
      <tr>
        <td>SKU</td>
        <td>
          <input type="text" name="wpic_variation_sku[]" value="<?php echo $pro_sku; ?>" />
          <p class="description">Enter SKU for this variation or left blank to use parents SKU. </p>
        </td>
      </tr>
      <tr>
        <td>Price</td>
        <td><input type="text" name="wpic_variation_price[]" value="0" /></td>
      </tr>
      <tr>
        <td>Sale Price</td>
        <td><input type="text" name="wpic_variation_sale_price[]" value="0" /></td>
      </tr>
      <tr>
        <td>Weight</td>
        <td><input type="text" name="wpic_variation_weight[]" value="0" /></td>
      </tr>
      <!--<tr>
        <td>Status</td>
        <td>
          <input type="checkbox" name="wpic_variation_status[]" value="yes" /> Enable 
        </td>
      </tr>-->
    </table>
    <input type="hidden" name="wpic_variation_status[]" value="yes" />
    <?php
    echo '</div>';
    echo '</div>';
  }else{
    echo 0;
  }
  wp_die();
}

add_action('wp_ajax_wpic_save_var_data','wpic_save_var_data');

function wpic_save_var_data(){
  $prod_id = $_POST['postid'];
  $var_data = $_POST['var_data'];
  parse_str($var_data,$data);
  $vardata = array();
  foreach($data as $key=>$val){
    for($i=0;$i<count($val);$i++){
      if(strpos($key,'attribute_')!==false){
        $attkey = preg_replace('/attribute_/','',$key,1);
        $vardata[$i]['attribute'][$attkey]=$val[$i];
      }else{
        $vardata[$i][$key]=$val[$i];
      }
    }
  }
  update_post_meta($prod_id,'_wpic_product_variation',$vardata);
  wp_die();
}

function wpic_save_custom_design_content($prod_id) {
	update_post_meta($prod_id, '_custom_product_image', (isset($_POST['_custom_product_image']) ? $_POST['_custom_product_image']:''));	

  if(isset($_POST["_prod_actual_price"])){
    update_post_meta($prod_id, '_prod_actual_price', (trim($_POST["_prod_actual_price"])!=''? $_POST["_prod_actual_price"]:0));
  }else{
    update_post_meta($prod_id, '_prod_actual_price', 0);
  }
  if(isset($_POST["_prod_sale_price"])){
    update_post_meta($prod_id, '_prod_sale_price', (trim($_POST["_prod_sale_price"])!=''? $_POST["_prod_sale_price"]:0));
  }else{
    update_post_meta($prod_id, '_prod_sale_price', 0);
  }
  update_post_meta($prod_id, '_prod_sku', (isset($_POST["_prod_sku"])? $_POST["_prod_sku"]:''));
  update_post_meta($prod_id, '_prod_weight', (isset($_POST["_prod_weight"])?$_POST["_prod_weight"]:''));
  update_post_meta($prod_id, '_prod_design_panel', (isset($_POST["_prod_design_panel"])?$_POST["_prod_design_panel"]:''));
}

add_filter( 'manage_edit-wpic_product_columns', 'wpic_edit_wpic_product_columns' ) ;

function wpic_edit_wpic_product_columns( $columns ) {
  $columns = array(
		'cb'            => '<input type="checkbox" />',
    'image'         => __( 'Image'),
		'title'         => __( 'Product Title' ),
		'sku'           => __( 'SKU' ),
		'price'         => __( 'Price' ),
    'wpic_category' => __( 'Categories'),
		'date'          => __( 'Date' )
	);
	return $columns;
}

add_action( 'manage_wpic_product_posts_custom_column', 'wpic_manage_wpic_product_columns', 10, 2 );

function wpic_manage_wpic_product_columns($column, $prod_id){
  global $post;

	switch( $column ) {
		case 'sku' :
			$prod_sku = get_post_meta( $prod_id, '_prod_sku', true );
			echo $prod_sku ;
			break;
      
		case 'wpic_category' :
      $terms = get_the_terms( $prod_id, 'wpic_category' );
      if ( !empty( $terms ) ) {
        $out = array();
        foreach ( $terms as $term ) {
          $out[] = sprintf( '<a href="%s">%s</a>',
            esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'wpic_category' => $term->slug ), 'edit.php' ) ),
            esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wpic_category', 'display' ) )
          );
        }
        echo join( ', ', $out );
      }
      else {
        _e( '-' );
      }     
			break;
    case 'price' :
      $prod_price = get_post_meta( $prod_id, '_prod_actual_price', true );
      echo wpic_get_display_price($prod_price);
      break;
    case 'image' :
      if ( has_post_thumbnail() ) {
        $front_img_url = wp_get_attachment_image_src(get_post_thumbnail_id($prod_id),array(60,60));
        $front_img_url=$front_img_url[0];
      }
      else{
        $front_img_url = WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg';
      }
      echo '<img src="'.$front_img_url.'" width="60px" height="60px" />';
      break;
		default :
			break;
	}
}

add_filter( 'post_row_actions', 'wpic_remove_row_actions', 10, 2 );

function wpic_remove_row_actions( $actions, $post )
{
  global $current_screen;
	if( $current_screen->post_type != 'wpic_product' ) return $actions;
	unset( $actions['inline hide-if-no-js'] );

	return $actions;
}

add_action( 'init', 'wpic_product_post_type' );
add_action( 'init', 'wpic_product_category_init' );
add_action( 'add_meta_boxes', 'wpic_product_custom_meta_box' );
add_action( 'save_post','wpic_save_custom_design_content', 10, 2 );