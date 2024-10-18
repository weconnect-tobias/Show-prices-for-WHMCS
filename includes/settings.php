<?php
/**
 * Developer : MohammadReza Kamali, Tobias Sörensson
 * Web Site  : IRANWebServer.Net, weconnect.se
 * E-Mail    : kamali@iranwebsv.net, tobias@weconnect.se
 * السلام علیک یا علی ابن موسی الرضا
 */

// Prevent direct access
defined('ABSPATH') || exit;

class WHMCSPrice
{
    private array $options = [];

    public function __construct()
    {
        add_action('admin_menu', [$this, 'whmcspr_plugin_page']);
        add_action('admin_init', [$this, 'whmcspr_init']);
        add_action('plugins_loaded', [$this, 'load_textdomain']); // Load text domain in the class
        add_action('admin_bar_menu', [$this, 'add_admin_bar_clear_cache'], 100); // Add clear cache button to admin bar
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('whmcs-price', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

public function whmcspr_plugin_page()
{
    add_menu_page(
        __('WHMCS Price Options', 'whmcs-price'), // Page title
        __('WHMCS Price Settings', 'whmcs-price'), // Menu title
        'manage_options', // Capability
        'whmcs_price', // Menu slug
        [$this, 'whmcspr_admin_page'], // Function to display the page content
        'dashicons-admin-generic', // Icon
        100 // Position
    );
}

    public function whmcspr_admin_page()
    {
        $this->options = get_option('whmcs_price_option', []);
        ?>
        <style type="text/css">
            pre {
                padding: 25px;
                line-height: 1;
                word-break: break-all;
                word-wrap: break-word;
                color: #333;
                background-color: #f5f5f5;
                border: 1px solid #ccc;
                border-radius: 4px;
                width: 80%;
            }

            code {
                padding-left: 0 !important;
                line-height: 2;
            }
        </style>
        <div class="wrap">
            <h1><?php _e('WHMCS Price Options', 'whmcs-price'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('price_option_group');
                do_settings_sections('whmcs_price');
                ?>
            </form>

            <!-- Clear Cache Button -->
            <form method="post">
                <input type="hidden" name="whmcs_clear_cache" value="1" />
                <input type="submit" class="button button-secondary" value="<?php _e('Clear Cache', 'whmcs-price'); ?>" />
            </form>
        </div>
        <?php

        // Check if cache clear request was made
        if (isset($_POST['whmcs_clear_cache'])) {
            $this->clear_whmcs_cache();
            echo "<div class='notice notice-success is-dismissible'><p>" . __('Cache cleared successfully!', 'whmcs-price') . "</p></div>";
        }
    }

    public function whmcspr_init()
    {
        register_setting(
            'price_option_group',
            'whmcs_price_option',
            [$this, 'sanitize']
        );

        add_settings_section(
            'setting_section_id',
            '',
            [$this, 'print_section_info'],
            'whmcs_price'
        );

        add_settings_field(
            'whmcs_url',
            __('WHMCS URL', 'whmcs-price'),
            [$this, 'whmcs_url_callback'],
            'whmcs_price',
            'setting_section_id'
        );
        add_settings_field(
            'products',
            __('Product Pricing', 'whmcs-price'),
            [$this, 'p_price_callback'],
            'whmcs_price',
            'setting_section_id'
        );
        add_settings_field(
            'domains',
            __('Domain Pricing', 'whmcs-price'),
            [$this, 'd_price_callback'],
            'whmcs_price',
            'setting_section_id'
        );
    }

    public function sanitize($input): array
    {
        $new_input = [];
        if (isset($input['whmcs_url'])) {
            $new_input['whmcs_url'] = sanitize_text_field($input['whmcs_url']);
        }
        return $new_input;
    }

    public function print_section_info()
    {
        _e('Dynamic way for extracting price from WHMCS for use on the pages of your website!<br /><br />Please input your WHMCS URL :', 'whmcs-price');
    }

    public function whmcs_url_callback()
    {
        $options = $this->options;
        $whmcs_url = $options['whmcs_url'] ?? '';

        if (isset($whmcs_url) && !empty($whmcs_url) && !filter_var($whmcs_url, FILTER_VALIDATE_URL)) {
            printf('<p style="color:red">%s</p>', __('Hey ! Your domain is not Valid !', 'whmcs-price'));
        }

        printf(
            '<input type="text" id="whmcs_url" style="width:310px; direction:ltr;" name="whmcs_price_option[whmcs_url]" value="%s" placeholder="%s" /><br /><p style="color:green">%s</p><br />',
            esc_attr($whmcs_url),
            __('https://whmcsdomain.tld', 'whmcs-price'),
            __('Valid URL Format: https://whmcs.com (Dont use "/" End of WHMCS URL)', 'whmcs-price')
        );
        submit_button();
        echo "<p>" . __('Note: After changing price in WHMCS, if you are using a cache plugin in your WordPress, to update price you must remove cache for posts and pages.', 'whmcs-price') . "</p>";
        printf('<hr>');
    }

    public function p_price_callback()
    {
        ?>
        <strong><?php _e('How to use short code in :', 'whmcs-price'); ?></strong><br /><br />
        <?php _e('Post / Pages :', 'whmcs-price'); ?> <input type="text" style="width:343px; direction:ltr; cursor: pointer;" value="[whmcs pid=&#34;1&#34; bc=&#34;1m&#34;]" onclick="this.select()" readonly /><br /><br />
        <?php _e('Theme :', 'whmcs-price'); ?>  <input type="text" style="width:500px; direction:ltr; cursor: pointer;" value="&#60;&#63;php echo do_shortcode(\'[whmcs pid=&#34;1&#34; bc=&#34;1m&#34;]\')&#59; &#63;&#62;" onclick="this.select()" readonly /><br /><br />
        <pre><strong><?php _e('English Document:', 'whmcs-price'); ?></strong><br />
        1. <?php _e('Change pid value in shortcode with your Product ID.', 'whmcs-price'); ?><br />
        2. <?php _e('Change bc value in shortcode with your BillingCycle Product. BillingCycles are :', 'whmcs-price'); ?><br /><br />
        <code><?php _e('Monthly (1 Month) : bc="1m"<br />Quarterly (3 Month) : bc="3m"<br />Semiannually (6 Month) : bc="6m"<br />Annually (1 Year) : bc="1y"<br />Biennially (2 Year) : bc="2y"<br />Triennially (3 Year) : bc="3y"', 'whmcs-price'); ?></code><br /><br />
        <strong><hr>
        <?php
    }

    public function d_price_callback()
    {
        printf(
            '<strong>How to use short code in :</strong><br /><br />Post / Pages : <input type="text" style="width:343px; direction:ltr; cursor: pointer;" value="[whmcs tld=&#34;com&#34; type=&#34;register&#34; reg=&#34;1y&#34;]" onclick="this.select()" readonly /><br /><br />Theme :  <input type="text" style="width:500px; direction:ltr; cursor: pointer;" value="&#60;&#63;php echo do_shortcode(\'[whmcs tld=&#34;com&#34; type=&#34;register&#34; reg=&#34;1y&#34;]\')&#59; &#63;&#62;" onclick="this.select()" readonly /><br /><br />
            <pre><strong>English Document:</strong><br />
            1. Change tld value in shortcode with your Domain TLD (<code>com, org, net, ...</code>).<br />
            2. Change type value in shortcode with <code>register, renew, transfer</code> .<br />
            3. Change reg value in shortcode with your Register Period of TLD. Registers Period are :<br /><br /><code>Annually (1 Year) : reg="1y"<br />Biennially (2 Year) : reg="2y"<br />Triennially (3 Year) : reg="3y"<br />...</code><br />
            4. If left like this <code>[whmcs tld]</code> it will call without any Domain TLD and it will take all the TLD that is in WHMCS<br /><br /><strong>
            <hr>'
        );
    }

    public function clear_whmcs_cache()
    {
        global $wpdb;

        // Find and delete all WHMCS-related transients
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_whmcs_%'");
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_whmcs_%'");
    }

    public function add_admin_bar_clear_cache($admin_bar)
    {
        if (current_user_can('manage_options')) {
            $admin_bar->add_menu(array(
                'id'    => 'whmcs-clear-cache',
                'title' => __('Clear WHMCS Cache', 'whmcs-price'),
                'href'  => add_query_arg('whmcs_clear_cache', '1'), // Add query argument to the URL
                'meta'  => array(
                    'title' => __('Clear WHMCS Cache', 'whmcs-price'),
                ),
            ));

            // Check if the clear cache query argument is present and clear the cache
            if (isset($_GET['whmcs_clear_cache']) && $_GET['whmcs_clear_cache'] == '1') {
                $this->clear_whmcs_cache();
                // Display admin notice
                add_action('admin_notices', function () {
                    echo "<div class='notice notice-success is-dismissible'><p>" . __('Cache cleared successfully!', 'whmcs-price') . "</p></div>";
                });
            }
        }
    }
}

?>