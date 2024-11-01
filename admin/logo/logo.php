<?php

function wpic_logo_post_type(){
	register_post_type( 'wpic_product_logo',
		array(
			'labels' => array(
				'name' => __( 'Logo' ),
				'singular_name' => __( 'Add Custom Art' ),
        'add_new_item' => __('Add New Logo'),
        'edit_item' => __('Edit Logo')
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'wpic_product_logo'),
      'menu_icon'   => 'dashicons-format-image',
			'supports' => array('title','editor','thumbnail')
		)
	);
}
	
function wpic_logo_category(){	
	register_taxonomy(
	  'wpic_logo_category',
	  'wpic_product_logo',
	  array(
	   'label' => __( 'Category' ),
	   'rewrite' => array( 'slug' => 'wpic_logo_category' ),
	   'hierarchical' => true,
	  )
	 );
}

add_filter( 'manage_edit-wpic_product_logo_columns', 'wpic_edit_wpic_product_logo_columns' ) ;

function wpic_edit_wpic_product_logo_columns( $columns ) {
  $columns = array(
		'cb'                  => '<input type="checkbox" />',
    'image'               => __( 'Image'),
		'title'               => __( 'Logo Title' ),
    'wpic_logo_category'  => __( 'Categories'),
		'date'                => __( 'Date' )
	);
	return $columns;
}

add_action( 'manage_wpic_product_logo_posts_custom_column', 'wpic_manage_wpic_product_logo_columns', 10, 2 );

function wpic_manage_wpic_product_logo_columns($column, $post_id){
  global $post;

	switch( $column ) {
      
		case 'wpic_logo_category' :
      $terms = get_the_terms( $post_id, 'wpic_logo_category' );
      if ( !empty( $terms ) ) {
        $out = array();
        foreach ( $terms as $term ) {
          $out[] = sprintf( '<a href="%s">%s</a>',
            esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'wpic_logo_category' => $term->slug ), 'edit.php' ) ),
            esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wpic_logo_category', 'display' ) )
          );
        }
        echo join( ', ', $out );
      }
      else {
        _e( '-' );
      }     
			break;
    case 'image' :
      if ( has_post_thumbnail() ) {
        echo get_the_post_thumbnail($post_id,array(60,60));
      }
      else{
        echo '';
      }
    
      break;
		default :
			break;
	}
}

add_action('init', 'wpic_logo_post_type');
add_action('init', 'wpic_logo_category');
