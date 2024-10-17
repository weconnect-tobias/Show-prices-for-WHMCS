<?php
/**
 * Developer : Tobias Sörensson
 * Web Site  : weconnect.se
 * E-Mail    : tobias@weconnect.se
 */

// Prevent direct access
defined('ABSPATH') || exit;

// WHMCS Short Code
$options = get_option('whmcs_price_option');
$whmcs_url = isset($options['whmcs_url']) ? $options['whmcs_url'] : '';

if (!empty($whmcs_url) && filter_var($whmcs_url, FILTER_VALIDATE_URL)) {
    function whmcs_func($atts)
    {
        $options = get_option('whmcs_price_option');
        $whmcs_url = isset($options['whmcs_url']) ? $options['whmcs_url'] : '';

        // Sanitize input attributes
        $atts = array_map('sanitize_text_field', $atts);

        // Validate and sanitize pid and bc
        if (isset($atts['pid']) && isset($atts['bc'])) {
            $pid = intval($atts['pid']);
            $bc = sanitize_text_field($atts['bc']);
            $bc_r = '';
            switch ($bc) {
                case "1m":
                    $bc_r = "monthly";
                    break;
                case "3m":
                    $bc_r = "quarterly";
                    break;
                case "6m":
                    $bc_r = "semiannually";
                    break;
                case "1y":
                    $bc_r = "annually";
                    break;
                case "2y":
                    $bc_r = "biennially";
                    break;
                case "3y":
                    $bc_r = "triennially";
                    break;
                default:
                    return "NA"; // Handle unrecognized billing cycle
            }
            // Fetch remote content
            $amount = wp_remote_get("$whmcs_url/feeds/productsinfo.php?pid=$pid&get=price&billingcycle=$bc_r");
            if (is_wp_error($amount) || wp_remote_retrieve_response_code($amount) !== 200) {
                return "NA"; // Handle failed request
            }
            $output = wp_remote_retrieve_body($amount);
            // Remove JavaScript 'document.write()' calls
            $output = preg_replace('/document\.write\(\'/', '', $output);
            $output = preg_replace('/\'\);/', '', $output);
            return "<div class='whmcs-product'>$output</div>";
        } elseif (isset($atts['tld']) && isset($atts['type']) && isset($atts['reg'])) {
            $tld = "." . sanitize_text_field($atts['tld']);
            $type = sanitize_text_field($atts['type']);
            $reg = sanitize_text_field($atts['reg']);
            $reg_r = str_replace("y", "", $reg);
            // Fetch remote content
            $amount = wp_remote_get("$whmcs_url/feeds/domainprice.php?tld=$tld&type=$type&regperiod=$reg_r&format=1");
            if (is_wp_error($amount) || wp_remote_retrieve_response_code($amount) !== 200) {
                return "NA"; // Handle failed request
            }
            $output = wp_remote_retrieve_body($amount);
            // Remove JavaScript 'document.write()' calls
            $output = preg_replace('/document\.write\(\'/', '', $output);
            $output = preg_replace('/\'\);/', '', $output);
            return "<div class='whmcs-price'>$output</div>";
        } else {
            // If no TLD is given, show feeds/domainpricing.php
            // Fetch remote content
            $amount = wp_remote_get("$whmcs_url/feeds/domainpricing.php");
            if (is_wp_error($amount) || wp_remote_retrieve_response_code($amount) !== 200) {
                return "NA"; // Handle failed request
            }
            $output = wp_remote_retrieve_body($amount);
            // Remove JavaScript 'document.write()' calls
            $output = preg_replace('/document\.write\(\'/', '', $output);
            $output = preg_replace('/\'\);/', '', $output);
            return "<div class='whmcs-price'>$output</div>";
        }
    }

    // Register ShortCodes
    function whmcspr_shortcodes()
    {
        add_shortcode('whmcs', 'whmcs_func');
    }

    add_action('init', 'whmcspr_shortcodes');
}
?>