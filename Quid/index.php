<?

/*
Plugin Name: QUID
Description: Monetize your posts by either requiring payment to view more than the excerpt, or display the post while still leaving a way for the reader to leave a tip or donation.
Version: 1.0
Author: QUID Works
Author URI: https://quid.works
*/

/* ^^^^^ The above content is needed to show the plugin on the user's plugin page */

require_once dirname( __FILE__ ) .'/payment.php';
require_once dirname( __FILE__ ) .'/settings.php';
require_once dirname( __FILE__ ) .'/database.php';
require_once dirname( __FILE__ ) .'/init.php';
require_once dirname( __FILE__ ) .'/postmeta.php';
require_once dirname( __FILE__ ) .'/javascript.php';
require_once dirname( __FILE__ ) .'/inputs.php';

//$baseURL = 'http://localhost:3000';
$baseURL = 'https://app.quid.works';
//$wpRoot = '/wordpress';
$wpRoot = '';


function filterPostContent($content) {
    global $post;
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