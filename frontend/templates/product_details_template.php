<?php
get_header();
if (has_post_thumbnail($post->ID)){	
	$meta_values = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
}
else{
	$meta_values = WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg';
}
$product_actual_price         =	get_post_meta($post->ID,'_prod_actual_price',true);
$product_sku                  =	get_post_meta($post->ID,'_prod_sku',true);
$product_weight               =	get_post_meta($post->ID,'_prod_weight',true);
$pro_currency                 =	get_option('wpic_currency' );
$product_image = get_post_meta($post->ID,'_custom_product_image',true);

echo template_init_function();
?>
<style type="text/css">
.site:before{
    display: none;
}
</style>
<div id="graph_overlay" class="overlay"></div>
<div class="wpic_design_container">
<div id="primary" class="site-content" style="margin-top:10px; width:100%; margin-left:0px;">
  <div id="content" role="main" style="margin:1%;">
    <div class="wpic_title"><?php echo $post->post_title;?></div>
    <div class="wpic_prod_design_div">
      <div class="wpic_canvas_image">
        <div id="prod_container" class="prod_container">
          <canvas id="prod_canvas" width="400" height="400"  ></canvas>
        </div>
        <div class="wpic_canvas_controls">
          <ul>
            <li class="wpic_bring_front_selected"><div class="wpic_front_icon"></div></li>
            <li class="wpic_send_back_selected"><div class="wpic_back_icon"></div></li>
            <li class="wpic_trash_selected"><div class="wpic_trash_icon"></div></li>
          </ul>
        </div>
      </div>
      <div class="wpic_bottom_panel">
        <ul>
          <li>
            <?php if(isset($product_image[1]) && $product_image[1]!=''){ ?>
            <a class="wpic_product_image" data-id="1" data-url="<?php echo $product_image[1];?>">
              <img height="75" width="75" src='<?php echo $product_image[1];?>'>
            </a>
            <?php } ?>
          </li>
        </ul>
      </div>
    </div>
    <div class="scpd-tab-container">
      <div class="scpd-tabs">
        <div class="scpd-tab-link current" data-tab="scpd-tab-1">
          <div class="wpic_product_icon"></div>
          <div class="wpic_tab_title">Product</div>
        </div>
        <div class="scpd-tab-link scpd-tab-2" data-tab="scpd-tab-2">
          <div class="wpic_text_icon"></div>
          <div class="wpic_tab_title">Text</div>
        </div>
        <div class="scpd-tab-link scpd-tab-3" data-tab="scpd-tab-3">
          <div class="wpic_art_icon"></div>
          <div class="wpic_tab_title">Art</div>
        </div>
        <div class="scpd-tab-link" data-tab="scpd-tab-5">
          <div class="wpic_help_icon"></div>
          <div class="wpic_tab_title">Help</div>
        </div>
      </div>
      <div class="clear"></div>

      <div id="scpd-tab-1" class="scpd-tab-content current">
        <div>
          <div class="wpic_tab_prod_title"><?php echo $post->post_title;?></div>
          <div class="wpic_tab_prod_sku">SKU : <?php echo $product_sku;?></div>
          <div class="wpic_tab_prod_price">Price : <span class="wpic_tab_prod_prc"><?php echo wpic_get_display_price($product_actual_price);?></div>
          <div class="wpic_tab_prod_cat">
            Category : 
            <?php 
            echo wpic_custom_taxonomies_terms_links();
            ?>
          </div>
          <div class="wpic_tab_prod_variation">
            <?php wpic_get_product_variation($post->ID); ?>
          </div>
        </div>
      </div>
      <div id="scpd-tab-2" class="scpd-tab-content">
        <div class="wpic_text_box">
          <div class="wpic_input">
            <div class="wpic_txtarea"><textarea name="wpic_txt" id="wpic_txt" style="width:95%;" >Type your text here</textarea> </div>
            <div class="clear"></div>
          </div>
          
          <div class="wpic_input">
            <div class="wpic_txt_label">Font :</div>
            <div class="wpic_txt_input">
              <select id="wpic_font">
                <option value="Times New Roman">Times New Roman</option>
                <option value="Arial">Arial</option>
                <option value="Calibri">Calibri</option>
                <option value="Comic Sans">Comic Sans</option>
              </select>
            </div>
            <div class="clear"></div>
          </div>
          
          <div class="wpic_input">
            <div class="wpic_txt_label">Color :</div>
            <div class="wpic_txt_input">
              <input type="text" size="7" name="wpic_txt_color" id="wpic_txt_color" class="color" value="#000000" />
            </div>
            <div class="clear"></div>
          </div>
          
          <div class="wpic_input">
            <div class="wpic_txt_label">Size :</div>
            <div class="wpic_txt_input"><input type="text" size="3" name="wpic_txt_size" id="wpic_txt_size" value="30" /></div>
            <div class="clear"></div>
          </div>
          
          <div class="wpic_add_txt_btn">
            <a id="wpic_add_text">ADD</a>
          </div>
          <div class="clear"></div>
        </div>
        
      </div>
      <div id="scpd-tab-3" class="scpd-tab-content">
        
        <div class="wpic_logos">
         <?php 
          $posts = get_posts(
                  array(
                  'post_type'   => 'wpic_product_logo',
                  'posts_per_page' => 20,
                  'post_status' => 'publish'
                  )
          );
          if(!empty($posts)){
            foreach($posts as $p){
              $custom_logo_id = 0;
              if (has_post_thumbnail($p->ID)){
                $custom_logo_id = get_post_thumbnail_id($p->ID);
                $meta_values = wp_get_attachment_url( get_post_thumbnail_id($p->ID) );
              }
              else{
                $meta_values = WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg';
              }
            ?>
            <div class="wpic_tab_logos">
              <a class="wpic_add_art" data-url="<?php echo $meta_values; ?>" >
                <img src="<?php echo $meta_values;?>" width="70px" height="70px" />
              </a>
            </div>
            <?php
            }
          }else{
            echo 'No logo data found.';
          }
          ?>    
        </div>
        
      </div>
      <div id="scpd-tab-5" class="scpd-tab-content">
        <div class="wpic_help_txt">
          <?php echo get_option('wpic_design_help_text');?>
        </div>
      </div>
      <div class="wpic_cart_act_container">
        <div class="wpic_tab_prod_quantity">
          <form>
            Quantity : <input type="number" name="wpic_prod_quantity" id="wpic_prod_quantity" style="width:50px;" min="1" value="1" />
          </form>
        </div>
        <div class="wpic_tab_prod_add_to_cart"><a class="wpic_prod_add_to_cart">Add to Cart</a></div>
        <div class="cart_success"></div>
      </div>
    </div>
<!--    </div>-->
  <div class="clear"></div>  
    
    
    <div id="graph_progress" class="showprogress">
      <img alt="" src='<?php echo WPIC_CUSTOM_PRODUCT_URL. '/resource/images/loader.svg';?>'>
      <span style="color: Blue;"><!--<b>Please wait...</b> --></span>
    </div>
	</div>
</div> 
<div class="clear"></div>
<?php

if (isset($product_image[0]) && $product_image[0]!=''){	
  $front_img_url = $product_image[0];
}
else{
  $front_img_url = WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg';
}
?>
<?php do_action('wpicommercce_before_product_details');?>
  <div class="wpic_prod_details_container">    
    <div class="wpic_prod_details">
      <div class="wpic_prod_details_content">
        <p><?php echo $post->post_content;?></p>
      </div>
    </div>
    <div class="clear"></div>
  </div>
</div>
<div class="clear"></div>

<?php do_action('wpicommercce_after_product_details');?>

<?php //get_sidebar(); ?>
<?php get_footer(); ?>