jQuery(document).ready(function(){
  jQuery('.wpic_gateway').hide();
  jQuery('.wpic_gateway :input').prop('disabled',true);
  jQuery('.checkout_gateway').change(function(){
    var test = jQuery(this).val();
    jQuery('.wpic_gateway').hide();
    jQuery('.wpic_gateway :input').prop('disabled',true);
    jQuery('.wpic_gateway_'+test).show();
    jQuery('.wpic_gateway_'+test+' :input').prop('disabled',false);
  });
})