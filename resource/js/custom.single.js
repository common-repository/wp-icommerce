var prodobj = {};
prodobj.prods={};
//prodobj.customitem = {};
jQuery(document).ready(function(){
  jQuery('.wpic_prod_image_gallery').on('click','.wpic_product_image', function(){
    var img_url = jQuery(this).data('url');
    jQuery('.wpic_thumb_image').attr('src',img_url);
 });
 if(jQuery('.wpic_product_image').length>0){
    product.image=jQuery('.wpic_product_image').first().data('url');
  }else{
    product.image=''; //need to fix this
  }
 jQuery('.wpic_prod_add_to_cart').click(function(){
   
   //variation check
   if(jQuery('#is_variation').val()=='yes'){
     var var_not_selected = 0;
     jQuery('.var_ddl').each(function(){
       if(!jQuery(this).val() && jQuery(this).val()!='-1'){
         var_not_selected=1;
       }
     });
   }
   
   if(var_not_selected){
     jQuery('.wpic_var_msg').html('<div class="wpic_error">Please Choose Options.</div>');
     return false;
   }

   var qtys = jQuery('#wpic_prod_quantity').val();
   if(qtys>0){
     product.quantity=qtys;
   }
   prodobj.prods = product;
   jQuery('#graph_overlay').slideDown('slow');
   jQuery('#graph_progress').fadeIn('fast');
   jQuery.ajax({
      url: scpdAjax.ajaxurl,
      type: 'post',
      data: {action:'wpic_display_custom_product_cart_page', product_data:prodobj},
      success: function(data){
        if(data){
          jQuery('#graph_overlay').slideUp();
          jQuery('#graph_progress').fadeOut();
          top.location.replace(carturl);
        }
      }
    });
 });
});