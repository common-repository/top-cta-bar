<?php
/**
 * @package 	Top CTA Bar
 * @version 	1.0.9
 * 
 * Front View Page
 * 
**/
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
//***********************************************************
//add a fixed top bar with settings option 
//in body tag to display at front view
//***********************************************************
if ( ! function_exists( 'topctabar_add_front_view_html' ) ) {
function topctabar_add_front_view_html() {
global $wpdb;
$status = "a"; 
$visitor = isset( $_COOKIE['top_cta_cookie'] ) ? sanitize_key($_COOKIE['top_cta_cookie']) : 'no';
$row = $wpdb->get_row($wpdb->prepare("select * from ".$wpdb->prefix."campaigns WHERE  status = %s", $status), ARRAY_A );
if(isset($row) && !empty($row)):
$campaign_arr = json_decode($row['settings'], true);
   $id = intval($row['id']);
   $name = esc_attr($campaign_arr['campaign_name']);
   $btn_text = esc_attr($campaign_arr['btn_text']);
   $height = intval($campaign_arr['height']);
   $txt_color = sanitize_hex_color($campaign_arr['txt_color']);
   $bg_color = sanitize_hex_color($campaign_arr['bg_color']);
   $eml_bg_color = sanitize_hex_color($campaign_arr['eml_bg_color']);
   $btn_bg_color = sanitize_hex_color($campaign_arr['btn_bg_color']);
   $btn_txt_color = sanitize_hex_color($campaign_arr['btn_txt_color']);
   $font_size = intval($campaign_arr['font_size']);
?>
<style>
#header_fixedBar{
	background:<?php echo $bg_color; ?>;
	color:<?php echo $txt_color; ?>; 
	height:<?php echo $height; ?>px;
	font-size:<?php echo $font_size; ?>px
}
#header_fixedBar #inner{
	color:<?php echo $txt_color; ?>; 
	height:<?php echo $height; ?>px;
}
#header_fixedBar #inner .confirm-button{
    background:<?php echo $btn_bg_color; ?>;
	color:<?php echo $btn_txt_color; ?>;
}
#header_fixedBar #inner input[name=cta_email]{
	background:<?php echo $eml_bg_color; ?>; 
}
</style>
<?php  if($visitor !== "ok"){ ?>
  <div id="header_fixedBar"><div id="inner" ><span><?php _e($name, 'topctabar'); ?></span><input type="text" name="cta_email" placeholder="<?php _e('Enter Email', 'topctabar'); ?>" /><a class="btn confirm-button" href="#" ><?php _e($btn_text, 'topctabar'); ?></a></div>
  <input type="hidden" name="cta_campaign" value="<?php echo $id; ?>"/>
  </div>
 <?php } endif;?>
<?php
}
add_action("wp_head", "topctabar_add_front_view_html"); //add bar to display in body tag

}//if function
function topctabar_save_registrar_submission(){ //get and save visitor email after submit
check_ajax_referer( 'topctabar-frontend-nonce', 'nonce_ajax' );
global $wpdb;
 if(!empty($_POST)) {
   $visitor_email = sanitize_email($_POST['visitor_email']);
   $cid = intval($_POST['id']);
   $confirm_view = $wpdb->get_var( $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."visitors WHERE reg_email =%s AND cid =%d", $visitor_email, $cid));
   $cdate = date("Y-m-d H:i:s");
 if(!isset($confirm_view) && empty($confirm_view)){
   $object_id = $wpdb->insert($wpdb->prefix.'visitors', array(
    'cid' => $cid,
	'reg_email' => $visitor_email,
	'cdate' => $cdate
   ), array( '%d', '%s', '%s' ));
  }	
  $expiry = strtotime('+1 month');
  setcookie( 'top_cta_cookie', 'ok', $expiry, COOKIEPATH, COOKIE_DOMAIN );
 }//outer if close
 wp_die();
}//function close
add_action("wp_ajax_topctabar_save_registrar_submission", "topctabar_save_registrar_submission");
add_action("wp_ajax_nopriv_topctabar_save_registrar_submission", "topctabar_save_registrar_submission");

//active or deactive a campaign from admin settings
function topctabar_switch_campaign_status(){ 
/* Check Localize Script Nonce */
check_ajax_referer( 'topctabar-admin-nonce', 'nonce_ajax' );
global $wpdb;
if($_POST){
  $object_id = $wpdb->update($wpdb->prefix .'campaigns', 
	array( 'status' => esc_js($_POST['campaign_switch']) ), 
	array( 'id' => intval($_POST['cid']) )
  );
}
  wp_die();

}//function close
add_action("wp_ajax_topctabar_switch_campaign_status", "topctabar_switch_campaign_status");
add_action("wp_ajax_nopriv_topctabar_switch_campaign_status", "topctabar_switch_campaign_status");
//***********************************************************
// add html code for pop up box to footer
// for displaying a thank you message
//***********************************************************
if ( ! function_exists( 'topctabar_popup_box_html' ) ) {
function topctabar_popup_box_html(){
?>
<div class="popupbox">
<div class="closelog"></div>
<div class="content-area">
<div class="box">
<div class="header-area"><?php _e('Popup window title', 'topctabar'); ?></div>
<div class="inner">
<p><?php _e('Thank you for staying with us', 'topctabar'); ?></p>
<p><a class="btn ok-button" href="#" ><?php _e('Ok', 'topctabar'); ?></a></p>
</div>
</div><!--box1-->

</div><!--log-area-->
</div><!--logbox-->
<?php 
}//function close
add_action("wp_footer", "topctabar_popup_box_html");// add a pop up box to footer

}//if function