<?php

namespace QUIDPaymentsDatabase {

    use QUIDPaymentsInputs as Inputs;
    use QUIDPaymentsMeta as Meta;

    class Database {

        function hasPurchasedAlready($userHash, $productID) {
            global $wpdb;
            $sql = $wpdb->prepare("SELECT `product-id` FROM {$wpdb->dbname}.{$wpdb->prefix}quidPurchases WHERE tip!='true' AND user = '%s' AND `product-id`='%s' LIMIT 1", $userHash, $productID);
            $results = $wpdb->get_results( $sql, ARRAY_N );
            return sizeof($results) > 0;
        }

        function createPurchaseDatabase() {
            global $wpdb;
            $table_name = $wpdb->prefix . "quidPurchases";

            if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
                $sql = "CREATE TABLE ".$table_name." (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    `user` VARCHAR(60) NOT NULL,
                    `product-id` VARCHAR(255) NOT NULL,
                    `tip` VARCHAR(5) DEFAULT 'false' NOT NULL,
                    PRIMARY KEY (id)
                );";

                require_once(ABSPATH. 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }

        function purchasedContentCheck() {
            $nonce = $_REQUEST['_wpnonce'];
            if ( ! wp_verify_nonce( $nonce, 'quid-cookie-nonce' ) ) {
                die( 'Security check' );
            }

            $inputs = new Inputs\Inputs();
            $metaInstance = new Meta\Meta();

            $productID = sanitize_text_field($_POST["productID"]);
            $postID = sanitize_text_field($_POST["postID"]);
            $quidUserHash = sanitize_text_field($_COOKIE["quidUserHash"]);

            $purchased = false;

            $meta = $metaInstance->getMetaFields(get_post($postID));

            $newContent = get_post_field('post_content', $postID);

            if ($meta['type'] == "Required") {

                $userCookie = '';

                if (isset($quidUserHash)) {
                    $userCookie = $quidUserHash;
                } else {
                    // payment is required but user is not signed in to quid so
                    // we can't tell if it's been already purchased
                    return;
                }

                if (!$this->hasPurchasedAlready($userCookie, $productID)) {
                    // payment is required but user has not already purchased
                    return;
                }

            } else if ($meta['type'] == "Optional") {

                // Payment is optional so we will add the tip payment options throughout the post content

                if (isset($meta['locations']['top'])) {
                    if ($meta['locations']['top'] == "true") {
                        if ($meta['input'] == "Slider") {
                            $newContent = $inputs->quidSlider($meta, true) . $newContent;
                        } else {
                            $newContent = $inputs->quidButton($meta, true) . $newContent;
                        }
                    }
                }

                if (isset($meta['locations']['nearTop'])) {
                    if ($meta['locations']['nearTop'] == "true") {
                        $postLength = strlen($newContent);
                        $locationCharacters = round($postLength * 0.05);
                        $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                        if ($meta['input'] == "Slider") {
                            $replacementString = '</p>' . $inputs->quidSlider($meta, true);
                        } else {
                            $replacementString = '</p>' . $inputs->quidButton($meta, true);
                        }
                        $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
                    }
                }

                if (isset($meta['locations']['nearMiddle'])) {
                    if ($meta['locations']['nearMiddle'] == "true") {
                        $postLength = strlen($newContent);
                        $locationCharacters = round($postLength * 0.50);
                        $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                        if ($meta['input'] == "Slider") {
                            $replacementString = '</p>' . $inputs->quidSlider($meta, true);
                        } else {
                            $replacementString = '</p>' . $inputs->quidButton($meta, true);
                        }
                        $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
                    }
                }

                if (isset($meta['locations']['nearBottom'])) {
                    if ($meta['locations']['nearBottom'] == "true") {
                        $postLength = strlen($newContent);
                        $locationCharacters = round($postLength * 0.95);
                        $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                        if ($meta['input'] == "Slider") {
                            $replacementString = '</p>' . $inputs->quidSlider($meta, true);
                        } else {
                            $replacementString = '</p>' . $inputs->quidButton($meta, true);
                        }
                        $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
                    }
                }

                if (isset($meta['locations']['bottom'])) {
                    if ($meta['locations']['bottom'] == "true") {
                        if ($meta['input'] == "Slider") {
                            $newContent .= $inputs->quidSlider($meta, true);
                        } else {
                            $newContent .= $inputs->quidButton($meta, true);
                        }
                    }
                }
            }

            echo do_shortcode($newContent);
        }

    }

}

?>