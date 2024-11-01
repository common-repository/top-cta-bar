(function($) {
  "use strict";
jQuery.noConflict();
jQuery(document).ready(function($){
// create a popup box							
function popup_box(){
    $(".popupbox").css("left", Math.max(0, (($(window).width() - $(".popupbox").outerWidth()) / 2) + $(window).scrollLeft()) + "px");
	$(".popupbox").show();
}

//get and save registrar submitted email
$(document).on("click", "#header_fixedBar #inner a", function(e){
   e.preventDefault();
   var visitor_email = $("#header_fixedBar #inner input[name='cta_email']").val();
   var cta_campaign_id = $("#header_fixedBar input[name='cta_campaign']").val();
   
   if (validateEmail(visitor_email)) {
	 popup_box();
     jQuery.ajax({
		url: frontend_ajax_object.ajaxurl,
		type: 'POST',
		data:{ action:'topctabar_save_registrar_submission', visitor_email:visitor_email, id:cta_campaign_id, nonce_ajax : frontend_ajax_object.nonce },
		success: function(response){
		}});
   }else{
	 alert('Error!! your Email is not valid..'); 
	 return;
   }
   
});
  //popup box close
  $(".closelog, .ok-button").live("click",function(event){
    $("#header_fixedBar").hide();
	$(".popupbox").hide();
  });


}); //close main document
function validateEmail(sEmail) {
 var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
 if (pattern.test(sEmail)) {
   return true;
 }else {
   return false;
 }
}
})(jQuery);