<?php

/*product catalog template */

while ( $posts_query->have_posts() ) : $posts_query->the_post();
  $front_img_url = get_post_meta(get_the_id(),'_custom_product_image',true);
  if ( has_post_thumbnail() ) {
    $front_img_url = wp_get_attachment_image_src(get_post_thumbnail_id(),'large');
    $front_img_url=$front_img_url[0];
  }
  else if (isset($front_img_url[0]) && $front_img_url[0]!=''){	
    $front_img_url = $front_img_url[0];
  }
  else{
    $front_img_url = WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg';
  }
  $product_actual_price=get_post_meta(get_the_id(),'_prod_actual_price',true);
  $permalink = get_the_permalink();
  $p_title = get_the_title();
  $p_sku = get_post_meta(get_the_id(),'_prod_sku',true);
  ?>
  <div class="prod_box">
    <div class="prod_box_img">
      <a href="<?php echo $permalink;?>"><img src="<?php echo $front_img_url;?>" /></a>
    </div>
    <div class="prod_box_title">
      <a href="<?php echo $permalink;?>"><?php echo (strlen($p_title)>40 ? substr($p_title,0,40).'...':$p_title); ?></a>
    </div>
    <div class="prod_box_sku">SKU : <?php echo $p_sku;?></div>
    <div class="prod_box_price"><?php echo wpic_get_display_price($product_actual_price);?></div>
    <div class="prod_box_button"><a class="prod_customize" href="<?php echo $permalink;?>" >Customize</a></div>
  </div>
  <?php        
endwhile;

?>
<div class="clear"></div>
<nav>
  <div><?php echo get_next_posts_link('Older', $posts_query->max_num_pages);?></div>
  <div><?php echo get_previous_posts_link('Newer', $posts_query->max_num_pages);?></div>
</nav>
