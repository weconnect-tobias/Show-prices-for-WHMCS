<?php
/*
 * Plugin Name: Show Price for WHMCS
 * Plugin URI: 
 * Description: Dynamic way for extracting product & domain price from WHMCS for use on the pages of your website!', 'whmcs-price');
 * Version: 2.0
 * Author: MohammadReza Kamali, Tobias Sörensson
 * Author URI: https://www.iranwebsv.net, https://weconnect.se
*/
/**
 * Developer : MohammadReza Kamali, Tobias Sörensson
 * Web Site  : IRANWebServer.Net, weconnect.se
 * E-Mail    : kamali@iranwebsv.net, tobias@weconnect.se
 * السلام علیک یا علی ابن موسی الرضا
 */

if (!defined('ABSPATH')) {
    wp_die(__('Access Denied', 'whmcs-price'));
}

// Define constants directly
define('WP_WHMCS_Prices_ROOT', __FILE__);
define('WP_WHMCS_Prices_DIR', plugin_dir_path(WP_WHMCS_Prices_ROOT));
define('WP_WHMCS_Prices_URL', plugin_dir_url(WP_WHMCS_Prices_ROOT));

// Short Codes
require_once WP_WHMCS_Prices_DIR . 'includes/short_code/short_code.php';

// Check WordPress Version
$wp_version = get_bloginfo('version');

if (!is_admin() || is_multisite() || version_compare($wp_version, '3.5', '<')) {
    return;
}

/*------------------------------------------------------------------------------------------------*/
/* CONSTANTS */
/*------------------------------------------------------------------------------------------------*/

define('SF_UUPE_VERSION', '1.0');

/*------------------------------------------------------------------------------------------------*/
/* INIT */
/*------------------------------------------------------------------------------------------------*/

// Setting
require_once WP_WHMCS_Prices_DIR . 'includes/settings.php';

// Initialize the plugin
if (is_admin()) {
    new WHMCSPrice(); // This will trigger the class constructor
}