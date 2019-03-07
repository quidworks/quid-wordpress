<?php

/*
Plugin Name: QUID
Description: Monetize your posts by either requiring payment to view more than the excerpt, or display the post while still leaving a way for the reader to leave a tip or donation.
Version: 1.0
Author: QUID Works
Author URI: https://quid.works
License: MIT
License URI: https://github.com/quidworks/quid-wordpress/blob/master/LICENSE
*/

require_once dirname( __FILE__ ) .'/database.php';
require_once dirname( __FILE__ ) .'/payment.php';
require_once dirname( __FILE__ ) .'/settings.php';
require_once dirname( __FILE__ ) .'/init.php';
require_once dirname( __FILE__ ) .'/postmeta.php';
require_once dirname( __FILE__ ) .'/javascript.php';
require_once dirname( __FILE__ ) .'/inputs.php';

// https://codex.wordpress.org/Creating_Tables_with_Plugins
register_activation_hook( __FILE__, 'createPurchaseDatabase' );

//$baseURL = 'http://localhost:3000';
$baseURL = 'https://app.quid.works';
$wpRoot = get_site_url();

function filterPostContent($content) {
    global $post;
    if ($post->post_type != 'post') return $content;
    $type = get_post_meta($post->ID, 'quid_field_type', true);
    $input = get_post_meta($post->ID, 'quid_field_input', true);
    if ($type == "Required") {
        if ($input == "Buttons") return quidButton([]);
        else return quidSlider([]);
    } else if ($type == "Optional") {
        if ($input == "Buttons") return $content.quidButton([]);
        else return $content.quidSlider([]);
    } else {
        return $content;
    }
}

add_filter( 'the_content', 'filterPostContent' );

?>