=== QUID Payments ===
Contributors: quidworks
Tags: payment,payments,micropayment,micropayments,donation,donations,paywall,monetization,pay-per-use
Requires at least: 5.0
Tested up to: 5.2.2
Stable tag: 1.1.5
Requires PHP: 7.0
License: MIT
License URI: https://github.com/quidworks/quid-wordpress/blob/master/LICENSE

Let Your Fans Support You! QUID is kickstarting the pay-per-use economy by letting users make payments and tips as low as 1¢ for content.

== Description ==

## QUID WORKS FOR BLOGGERS
*   Monetize your content with micropayments instead of ads.
*   Earn revenue for your work without resorting to subscription paywalls.
*   Give your fans a way to reward you for the content they love.

## QUID WORKS FOR YOU
*   QUID is at least 30% cheaper than other payment processors for transactions under a dollar.
*   The savings keep going up as the transaction sizes get smaller.
*   You really can charge money for the content you create, in addition to how you currently earn income!

## QUID WORKS FOR WORDPRESS
*   Enable QUID payments on only the posts you want and either require a payment or make it optional.
*   Choose to display a payment button with a price that you set for each post or display a slider that lets the user choose the amount to pay.
*   Automatically display content that has already been purchased by the user.

== Installation ==

To start accepting QUID payments on your WordPress site you will need to [signup for a QUID merchant account](https://app.quid.works/sell) and generate API keys to use with this plugin.

1.  Download the plugin from our [release page](https://github.com/quidworks/quid-wordpress/releases) or install directly from the WordPress plugin directory.
1.  Activate the plugin through the ‘Plugins’ screen in WordPress admin
1.  Use the ‘Settings > QUID’ screen to configure the plugin by adding your Public and Private QUID API keys
1.  Write new posts or edit existing posts to add the QUID payment option

== Frequently Asked Questions ==

= What is QUID? =

QUID is a modern payment platform that lets you transact in pennies at very high volumes. It's designed to work with all kinds of currencies (fiat and crypto) and in all kinds of environments (Web, IoT, SaaS, etc.)

= Why do I need QUID? =

Accepting payments under a dollar today is extraordinarily expensive. Most payment providers charge a minimum of 30¢ + 3% per transaction, which comes out to a 30% premium (at least) on your product, and this is excluding the cost of monthly fees, chargebacks, and fraud prevention.

With QUID, you can actually charge a dollar, or 10 cents, or even a penny for your product, and relieve yourself from the hassle of complex (and unreliable) monetization schemes like ads, subscriptions, or arbitrarily bundled products.

= What countries are currently supported? =

QUID currently supports sellers in Canada and the US, and buyers worldwide. We plan to expand to other countries soon.

= What currencies does it support? =

QUID supports USD and CAD and will add support for other currencies soon.

= Are there restrictions on how QUID can be used? =

QUID is meant to be used for the purchase goods and services, not to transfer money between individuals or organizations. QUID also prohibits the selling or trading of counterfeit, dangerous, dishonest, or inappropriate goods, services or content. QUID also restricts the sale of alcohol, adult content, copyright-infringing content, drugs and pharmaceuticals, and gambling-related services on our platform. We reserve the right to reject or close accounts that are found to be trading in such goods and services.

= Where can I find more information on QUID? =

Check out our [support knowledge base](https://how.quid.works/en/collections/1780630-quid-wordpress-plugin) or start a support conversation on [our site](https://quid.works/).

== Screenshots ==

1. After installing and activating the QUID Payments plugin, navigate to the Settings > QUID Payments settings page and add your API key and secret from your [QUID merchant account](https://app.quid.works/merchant). Your API secret will not be saved in the database, only a hash of the key is saved.
2. Configure the QUID post settings fields for each post where you want to display the payment button or slider. For detailed instructions on how to use each field visit our [knowledge base](https://how.quid.works/quid-wordpress-plugin/). Include an excerpt of the post for the plugin to display before a user has paid.
3. Once your post is published, the payment button or slider will be displayed below the excerpt of the post.
4. Upon clicking the Pay button the user is presented with a payment confirmation screen.
5. Once the payment succeeds, the full post content is displayed.
6. Enable the floating tip button to display the tip button on each page of your site.

== Changelog ==

= 1.1.5 =
* Add a floating tip button to your site so visitors can tip from any page
* For already puchased content, Control if your theme displays the full post or the excerpt on blog and archive pages
* Bug fixes

= 1.1.4 =
* Access plugin settings, documentation, and support directly from your plugin admin page
* Improved error messages and logging

= 1.1.3 =
* QUID Payments now works better with caching plugins and services
* Added debugging tools and error logging

= 1.1.2 =
* Fixes an issue where payments made outside of a blog post or page (e.g. in a widget area) were failing

= 1.1.1 =
* Added a setting option for merchant currency

= 1.1 =
* Use the new quid-button and quid-slider shortcodes to add a QUID payment button or slider anywhere shortcodes are supported
* Most required payment fields are now set from the details of the post or page
* Set the horizontal alignment of the buttons and slider for the entire site from the QUID Settings page
* Maximum payment value of $2 is enforced
* Bug fixes

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.5 =
Version 1.1.5 adds the floating tip button feature and a display option for previously purchased content

= 1.1.4 =
Version 1.1.4 has improved debugging and error logging

= 1.1.3 =
Version 1.1.3 works better with content caching plugins and services

= 1.1.2 =
Version 1.1.2 fixes an issue where payments made outside of a blog post or page (e.g. in a widget area) were failing

= 1.1.1 =
Version 1.1.1 allows merchants to select their currency

= 1.1 =
Version 1.1 adds shortcode support and simplifies the per-post payment settings

== Third Party Service ==

The QUID Payments plugin relies on the QUID Payment service hosted at [app.quid.works](https://app.quid.works) for payment processing. In order to accept payments using this plugin you must [create a QUID merchant account](https://app.quid.works/sell) and, in doing so, accept the [Merchant Terms of Use](https://how.quid.works/terms-and-policies/merchant-terms-of-use).

Visitors to your site will need a [QUID user account](https://app.quid.works/signup) in order to make payments. Users can use an existing QUID account or can complete the simple Signup & Pay process without leaving your site.

For more information on the QUID payment service visit the [knowledge base](https://how.quid.works).

== Privacy ==

Using the QUID Payments plugin allows you to accept payments from visitors without having to collect any of their personal or payment card information. The visitor's information is collected directly by QUID and stored in our North American data centre environments. 

When a visitor completes a purchase using QUID, we provide your WordPress site with a unique, randomly-generated user ID that identifies that user within your site. Each user ID created for the visitor is shared with only one merchant and each merchant will be given a different unique, randomly-generated user ID associated with that user's QUID account. This unique user ID allows the QUID Payments plugin to know which visitors have paid for which posts.

When you [create a QUID Merchant Account](https://app.quid.works/sell) you provide your legal name (either your own name or that of your business). This name will be shared with users that have completed payments on your website using QUID. We may also include your business name or website address to promote your use of the QUID service to current and prospective QUID users.

Read the [QUID Privacy Policy](https://how.quid.works/terms-and-policies/quid-privacy-policy) for more information.

== Open Source ==

The QUID Payments plugin is open source software. Feel free to contribute or fork this code on [GitHub](https://github.com/quidworks/quid-wordpress/).
