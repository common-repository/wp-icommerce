<?php get_header(); ?>
<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
<header class="entry-header">
  <h1 class="entry-title"><?php echo single_cat_title( '', false ); ?></h1>
</header>
<div class="entry-content">
<?php 
if ( have_posts() ) : 
  while ( have_posts() ) : the_post();
  $product_image = get_post_meta($post->ID,'_custom_product_image',true);
    if ( has_post_thumbnail() ) {
      $front_img_url = wp_get_attachment_image_src(get_post_thumbnail_id(),'large');
      $front_img_url=$front_img_url[0];
    }
    else if ($product_image[0]!=''){	
      $front_img_url = $product_image[0];
    }
    else{
      $front_img_url = WPIC_CUSTOM_PRODUCT_URL. '/resource/product/noimage.jpg';
    }
  $product_actual_price = get_post_meta(get_the_id(),'_prod_actual_price',true);
  $prod_sku = get_post_meta(get_the_id(),'_prod_sku',true);
  ?>
  <div class="prod_box">
    <div class="prod_box_img">
      <a href="<?php the_permalink();?>">
        <img src="<?php echo $front_img_url; ?>" />
      </a>
    </div>
    <div class="prod_box_title">
      <a href="<?php the_permalink();?>">
        <?php echo (strlen(get_the_title())>40 ? substr(get_the_title(),0,40).'...':get_the_title()); ?>
      </a>
    </div>
    <div class="prod_box_sku">SKU : <?php echo $prod_sku; ?></div>
    <div class="prod_box_price"><?php echo wpic_get_display_price($product_actual_price); ?></div>
    <div class="prod_box_button">
      <a class="prod_customize" href="<?php the_permalink();?>" >Customize</a>
    </div>
  </div>
  <?php
  endwhile;
  echo '<div class="clear"></div>';
else :
 _e('Sorry, no product found.');
endif; ?>
</div></div></section>
<?php get_sidebar( 'content' ); get_sidebar(); ?>
<?php get_footer(); ?>