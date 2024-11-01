<?php
/**
 * @package 	Top CTA Bar
 * @version 	1.0.9
 * 
 * Settings Page
 * 
**/
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
if ( ! function_exists( 'topctabar_campaign_settings_admin_menu' ) ) {
add_action( 'admin_menu', 'topctabar_campaign_settings_admin_menu' );
function topctabar_campaign_settings_admin_menu() {
	add_menu_page(
		__( 'Top CTA Bar', 'topctabar' ),
		__( 'Top CTA Bar', 'topctabar' ),
		'manage_options',
		'topctabar_campaign_settings',
		'topctabar_campaign_settings_admin_page',
		plugins_url( 'top-cta-bar/images/menu-icon.png' ),
		'8'
	);
}

}//if function
function topctabar_campaign_settings_admin_page() {
global $cta_option;
$cta_option = get_option('cta_option');
$site = 'admin.php?page=topctabar_campaign_settings';
 echo "<h1>". __("Settings for Top CTA Bar", "topctabar") ."</h1>";
 $tab = isset( $_GET['tab'] ) ? sanitize_key($_GET['tab']) : 'general';
 $set_menu_1 = ( $tab === "general" || $tab === "" ) ? "nav-tab nav-tab-active" : "nav-tab";
 $set_menu_2 = ( ($tab === "new_campaign") || $tab === "edit_campaign") ? "nav-tab nav-tab-active" : "nav-tab";
  $set_menu_3 = ( ($tab === "all_visitors") ) ? "nav-tab nav-tab-active" : "nav-tab";
 if($tab === "edit_campaign") $menu_head = "Edit Campaign";
 else $menu_head = "New Campaign";

?>
 <h3 class="nav-tab-wrapper"><?php 
    echo '<a href="' . $site . '&amp;tab=general" class="' . $set_menu_1 . '" title="' . __('All Campaigns', 'topctabar') . '" >' . __('All Campaigns', 'topctabar') . '</a>';
    echo '<a href="' . $site . '&amp;tab=new_campaign" class="' . $set_menu_2 . '" title="' . __($menu_head, 'topctabar') . '">' . __($menu_head, 'topctabar') . '</a>';
	echo '<a href="' . $site . '&amp;tab=all_visitors" class="' . $set_menu_3 . '" title="' . __('All Visitors','topctabar') . '" >' . __('All Visitors', 'topctabar') . '</a>';	
  ?></h3><?php
  if ($tab == '' || $tab == 'general') {
  ?>
    <div>
	<?php echo '<h2><strong>'.__('All Campaigns', 'topctabar').'</strong></h2>';
      topctabar_showall_created_capmpaigns_data(); ?>
	</div>
  <?php
  }elseif ( ($tab === 'new_campaign') || ($tab === 'edit_campaign')) { ?>
    <div>
	  <?php topctabar_create_edit_new_capmpaign(); ?>
	</div>
  <?php 
  }elseif($tab === 'all_visitors')  { ?>
    <div>
	  <?php topctabar_showall_capmpaign_registered_visitors_data(); ?>
	</div>
  <?php 
  }
}//function close

