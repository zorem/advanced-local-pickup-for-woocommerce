=== Zorem Local Pickup for WooCommerce ===
Contributors: zorem,gaurav1092,eranzorem,virendesai
Tags: local pickup, click and collect, in-store pickup, woocommerce, shipping
Requires at least: 5.5
Tested up to: 7.1
Stable tag: 1.9.2
Requires PHP: 7.0
WC requires at least: 5.0
WC tested up to: 10.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a complete local pickup workflow to WooCommerce. Custom order statuses, automated pickup notifications, and pickup instructions — free.

== Description ==

**Zorem Local Pickup** extends WooCommerce's default Local Pickup shipping method into a full click-and-collect fulfillment workflow — with dedicated order statuses, automated customer notifications, and customizable pickup instructions.

Out of the box, WooCommerce Local Pickup has no way to tell customers their order is ready for collection, no pickup-specific order status, and no mechanism to share pickup instructions. This plugin fixes all of that, giving you and your customers a clear, professional pickup experience from order placement to collection.

**HPOS Compatible** · **3,000+ Active Stores** · **Free & Open Source**

= How It Works =

1. A customer places an order and selects Local Pickup at checkout.
2. You prepare the order and mark it as **Ready for Pickup** — the customer instantly receives an email with pickup instructions, location details, and store hours.
3. The customer collects their order. You mark it as **Picked Up** — optionally sending a final confirmation email.

No complex setup. Works with your existing WooCommerce Local Pickup configuration.

= What's Included in the Free Version =

* **"Ready for Pickup" order status** — A dedicated status that triggers an automatic email notification to the customer, letting them know their order is ready for collection.
* **"Picked Up" order status** — A second status to confirm collection, with an optional confirmation email.
* **Automated email notifications** — Emails fire automatically when you change the order status — no manual messaging needed.
* **Customizable email content** — Modify the subject line, heading, and body content of both pickup status emails from the WooCommerce email customizer.
* **Pickup location setup** — Add your store name, address, opening hours, and special pickup instructions. These appear automatically in the Ready for Pickup email.
* **Pickup instructions in the Processing email** — Optionally show pickup details in the initial order confirmation email so customers know what to expect right away.
* **Pickup instructions on the Thank You page** — Display pickup instructions on the order received page immediately after checkout.
* **Bulk order actions** — Change multiple orders to Ready for Pickup or Picked Up in bulk from the WooCommerce orders list.
* **HPOS Compatible** — Fully compatible with WooCommerce High-Performance Order Storage.

= Free vs PRO — What's the Difference? =

The free version covers single-location pickup workflows. ALP PRO is built for stores with multiple locations, appointment scheduling, and advanced fulfillment needs.

**ALP Free includes:**

* Ready for Pickup and Picked Up custom order statuses
* Automated email notifications for both statuses
* Single pickup location with name, address, hours, and instructions
* Customizable email content (subject, heading, body)
* Pickup instructions on Thank You page and Processing email
* Bulk order status actions
* HPOS compatible

**ALP PRO adds:**

* **Multiple pickup locations** — Add and manage unlimited pickup points, each with its own address, hours, instructions, and contact details.
* **Pickup appointment scheduling** — Let customers choose a pickup date and time slot during checkout, with configurable availability per location.
* **Split work hours by day** — Define different opening hours for each day of the week per location.
* **Restrict pickup locations by product or category** — Assign specific products or categories to specific pickup locations only.
* **Mixed pickup + shipping orders** — Support orders where some items are shipped and others are picked up in the same transaction.
* **Per-item pickup from different locations** — Allow different items in one order to be picked up from different locations.
* **Location-specific email notifications** — Send Ready for Pickup emails from the specific location handling the order, with that location's details.
* **Pickup discounts** — Offer a discount or fee per pickup location to incentivize in-store collection.
* **Force local pickup** — Make local pickup the only available shipping method for specific products or categories.
* **Display pickup availability messages** — Show customers when pickup is available before they reach checkout.
* **Google Maps integration** — Sort pickup locations by distance from the customer's address.
* **Custom email templates** — Advanced pickup email styling and layout customization.
* **Priority support** — Faster, dedicated assistance from the Zorem team.

