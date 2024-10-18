<?php
/**
 * Developer : MohammadReza Kamali, Tobias Sörensson
 * Web Site  : IRANWebServer.Net, weconnect.se
 * E-Mail    : kamali@iranwebsv.net, tobias@weconnect.se
 * السلام علیک یا علی ابن موسی الرضا
 */

// Prevent direct access
defined('ABSPATH') || exit;

// WHMCS Short Code
$options = get_option('whmcs_price_option');
$whmcs_url = isset($options['whmcs_url']) ? $options['whmcs_url'] : '';

// Load text domain for translations
function whmcspr_load_textdomain() {
    load_plugin_textdomain('whmcspr', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'whmcspr_load_textdomain');

if (!empty($whmcs_url) && filter_var($whmcs_url, FILTER_VALIDATE_URL)) {
    function whmcs_func($atts)
    {
        $options = get_option('whmcs_price_option');
        $whmcs_url = isset($options['whmcs_url']) ? $options['whmcs_url'] : '';

        // Sanitize input attributes
        $atts = array_map('sanitize_text_field', $atts);

        // Define cache expiry time (1 hour)
        $cache_expiry = 3600;

        // Handle product pricing shortcode with multiple PIDs
        if (isset($atts['pid']) && isset($atts['bc'])) {
            $pids = explode(',', $atts['pid']); // Split the PIDs by commas
            $bc = sanitize_text_field($atts['bc']);
            $show = isset($atts['show']) ? explode(',', sanitize_text_field($atts['show'])) : []; // Get the show attribute
            
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

            // Prepare the output as a table
            $output = "<table class='whmcs-product-table'>";
            $output .= "<tr>";
            $output .= "<th>" . esc_html__('Name', 'whmcspr') . "</th>"; // Translating "Name"
            $output .= "<th>" . esc_html__('Description', 'whmcspr') . "</th>"; // Translating "Description"
            $output .= "<th>" . esc_html__('Price', 'whmcspr') . "</th>"; // Translating "Price"
            $output .= "</tr>";

            // Loop through each product ID
            foreach ($pids as $pid) {
                $pid = intval($pid); // Ensure the PID is an integer
                $row_data = [];

                // Loop through each requested attribute (name, description, price)
                foreach ($show as $attribute) {
                    $attribute = sanitize_text_field($attribute); // Sanitize the attribute
                    
                    // Set the cache key for the current attribute
                    $cache_key = "whmcs_product_{$pid}_{$bc_r}_{$attribute}";
                    $cached_data = get_transient($cache_key);
                    if ($cached_data !== false) {
                        $row_data[$attribute] = $cached_data; // Use cached data if available
                        continue;
                    }

                    // Fetch remote content for the specific attribute
                    $amount = wp_remote_get("$whmcs_url/feeds/productsinfo.php?pid=$pid&get=$attribute&billingcycle=$bc_r");
                    if (is_wp_error($amount) || wp_remote_retrieve_response_code($amount) !== 200) {
                        return "NA"; // Handle failed request
                    }
                    
                    // Get the response body (assuming it's plain text/HTML, not JSON)
                    $response_body = wp_remote_retrieve_body($amount);

                    // Remove JavaScript 'document.write()' calls if any (from older versions of WHMCS)
                    $response_body = preg_replace('/document\.write\(\'/', '', $response_body);
                    $response_body = preg_replace('/\'\);/', '', $response_body);

                    // Cache the response for the attribute
                    set_transient($cache_key, esc_html($response_body), $cache_expiry);
                    
                    // Store the attribute value for displaying in the table
                    $row_data[$attribute] = esc_html($response_body);
                }

                // Append the row data (name, description, price) to the table
                $output .= "<tr>";
                $output .= "<td>" . ($row_data['name'] ?? 'N/A') . "</td>";
                $output .= "<td>" . ($row_data['description'] ?? 'N/A') . "</td>";
                $output .= "<td>" . ($row_data['price'] ?? 'N/A') . "</td>";
                $output .= "</tr>";
            }

            $output .= "</table>";

            return $output;
        } 
        
        // Handle domain pricing shortcode
        elseif (isset($atts['tld']) && isset($atts['type']) && isset($atts['reg'])) {
            $tld = "." . sanitize_text_field($atts['tld']);
            $type = sanitize_text_field($atts['type']);
            $reg = sanitize_text_field($atts['reg']);
            $reg_r = str_replace("y", "", $reg);

            // Check for cached data
            $cache_key = "whmcs_domain_{$tld}_{$type}_{$reg_r}";
            $cached_data = get_transient($cache_key);
            if ($cached_data !== false) {
                return $cached_data;
            }

            // Fetch remote content
            $amount = wp_remote_get("$whmcs_url/feeds/domainprice.php?tld=$tld&type=$type&regperiod=$reg_r&format=1");
            if (is_wp_error($amount) || wp_remote_retrieve_response_code($amount) !== 200) {
                return "NA"; // Handle failed request
            }
            $output = wp_remote_retrieve_body($amount);

            // Remove JavaScript 'document.write()' calls
            $output = preg_replace('/document\.write\(\'/', '', $output);
            $output = preg_replace('/\'\);/', '', $output);
            $formatted_output = "<div class='whmcs-price'>$output</div>";

            // Cache the response
            set_transient($cache_key, $formatted_output, $cache_expiry);

            return $formatted_output;
        } 
        
        // Handle the case where no TLD is given (fetch all domain prices)
        else {
            // Check for cached data
            $cache_key = "whmcs_all_domains";
            $cached_data = get_transient($cache_key);
            if ($cached_data !== false) {
                return $cached_data;
            }

            // Fetch remote content
            $amount = wp_remote_get("$whmcs_url/feeds/domainpricing.php");
            if (is_wp_error($amount) || wp_remote_retrieve_response_code($amount) !== 200) {
                return "NA"; // Handle failed request
            }
            $output = wp_remote_retrieve_body($amount);

            // Remove JavaScript 'document.write()' calls
            $output = preg_replace('/document\.write\(\'/', '', $output);
            $output = preg_replace('/\'\);/', '', $output);
            $formatted_output = "<div class='whmcs-price'>$output</div>";

            // Cache the response
            set_transient($cache_key, $formatted_output, $cache_expiry);

            return $formatted_output;
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