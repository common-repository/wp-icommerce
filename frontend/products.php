<?php

function wpic_get_product_details_template($single_template) {
  global $post;
  
  if ($post->post_type == 'wpic_product') {
    if(get_post_meta($post->ID,'_prod_design_panel',true)){
      wp_enqueue_script( 'custom.js', WPIC_CUSTOM_PRODUCT_URL.'/resource/js/custom.js',array( 'jquery' ) );
      wp_localize_script( 'custom.js', 'scpdAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
      if($theme_file = locate_template( array( 'wpicommerce/frontend/templates/product_details_template.php' ) )){
        $single_template = $theme_file;
      }else{
        $single_template = dirname( __FILE__ ) . '/templates/product_details_template.php';
      }
    }else{
      wp_enqueue_script( 'custom.single.js', WPIC_CUSTOM_PRODUCT_URL.'/resource/js/custom.single.js',array( 'jquery' ) );
      wp_localize_script( 'custom.single.js', 'scpdAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
      if($theme_file = locate_template( array( 'wpicommerce/frontend/templates/product_details.php' ) )){
        $single_template = $theme_file;
      }else{
        $single_template = dirname( __FILE__ ) . '/templates/product_details.php';
      }
    }
  }
  return $single_template;
}


function wpic_product_list(){
  $product_output = "";
  $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1; // setup pagination

  $posts_query = new WP_Query( array( 
      'post_type' => 'wpic_product',
      'paged' => $paged,
      'post_status' => 'publish',
      'posts_per_page' => get_option('wpic_product_per_page')
      ) 
  );
  
  ob_start();
	if($theme_file = locate_template( array( 'wpicommerce/frontend/templates/product_template.php'))){
    include $theme_file;
  }else{
    include dirname( __FILE__ ) . '/templates/product_template.php';
  }
  
  $product_output .=ob_get_contents();
  ob_end_clean();
  wp_reset_postdata();
 
  return $product_output;
}

// get taxonomies terms links
function wpic_custom_taxonomies_terms_links(){
  global $post;
  $post = get_post( $post->ID );
  $post_type = $post->post_type;
  $taxonomies = get_object_taxonomies( $post_type, 'objects' );

  $out = array();
  foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){
    $terms = get_the_terms( $post->ID, $taxonomy_slug );

    if ( !empty( $terms ) ) {
      $out[] = "";
      foreach ( $terms as $term ) {
        $out[] =
          '<a href="'
        .    get_term_link( $term->slug, $taxonomy_slug ) .'">'
        .    $term->name
        . "</a>";
      }
      $out[] = "";
    }
  }

  return implode('', $out );
}

function wpic_archive_template(){
  global $post;
  if ($post->post_type == 'wpic_product') {
    if($theme_file = locate_template( array( 'wpicommerce/frontend/templates/archive_template.php' ) )){
      $archive_template = $theme_file;
    }else{
      $archive_template = dirname( __FILE__ ) . '/templates/archive_template.php';
    }
  }
  return $archive_template;
}

add_filter( 'archive_template', 'wpic_archive_template' );
add_filter( 'single_template', 'wpic_get_product_details_template' );
add_shortcode('wpcustomproduct', 'wpic_product_list');

function template_init_function(){
  global $post,$wpdb;
  $reg_price = get_post_meta($post->ID,'_prod_actual_price',true);
  $sale_price = get_post_meta($post->ID,'_prod_sale_price',true);
  $sku = get_post_meta($post->ID,'_prod_sku',true);
  $weight = get_post_meta($post->ID,'_prod_weight',true);
  //$currency = get_option('wpic_base_currency' );

	$currency_symbol='';
	$custom_table_prefix = wpic_get_custom_table_prefix();
	$currency_info = "Select * from ".$wpdb->prefix.$custom_table_prefix."currency_list where code = '".get_option( 'wpic_base_currency' )."' ORDER BY country ASC limit 1";
	$currency_table_info = $wpdb->get_results($currency_info,ARRAY_A);
  if($currency_table_info){
    $currency_symbol = $currency_table_info[0]['symbol'];
    $currency_code = $currency_table_info[0]['code'];
    if($currency_symbol==''){
      $currency_symbol = $currency_code;
    }
  }
  ?>
  <script type="text/javascript">
    var carturl = '<?php echo get_option( 'siteurl' )?>/?page_id=<?php echo get_option( 'Cart' )?>';
    var product = {};
    product.id = '<?php echo $post->ID;?>';
    product.title = '<?php echo $post->post_title;?>';
    product.price = '<?php echo (($sale_price)?$sale_price:$reg_price);?>';
    product.sku = '<?php echo $sku;?>';
    product.weight = '<?php echo $weight;?>';
    product.quantity = '1';
    var currency_code = '<?php echo $currency_symbol; ?>';
  </script>
  <?php
}