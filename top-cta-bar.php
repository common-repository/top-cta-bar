<?php
/*
Plugin Name: Top CTA Bar
Plugin URI: https://vrapparel.net/
Description: Handling top most header bar for a campaign to collect registered email.
Version: 1.0.9
Author: Md Mamunur Rahman
Author URI: http://fixrunner.com/
Min WP Version: 3.5
Max WP Version: 5.0
*/
/*
   LICENCE
   Copyright 2015-2016 Md Mamunur Rahman
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
register_activation_hook(__FILE__,'topctabar_set_plugin_campaign_options');
//********************************************
//	Constant Paths
//***********************************************************
if (!defined('WP_TOP_CALL_TO_ACTION_FIXED_BAR_VERSION')) {
	define('WP_TOP_CALL_TO_ACTION_FIXED_BAR_VERSION', '1.0.9');
}
if( !defined('TOPCTABAR_DIR') ){
	define( 'TOPCTABAR_DIR', plugins_url() . '/top-cta-bar/' );
}

if( !defined("TOPCTABAR_JS_DIR") ){
	define("TOPCTABAR_JS_DIR", TOPCTABAR_DIR . "js/");
}

if( !defined("TOPCTABAR_CSS_DIR") ){
	define("TOPCTABAR_CSS_DIR", TOPCTABAR_DIR . "css/");
}
//********************************************
//	Top CTA Bar Front Styles
//***********************************************************
function topctabar_frontend_scripts(){
	wp_enqueue_script( 'jquery' );	
	wp_enqueue_style('topctabar_css', TOPCTABAR_CSS_DIR . 'front_style.css' );
	wp_enqueue_script('topctabar', TOPCTABAR_JS_DIR . 'front.js', array('jquery'), WP_TOP_CALL_TO_ACTION_FIXED_BAR_VERSION, true); 
    wp_localize_script('topctabar', 'frontend_ajax_object',
    array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce('topctabar-frontend-nonce')
    )
  );
}
add_action('wp_enqueue_scripts', 'topctabar_frontend_scripts');

//********************************************
//	Top CTA Bar Admin Styles
//***********************************************************
function topctabar_backend_styles(){	
	wp_enqueue_style( 'topctabar_admin', TOPCTABAR_CSS_DIR . "admin.css");
	wp_enqueue_style( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'topctabar_backend_styles' );

//********************************************
//	Top CTA Bar Admin Scripts
//***********************************************************
function topctabar_backend_scripts(){
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_script( 'topctabar_admin', TOPCTABAR_JS_DIR . 'admin.js', array('jquery','wp-color-picker'), WP_TOP_CALL_TO_ACTION_FIXED_BAR_VERSION, true );
	wp_localize_script( 'topctabar_admin', 'ajax_object', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce('topctabar-admin-nonce')
	));	
		
}
add_action( 'admin_enqueue_scripts', 'topctabar_backend_scripts' );
//***********************************************************
// Load text domain
//***********************************************************
if ( ! function_exists( 'topctabar_load_text_domain' ) ) {
function topctabar_load_text_domain() {
  load_plugin_textdomain( 'topctabar', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
}
add_action( 'plugins_loaded', 'topctabar_load_text_domain' );
}//if function
if ( is_admin() ){
  require_once( dirname( __FILE__ ) .'/admin/settings.php' );
}
require_once( dirname( __FILE__ ) .'/include/front-view.php' );

function topctabar_set_plugin_campaign_options(){
    global $wpdb;

    $campaigns = $wpdb->prefix . 'campaigns'; // create Campaigns table

    $visitors = $wpdb->prefix . 'visitors'; // create visitors table

    $charset_collate = '';
        if ( ! empty($wpdb->charset) ) {
            $charset_collate = " DEFAULT CHARACTER SET $wpdb->charset ";
        }	
        if ( ! empty($wpdb->collate) ) {
            $charset_collate .= " COLLATE $wpdb->collate ";
        }
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $campaigns;

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $campaigns (
  				`id` int(4) NOT NULL AUTO_INCREMENT,
  				`settings` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
				`status` CHAR(5) NOT NULL DEFAULT 'd',
  				`cdate` datetime NOT NULL,
  				PRIMARY KEY (`id`)
		) $charset_collate;";

        dbDelta($sql);
	}//if

	$table_name = $visitors;

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	    $sql = "CREATE TABLE $visitors (
  				`id` int(11) NOT NULL AUTO_INCREMENT,
  				`cid` int(11) NOT NULL,
  				`reg_email` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
				`cdate` datetime NOT NULL,
  				PRIMARY KEY (`id`)
					)$charset_collate;";
	  dbDelta($sql);
	}//if
    
}// function close

?>