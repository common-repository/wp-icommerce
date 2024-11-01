<?php

function wpic_order_listing(){
  global $wpdb;
  if(isset($_GET['order_id'])){
    wpic_order_details();
  }else{
    $custom_table_prefix = wpic_get_custom_table_prefix();
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    $limit =10;
    $offset = ( $pagenum - 1 ) * $limit;
    $wpdb_all_prefix = $wpdb->prefix.$custom_table_prefix;
    $entries = $wpdb->get_results("SELECT * FROM {$wpdb_all_prefix}order ORDER BY order_id DESC LIMIT $offset, $limit" );
    ?>
    <script type="text/javascript">
    	function change_product_order_satus(order_id,obj){
			jQuery('#wpic_order_status_loader_'+order_id).show();
			jQuery.ajax({
			  url: ajaxurl,
			  data: {
				  'action':'wpic_update_order_status',
				  'order_id' : order_id,
				  'current_order_status' : obj.value
			  },
			  success:function(data) {
				  if(data=='Success'){
            jQuery('#wpic_order_status_'+order_id).css("background-color","yellow");
            jQuery('#wpic_order_status_loader_'+order_id).hide();
            jQuery('#wpic_order_status_msg_'+order_id).html('<br><font style="color:#0C0;">Success</font>');
				  }
				  // This outputs the result of the ajax request
				  //console.log(data);
			  },
			  error: function(errorThrown){
				  //console.log(errorThrown);
			  }
		  });
		}
    </script>
    <div class="wrap">
      <h2>Orders</h2>
      <table class="widefat">
        <thead>
          <tr>
            <th scope="col" class="manage-column column-name" style="">Order ID</th> 
            <th scope="col" class="manage-column column-name" style="">Name</th>
            <th scope="col" class="manage-column column-name" style="">Email</th>
            <th scope="col" class="manage-column column-name" style="">Amount</th>
            <th scope="col" class="manage-column column-name" style="">Status</th>
            <th scope="col" class="manage-column column-name" style="">Date</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th scope="col" class="manage-column column-name" style="">Order ID</th> 
            <th scope="col" class="manage-column column-name" style="">Name</th>
            <th scope="col" class="manage-column column-name" style="">Email</th>
            <th scope="col" class="manage-column column-name" style="">Amount</th>
            <th scope="col" class="manage-column column-name" style="">Status</th>
            <th scope="col" class="manage-column column-name" style="">Date</th>
          </tr>
        </tfoot>
        <tbody>
        <?php if( $entries ) { ?>
        <?php
        $count = 1;
        $class = '';
        foreach( $entries as $entrys ) {
          $class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
        ?>
          <tr<?php echo $class; ?>>
            <td>
              <a class="row-title" style="cursor:pointer;" href="admin.php?page=wpic_order_page&order_id=<?php echo $entrys->order_id;?>" >
                <?php echo $entrys->order_id;?>
              </a>
            </td>
            <td>
              <a class="row-title" style="cursor:pointer;" href="admin.php?page=wpic_order_page&order_id=<?php echo $entrys->order_id;?>" >
                <?php echo $entrys->billing_name;?>
              </a>
            </td>
            <td><?php echo $entrys->billing_email;?></td>
            <td>
              <a style="cursor:pointer;" href="admin.php?page=wpic_order_page&order_id=<?php echo $entrys->order_id;?>" >
                <?php echo wpic_get_display_price($entrys->order_total);?>
              </a>
            </td>
            <td>
            	<select id="wpic_order_status_<?php echo $entrys->order_id;?>" name="wpic_order_status" onchange="change_product_order_satus('<?php echo $entrys->order_id;?>',this);">
                <option value="1" <?php if($entrys->order_status=='1')echo 'selected="selected"';?> >Order Received</option>
                <option value="2" <?php if($entrys->order_status=='2')echo 'selected="selected"';?> >Incomplete Sale</option>
                <option value="3" <?php if($entrys->order_status=='3')echo 'selected="selected"';?> >Accepted Payment</option>
                <option value="4" <?php if($entrys->order_status=='4')echo 'selected="selected"';?> >Job Dispatched</option>
                <option value="5" <?php if($entrys->order_status=='5')echo 'selected="selected"';?> >Closed Order</option>
                <option value="6" <?php if($entrys->order_status=='6')echo 'selected="selected"';?> >Payment Declined</option>
              </select>
              <img id="wpic_order_status_loader_<?php echo $entrys->order_id;?>" style="display:none;" alt="Loading..." src='<?php echo WPIC_CUSTOM_PRODUCT_URL. '/resource/images/upload_loader.gif';?>'><span id="wpic_order_status_msg_<?php echo $entrys->order_id;?>"></span>
            </td>
            <td><?php echo $entrys->order_date;?></td>
          </tr>
          <?php
            $count++;
          }
          ?>
          <?php } else { ?>
          <tr>
            <td colspan="2">No Orders yet</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php
      $total = $wpdb->get_var( "SELECT count(*) FROM {$wpdb_all_prefix}order ORDER BY order_id DESC");
      $num_of_pages = ceil( $total / $limit );
      $page_links = paginate_links( array(
          'base' => add_query_arg( 'pagenum', '%#%' ),
          'format' => '',
          'prev_text' => __( '&laquo;' ),
          'next_text' => __( '&raquo;' ),
          'total' => $num_of_pages,
          'current' => $pagenum
        ));
      if ( $page_links ) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
      }
    ?></div><?php
  }
}

