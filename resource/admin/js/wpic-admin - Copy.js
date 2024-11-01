jQuery(document).ready(function(){
  var i=0;
	jQuery('ul.wpic_tab li').click(function(){
		var tab_id = jQuery(this).attr('data-tab');
		jQuery('ul.wpic_tab li').removeClass('current');
		jQuery('.wpic_tab_content').removeClass('current');
		jQuery(this).addClass('current');
		jQuery("#"+tab_id).addClass('current');
	});
  
  /*add attributes*/
  jQuery('.wpic_attribute_section').on('click', 'button.wpic_add_attribute', function(){
    jQuery('.wpic_add_attribute').before('<div class="wpic_attr_container closed"><h3><div class="wpic_attr_title"></div><button class="button wpic_remove_attributes" type="button">Remove</button><div class="wpic_toggle handlediv"></div><div class="clear"></div></h3><div class="wpicattr"><table><tr><td ><strong>Attribute Name:</strong><br /><input type="text" class="wpic_attribute_name" name="wpic_attribute_name[]" /></td><td class="wpic_attr_vals"><strong>Attribute Values:</strong><br /><textarea cols="40" name="wpic_attribute_value[]"></textarea><p class="description">Enter "," comma spearated text for attribute value.</p></td></tr></table></div></div>');
  });
  
  jQuery('.wpic_attribute_section').on('click', 'button.wpic_remove_attributes', function(){
    jQuery(this).parent().parent('div').remove();
    return false;
  });

  jQuery('.wpic_attribute_section').on('click', '.wpic_attr_container h3', function(){
    var vis = jQuery(this).parent().find('.wpicattr').is(':visible');
    if(vis){
      jQuery(this).parent().addClass('closed');
    }else{
      jQuery(this).parent().removeClass('closed');
    }
    jQuery(this).parent().find('.wpicattr').toggle('fast');
  });
  
  jQuery('.wpic_attribute_section').on('keyup', 'input.wpic_attribute_name', function(){
    jQuery(this).parent().parent().parent().parent().parent().parent().find('.wpic_attr_title').html(jQuery(this).val());
  });
  
  jQuery('.wpic_attribute_section').on('click', 'button.wpic_save_attributes', function(){
    show_loader(true);
    var attr_data = jQuery('.wpic_attribute_section').find('input,textarea').serialize();
    jQuery.ajax({
      url: wpicAjax.ajaxurl,
      type: 'post',
      data: {action:'wpic_save_attr_data', attr_data:attr_data, postid:wpic_prod_id},
      success: function(data){
        show_loader(false);
      }
    });
  });
  
  jQuery('.wpic_variation_section').on('click','button.wpic_add_variation',function(){
    show_loader(true);
    jQuery.ajax({
      url: wpicAjax.ajaxurl,
      type: 'post',
      data: {action:'wpic_get_attr_data', postid:wpic_prod_id},
      success: function(data){
        if(data!='0'){
          jQuery('.wpic_add_variation').before(data);
        }else{
          jQuery('.wpic_message').html('<div class="wpic_warning">Please add some <strong>Attributes</strong> before adding <strong>Variations</strong>');
        }
        show_loader(false);
      }
    });
  });
  
  
  jQuery('.wpic_variation_section').on('click', '.wpic_var_container h3', function(){
    var vis = jQuery(this).parent().find('.wpicvariation').is(':visible');
    if(vis){
      jQuery(this).parent().addClass('closed');
    }else{
      jQuery(this).parent().removeClass('closed');
    }
    jQuery(this).parent().find('.wpicvariation').toggle('fast');
  });
  
  jQuery('.wpic_variation_section').on('click','.wpic_var_container h3 select',function(e){
    e.stopPropagation();
  });
  
  jQuery('.wpic_variation_section').on('click','.wpic_remove_variation', function(e){
    jQuery(this).parent().parent('div').remove();
    e.stopPropagation();
  });
  
  jQuery('.wpic_variation_section').on('click','.wpic_save_variation', function(e){
    show_loader(true);
    var var_data = jQuery('.wpic_variation_section').find('input,select').serialize();
    jQuery.ajax({
      url: wpicAjax.ajaxurl,
      type: 'post',
      data: {action:'wpic_save_var_data', var_data:var_data, postid:wpic_prod_id},
      success: function(data){
        show_loader(false);
      }
    });
  });
  
  function show_loader(state){
    var height = jQuery('.wpic_tab_contant_area').height();
    if(state){
      jQuery('.wpic_loader').show('fast');
      jQuery('.wpic_loader img').css('margin-top',((height/2)-16));
    }else{
      jQuery('.wpic_loader').hide('fast');
    }
  }
  
});