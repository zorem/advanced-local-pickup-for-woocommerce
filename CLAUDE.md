# CLAUDE.md — Zorem Local Pickup (Free)

## Plugin Overview

**Plugin Name:** Zorem Local Pickup
**Folder:** `advanced-local-pickup-for-woocommerce`
**Main File:** `woo-advanced-local-pickup.php`
**Version:** 1.7.9
**Type:** Free / WordPress.org
**Text Domain:** `zorem-local-pickup`
**WC Requires:** 4.0+
**WC Tested Up To:** 10.5.1
**License:** GPLv3

Extends WooCommerce's built-in Local Pickup shipping method. Adds multiple pickup locations stored in a custom DB table, custom order statuses (Ready for Pickup, Picked Up), and customer-facing email notifications for those statuses.

**Pro counterpart:** `advanced-local-pickup-pro` — when the Pro plugin is active, this free plugin fully deactivates itself (no code runs).

---

## Folder Structure

```
advanced-local-pickup-for-woocommerce/
├── woo-advanced-local-pickup.php          # Main plugin file, bootstraps everything
├── include/
│   ├── wc-local-pickup-admin.php          # Admin settings UI (class WC_Local_Pickup_admin)
│   ├── wc-local-pickup-installation.php   # DB creation & migration (class WC_Local_Pickup_install)
│   ├── wclp-wc-admin-notices.php          # Admin notice helpers
│   ├── customizer/
│   │   └── customizer-admin.php           # Live preview customizer (class WC_Local_Pickup_Customizer)
│   ├── emails/
│   │   ├── pickup-order.php               # WC_Email_Customer_Pickup_Order class
│   │   └── ready-pickup-order.php         # WC_Email_Customer_Ready_Pickup_Order class
│   └── views/
│       ├── wclp_setting_tab.php           # General settings tab HTML
│       ├── wclp_locations_tab.php         # Locations list/management tab HTML
│       ├── wclp-edit-location-form.php    # Add/edit location form HTML
│       ├── wclp_addon_tab.php             # Addons/upsell tab HTML
│       ├── wclp_pickup_location_instruction_preview.php
│       └── admin_message_panel.php
├── templates/
│   ├── emails/
│   │   ├── pickup-order.php               # Customer: Order Picked Up email template
│   │   ├── ready-pickup-order.php         # Customer: Ready for Pickup email template
│   │   ├── pickup-instruction.php         # Pickup instructions email partial
│   │   └── plain/                         # Plain-text versions of all email templates
│   └── myaccount/
│       └── pickup-instruction.php         # My Account pickup instructions display
├── assets/
│   ├── css/admin.css                      # Admin styles
│   ├── js/admin.js                        # Admin JS (jQuery, Select2, TipTip)
│   └── images/                            # Plugin logos/icons
├── lang/                                  # Translations (.pot, .po, .mo for 6 locales)
├── zorem-tracking/                        # Shared usage-tracking sub-module
│   └── zorem-tracking.php                 # class WC_Trackers — opt-in analytics
└── wpml-config.xml                        # WPML compatibility config
```

---

## Key Classes & Global Functions

| Symbol | File | Purpose |
|---|---|---|
| `Woocommerce_Local_Pickup` | `woo-advanced-local-pickup.php` | Main plugin class |
| `wc_local_pickup()` | `woo-advanced-local-pickup.php` | Global singleton accessor |
| `WC_Local_Pickup_admin` | `include/wc-local-pickup-admin.php` | Admin settings pages |
| `WC_Local_Pickup_install` | `include/wc-local-pickup-installation.php` | DB table creation & migration |
| `WC_Local_Pickup_Customizer` | `include/customizer/customizer-admin.php` | Live preview customizer |
| `WC_Email_Customer_Ready_Pickup_Order` | `include/emails/ready-pickup-order.php` | "Ready for Pickup" email |
| `WC_Email_Customer_Pickup_Order` | `include/emails/pickup-order.php` | "Picked Up" email |
| `WC_Trackers` | `zorem-tracking/zorem-tracking.php` | Usage tracking (shared module) |

---

## Database

**Table:** `{$wpdb->prefix}alp_pickup_location`

Created via `dbDelta()` on activation. Columns:

| Column | Type | Description |
|---|---|---|
| `id` | int AUTO_INCREMENT | Primary key |
| `store_name` | text | Location display name |
| `store_address` | text | Street address line 1 |
| `store_address_2` | text | Street address line 2 |
| `store_city` | text | City |
| `store_country` | text | Country code |
| `store_postcode` | text | Postal code |
| `store_phone` | text | Phone number |
| `store_time_format` | text | Time format preference |
| `store_days` | text | Business days (serialized) |
| `store_instruction` | text | Pickup instructions |

---

## Custom Order Statuses

| Status Slug | WC Status Key | Description |
|---|---|---|
| `ready-pickup` | `wc-ready-pickup` | Order is ready for customer collection |
| `pickup` | `wc-pickup` | Order has been collected (Picked Up) |

These statuses are registered on plugin init. When deactivating, the plugin shows a modal to reassign any orders in these statuses to a standard WC status (to avoid orphaned orders).

---

## WordPress Hooks

### Actions Added

