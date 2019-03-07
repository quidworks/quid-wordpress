<?php

/*
Plugin Name: QUID
Description: Let Your Fans Support You! QUID is kickstarting the pay-per-use economy by letting users make payments and tips as low as 1¢ for blog content.
Version: 1.0
Author: QUID Works
Author URI: https://quid.works
License: MIT
License URI: https://github.com/quidworks/quid-wordpress/blob/master/LICENSE
*/

require_once dirname( __FILE__ ) .'/payment.php';
require_once dirname( __FILE__ ) .'/settings.php';
require_once dirname( __FILE__ ) .'/database.php';
require_once dirname( __FILE__ ) .'/init.php';
require_once dirname( __FILE__ ) .'/postmeta.php';
require_once dirname( __FILE__ ) .'/javascript.php';
require_once dirname( __FILE__ ) .'/inputs.php';

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
