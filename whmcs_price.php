<?php
/*
 * Plugin Name: WHMCS Price
 * Plugin URI: 
 * Description: <?php _e('Dynamic way for extracting product & domain price from WHMCS for use on the pages of your website!', 'whmcs-price'); ?>
 * Version: 1.4
 * Author: Tobias Sörensson
 * Author URI: https://weconnect.se
 * Orginal Author: kamalireal
 * Orginal Author Donate URI: https://www.iranwebsv.net
*/
/**
 * Developer : Tobias Sörensson, kamalireal
 * Web Site  : weconnect.se
 * E-Mail    : tobias@weconnect.se
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