function topctabar_create_edit_new_capmpaign(){
global $wpdb;
$msg = "";
$campaign = array();

$tab = isset( $_GET['tab'] ) ? sanitize_key($_GET['tab']) : 'general';
$submit_url = esc_url($_SERVER['REQUEST_URI']);
if ($tab === 'edit_campaign'){
 $id = intval($_GET['id']);
 $site = 'admin.php?page=topctabar_campaign_settings&tab=new_campaign';
 echo "<a href='" . $site ."' ><b>New Campaign</b></a>";
 echo "<h4><strong>".__( "Edit " .$id. " no Campaign Settings", "topctabar" )."</strong></h4>";
 $submit = "update";
 $row = $wpdb->get_row($wpdb->prepare("select * from ".$wpdb->prefix."campaigns WHERE id =%d", $id ), ARRAY_A );  
 $campaign_arr = json_decode($row['settings'], true);
 $campaign['campaign_name'] = esc_attr($campaign_arr['campaign_name']);
 $campaign['bg_color'] = (empty($campaign_arr['bg_color']) ? '' : sanitize_hex_color($campaign_arr['bg_color']));
 $campaign['height'] = (empty($campaign_arr['height']) ? '55' : $campaign_arr['height']);
 $campaign['txt_color'] = (empty($campaign_arr['txt_color']) ? '#727272' :$campaign_arr['txt_color']);
 $campaign['font_size'] = (empty($campaign_arr['font_size']) ? '14' : $campaign_arr['font_size']);
 $campaign['eml_bg_color'] = (empty($campaign_arr['eml_bg_color']) ? '#828292' : $campaign_arr['eml_bg_color']);
 $campaign['btn_text'] = (empty($campaign_arr['btn_text']) ? $campaign['btn_text'] : $campaign_arr['btn_text']);
 $campaign['btn_txt_color'] = (empty($campaign_arr['btn_txt_color']) ? '#727272' : $campaign_arr['btn_txt_color']);
 $campaign['btn_bg_color'] = (empty($campaign_arr['btn_bg_color']) ? '#828292' : $campaign_arr['btn_bg_color']);
}elseif($tab === 'new_campaign'){
echo "<h4><strong>New Campaign Settings </strong></h4>";
 $submit = "save";
}
if ( ! empty( $_POST ) && check_admin_referer( 'topctabar_nonce', 'submit_capmpaign' ) ) {
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING); // Sanitize POST
if(isset($_POST['save_capmpaign']) && current_user_can('manage_options')){
$redirect_url = esc_url(admin_url( '?page=topctabar_campaign_settings&amp;tab=general' ));
$campaign['campaign_name'] = (isset($_POST['campaign_name']) && !empty($_POST['campaign_name'])) ? esc_attr(htmlspecialchars(stripslashes($_POST['campaign_name']), ENT_QUOTES)):'Your Campaign Title';
$campaign['btn_text'] = (isset($_POST['btn_text']) && !empty($_POST['btn_text'])) ? esc_html($_POST['btn_text']):'Get Started';
$campaign['bg_color'] = (isset($_POST['bg_color']) && !empty($_POST['bg_color'])) ? sanitize_hex_color($_POST['bg_color']):'#CC6600';
$campaign['eml_bg_color'] = (isset($_POST['eml_bg_color']) && !empty($_POST['eml_bg_color'])) ? sanitize_hex_color($_POST['eml_bg_color']):'#817161';
$campaign['txt_color'] = (isset($_POST['txt_color']) && !empty($_POST['txt_color'])) ? sanitize_hex_color($_POST['txt_color']):'#0D92AD';
$campaign['btn_txt_color'] = (isset($_POST['btn_txt_color']) && !empty($_POST['btn_txt_color'])) ? sanitize_hex_color($_POST['btn_txt_color']):'#81d742';
$campaign['height'] = (isset($_POST['height']) && !empty($_POST['height'])) ? intval($_POST['height']):'50';

$campaign['font_size'] = (isset($_POST['font_size']) && !empty($_POST['font_size'])) ? intval($_POST['font_size']):'13';
$campaign['btn_bg_color'] = (isset($_POST['btn_bg_color']) && !empty($_POST['btn_bg_color'])) ? sanitize_hex_color($_POST['btn_bg_color']):'#817161';
$mysqldate = date("Y-m-d H:i:s");
$campaign_str = json_encode($campaign, true);
  if(sanitize_key($_POST['save_capmpaign']) === "save"){
   $object_id = $wpdb->insert($wpdb->prefix .'campaigns', array(
	'settings' => $campaign_str,
	'status' => 'a',
	'cdate' => $mysqldate
   ));
  }elseif(sanitize_key($_POST['save_capmpaign']) === "update"){
   $object_id = $wpdb->update($wpdb->prefix .'campaigns', 
	array( 'settings' => $campaign_str	), 
	array( 'id' => $id )
    );
  }
 echo "<script type='text/javascript'>window.location='".$redirect_url."';</script>";
 exit();
}else{//check submit button
 echo "check user permission";
 return;
}
}//check referer
?>
<hr/>
<form id="newcapmpaign" name="newcapmpaign" method="post" action="<?php echo $submit_url; ?>">
  <table border="0" cellspacing="0" cellpadding="0" class="widefat">
	 <tr><td><?php _e('Campaign Name:', 'topctabar'); ?></td><td><input name="campaign_name" type="text" id="campaign_name" value="<?php echo $campaign['campaign_name']; ?>" placeholder="Campaign Title" /></td></tr>
     <tr><td><?php _e('Font Size:', 'topctabar'); ?></td><td><input name="font_size" type="text" value="<?php echo $campaign['font_size']; ?>" placeholder="Campaign text size" /> px</td></tr>
	 <tr><td><?php _e('Background Color:', 'topctabar'); ?></td><td>
	 <input type="text" name="bg_color" value="<?php echo $campaign['bg_color']; ?>" id="bg_color" />
</td></tr>
	 <tr><td><?php _e('Text Color:', 'topctabar'); ?></td><td>
	 <input type="text" name="txt_color" value="<?php echo $campaign['txt_color']; ?>" id="txt_color" />
