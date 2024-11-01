(function($) {
  "use strict";
jQuery(document).ready( function($){
 //settings color input option
 $('#bg_color, #txt_color, #eml_bg_color, #btn_bg_color, #btn_txt_color').wpColorPicker();
 
 //campaign status change
 $(document).on("change", ".campaign input[name='campaign-switch']", function(e){
	var cid = $(this).attr('id');																	  	if ($(this).is(':checked')) {
		var campaign_switch = 'a';
    }else{
 		var campaign_switch = 'd';
	}
	
	jQuery.ajax({
	  url: ajax_object.ajaxurl,
	  type: 'POST',
	  data: { action: 'topctabar_switch_campaign_status', campaign_switch:campaign_switch, cid:cid, nonce_ajax : ajax_object.nonce},
	  success:function(response){
	  },
	  error: function(){
			console.log(errorThrown); // error
	  }
	});

 });
 
});//close  document
})(jQuery);


