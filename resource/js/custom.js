var chunq = 1;
var prodobj = {};
prodobj.prods={};
prodobj.customitem = {};
var proobjvalue = {};

jQuery(document).ready(function(){
	
	jQuery('div.scpd-tabs div').click(function(){
		var tab_id = jQuery(this).attr('data-tab');

		jQuery('div.scpd-tabs div').removeClass('current');
		jQuery('.scpd-tab-content').removeClass('current');

		jQuery(this).addClass('current');
		jQuery("#"+tab_id).addClass('current');
	});

});

jQuery(document).ready(function(){
  var canvobj = {};
  var canvas = new fabric.Canvas('prod_canvas');
  var prev_canvobj = 0;
  
  canvas.on('object:selected', viewObject);
  canvas.on('selection:cleared', clearPanel);
  
  function viewObject(){
    var object = canvas.getActiveObject();
    if(object){
      if(object['type'] == 'text'){
        var tab_id = 'scpd-tab-2';
        scpdtabchange(tab_id);
        jQuery("#wpic_txt").val(object['text']);
      }
      else if(object['type'] == 'image'){
        var tab_id = 'scpd-tab-3';
        scpdtabchange(tab_id);
      }
    }
  }
  
  function scpdtabchange(tabid){
    jQuery('div.scpd-tabs div').removeClass('current');
    jQuery('.scpd-tab-content').removeClass('current');
    jQuery('.'+tabid).addClass('current');
    jQuery("#"+tabid).addClass('current');
  }
  
  function clearPanel(){
    
  }
  
  if(jQuery('.wpic_product_image').length>0){
    prev_canvobj = jQuery('.wpic_product_image').first().data('id');
    drawproductasbg(jQuery('.wpic_product_image').first().data('url'));
    product.image=jQuery('.wpic_product_image').first().data('url');
  }else{
    product.image=''; //need to fix this
  }
  
  jQuery('.wpic_product_image').click(function(){
    var canvas_id = jQuery(this).data('id');
    
    canvas.deactivateAll().renderAll();
    canvobj[prev_canvobj]={imgid:prev_canvobj,customdata:JSON.stringify(canvas.toJSON()),screenshot:canvas.toDataURL()};
    
    canvas.clear();
    prev_canvobj=canvas_id;
    
    if(typeof(canvobj[canvas_id])!='undefined'){
      canvas.loadFromJSON(canvobj[canvas_id].customdata,canvas.renderAll.bind(canvas));
    }else{
      drawproductasbg(jQuery(this).data('url'));
    }
  });
  
  jQuery('.wpic_trash_selected').click(function(){
    var activeObject = canvas.getActiveObject();
    if(activeObject){
      if(confirm('Are you sure, you want to delete selected item.')){
        //if(activeObject){
          canvas.remove(activeObject);
        //}
      }
    }else{
      alert('Please select an object.')
    }
  });
  
  jQuery('.wpic_bring_front_selected').click(function(){
    var activeObject = canvas.getActiveObject();
    if(activeObject){
      canvas.bringToFront(activeObject);
    }
  });
  
  jQuery('.wpic_send_back_selected').click(function(){
    var activeObject = canvas.getActiveObject();
    if(activeObject){
      canvas.sendToBack(activeObject);
    }
  });
  
  jQuery('#wpic_add_text').click(function(){
    var txtobject = {};
    //txtobject.txtidunq = txtid;
    txtobject.txtstr = jQuery('#wpic_txt').val();
    txtobject.txtcolor = '#'+jQuery('#wpic_txt_color').val();
    txtobject.txtfont = jQuery('#wpic_font').val();
    txtobject.txtfontweight = 'normal';
    txtobject.txtfontsize = jQuery('#wpic_txt_size').val();
    txtobject.txtborder = '';
    txtobject.txtbordercolor = '';
    
    if(txtobject.txtstr){
      drawTextObject(txtobject);
    }else{
      alert('Please Type Text');
      return false;
    }
  });
  
  jQuery('#wpic_txt').keyup(function(){
    var object = canvas.getActiveObject();
    if (object) {
        object.set('text', jQuery(this).val());
    }
    canvas.renderAll();
  });
  
  jQuery("#wpic_font").change(function(){
    var object = canvas.getActiveObject();
    if (object) {
        object.set('fontFamily', jQuery(this).val());
    }
    canvas.renderAll();
  });
  
  jQuery("#wpic_txt_color").change(function(){
    var object = canvas.getActiveObject();
    if (object) {
        object.set('fill', '#' + jQuery(this).val());
    }
    canvas.renderAll();
  });
  
  jQuery("#wpic_txt_size").keyup(function(){
    var object = canvas.getActiveObject();
    if (object) {
        object.set('fontSize', jQuery(this).val());
    }
    canvas.renderAll();
  });
  
  jQuery('.wpic_add_art').click(function(){
    var img_src = jQuery(this).data('url');
    var logoobj = new Image();
    logoobj.src = img_src;
      drawlogoImage(logoobj);
    
  });
  
  function drawTextObject(txtobject){
    var text = new fabric.Text(txtobject.txtstr, { 
      left: 100, 
      top: 100,
      fontFamily:txtobject.txtfont,
      textDecoration:txtobject.txtdecoration,
      fontWeight:txtobject.txtfontweight,
      fontStyle:txtobject.txtfontstyle,
      fontSize:txtobject.txtfontsize,
      fill:txtobject.txtcolor,
      //shadow:txtobject.txtshadowcolor,
      
      stroke:txtobject.txtbordercolor,
      strokeWidth:Number(txtobject.txtborder)
      //strokeWidth:5
    });
    canvas.add(text);
  }
  
  function drawlogoImage(logoobj){
    setTimeout(function() {
      var imgInstance = new fabric.Image(logoobj, {
        left: 100,
        top: 100,
        width : 200,
        height : 200,
      });
      canvas.add(imgInstance);
    }, 10);
  }
 
 function drawproductasbg(image_url){
   canvas.setBackgroundImage(image_url, canvas.renderAll.bind(canvas), {
    	backgroundImageStretch: false,
      width:400,
      height:400
	});
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

   canvas.deactivateAll().renderAll();
   canvobj[prev_canvobj]={imgid:prev_canvobj,customdata:JSON.stringify(canvas.toJSON()),screenshot:canvas.toDataURL()};
   
   for(var key in canvobj){
     var cobj = JSON.parse(canvobj[key].customdata);
     if(!cobj.objects.length){
      delete canvobj[key];
     }
   }
   var qtys = jQuery('#wpic_prod_quantity').val();
   if(qtys>0){
     product.quantity=qtys;
   }
   
   prodobj.prods = product;
   prodobj.customitem = canvobj;
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