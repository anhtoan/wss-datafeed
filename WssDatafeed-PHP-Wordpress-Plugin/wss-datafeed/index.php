<?php 
    /*
    Plugin Name: Websosanh.vn Datafeed
    Plugin URI: http://anhtoan.info
    Description: Plugin for datafeed XML building
    Author: ToanNguyen
    Version: 1.0
    Author URI: http://anhtoan.info
    Date Modified: 20/08/2015
    */
?>
<?php

/* Including */
include("wss_datafeed_functions.php");

/* Settings Panel */
add_action('admin_menu', 'wss_datafeed_setting_page');
function wss_datafeed_setting_page() {
    add_options_page('Wss Datafeed', 'Wss Datafeed', 'manage_options', 'wss_datafeed_option', 'wss_datafeed_option_page');
}
function wss_datafeed_option_page() {
	include("wss_datafeed_admin.php");
}

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'wss_datafeed_plugin_install'); 
function wss_datafeed_plugin_install(){

}

/* Runs on plugin deactivation*/
register_deactivation_hook(__FILE__, 'wss_datafeed_plugin_remove');
function wss_datafeed_plugin_remove(){

}

/* Datafeed Page */
add_filter('page_template', 'wss_datafeed_display_page');
function wss_datafeed_display_page( $page_template )
{
    $wss_datafeed_page = get_option('wss_datafeed_page', array(
        'title' => 'Wss Datafeed',
        'slug' => 'wss-datafeed',
        'id' => 0
    ));
    
    if(is_page($wss_datafeed_page['slug']))
    {
        $page_template = dirname( __FILE__ ).'/wss_datafeed_page.php';
    }
    return $page_template;
}