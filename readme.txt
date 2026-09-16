=== EPS - Easy Payment System ===
Contributors: eps, themubiin
Tags: woocommerce, payment gateway, eps, bangladesh, bkash
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.0.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Accept Visa, Mastercard, and Mobile Financial Services (bKash, Nagad) payments securely in WooCommerce with EPS.

== Description ==

Accept **Visa, Mastercard, and Mobile Financial Services (bKash, Nagad, Rocket, etc.)** payments directly on your WooCommerce store using the **EPS (Easy Payment System)** gateway — Bangladesh's premier digital payment platform.

### Key Features
* Full support for Visa, Mastercard, and Bangladeshi MFS (bKash, Nagad, Rocket, Upay, etc.).
* Support for both WooCommerce Classic Checkout and WooCommerce Checkout Blocks.
* Smooth pre-redirect loading overlay on checkout.
* Bank-grade HMAC-SHA512 request signing on all outbound requests.
* Sandbox and Live production environment switching.
* Built-in admin transaction dashboard with transaction reconciliation and 7-day / 12-month gateway sync.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/EPS/` directory or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to **EPS Payment** -> **Settings** in the WordPress admin menu.
4. Fill in your Merchant ID, Store ID, User Name, Password, and Hash Key provided by EPS.
5. Go to **WooCommerce** -> **Settings** -> **Payments** and enable **EPS**.

== Frequently Asked Questions ==

= Does EPS support WooCommerce Blocks checkout? =
Yes, EPS supports both WooCommerce Checkout Blocks and Classic Checkout.

= Where do I get my EPS credentials? =
Contact the EPS team at https://eps.com.bd to create a merchant account and obtain API credentials.

== Changelog ==

= 0.0.6 =
* Security enhancements, direct DB abstraction and full WordPress Plugin Check compliance.
* Redesigned settings and dashboard interfaces.
* Added smooth pre-payment redirection overlay for customer checkout.
* Added support for WooCommerce Blocks checkout.