function wpic_order_details(){
  add_action('admin_footer', 'wpic_admin_popup_overlay');
  global $wpdb;
  $custom_table_prefix = wpic_get_custom_table_prefix();
 
  $wpdb_all_prefix = $wpdb->prefix.$custom_table_prefix;
  $order_item = $wpdb->get_row("SELECT * FROM {$wpdb_all_prefix}order WHERE order_id = ".$_GET['order_id']." ORDER BY order_id ASC", ARRAY_A);
  $ordered_items = $wpdb->get_results("SELECT * FROM {$wpdb_all_prefix}order_item where fk_order_id = ".$_GET['order_id']." ORDER BY order_item_id ASC " );
  if( $ordered_items ) {
    ?>
    <div class="wrap wpic_order_details">
      <h2>Order Details</h2>
      <h3>Order Id: <?php echo $order_item['order_id'];?> <br />
        <span style="color:#666;">Order Date : <?php echo $order_item['order_date'];?></span>
      </h3>
      <div class="metabox-holder">
        <div class="scpd-post-box wpic_margin_right">
            <div class="postbox">
              <h3 class="hndle">Billing Details</h3>
              <div class="inside">
                <table>
                  <tr>
                    <th scope="row">Name : </th>
                    <td><?php echo $order_item['billing_name'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">E-mail : </th>
                    <td><?php echo $order_item['billing_email'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Phone : </th>
                    <td><?php echo $order_item['billing_phone'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Address 1 : </th>
                    <td><?php echo $order_item['billing_address1'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Address 2 : </th>
                    <td><?php echo $order_item['billing_address2'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">City : </th>
                    <td><?php echo $order_item['billing_city'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">State : </th>
                    <td><?php echo $order_item['billing_state'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Zip : </th>
                    <td><?php echo $order_item['billing_zip'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Country : </th>
                    <td><?php echo wpic_get_country_name_by_country_code($order_item['billing_country']);?></td>
                  </tr>
                  <tr>
                    <th scope="row">Payment Method : </th>
                    <td><?php echo $order_item['payment_method'];?></td>
                  </tr>
                </table>
              </div>
            </div>
        </div>
        <div class="scpd-post-box">
            <div class="postbox">
              <h3 class="hndle">Shipping Details</h3>
              <div class="inside">
                <table>
                  <tr>
                    <th scope="row">Name : </th>
                    <td><?php echo $order_item['shipping_name'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">E-mail : </th>
                    <td><?php echo $order_item['shipping_email'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Phone : </th>
                    <td><?php echo $order_item['shipping_phone'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Address 1 : </th>
                    <td><?php echo $order_item['shipping_address1'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Address 2 : </th>
                    <td><?php echo $order_item['shipping_address2'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">City : </th>
                    <td><?php echo $order_item['shipping_city'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">State : </th>
                    <td><?php echo $order_item['shipping_state'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Zip : </th>
                    <td><?php echo $order_item['shipping_zip'];?></td>
                  </tr>
                  <tr>
                    <th scope="row">Country : </th>
                    <td><?php echo wpic_get_country_name_by_country_code($order_item['shipping_country']);?></td>
                  </tr>
                  <tr>
                    <th scope="row">Shipping Method : </th>
                    <td><?php echo $order_item['shipping_method'];?></td>
                  </tr>
                </table>
              </div>
            </div>
        </div>
      </div>
      
      <table class="widefat">
        <thead>
          <tr>
            <th scope="col" class="manage-column column-name" style="">SN</th>
            <th scope="col" class="manage-column column-name" style="">Title</th>
            <th scope="col" class="manage-column column-name" style="">Image</th>
            <th scope="col" class="manage-column column-name" style="">SKU</th>
            <th scope="col" class="manage-column column-name" style="">Quantity</th>
            <th scope="col" class="manage-column column-name" style="">Unit Price</th>
            <th scope="col" class="manage-column column-name" style="">Total</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        $class = '';
        $sub_total = 0;
        $tax_cost = 0;
        $shipping_cost = 0;
        foreach( $ordered_items as $odr_itms ) {
          $class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
          $sub_total = $sub_total+($odr_itms->product_qty*$odr_itms->product_price);
          ?>

          <tr<?php echo $class; ?>>
            <td><?php echo $count; ?></td>
            <td>
              <a class="wpic_admin_popup" data-popupid="<?php echo $count; ?>" style="cursor:pointer;"><?php echo $odr_itms->product_title;?></a>
                <?php
                $product_var = unserialize($odr_itms->product_variation);
                if(isset($product_var['attribute'])){
                  foreach($product_var['attribute'] as $keys=>$vals){
                    echo '<br /><strong>'.$keys.' : </strong>'.$vals;
                  }
                }
                ?>
            </td>
            <td>
              <?php
              $prod_attr = unserialize($odr_itms->product_attribute);
              if($prod_attr){
                foreach($prod_attr as $pimg){
                  echo '<img class="product_order_image" src="'.$pimg['screenshot'].'" width="60" height="60" />';
                }
              }else{
                if($odr_itms->product_image){
                  echo '<img class="product_order_image" src="'.$odr_itms->product_image.'" width="60" height="60" />';
                }
              }
              ?>
            </td>
            <td>
              <a href="<?php echo admin_url( 'post.php?post=' . $odr_itms->product_id ) . '&action=edit';?>">
                <?php echo (isset($product_var['wpic_variation_sku'])?$product_var['wpic_variation_sku']:$odr_itms->product_sku);?>
              </a>
            </td>
            <td><?php echo $odr_itms->product_qty;?></td>
            <td><?php echo wpic_get_display_price($odr_itms->product_price);?></td>
            <td><?php echo wpic_get_display_price($odr_itms->product_qty*$odr_itms->product_price);?>           
              
              <div id="scpd-popup-content-<?php echo $count; ?>" class="wpic_popup_content" style="display:none;">
                  <div class="wpic_popup_header">
                    <div class="wpic_popup_title">
                      <div class="wpic_prod_title"><?php echo $odr_itms->product_title;?></div>
                      <div class="wpic_prod_sku"><strong>SKU :</strong> 
                        <?php
                        echo (isset($product_var['wpic_variation_sku'])?$product_var['wpic_variation_sku']:$odr_itms->product_sku);
                        ?>
                      </div>
                    </div>
                    <div class="wpic_popup_close">X</div>
                  </div>
                  <div class="wpic_popup_body">
                    <table width="100%" class="wpic_popup_table">
                    <?php
                    if($prod_attr){
                      foreach($prod_attr as $pattr){
                        ?>
                        <tr>
                          <td width="450px;" style="padding-top:10px; border-bottom: solid 1px #ccc; text-align: center;">
                            <img src="<?php echo $pattr['screenshot']; ?>" width="400px" /><br />
                            <a href="<?php echo $pattr['screenshot'] ?>" target="_blank" download >Download Image</a>
                          </td>
                          <td style="vertical-align:top; padding-top: 10px; border-bottom: solid 1px #ccc;">
                            <table width="100%">
                              <?php
                              $prod_custom = json_decode(stripslashes(htmlspecialchars_decode($pattr['customdata'])));
                              if($prod_custom){
                                foreach($prod_custom->objects as $pcustom){
                                  if($pcustom->type=='text'){
                                    ?>
                                    <tr><td colspan="3" style="border: solid 1px #ccc;"><strong>Text Data</strong></td></tr>
                                    <tr><td>Text</td><td> : </td><td><?php echo $pcustom->text; ?></td></tr>
                                    <tr><td>Font Family</td><td> : </td><td><?php echo $pcustom->fontFamily; ?></td></tr>
                                    <tr><td>Font Size</td><td> : </td><td><?php echo $pcustom->fontSize; ?></td></tr>
                                    <tr><td>Font Weight</td><td> : </td><td><?php echo $pcustom->fontWeight; ?></td></tr>
                                    <tr><td>Font Style</td><td> : </td><td><?php echo $pcustom->fontStyle; ?></td></tr>
                                    <tr><td>Font Color</td><td> : </td><td><?php echo $pcustom->fill; ?></td></tr>
                                    <tr><td>Font Stroke</td><td> : </td><td><?php echo $pcustom->stroke; ?></td></tr>
                                    <tr><td>Font Stroke Width</td><td> : </td><td><?php echo $pcustom->strokeWidth; ?></td></tr>
                                    <tr><td>Angle</td><td> : </td><td><?php echo $pcustom->angle; ?></td></tr>
                                    <tr><td>Shadow</td><td> : </td><td><?php echo $pcustom->shadow; ?></td></tr>
                                    <tr><td>Text Decoration</td><td> : </td><td><?php echo $pcustom->textDecoration; ?></td></tr>
                                    <?php
                                  }
                                  if($pcustom->type=='image'){
                                    ?>
                                    <tr><td colspan="3" style="border: solid 1px #ccc;"><strong>Image Data</strong></td></tr>
                                    <tr>
                                      <td>Image</td>
                                      <td> : </td>
                                      <td style="padding-top:10px;">
                                        <img src="<?php echo $pcustom->src; ?>" width="200px" /><br />
                                      </td>
                                    </tr> 
                                    <tr><td>Angle</td><td> : </td><td><?php echo $pcustom->angle; ?></td></tr>
                                    <tr>
                                      <td colspan="3" style="text-align: center;">
                                        <a href="<?php echo $pcustom->src; ?>" target="_blank" download >Download Image</a>
                                      </td>
                                    </tr>
                                    <?php
                                  }
                                ?>
                                <tr>
                                  <td>
                                    <?php //print_r($pcustom);?>
                                  </td>
                                </tr>
                              <?php }
                                } ?>
                            </table>
                          </td>
                        </tr>
                        <?php
                      }
                    }else{
                      echo '<tr><td width="450px;" style="padding-top:10px; border-bottom: solid 1px #ccc; text-align: center;"><img src="'.$odr_itms->product_image.'" width="400px" /></td><td>There is no customization data available for this product.</td></tr>';
                    }
                    ?>
                    </table>
                  </div>
              </div>
            </td>
          </tr>
          
        <?php
          $count++;
        }
        ?>
        <tr>
          <td class="wpic_txt_align_right" style="border-top:solid 1px #e1e1e1;" colspan="6"><strong>Sub Total : </strong></td>
          <td style="border-top:solid 1px #e1e1e1;"><?php echo wpic_get_display_price($sub_total);?></td>
        </tr>
        <tr>
          <td  class="wpic_txt_align_right" colspan="6"><strong>Tax : </strong></td>
          <td><?php echo wpic_get_display_price($order_item['tax']);?></td>
        </tr>
        <tr>
          <td class="wpic_txt_align_right" colspan="6"><strong>Shipping : </strong></td>
          <td><?php echo wpic_get_display_price($order_item['shipping_price']);?></td>
        </tr>
        <tr>
          <td class="wpic_txt_align_right" colspan="6"><strong>Total : </strong></td>
          <td><?php echo wpic_get_display_price($sub_total+$order_item['tax']+$order_item['shipping_price']);?></td>
        </tr>
      </tbody>
    </table>
  </div>
  <script>
  jQuery('.wpic_admin_popup').click(function(){
    var popupid = jQuery(this).data('popupid');
    jQuery('.wpic_popup_overlay').show();
    jQuery('.wpic_popup_content').hide();
    jQuery('#scpd-popup-content-'+popupid).show();
  });
  
  jQuery('.wpic_popup_close').click(function(){
    jQuery('.wpic_popup_overlay').hide();
    jQuery('.wpic_popup_content').hide();
  });
  jQuery(document).mouseup(function(e){
    var container = jQuery('.wpic_popup_content');
    if (!container.is(e.target) && container.has(e.target).length === 0){
        container.hide();
        jQuery('.wpic_popup_overlay').hide();
    }
  });
  jQuery(document).on( 'keydown', function(e){
    if (e.keyCode === 27 ){ 
      jQuery('.wpic_popup_overlay').hide();
      jQuery('.wpic_popup_content').hide();
    }
  });
  </script>
  <?php
  }
}

function wpic_update_order_status(){
	if ( isset($_REQUEST) ) {
	  global $wpdb;
    $custom_table_prefix = wpic_get_custom_table_prefix();
    $wpdb_all_prefix = $wpdb->prefix.$custom_table_prefix;
	  $order_id = $_REQUEST['order_id'];
	  $received_order_status_id = $_REQUEST['current_order_status'];
	  if(($order_id!='')&&($received_order_status_id!='')){		  
      if( $wpdb->update($wpdb_all_prefix.'order',array('order_status'=>$received_order_status_id),array('order_id'=>$order_id)) === FALSE){
        echo "Your update request failed";
      }
      else{
        echo "Success";
      }		 
	  }
	  else{
		  echo "There have some problem. Please try again.";
	  }
	}
	exit;
}
add_action( 'wp_ajax_wpic_update_order_status', 'wpic_update_order_status' );

function wpic_admin_popup_overlay(){
  echo '<div class="wpic_popup_overlay" style="display:none;"></div>';
}