| Hook | Callback | Priority | Description |
|---|---|---|---|
| `plugins_loaded` | `on_plugins_loaded` | default | Loads admin notice helper |
| `plugins_loaded` | `load_textdomain` | default | Loads translation files |
| `admin_init` | `wclp_update_install_callback` | default | Runs DB migration checks |
| `admin_enqueue_scripts` | `alp_script_enqueue` | default | Enqueues admin CSS/JS (only on `?page=local_pickup`) |
| `admin_footer` | `uninstall_notice` | default | Renders deactivation/reassign modal on plugins.php |
| `upgrader_process_complete` | `alp_plugin_update_hook` | 10 | Clears notice dismiss option on plugin update |
| `woocommerce_order_status_ready-pickup` | `email_trigger_ready_pickup` | 10 | Fires "Ready for Pickup" email |
| `woocommerce_order_status_pickup` | `email_trigger_pickup` | 10 | Fires "Picked Up" email |
| `wp_ajax_reassign_order_status` | `reassign_order_status` | — | AJAX: bulk reassign custom order statuses |
| `before_woocommerce_init` | _(closure)_ | — | Declares HPOS compatibility |

### Filters Added

| Hook | Callback | Description |
|---|---|---|
| `woocommerce_email_classes` | `custom_init_emails` | Registers custom WC email classes |
| `plugin_action_links_{basename}` | `my_plugin_action_links` | Adds Settings / Docs / Review / Go Pro links |

---

## WP Options Used

| Option Key | Default | Description |
|---|---|---|
| `wclp_status_ready_pickup` | `0` | Enable "Ready for Pickup" email notification |
| `wclp_status_picked_up` | `0` | Enable "Picked Up" email notification |
| `alp_notice_ignore` | — | Whether the admin notice has been dismissed |

---

## Admin Page

**Menu slug:** `local_pickup`
**URL:** `wp-admin/admin.php?page=local_pickup`
**Registered in:** `WC_Local_Pickup_admin`

Tabs: Settings, Locations, Add-ons

---

## AJAX Actions

| Action | Handler | Auth | Description |
|---|---|---|---|
| `reassign_order_status` | `Woocommerce_Local_Pickup::reassign_order_status()` | Nonce `alp-ajax-nonce` | Reassigns orders from custom statuses before deactivation |

---

## Localization

**Text domain:** `zorem-local-pickup`
**Lang directory:** `lang/`
Loaded via `load_plugin_textdomain()` on `plugins_loaded`.
Bundled translations: de_DE, es_ES, fr_FR, he_IL, it_IT, nb_NO.

---

## Coding Standards

- WordPress Coding Standards (WPCS)
- All user input sanitized with `sanitize_text_field()`, `absint()`, etc.
- All output escaped with `esc_html()`, `esc_url()`, `esc_attr()`
- Nonces verified for all AJAX and form submissions
- Singleton pattern via `get_instance()` static method on all sub-classes
- `ABSPATH` guard at top of every file
- `SCRIPT_DEBUG` flag respected for minified vs. non-minified asset loading

---

## Build Instructions

No build process required. This is a plain PHP/JS/CSS plugin.

To work on assets:
- Edit `assets/js/admin.js` directly (no bundler)
- Edit `assets/css/admin.css` directly
- For translations: edit `.po` files in `lang/` and compile to `.mo` with Poedit or WP-CLI (`wp i18n make-mo`)

---

## Free / Pro Relationship

- When `advanced-local-pickup-pro` is active, this plugin's `__construct()` calls `is_alp_pro_active()` and returns early — **nothing runs**.
- The free plugin itself blocks its own activation via `on_activation()` if Pro is already active.
- The Pro plugin deactivates the free plugin on its own activation hook.
- **Never have both active simultaneously.**

---

## Compatibility Notes

- **WooCommerce HPOS:** Declared compatible via `FeaturesUtil::declare_compatibility('custom_order_tables', ...)`.
- **WooCommerce Blocks checkout:** Not supported in the free version (Pro only).
- **WPML:** `wpml-config.xml` is present for string translation compatibility.
- **PHP:** Requires PHP 7.4+ (follows WooCommerce minimum).
- **Multisite:** Not explicitly tested for multisite use.

---

## AI Usage Notes

### Safe to modify
- View files in `include/views/` (admin tab HTML)
- Email templates in `templates/emails/` — follow WooCommerce email template conventions; always keep `<?php defined('ABSPATH') || exit; ?>` guard
- CSS in `assets/css/`
- JS in `assets/js/` — keep jQuery-based, no ES modules

### Be careful with
- `woo-advanced-local-pickup.php` — bootstrap logic; changing hook priorities or class instantiation order can break initialization
- `include/wc-local-pickup-installation.php` — DB migration logic; always use `dbDelta()`, never raw `CREATE TABLE` without it
- Custom order status registration — must stay in sync with the Pro plugin's status registrations
- AJAX nonce key `alp-ajax-nonce` — must match in both PHP and JS

### Do NOT modify
- `zorem-tracking/` — shared sub-module used across all Zorem plugins; changes here must be replicated across all plugins
- `lang/` — generated translation files; edit `.po` source and recompile
- The `is_alp_pro_active()` check in the constructor — this is the free/pro guard

### Do NOT add
- License/subscription gating — this is a free plugin
- Gutenberg checkout block support — Pro feature only
- Per-item session or fulfillment dashboard — Pro features only
