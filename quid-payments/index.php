<?php

/*
Plugin Name: QUID Payments
Description: Let Your Fans Support You! QUID is kickstarting the pay-per-use economy by letting users make payments and tips as low as 1¢ for content.
Version: 1.1.2
Author: QUID Works Inc.
Author URI: https://quid.works
License: MIT
License URI: https://github.com/quidworks/quid-wordpress/blob/master/LICENSE
*/

namespace QUIDPayments {

    require_once dirname( __FILE__ ) .'/database.php';
    require_once dirname( __FILE__ ) .'/payment.php';
    require_once dirname( __FILE__ ) .'/settings.php';
    require_once dirname( __FILE__ ) .'/init.php';
    require_once dirname( __FILE__ ) .'/postmeta.php';
    require_once dirname( __FILE__ ) .'/javascript.php';
    require_once dirname( __FILE__ ) .'/inputs.php';
    require_once dirname(__FILE__) .'/post.php';
    require_once dirname(__FILE__) .'/helpers.php';

    use QUIDPaymentsDatabase as Database;
    use QUIDPaymentsInit as Init;
    use QUIDPaymentsInputs as Inputs;
    use QUIDPaymentsFooter as Footer;
    use QUIDPaymentsPayment as Payment;
    use QUIDPaymentsPost as Post;
    use QUIDPaymentsMeta as Meta;
    use QUIDPaymentsSettings as Settings;

    $baseURL = 'https://app.quid.works';

    // https://codex.wordpress.org/Creating_Tables_with_Plugins
    register_activation_hook( __FILE__, array(new Database\Database(), 'createPurchaseDatabase') );

    add_action( 'wp_footer', array(new Footer\Footer(), 'js') );

    add_shortcode('quid-slider', array(new Inputs\Inputs(), 'quidSlider'));
    add_shortcode('quid-button', array(new Inputs\Inputs(), 'quidButton'));
    
    add_filter( 'the_content', array(new Post\Post(), 'filterPostContent') );

    add_action( 'admin_post_nopriv_purchase-check', array(new Inputs\Inputs(), 'returnUserCookie') );
    add_action( 'admin_post_purchase-check', array(new Inputs\Inputs(), 'returnUserCookie') );

    add_action( 'admin_post_nopriv_quid-settings', array(new Settings\Settings(), 'saveSettings') );
    add_action( 'admin_post_quid-settings', array(new Settings\Settings(), 'saveSettings') );

    add_action( 'admin_post_nopriv_quid-article', array(new Payment\Payment(), 'paymentCallback') );
    add_action( 'admin_post_quid-article', array(new Payment\Payment(), 'paymentCallback') );

    add_action( 'admin_post_nopriv_quid-tip', array(new Payment\Payment(), 'tipCallback') );
    add_action( 'admin_post_quid-tip', array(new Payment\Payment(), 'tipCallback') );

    add_action( 'save_post', array(new Meta\Meta(), 'save_postdata') );
    add_action( 'add_meta_boxes', array(new Meta\Meta(), 'addMetaFields') );

    add_action( 'admin_menu', array(new Settings\Settings(), 'addMenuPage') );

    add_action( 'wp_enqueue_scripts', array(new Init\Init(), 'addScripts') );

    add_action( 'admin_enqueue_scripts', array(new Settings\Settings(), 'addScripts') );
}

?>
