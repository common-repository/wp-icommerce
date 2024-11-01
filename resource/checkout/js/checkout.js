jQuery(document).ready(function(){
  jQuery('.wpic_gateway').hide();
  jQuery('.wpic_gateway :input').prop('disabled',true);
  jQuery('.checkout_gateway').change(function(){
    var gatewayid = jQuery(this).val();
    jQuery('.wpic_gateway').hide();
    jQuery('.wpic_gateway :input').prop('disabled',true);
    jQuery('.wpic_gateway_'+gatewayid).show();
    jQuery('.wpic_gateway_'+gatewayid+' :input').prop('disabled',false);
  });
  
  jQuery('.wpic_checkout_title').click(function(e){ 
    if(jQuery(this).parent().hasClass('enable')){
      jQuery(this).parent().nextAll().removeClass('enable');
      jQuery(this).parent().nextAll().find('.wpic_checkout_title').removeClass('active');
      jQuery('.wpic_step').hide();
      jQuery(this).parent().addClass('enable');
      jQuery(this).addClass('active');
      jQuery(this).parent().find('.wpic_step').show('slide');
    }
  });
  
  jQuery('.checkout_billing').click(function(e){
    jQuery('.checkout_billing').after('<span class="checkout_loader"> </span>');
    //billing required field check
    var required_check = 1;
    //var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    jQuery(this).parent().find('.checkout_required').each(function(){
      if(jQuery.trim(jQuery(this).val())==''){
        jQuery(this).next('span').remove();
        jQuery(this).after('<span class="wpic_required"><br />This field is required.</span>');
        required_check = 0;
      }else{
        jQuery(this).next('span').remove();
      }
    });
    
    if(!required_check){
      jQuery('.checkout_loader').remove();
      return false;
    }
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if(!emailReg.test(jQuery.trim(jQuery("#billing_email").val()))){
      alert('please enter valid email address');
      jQuery('.checkout_loader').remove();
      return false;
    }
    //get all billing input field data
    var input = jQuery('#wpic_billing :input');
    var values = {};
    input.each(function() {
      if(this.type=='text' || this.type=='select-one'){
        values[this.name] = jQuery(this).val();
      }
      if(this.type=='checkbox'){
        if(jQuery(this).prop('checked')){
          values[this.name] = 'on';
        }else{
          values[this.name] = 'off';
        }
      }
    });
    
    jQuery.ajax({
      url: scpdAjax.ajaxurl,
      type: 'post',
      data: {action:'wpic_checkout_billing', bill_data:values},
      success: function(data){
        //alert(data);
        //alert(values.toSource());
        var resp_data = JSON.parse(data);
        if(resp_data.response){
          if(values['wpic_same_shipping']=='on'){
            set_shipping_data(values);
          }
          jQuery('.checkout_billing').parent().parent().find('.wpic_step').hide('slide');
          jQuery('#wpic_shipping').addClass('enable');
          jQuery('#wpic_shipping').find('.wpic_checkout_title').addClass('active');
          jQuery('#wpic_shipping').find('.wpic_step').show('slide');
        }
        jQuery('.checkout_loader').remove();
      }
    });
  });
  
  jQuery('.checkout_shipping').click(function(e){
    jQuery('.checkout_shipping').after('<span class="checkout_loader"> </span>');
    //shipping required field checking
    var required_check = 1;
    jQuery(this).parent().find('.checkout_required').each(function(){
      if(jQuery.trim(jQuery(this).val())==''){
        jQuery(this).next('span').remove();
        jQuery(this).after('<span class="wpic_required"><br />This field is required.</span>');
        required_check = 0;
      }else{
        jQuery(this).next('span').remove();
      }
    });
    if(!required_check){
      jQuery('.checkout_loader').remove();
      return false;
    }
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if(!emailReg.test(jQuery.trim(jQuery("#shipping_email").val()))){
      alert('please enter valid email address');
      jQuery('.checkout_loader').remove();
      return false;
    }
    
    var input = jQuery('#wpic_shipping :input');
    var values = {};
    input.each(function() {
      if(this.type=='text' || this.type=='select-one'){
        values[this.name] = jQuery(this).val();
      }
      if(this.type=='checkbox'){
        if(jQuery(this).prop('checked')){
          values[this.name] = 'on';
        }else{
          values[this.name] = 'off';
        }
      }
    });
    
    jQuery.ajax({
      url: scpdAjax.ajaxurl,
      type: 'post',
      data: {action:'wpic_checkout_shipping', ship_data:values},
      success: function(data){
        var resp_data = JSON.parse(data);
        if(resp_data.response){
          jQuery.ajax({
            url: scpdAjax.ajaxurl,
            type: 'post',
            data: {action:'wpic_shipping_methods'},
            success: function(data){
              if(data){
                jQuery('#wpic_ship_container').html(data);
                jQuery('.checkout_shipping').parent().parent().find('.wpic_step').hide('slide');
                jQuery('#wpic_shipping_method').addClass('enable');
                jQuery('#wpic_shipping_method').find('.wpic_checkout_title').addClass('active');
                jQuery('#wpic_shipping_method').find('.wpic_step').show('slide');
              }
              jQuery('.checkout_loader').remove();
            }
          });
        }
      }
    });
  });
  
  jQuery('.checkout_shipping_method').click(function(e){
    jQuery('.checkout_shipping_method').after('<span class="checkout_loader"> </span>');
    var required_check = 0;
    jQuery(this).parent().find('.checkout_required').each(function(){
      if(jQuery(this).prop('checked')){ 
        jQuery('#wpic_shipping_error').html('');
        required_check = 1;
      }
    });
    if(!required_check){
      jQuery('#wpic_shipping_error').html('<span class="wpic_required">Please select a shipping method.</span>');
      jQuery('.checkout_loader').remove();
      return false;
    }
    var input = jQuery('#wpic_shipping_method :input');
    var values = {};
    input.each(function() {
      if(jQuery(this).prop('checked')){
        values[this.name] = jQuery(this).val();
      }   
    });
    
    jQuery.ajax({
      url: scpdAjax.ajaxurl,
      type: 'post',
      data: {action:'wpic_shipping_method_update', ship_method:values},
      success: function(data){
        var resp_data = JSON.parse(data);
        if(resp_data.response){
          jQuery('.checkout_shipping_method').parent().parent().find('.wpic_step').hide('slide');
          jQuery('#wpic_payment_method').addClass('enable');
          jQuery('#wpic_payment_method').find('.wpic_checkout_title').addClass('active');
          jQuery('#wpic_payment_method').find('.wpic_step').show('slide');
        }else{
          jQuery('.checkout_loader').remove();
          return false;
        }
        jQuery('.checkout_loader').remove();
      }
    });
  });
  
  jQuery('.checkout_payment_method').click(function(e){
    jQuery('.checkout_payment_method').after('<span class="checkout_loader"> </span>');
    //required field checking
    var required_check = 0;
    jQuery(this).parent().find('.checkout_required').each(function(){
      if(jQuery(this).prop('checked')){ 
        jQuery('#wpic_payment_error').html('');
        required_check = 1;
      }
    });
    if(!required_check){
      jQuery('#wpic_payment_error').html('<span class="wpic_required">Please select a payment method.</span>');
      jQuery('.checkout_loader').remove();
      return false;
    }
    
    jQuery.ajax({
      url: scpdAjax.ajaxurl,
      type: 'post',
      data: {action:'wpic_checkout_overview'},
      success: function(data){
        if(data){
          jQuery('#wpic_overview_content').html(data);
          jQuery('.checkout_payment_method').parent().parent().find('.wpic_step').hide('slide');
          jQuery('#wpic_overview').addClass('enable');
          jQuery('#wpic_overview').find('.wpic_checkout_title').addClass('active');
          jQuery('#wpic_overview').find('.wpic_step').show('slide');
        }else{
          jQuery('.checkout_loader').remove();
          return false;
        }
        jQuery('.checkout_loader').remove();
      }
    });
  });
  
  jQuery('.checkout_submit').click(function(e){
    jQuery('#wpic_checkout').submit();
  });
  
  function set_shipping_data(values){
    jQuery('#shipping_first_name').val(values['billing_first_name']);
    jQuery('#shipping_last_name').val(values['billing_last_name']);
    jQuery('#shipping_email').val(values['billing_email']);
    jQuery('#shipping_address_1').val(values['billing_address_1']);
    jQuery('#shipping_address_2').val(values['billing_address_2']);
    jQuery('#shipping_city').val(values['billing_city']);
    jQuery('#shipping_zip').val(values['billing_zip']);
    jQuery('#shipping_state').val(values['billing_state']);
    jQuery('#shipping_country').val(values['billing_country']);
    jQuery('#shipping_phone').val(values['billing_phone']);
  }
  
  
});