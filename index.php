<?

/*
Plugin Name: QUID Article
Description: Fetches article once user has paid.
Version: 0.1.0
Author: QUID
Author URI: https://quid.works
*/

add_action( 'admin_post_quid-article', 'paymentCallback' );

function paymentCallback() {
    $json = json_decode(file_get_contents('php://input'));
    if (!validatePaymentResponse($json->paymentResponse)) return;
    print_r(fetchContent($json->article));
}

function fetchContent($postTitle) {
    global $wpdb;
    $sql = "SELECT post_content FROM {$wpdb->dbname}.wp_posts WHERE post_status = 'private' AND post_title = '{$postTitle}' ORDER BY ID DESC LIMIT 1";
    $results = $wpdb->get_results( $sql, ARRAY_N );
    return $results[0][0];
}

function validatePaymentResponse($response) {
    $infoArray = [
        $response->id,
        $response->userHash,
        $response->merchantID,
        $response->productID,
        $response->currency,
        $response->amount,
        $response->tsUnix,
    ];
    $payload = implode(',', $infoArray);
    $secret = base64_encode(hash('sha256', 'ks-UIN65XEORSNQ258VKSGG1KOCJXTARYGF', true));
    $sig = base64_encode(hash_hmac('sha256', $payload, $secret, true));

    if ($sig == $response->sig) {
        return true;
    }
    return false;
}

?>