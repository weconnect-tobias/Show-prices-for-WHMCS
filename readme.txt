=== Show prices for WHMCS ===
Contributors: Tobias Sörensson
Tags: page, post, price, show, WHMCS
Requires at least: 5.0
Tested up to: 6.6.2
Requires PHP: 8.1
Stable tag: 2.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display product and domain prices from WHMCS on your WordPress site with the "Show Prices for WHMCS" plugin. Easily integrate pricing information through customizable shortcodes.

== Description ==
The "Show Prices for WHMCS" plugin provides a dynamic way to display product and domain prices directly from your WHMCS installation on your WordPress website. With this powerful tool, you can effortlessly integrate WHMCS pricing data into your posts, pages, or theme templates using simple shortcodes.

Key Features:

    Dynamic Pricing: Automatically fetches product and domain prices from WHMCS.
    Customizable Shortcodes: Use shortcodes to display specific product or domain pricing information.
    Cache Management: Includes cache management features to enhance performance and reduce server load.
    Easy Setup: Simple configuration through the WordPress admin interface.

How to Use:

    Install and activate the "Show Prices for WHMCS" plugin.
    Navigate to the settings page to enter your WHMCS URL.
    Use the provided shortcodes to display prices in your desired locations.

This plugin is perfect for web hosting companies, domain registrars, or anyone who uses WHMCS for billing and wants to showcase their pricing on a WordPress site. Enjoy seamless integration and an enhanced user experience!

== Installation ==
Installation Instructions:

    Download the Plugin:
        Download the latest version of the "Show Prices for WHMCS" plugin from the WordPress Plugin Directory.

    Upload the Plugin:
        Go to your WordPress admin dashboard.
        Navigate to Plugins > Add New.
        Click on the Upload Plugin button.
        Select the downloaded ZIP file and click Install Now.

    Activate the Plugin:
        After the installation is complete, click on the Activate Plugin link.

== Changelog ==
= 2.0 =
Updated https://wordpress.org/plugins/whmcs-price/ to work with php 8.x and with WordPress 6.6.2
Added Show=" " feature so that the plugin gets name description and price like this [whmcs pid="1" show="name,description,price" bc="1y"]
Added cache to the plugin so that it dont overload the WHMCS site if heavy traffic.
Added so that Product ID can handle more then one ID like this [whmcs pid="1,2,3,4" show="name,description,price" bc="1y"] then format them in a table for easy display on a page.
