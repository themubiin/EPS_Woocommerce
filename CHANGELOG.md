# Changelog & Contribution Summary

All notable fixes, improvements, and architectural updates contributed to the **EPS – Easy Payment System for WooCommerce** plugin.

---

## [0.0.6] – 2026-09-16

### 🎨 UI & Design Modernization
* **Redesigned Admin Settings Screen**: Cleaned up clutter, removed redundant placeholder texts, and introduced a structured card-and-grid layout with live/sandbox status badges, password visibility toggles, and unified input fields.
* **Modernized Transactions Dashboard**: Re-architected the admin transaction management table with a clean filter bar, status badges (Success, Failure, Cancel, Initialize), and quick actions for 7-day and 12-month reconciliation.
* **Pre-Redirect Checkout UX**: Designed and implemented a sleek, modern pre-payment loading overlay for WooCommerce checkout with pulsing badge animation and clear status hints while transitioning customers to the secure EPS payment gateway.
* **Gateway Branding & Logos**: Fixed gateway title casing and aligned responsive payment method badges and logos for both classic checkout and modern block checkout layouts.

---

### 🛡️ Security & Reliability Hardening
* **Direct File Access Protection**: Added `if ( ! defined( 'ABSPATH' ) ) exit;` guards across all PHP files positioned accurately after namespace declarations to completely prevent unauthorized direct script execution.
* **SQL Injection & DB Hardening**: Abstracted database queries to ensure all custom queries use `$wpdb->prepare()` with explicit parameter types. Added proper caching and direct query annotations complying with WordPress VIP/PCP standards.
* **Settings JSON Normalization**: Replaced insecure PHP serialized settings handling with safe JSON decode/encode fallbacks, eliminating `unserialize(): Argument #1 ($data) must be of type string, array given` fatal errors on newer PHP versions.
* **Admin AJAX Protection**: Enforced strict `current_user_can('manage_options')` authorization checks and nonces across all AJAX endpoints (`eps_update_product_status`, `eps_sync_gateway`, `eps_transection_endpoint`).
* **External Gateway Return Security**: Added proper exception handling and verified return transaction IDs against order metadata before status transitions.

---

### 🌐 WordPress.org Plugin Check (PCP) Compliance
* **Text Domain Standardization**: Standardized text domain headers and unified all 40+ translation function calls (`__()`, `esc_html__()`, `esc_html_e()`) to lowercase `'eps'`.
* **Translators Comments**: Accompanied all parameterized translation strings with clarifying `/* translators: ... */` comments as required by WordPress i18n standards.
* **Prefixing Global Functions & Hooks**: Prefixed global functions (`eps_insert_transaction_info`, `eps_get_transaction_info`, `eps_ajax_get_transactions`, `eps_handle_cancel_payment`, `eps_delete_token`, `eps_get_plugin`) and filter hooks to avoid namespace collisions, while preserving legacy wrappers for backward compatibility.
* **Repository Metadata & Standards**: Updated `readme.txt` header to `Tested up to: 7.1`, capped tags to the 5-tag limit, and removed hidden development files (`.gitattributes`) to ensure smooth WordPress.org approval.

---

### ⚡ WooCommerce Blocks & PHP Compatibility
* **WooCommerce Checkout Blocks Integration**: Implemented and verified full compatibility with Cart & Checkout Blocks (`AbstractPaymentMethodType` via `class-block.php` and `checkout.js`).
* **PHP 7.4 – 8.2+ Compatibility**: Eliminated deprecated syntax, cleaned up unquoted constant references, removed leftover debug statements, and aligned Composer metadata for production stability.