</td></tr>
	 <tr><td><?php _e('Height:', 'topctabar'); ?></td><td><input name="height" type="text" value="<?php echo $campaign['height']; ?>" placeholder="CTA Bar height" /> px</td></tr>
	 <tr><td><?php _e('Email Bg Color:', 'topctabar'); ?></td><td><input type="text" name="eml_bg_color" value="<?php echo $campaign['eml_bg_color']; ?>" id="eml_bg_color" /></td></tr>
	 <tr><td><?php _e('Button Text', 'topctabar'); ?></td><td><input name="btn_text" type="text" value="<?php echo $campaign['btn_text']; ?>" placeholder="Submit button text" /></td></tr>
	 <tr><td><?php _e('Button Bg Color:', 'topctabar'); ?></td><td><input type="text" name="btn_bg_color" value="<?php echo $campaign['btn_bg_color']; ?>" id="btn_bg_color" /></td></tr>
	 <tr><td><?php _e('Button Text Color:', 'topctabar'); ?></td><td><input type="text" name="btn_txt_color" value="<?php echo $campaign['btn_txt_color']; ?>" id="btn_txt_color" /></td></tr>
     <tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;<?php wp_nonce_field( 'topctabar_nonce', 'submit_capmpaign' ); ?><input name="save_capmpaign" type="submit" class="btn button-secondary" value= "<?php _e($submit, 'topctabar'); ?>" /></td></tr>
  </table>
</form>
<?php
}// function close

function topctabar_showall_created_capmpaigns_data(){
global $wpdb;
$edit = 'admin.php?page=topctabar_campaign_settings&tab=edit_campaign';
$del = 'admin.php?page=topctabar_campaign_settings&tab=general&action=del';
$tab = isset( $_GET['tab'] ) ? sanitize_key($_GET['tab']) : 'general';
if (($tab === 'general') && (sanitize_key($_GET['action']) === 'del') && current_user_can('manage_options')){
 if(isset($_GET['id']) && !empty($_GET['id'])){
   $wpdb->delete( $wpdb->prefix .'campaigns', array( 'id' => intval($_GET['id']) ) );
 }
}
$settings = "";
$rows = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."campaigns WHERE  settings <> %s", $settings), ARRAY_A );
?>

<table border="0" cellspacing="0" cellpadding="0" class="campaign widefat">
<tr><th><?php _e('Campaign Id', 'topctabar'); ?></th><th><?php _e('Campaign name', 'topctabar'); ?></th><th><?php _e('Created', 'topctabar'); ?></th><th><?php _e('Total Visitors', 'topctabar'); ?></th><th><?php _e('Status', 'topctabar'); ?></th><th><?php _e('Actions', 'topctabar'); ?></th></tr>
<?php 
 foreach($rows as $row):
   $campaign_arr = json_decode($row['settings'],true);
   $id = $row['id'];
   $checked ='';
if($row['status'] == 'a'){
 $checked = ' checked ';
}
 $cdate = date('Y-m-d',strtotime($row['cdate']));
 $row_visit = $wpdb->get_row( $wpdb->prepare("SELECT COUNT(id) as total_visitor FROM ".$wpdb->prefix."visitors WHERE cid = %d", $id), ARRAY_A );
 $total_visitor = $row_visit['total_visitor'];
 echo "<tr><td>".$id."</td><td>".esc_attr($campaign_arr['campaign_name'])."</td><td>".$cdate."</td><td><b>".$total_visitor."</b></td><td><input type='checkbox' id='".$id."' name='campaign-switch' ".$checked." /><label for='".$id."' ></label></td><td><a href='" . $edit ."&id=$id'>edit</a>/<a href='" . $del ."&id=$id'>delete</a></td></tr>";
 endforeach;
?>
</table>
<br/><br/>
<?php
}// function close
function topctabar_showall_capmpaign_registered_visitors_data(){
global $wpdb;
$reg_email = "";
$rows = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."visitors WHERE  reg_email <> %s", $reg_email), ARRAY_A );
?>
<table border="0" cellspacing="0" cellpadding="0" class="widefat">
   <tr><th><?php _e('Id', 'topctabar'); ?></th><th><?php _e('Visitors Email', 'topctabar'); ?></th><th><?php _e('Submitted', 'topctabar'); ?></th></tr>
 <?php 
   foreach($rows as $row):
   $cdate = date('Y-m-d',strtotime($row['cdate']));
   ?>
   <tr><th><?php echo $row['id']; ?></th><th><?php echo $row['reg_email']; ?></th><th><?php echo $cdate; ?></th></tr>
<?php endforeach; ?>
</table>
<br/><br/>
<?php
}// function close