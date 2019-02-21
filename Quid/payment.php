<?php

// https://codex.wordpress.org/Plugin_API/Action_Reference/admin_post_(action)
add_action( 'admin_post_nopriv_quid-article', 'paymentCallback' );
add_action( 'admin_post_quid-article', 'paymentCallback' );

add_action( 'admin_post_nopriv_quid-tip', 'tipCallback' );
add_action( 'admin_post_quid-tip', 'tipCallback' );

function paymentCallback() {
    // gets POST content
    $json = json_decode(file_get_contents('php://input'));
    if (!validatePaymentResponse($json->paymentResponse)) {
        print_r("validation failed");
        return;
    };
    setcookie( "quidUserHash", $json->paymentResponse->userHash, time() + (86400 * 30), "/" );

    $cost = (float)$json->paymentResponse->amount;
    if ($cost != 0.000000000) {
        if (!storePurchase(
            $json->paymentResponse->userHash,
            $json->paymentResponse->productID
        )) {
            print_r("database error");
            return;
        };
    } else {
        if (!checkIfPurchasedAlready(
            $json->paymentResponse->userHash,
            $json->paymentResponse->productID
        )) {
            print_r("unpurchased");
            return;
        }
    }
    print_r(fetchContent($json->postid));
}

function tipCallback() {
    $json = json_decode(file_get_contents('php://input'));
    if (!validatePaymentResponse($json->paymentResponse)) {
        print_r("error");
        return;
    };
    echo "success";
}

// https://how.quid.works/developer/verifying-payments
function validatePaymentResponse($paymentResponse) {
    $infoArray = [
        $paymentResponse->id,
        $paymentResponse->userHash,
        $paymentResponse->merchantID,
        $paymentResponse->productID,
        $paymentResponse->currency,
        $paymentResponse->amount,
        $paymentResponse->tsUnix,
    ];
    $payload = implode(',', $infoArray);
    // $secret must be formatted as follows: base64_encode(hash('sha256', SECRET_KEY_HERE, true));
    // ours is already formatted in the settings section of this file
    $secret = get_option('quid-secretKey');
    $sig = base64_encode(hash_hmac('sha256', $payload, $secret, true));

    return ($sig == $paymentResponse->sig);
}

function checkIfPurchasedAlready($userHash, $productID) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT user FROM {$wpdb->dbname}.{$wpdb->prefix}quidPurchases WHERE user = '%s' AND `product-id` = '%s' ORDER BY ID DESC LIMIT 1", $userHash, $productID);
    $results = $wpdb->get_results( $sql, ARRAY_N );
    return sizeof($results) > 0;
}

function storePurchase($userHash, $productID) {
    global $wpdb;
    $table_name = $wpdb->prefix . "quidPurchases";

    $wpdb->insert( 
        $table_name, 
        array(
            'time' => current_time( 'mysql' ), 
            'user' => $userHash,
            'product-id' => $productID,
        ) 
    );
    if($wpdb->last_error !== '') {
        echo $wpdb->last_error;
        return false;
    }
    return true;
}

function fetchContent($postid) {
    return wpautop(get_post_field('post_content', $postid));
}

?>