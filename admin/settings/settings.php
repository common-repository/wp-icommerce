<?php

function wpic_admin_setting(){
  add_object_page( 'Wpicommerce', 'Wpicommerce', 'edit_posts', 'wpic_order_page', 'wpicommerce_init', WPIC_CUSTOM_PRODUCT_URL.'/resource/images/logo_thumbnail.png' );
  add_submenu_page('wpic_order_page', 'Products Orders', 'Orders', 'edit_posts', 'wpic_order_page', 'wpic_order_listing');
	add_submenu_page('wpic_order_page', 'Products Settings', 'Settings', 'edit_posts', 'wpic_settings', 'wpic_global_settings');
}

function wpicommerce_init(){
  global $title;
}

function wpic_cust_admin_tabs( $current = 'general' ) { 
  $tabs = array( 'general' => 'General', 'payment' => 'Payment', 'shipping' => 'Shipping' ); 
  $links = array();
  echo '<div id="icon-themes" class="icon32"><br></div>';
  echo '<h2 class="nav-tab-wrapper">';
  foreach( $tabs as $tab => $name ){
    $class = ( $tab == $current ) ? ' nav-tab-active' : '';
    echo "<a class='nav-tab$class' href='admin.php?page=wpic_settings&tab=$tab'>$name</a>";
  }
  echo '</h2>';
}

function wpic_global_settings(){
  ?>
  <div class="wrap">
    <h2>Settings</h2>
    <?php if ( isset ( $_GET['tab'] ) ) wpic_cust_admin_tabs($_GET['tab']); else wpic_cust_admin_tabs('general'); ?>
    <div id="poststuff">
			<form method="post" action="">
				<?php				
					if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab']; 
					else $tab = 'general'; 
					
          echo '<div class="wpic_settings">';
					switch ( $tab ){
						case 'general' :
              if(isset($_POST['wpic_general_settings'])){
                update_option('wpic_base_country',sanitize_text_field($_POST['wpic_country']));
                update_option('wpic_origin_city',sanitize_text_field($_POST['wpic_origin_city']));
                update_option('wpic_origin_postcode',sanitize_text_field($_POST['wpic_origin_postcode']));
                update_option('wpic_base_currency',sanitize_text_field($_POST['wpic_currency']));
                update_option('wpic_admin_email',sanitize_email($_POST['wpic_admin_email']));
                update_option('wpic_base_tax',$_POST['wpic_tax']);
                update_option('wpic_design_help_text',sanitize_text_field($_POST['wpic_design_help_text']));
                update_option('wpic_product_per_page',intval($_POST['wpic_product_per_page']));
              }
							$country_data = wpic_currency_table_query();
              ?>
              <h2>General</h2>
              <table class="form-table">
                <tr>
                  <th scope="row">Country</th>
                  <td>
                    <select name="wpic_country">
                      <option value="">Select</option>
                      <?php
                      foreach($country_data as $country){
                        echo '<option value="'.$country->isocode.'" '.((get_option('wpic_base_country')==$country->isocode) ? 'selected="selected"':'').' >'.$country->country.'</option>';
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Origin City</th>
                  <td><input type="text" name="wpic_origin_city" class="regular-text" value="<?php echo get_option('wpic_origin_city');?>" /></td>
                </tr>
                <tr>
                  <th scope="row">Origin Postcode</th>
                  <td><input type="text" name="wpic_origin_postcode" class="regular-text" value="<?php echo get_option('wpic_origin_postcode');?>" /></td>
                </tr>
                <tr>
                  <th scope="row">Administrator email</th>
                  <td><input type="text" name="wpic_admin_email" class="regular-text" value="<?php echo get_option('wpic_admin_email');?>" /></td>
                </tr>
                <tr>
                  <th scope="row">Tax</th>
                  <td>
                    <input type="text" name="wpic_tax" class="regular-text" value="<?php echo get_option('wpic_base_tax');?>" /> %
                  </td>
                </tr>
                <tr>
                  <th scope="row">Product Per Page</th>
                  <td>
                    <input type="text" name="wpic_product_per_page" class="regular-text" value="<?php echo get_option('wpic_product_per_page');?>" />
                  </td>
                </tr>
                <tr>
                  <th scope="row">Currency</th>
                  <td>
                    <select name="wpic_currency" class="regular-text">
                      <option value="">Select</option>
                      <?php
                      foreach($country_data as $currency){
                        echo '<option value="'.$currency->code.'" '.((get_option('wpic_base_currency')==$currency->code) ? 'selected="selected"':'').' >'.$currency->country.' - '.$currency->currency.' ('.$currency->code.')</option>';
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Design Panel Help Text</th>
                  <td><textarea  name="wpic_design_help_text" rows="5" cols="38"><?php echo get_option('wpic_design_help_text');?></textarea></td>
                </tr>
              </table>
              <input type="hidden" name="wpic_general_settings" value="yes" />
							<?php
						break; 
						case 'payment' : 
							?>
              <div class="meta-box-sortables ">
                <?php
                echo wpic_admin_gateway_form();
                ?>
              </div>
							<?php
						break;
						case 'shipping' : 
							?>
              <div class="meta-box-sortables ">
                <?php
                echo wpic_admin_shipping_form();
                ?>
              </div>
							<?php
						break;
					}
          echo '</div>';
				
				?>
				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
				</p>
			</form>
			
		</div>
  </div>
  <?php
}

add_action('admin_menu', 'wpic_admin_setting');