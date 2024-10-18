# WHMCS Price Plugin for WordPress

This plugin is compatible with PHP 8.x and the latest PHP version.

## Description

The WHMCS Price Plugin is designed to integrate WHMCS prices into your WordPress site. Originally created by MohammedReza Kamali.
Dynamic way for extracting product & domain price from WHMCS for use on the pages of your website!

## Plugin features:
* Extract product price.
* Extract domain price.
* Use this plugin to Show price in posts and pages.
* Use this plugin to Show price in theme.

## Product Pricing
This is shortcode for extract product price:
<pre><code>[whmcs pid="1,2,3,4" show="name,description,price" bc="1m"]</code></pre>
1. Change pid value in shortcode with your Product ID, you can select more then one Product ID.
2. Added show="" to toggle the name, description, price from the datafeed of WHMCS
3. Change bc value in shortcode with your BillingCycle Product. BillingCycles are :
<pre><code>Monthly (1 Month) : bc="1m"
Quarterly (3 Month) : bc="3m"
Semiannually (6 Month) : bc="6m"
Annually (1 Year) : bc="1y"
Biennially (2 Year) : bc="2y"
Triennially (3 Year) : bc="3y"</code></pre>

## Domain Pricing

This is shortcode for extract domain price:

<pre><code>[whmcs tld="com" type="register" reg="1y"]</code></pre>
1. Change tld value in shortcode with your Domain TLD (com, org, net, ...).
2. Change type value in shortcode with register, renew, transfer .
3. Change reg value in shortcode with your Register Period of TLD. Registers Period are :
<pre><code>Annually (1 Year) : reg="1y"
Biennially (2 Year) : reg="2y"
Triennially (3 Year) : reg="3y"
...</code></pre>
If left like this <pre><code>[whmcs tld]</code></pre> then it will fetch all TLDs from WHMCS.

## Installation

1. Download the plugin file from the WordPress Plugin Directory.
2. Upload the plugin file to your WordPress site via `Plugins > Add New > Upload Plugin`.
3. Activate the plugin by going to `Plugins > Installed Plugins` and clicking on `Activate`.

## Compatibility

- PHP 8.x
- Latest PHP version

## Usage

After activation, configure the plugin by going to `WHMCS Price` and entering your WHMCS API details.

## Support

For support and questions, please create a ticket on our GitHub Issues page.

## Author

Original code by MohammedReza Kamali.
The project appears to have been abandoned by the original author, but I am committed to continuing its development and support.
This is the Project page https://wordpress.org/plugins/whmcs-price/