👉 [Get ALP PRO](https://www.zorem.com/product/zorem-local-pickup-pro/)

= Compatible With =

Zorem Local Pickup is tested and compatible with:

* **Email customizers** — Kadence WooMail, YayMail, WP HTML Mail
* **SMS notifications** — SMS for WooCommerce
* **Multilingual** — WPML (special instruction text translatable)
* **Page builders** — Elementor, Divi
* **Subscriptions** — WooCommerce Subscriptions (pickup instructions in renewal emails)
* **Multi-vendor** — Dokan, WCFM Marketplace

[Full compatibility list →](https://docs.zorem.com/docs/zorem-local-pickup/compatibility/)

= Translations =

Zorem Local Pickup is fully translatable. Current translations include English, German, Spanish, French, Hebrew, and Italian.

[Translate into your language →](https://translate.wordpress.org/projects/wp-plugins/advanced-local-pickup-for-woocommerce)

= Documentation & Support =

* 📖 [Full documentation and setup guides](https://docs.zorem.com/docs/zorem-local-pickup/)
* 💬 [WordPress.org support forum](https://wordpress.org/support/plugin/advanced-local-pickup-for-woocommerce/)

= More Plugins by Zorem =

* [Advanced Shipment Tracking for WooCommerce](https://www.zorem.com/product/woocommerce-advanced-shipment-tracking/) — The #1 WooCommerce shipment tracking plugin. Trusted by 80,000+ stores.
* [SMS for WooCommerce](https://www.zorem.com/product/sms-for-woocommerce/) — Automated SMS order and shipping notifications via Twilio, WhatsApp, and 18 other providers.
* [Zorem Returns](https://www.zorem.com/product/zorem-returns/) — Self-service returns, exchanges, and store credit management for WooCommerce.
* [Customer Email Verification PRO](https://www.zorem.com/product/customer-email-verification/) — Block fake accounts and spam orders with OTP-based email verification.
* [Country Based Restrictions PRO](https://www.zorem.com/product/country-based-restriction-pro/) — Restrict products and payment gateways by customer country using WooCommerce geolocation.

Explore the full catalog at [zorem.com →](https://www.zorem.com/)

== Installation ==

1. Go to **Plugins > Add New** in your WordPress admin and search for "Zorem Local Pickup".
2. Click **Install Now**, then **Activate**.
3. Go to **WooCommerce > Local Pickup** to configure your pickup location and notification settings.
4. Set your store name, address, opening hours, and pickup instructions.
5. When an order is ready for collection, open it from **WooCommerce > Orders** and change the status to **Ready for Pickup** — the customer will be notified automatically.

Alternatively, upload the `advanced-local-pickup-for-woocommerce` folder to `/wp-content/plugins/` and activate through the Plugins menu.

== Frequently Asked Questions ==

= How is this different from the built-in WooCommerce Local Pickup? =

WooCommerce's built-in Local Pickup is a shipping method only — it has no order statuses for pickup, no way to notify customers when their order is ready for collection, and no mechanism to share pickup instructions. Zorem Local Pickup adds a complete fulfillment workflow on top: dedicated Ready for Pickup and Picked Up statuses, automated customer emails, and customizable pickup instructions — all for free.

= How do customers know their order is ready to collect? =

When you mark an order as "Ready for Pickup", an automated email is sent to the customer containing your pickup location details, opening hours, and any special instructions you've configured. No manual messaging needed.

= Can I customize the pickup notification emails? =

Yes. Both the Ready for Pickup and Picked Up emails can be customized from the WooCommerce email settings — you can change the subject line, heading, and body content. Your pickup location details and instructions are inserted automatically.

= Can I manage multiple pickup locations? =

Multiple pickup locations — each with its own address, hours, instructions, and location-specific notifications — are available in [ALP PRO](https://www.zorem.com/product/zorem-local-pickup-pro/).

= Can customers choose a pickup date and time? =

Pickup appointment scheduling, where customers select a date and time slot at checkout, is available in [ALP PRO](https://www.zorem.com/product/zorem-local-pickup-pro/).

= Can I offer a discount for choosing local pickup? =

Pickup discounts per location are available in [ALP PRO](https://www.zorem.com/product/zorem-local-pickup-pro/).

= Can I have some items shipped and some picked up in the same order? =

Mixed pickup + shipping orders (where some items are collected in-store and others are shipped) are available in [ALP PRO](https://www.zorem.com/product/zorem-local-pickup-pro/).

= Does this work with WooCommerce Subscriptions? =

Yes. Pickup instructions can be added to subscription renewal emails. The plugin is compatible with WooCommerce Subscriptions.

= Is Zorem Local Pickup compatible with HPOS? =

Yes. Zorem Local Pickup is fully compatible with WooCommerce High-Performance Order Storage (custom order tables).

= Does it work with WPML for multilingual stores? =

Yes. The pickup instructions text is translatable via WPML. All plugin strings are fully translatable using standard WordPress translation tools.

= Can I change multiple orders to Ready for Pickup at once? =

Yes. Zorem Local Pickup adds bulk order actions to the WooCommerce orders list, so you can select multiple orders and change their status to Ready for Pickup or Picked Up in one action.

== Changelog ==

= 1.9.2 =
* Dev – Tested with WooCommerce 10.1.0 and WordPress 7.1.

= 1.9.1 =
* Dev – Tested with WooCommerce 10.9.4 and WordPress 7.0.2.

= 1.9.0 =
* Improved – Upgraded the Settings page design.
* Dev – Tested with WooCommerce 10.9.3 and WordPress 7.0.

= 1.8.0 =
* Fix – Resolved PHP fatal error when no pickup locations exist in the database.
* Dev – Tested with WooCommerce 10.7.0 and WordPress 6.9.4.

= 1.7.9 =
* Dev – Tested with WooCommerce 10.5.1 and WordPress 6.9.1.
* Improved – Redesigned ALP Free Settings Page and improved PRO promotion.

= 1.7.8 =
* Dev – Tested with WooCommerce 10.4.2 and WordPress 6.9.

= 1.7.7 =
* Dev – Tested compatibility with WooCommerce 10.3.5 and WordPress 6.8.3.
* Fix – Updated deprecated WooCommerce script handles to new handles (WC 10.3.0+).

= 1.7.6 =
* Improved – Updated the promotional notice.
* Dev – Tested with WooCommerce 10.1.2.

= 1.7.5 =
* Dev – Tested compatibility with WooCommerce 10.0.4 and WordPress 6.8.2.
* Improved – Updated the promotional notice.
* Improved – Improved the settings design.

= 1.7.4 =
* Dev – Tested compatibility with WooCommerce 9.8.5 and WordPress 6.8.1.
* Improved – Updated the promotional notice.

= 1.7.3 =
* Dev – Tested compatibility with WooCommerce 9.8.1 and WordPress 6.8.
* Fix – Resolved TypeError: Cannot read properties of undefined (reading 'analytics').

= 1.7.2 =
* Enhancement – Added a review request admin notice.

= 1.7.1 =
* Dev – Tested with WooCommerce 9.7.1 and WordPress 6.7.2.
* Improved – Updated the promotional notice on the settings page.

= 1.7.0 =
* Dev – Tested with WooCommerce 9.6.0 and WordPress 6.7.1.
* Fix – Resolved design issue in the Local Pickup Workflow Settings.

= 1.6.9 =
* Dev – Tested with WooCommerce 9.4.2 and WordPress 6.7.
* Enhancement – Added a Black Friday admin message.

= 1.6.8 =
* Enhancement – Added an admin message for the Returns plugin.

= 1.6.7 =
* Fix – Resolved translations issue with Loco Translate plugin.

= 1.6.6 =
* Dev – Tested with WooCommerce 9.2.3 and WordPress 6.6.

= 1.6.5 =
* Fix – Resolved "Uncaught TypeError: in_array() Argument #2 ($haystack) must be of type array" issue.
* Dev – Tested with WooCommerce 9.0.2.

= 1.6.4 =
* Enhancement – Added UTM links for all external links to zorem.com.
* Dev – Tested with WooCommerce 8.7.0 and WordPress 6.5.

= 1.6.3 =
* Fix – Patched security vulnerability.
* Fix – Patched vulnerability with nonce in admin notices.
* Dev – Tested with WooCommerce 8.5.2.

= 1.6.2 =
* Fix – Patched security vulnerability.
* Dev – Tested with WooCommerce 8.5.1.

= 1.6.1 =
* Fix – HTML not supported in additional content in Processing email.
* Dev – Tested with WooCommerce 8.4.0.

= 1.6.0 =
* Improved – Improved the settings design.
* Dev – Tested with WooCommerce 8.2.1 and WordPress 6.4.
* Dev – Compatibility with PHP 8.2.
* Fix – PHP Deprecated warning.
* Fix – Database error on plugin activation.
* Fix – Security vulnerability patched.

= 1.5.5 =
* Improved – Improved the settings design.
* Dev – Tested with WooCommerce 7.9.0 and WordPress 6.3.

= 1.5.4 =
* Enhancement – Improved the customizer design.
* Fix – Translation string issue.
* Fix – HTML not supported in email content in customizer.
* Dev – Tested with WooCommerce 7.8.0 and WordPress 6.2.

= 1.5.3 =
* Fix – Vulnerable to Cross Site Request Forgery (CSRF).
* Dev – Tested with WooCommerce 7.5.1 and WordPress 6.1.

= 1.5.2 =
* Dev – Tested with WooCommerce 7.4.

= 1.5.1 =
* Fix – Uncaught Error: Call to undefined function wp_kses_post().

= 1.5 =
* Dev – Tested with WooCommerce 7.1 and WordPress 6.1.
* Enhancement – Added HTML support in email content.
* Enhancement – Added compatibility with High-Performance Order Storage (HPOS).
* Improved – Special instruction text WPML translation.

= 1.4.1 =
* Dev – Tested with WooCommerce 6.7 and WordPress 6.0.1.
* Enhancement – Improved the customizer design.

= 1.4.0 =
* Dev – Tested with WooCommerce 6.3.
* Enhancement – Set up a new customizer.

= 1.3.6 =
* Dev – Tested with WordPress 5.9.
* Enhancement – Added Docs and Review links on the plugins page.
* Fix – Translations for all strings across all languages.
* Fix – Invalid argument supplied for foreach() issue.

= 1.3.5 =
* Dev – Tested with WooCommerce 5.9.
* Fix – Translation issue.
* Fix – Admin JS issue.

= 1.3.4 =
* Dev – Tested with WooCommerce 5.8.
* Fix – All weekdays translatable.

= 1.3.3 =
* Dev – Tested with WooCommerce 5.6.
* Fix – Uncaught ReferenceError: setCountryCookie is not defined.
* Improved – Updated settings design skin colors.

= 1.3.2 =
* Dev – Tested with WooCommerce 5.5.2 and WordPress 5.8.
* Fix – Customizer fatal errors.
* Improved – Code review.

= 1.3.1 =
* Fix – Errors on activation.

= 1.3.0 =
* Improved – Updated the general settings design (header/menu).
* Dev – Tested with WooCommerce 5.4.1.

= 1.2.9 =
* Enhancement – Compatibility with SMS for WooCommerce.
* Dev – Tested with WooCommerce 5.2.

= 1.2.8 =
* Tweak – Updated settings design.
* Dev – Tested with WooCommerce 5.1 and WordPress 5.7.

= 1.2.7 =
* Tweak – Updated settings design.
* Enhancement – Customizer setting option.

= 1.2.6 =
* Tweak – Updated settings design.
* Enhancement – Free plugin does not run if PRO is activated.

= 1.2.5 =
* Enhancement – Updated design in settings.

= 1.2.4 =
* Enhancement – Updated design in settings.
* Localization – Updated translation files.

= 1.2.3 =
* Enhancement – Updated design in settings.

= 1.2.2 =
* Enhancement – Updated design in settings.
* Enhancement – Added PRO option: Local Pickup instructions on Completed Renewal email (Subscriptions).

= 1.2.1 =
* Enhancement – Updated design in settings.
* Enhancement – Changed label of "Time format" to "Display Time Format" in location settings.
* Enhancement – Display time format applies only on frontend (not admin).

= 1.2.0 =
* Enhancement – Updated design in settings.
* Fix – Translate "to" string issue.
* Fix – Work Hours issue for 12-hour format.
* Fix – Removed default "Complete Order" action button for pickup orders.
* Dev – Tested with WooCommerce 4.8 and WordPress 5.6.

= 1.1.9 =
* Fix – Double display of additional pickup note.
* Fix – Translate "to" string issue.
* Fix – Work Hours issue for 12-hour format.

= 1.1.8 =
* Fix – WP_User error in customizer.
* Enhancement – Minor design changes.
* Enhancement – Added Add-ons tab in settings.
* Enhancement – Updated date text for 12-hour date format on admin and frontend.

= 1.1.7 =
* Fix – Custom order status emails not sending.
* Fix – Working hours issue on frontend when settings changed from WordPress general settings.
* Fix – {customer_first_name} variable issue in Ready For Pickup email.
* Enhancement – Updated date text for 12-hour date format.
* Enhancement – Added phone number field in location form.
* Enhancement – Redesigned settings.

= 1.1.6 =
* Dev – Added compatibility with WooCommerce 4.5.
* Fix – Work Hours translation issue.
* Enhancement – Set work days list as a WordPress general setting.
* Enhancement – Redesigned settings.
* Enhancement – Re-assign Ready for Pickup and Picked Up orders to other statuses on uninstall.
* Enhancement – Added admin notice asking for a review.

= 1.1.5 =
* Localization – Added translation files for Italian.
* Dev – Tested with WordPress 5.5.

= 1.1.4 =
* Localization – Added translation files for Danish.
* Localization – Updated French translation files.
* Fix – No Order Again button for Picked Up status.

= 1.1.3 =
* Dev – Added compatibility with WooCommerce 4.3.0.
* Localization – Added work days in translations.

= 1.1.2 =
* Enhancement – Added available placeholders section in Ready for Pickup and Picked Up email customizer.
* Enhancement – Added option in location settings to select time format for work hours.
* Dev – Added 'Ready for Pickup' and 'Picked Up' text in translations.
* Dev – Updated code for better security.

= 1.1.1 =
* Fix – Ready for Pickup and Picked Up emails not sending from orders page action panel.

= 1.1.0 =
* Fix – Ready for Pickup and Picked Up emails not sending from orders page action panel.

= 1.0.9 =
* Enhancement – Removed am/pm from hour display in settings.
* Enhancement – Separate State and Country fields in location settings.
* Enhancement – Added plugin uninstall message on plugins page.
* Localization – Updated French translation files.

= 1.0.8 =
* Fix – Multiple emails sending on bulk action when order status changes to Ready for Pickup or Picked Up.
* Localization – Updated template language file and added all days of the week.

= 1.0.7 =
* Enhancement – Added validation for work hours when saving settings.
* Enhancement – Renamed Locations tab to Pickup Locations.
* Enhancement – Pickup Instruction header no longer displays in email/My Account/order-received if the location Pickup Instruction field is empty.
* Enhancement – Added action buttons in orders panel to change status from Processing to Ready for Pickup and from Ready for Pickup to Picked Up.
* Fix – Email content resetting when settings saved.
* Fix – Uncaught Error: Call to undefined function array_key_first().
* Localization – Updated translation template file and language files.

= 1.0.6 =
* Fix – Save issue for "Additional content on processing email in case of local pickup orders".
* Fix – Email subject and heading issue in Ready for Pickup and Picked Up email customizer.
* Localization – Changed text domain from "woo-local-pickup" to "advanced-local-pickup-for-woocommerce".
* Localization – Added translation files for German, Spanish, French, and Hebrew.

= 1.0.5 =
* Fix – Bug in Orders page bulk action dropdown.
* Fix – Additional content not changing in Ready for Pickup and Picked Up email customizer.
* Enhancement – Updated settings page design.
* Localization – Added language .pot and .po file.

= 1.0.4 =
* Fix – Warning in pickup instructions in order details page and Ready for Pickup email.
* Fix – Additional instructions now only display in Processing email if shipping method is Local Pickup.
* Fix – All Work Hours section bugs resolved; Work Hours section does not display if no days are selected.
* Enhancement – Added bulk order action to change order status to Ready for Pickup and Picked Up.

= 1.0.3 =
* Fix – Error in customizer: Call to undefined method OrderRefund::get_billing_first_name().
* Fix – Warning in pickup instructions in email.

= 1.0.2 =
* Enhancement – Updated settings page design.
* Enhancement – Updated Pickup Instruction display design in email and orders page.
* Enhancement – Added options in Ready for Pickup email customizer to change Pickup Instruction heading, padding, background color, and border color.
* Dev – Changed position of pickup instruction display in email and orders page.

= 1.0.1 =
* Enhancement – Added option to select different pickup times for different days.

= 1.0.0 =
* Initial version.
