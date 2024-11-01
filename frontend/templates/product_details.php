<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php
if (has_post_thumbnail($post->ID)){	
	$thumbnail = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
}
else{
	$thumbnail = WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg';
}

$product_actual_price         =	get_post_meta($post->ID,'_prod_actual_price',true);
$product_sale_price           = get_post_meta($post->ID,'_prod_sale_price',true);
$product_sku                  =	get_post_meta($post->ID,'_prod_sku',true);
$product_weight               =	get_post_meta($post->ID,'_prod_weight',true);
$pro_currency                 =	get_option('wpic_currency' );
$product_image = get_post_meta($post->ID,'_custom_product_image',true);

echo template_init_function();
?>

<div id="graph_overlay" class="overlay"></div>
<div class="wpic_product_container">
  <div id="primary" class="site-content" style="margin-top:10px; width:100%; margin-left:0px;">
    <div id="content" role="main" style="margin:1%;">
      <div class="wpic_left_container">
        <div class="wpic_prod_image_container">
          <img class="wpic_thumb_image" src="<?php echo $thumbnail;?>" />
        </div>
        <div class="wpic_prod_image_gallery">
          <ul>
            <li>
              <a class="wpic_product_image" data-url="<?php echo $thumbnail;?>">
                <img height="75" width="75" src='<?php echo $thumbnail;?>'>
              </a>
            </li>
            <?php if(isset($product_image[1]) && $product_image[1]!=''){ ?>
              <li>
                <a class="wpic_product_image" data-url="<?php echo $product_image[1];?>">
                  <img height="75" width="75" src='<?php echo $product_image[1];?>'>
                </a>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <div class="wpic_right_container">
        <div class="wpic_title"><?php echo $post->post_title;?></div>
        <div>
          <div class="wpic_tab_prod_sku"> <?php echo (($product_sku)? 'SKU : '.$product_sku:'');?></div>
          <div class="wpic_tab_prod_price">Price : 
            <span class="wpic_tab_prod_prc">
              <?php
              if($product_sale_price){
                echo '<span class="wpic_ac_price">'.wpic_get_display_price($product_actual_price).' </span>';
                echo '<span class="wpic_sl_price">'.wpic_get_display_price($product_sale_price).' </span>';
              }else{
                echo wpic_get_display_price($product_actual_price);
              }
              ?>
            </span>
          </div>
          <div class="wpic_tab_prod_cat"> 
            <?php
            if(wpic_custom_taxonomies_terms_links()){
              echo ' Category : '.wpic_custom_taxonomies_terms_links();
            }
            ?>
          </div>
          <div class="wpic_tab_prod_variation">
            <?php wpic_get_product_variation($post->ID); ?>
          </div>
        </div>
       
        <div class="wpic_sngl_prod_quantity">
          <form>
            Quantity : <input type="number" name="wpic_prod_quantity" id="wpic_prod_quantity" style="width:50px;" min="1" value="1" />
          </form>
        </div>
        <div class="wpic_sngl_prod_add_to_cart"><a class="wpic_prod_add_to_cart">Add to Cart</a></div>
        <div class="cart_success"></div>
      </div>
      <div class="clear"></div>
      <div class="wpic_prod_description">
          <?php the_content();?>
      </div>
    </div>
  </div>
  <div class="clear"></div>
  <div id="graph_progress" class="showprogress">
      <img alt="" src='<?php echo WPIC_CUSTOM_PRODUCT_URL. '/resource/images/loader.svg';?>'>
      <span style="color: Blue;"><!--<b>Please wait...</b> --></span>
    </div>
</div>
<?php 
endwhile;
endif;
?>
<?php get_footer(); ?>