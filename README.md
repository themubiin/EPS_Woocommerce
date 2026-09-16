<p align="center">
  <img src="assets/images/EPS_logo.png" alt="EPS Logo" width="160" />
</p>

# EPS – Easy Payment System for WooCommerce

[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue?logo=wordpress)](https://wordpress.org)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-6.0%2B-purple?logo=woocommerce)](https://woocommerce.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4?logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL--2.0-green)](LICENSE)
[![Version](https://img.shields.io/badge/Version-0.0.6-orange)]()

> Accept **Visa, Mastercard, and Mobile Financial Services (bKash, Nagad, etc.)** payments on your WooCommerce store using the [EPS (Easy Payment System)](https://eps.com.bd) payment gateway — Bangladesh's secure digital payment platform.

---

## Features

- Supports **Visa**, **Mastercard**, and **MFS** (bKash, Nagad, Rocket, etc.)
- Fully compatible with **WooCommerce Blocks & Classic Checkout**
- Modern **Pre-Payment Redirection Overlay** with smooth loading animation
- Secure **HMAC-SHA512** request signing
- **Sandbox & Production** environment mode switching
- **Gateway Sync** — pull transaction history directly from EPS (7 days / 12 months)
- Modernized transaction dashboard inside WordPress admin with live status badges
- Lightweight — fast, secure, and zero unnecessary bloat

---

## Requirements

| Requirement | Version |
|-------------|---------|
| WordPress | 5.8 or higher |
| WooCommerce | 6.0 or higher |
| PHP | 7.4 or higher |
| EPS Merchant Account | [Register here](https://eps.com.bd) |

---

## Installation

### From WordPress Dashboard (Recommended)

1. Download the plugin `.zip` from this repository *(Code → Download ZIP)*
2. Log in to your **WordPress Dashboard** → **Plugins** → **Add New**
3. Click **Upload Plugin** → Choose the `.zip` file → **Install Now**
4. Click **Activate Plugin**

### Manual (via FTP)

1. Extract the `.zip` file
2. Upload the folder to `/wp-content/plugins/`
3. Go to **WordPress Dashboard** → **Plugins** → Activate **EPS – Easy Payment System**

---

## Configuration

1. Go to **WordPress Dashboard** → **EPS Payment** → **Settings**
2. Enter your merchant credentials:
   - **User Name**
   - **Password**
   - **Hash Key**
   - **Merchant ID**
   - **Store ID**
3. Select **Mode**: `Sandbox` (testing) or `Production` (live)
4. Click **Save Changes**
5. Go to **WooCommerce** → **Settings** → **Payments** → Enable **EPS**

**Video Tutorial:** [Watch on YouTube](https://youtu.be/SY6LFRjH6ZM)

---

## Security

This plugin implements the following security measures:

- HMAC-SHA512 request signing on all API calls
- SSL/TLS verification enforced on all outbound requests
- Transaction ID bound to order meta and verified on payment return
- Orders only marked **Paid** after gateway confirms `Success` status
- All database queries use `$wpdb->prepare()` (SQL injection prevention)
- Settings stored as JSON (not PHP serialized objects)
- All admin AJAX endpoints require `manage_options` capability + nonce
- Cryptographically secure invoice ID generation (`random_bytes`)
- All user inputs sanitized before processing or storage

---

## Recent Improvements

- **Modern Settings UI**: Clean, compact 2-column layout with quick environment switching pills.
- **Enhanced Dashboard**: Updated transaction table with clean status badges, formatted BDT amounts, and direct order links.
- **Refined Controls**: Unified 38px button heights, vector icons, and seamless gateway sync.
- **Redirection Animation**: Smooth backdrop blur and pulsing EPS loader when customer places order.
- **Security & Stability Fixes**:
  - Prevented premature order completion on failed payments.
  - Eliminated SQL injection vulnerability in token retrieval.
  - Resolved PHP notice for `$mode` variable on transaction view.
  - Enforced SSL verification across all outbound HTTP requests.
  - Upgraded table migrations across both production and sandbox tables.

---

## Plugin Structure

```
EPS/
├── assets/              # CSS, JS, branding images
├── includes/
│   ├── API/             # REST API controllers
│   ├── Admin/           # Admin menu, settings & transaction views
│   ├── Frontend/        # Frontend hooks & assets
│   ├── Gateway/         # Payment gateway logic (EPS.php, Processor.php)
│   ├── Installer.php    # Database migrations & upgrades
│   └── functions.php    # Helper functions & AJAX endpoints
├── class-block.php      # WooCommerce Blocks checkout support
├── checkout.js          # Blocks checkout script
└── woo-payment-eps.php  # Main plugin entry point
```

---

## Development

```bash
# Clone the repository
git clone https://github.com/themubiin/EPS_Woocommerce.git

# Navigate to folder and install dependencies
cd EPS_Woocommerce
composer install
```

---

## Support

- Website: [https://eps.com.bd](https://eps.com.bd)
- For plugin issues, open a [GitHub Issue](../../issues)
- Setup Guide: [YouTube Tutorial](https://youtu.be/SY6LFRjH6ZM)

---

## Contributors

| Contributor | Role |
|-------------|------|
| [EPS Team](https://eps.com.bd) | Author & Maintainer |
| [Muntasir Mubin](https://github.com/themubiin) | Contributor |

---

## License

This plugin is licensed under the [GPL-2.0 License](LICENSE).

---

<p align="center">Made with ❤️ for Bangladesh's digital payment ecosystem</p>


