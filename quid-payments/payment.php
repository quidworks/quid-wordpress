<?php

namespace QUIDPaymentsPayment {

    use QUIDPaymentsDatabase as Database;

    class Payment {

        function paymentCallback() {
            $nonce = $_REQUEST['_wpnonce'];
            if ( ! wp_verify_nonce( $nonce, 'quid-payment-nonce' ) ) {
                die( 'Security check' ); 
            }

            $database = new Database\Database();;
            // gets POST content
            $json = json_decode(file_get_contents('php://input'));
            if (!$this->validatePaymentResponse($json->paymentResponse)) {
                print_r("validation failed");
                return;
            };
            setcookie( "quidUserHash", $json->paymentResponse->userHash, time() + (86400 * 30), "/" );

            $cost = (float)$json->paymentResponse->amount;
            if ($cost != 0.000000000) {
                if (!$this->storePurchase(
                    $json->paymentResponse->userHash,
                    $json->paymentResponse->productID,
                    false)
                ) {
                    print_r("database error");
                    return;
                };
            } else {
                if (!$database->hasPurchasedAlready(
                    $json->paymentResponse->userHash,
                    $json->paymentResponse->productID
                )) {
                    print_r("unpurchased");
                    return;
                }
            }
            echo $this->fetchContent($json->postid);
        }

        function tipCallback() {
            $nonce = $_REQUEST['_wpnonce'];
            if ( ! wp_verify_nonce( $nonce, 'quid-payment-nonce' ) ) {
                die( 'Security check' ); 
            }
            
            $json = json_decode(file_get_contents('php://input'));
            if (!$this->validatePaymentResponse($json->paymentResponse)) {
                print_r("error");
                return;
            };

            setcookie( "quidUserHash", $json->paymentResponse->userHash, time() + (86400 * 30), "/" );

            $cost = (float)$json->paymentResponse->amount;
            if ($cost != 0.000000000) {
                if (!$this->storePurchase(
                    $json->paymentResponse->userHash,
                    $json->paymentResponse->productID,
                    true)
                ) {
                    print_r("error");
                    return;
                };
            }

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

            if ($sig != $paymentResponse->sig) {
                error_log(
                    print_r($paymentResponse, true)."\n".print_r('Sig: '.$sig, true)."\n",
                    3,
                    plugin_dir_path(__FILE__)."error_log"
                );
            }

            return ($sig == $paymentResponse->sig);
        }

        function storePurchase($userHash, $productID, $isTip) {
            global $wpdb;
            $table_name = $wpdb->prefix . "quidPurchases";

            $tip = 'false';
            if ($isTip) {
                $tip = 'true';
            }

            $wpdb->insert( 
                $table_name, 
                array(
                    'time' => current_time( 'mysql' ), 
                    'user' => $userHash,
                    'product-id' => $productID,
                    'tip' => $tip,
                ) 
            );
            if($wpdb->last_error !== '') {
                echo $wpdb->last_error;
                return false;
            }
            return true;
        }

        function fetchContent($postid) {
            return do_shortcode(get_post_field('post_content', $postid));
        }

    }

}

?>