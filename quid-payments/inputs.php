<?php

namespace QUIDPaymentsInputs {

    use QUIDPaymentsDatabase as Database;
    use QUIDPaymentsMeta as Meta;

    class Inputs {

        function returnUserCookie() {

            $nonce = $_REQUEST['_wpnonce'];
            if ( ! wp_verify_nonce( $nonce, 'quid-cookie-nonce' ) ) {
                die( 'Security check' ); 
            }

            $productID = sanitize_text_field($_POST["productID"]);
            $postID = sanitize_text_field($_POST["postID"]);
            $quidUserHash = sanitize_text_field($_COOKIE["quidUserHash"]);

            $database = new Database\Database();
            $purchased = false;
            $userCookie = '';

            if (isset($quidUserHash)) {
                $userCookie = $quidUserHash;
            } else {
                echo '';
                return;
            }

            if ($database->hasPurchasedAlready($userCookie, $productID)) {
                $purchased = true;
            } else {
                echo '';
                return;
            }

            if ($purchased) {
                echo wpautop(get_post_field('post_content', $postID));
            } else {
                echo '';
                return;
            }
        }

        function quidButton() {
            global $post;
            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            $html = "";

            $html .= <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}">
HTML;

            if ($meta['type'] == "Required") {
                $html .= <<<HTML
                    <p id="{$post->ID}-excerpt">$post->post_excerpt</p>
HTML;
            }

            $quidPaymentsAlreadyPaidButtonHTML = '';
            $quidPaymentsAlreadyPaidButtonJS = '';

            if ($meta['type'] == "Required") {
                $quidPaymentsAlreadyPaidButtonHTML .= <<<HTML
                <div id="{$meta['id']}_free"
                    class="quid-pay-already-paid"
                    quid-amount="0"
                    quid-currency="CAD"
                    quid-product-id="{$meta['id']}"
                    quid-product-url="{$meta['url']}"
                    quid-product-name="{$meta['name']}"
                    quid-product-description="{$meta['description']}"
                    style="display: inline-flex"
                ></div>
HTML;
            }

            $html .= <<<HTML
                <div class="quid-pay-error-container" style="text-align: center; margin: 0px; display: none;">
                    <div id="quid-error-{$post->ID}" class="quid-pay-error" style="display: inline-flex;">
                        <img class="quid-pay-error-image" src="https://js.quid.works/v1/assets/quid.png" />
                        Payment validation failed
                    </div>
                </div>
                <div class="quid-pay-buttons">
HTML;
                    $html .= $quidPaymentsAlreadyPaidButtonHTML;

                    $html .= <<<HTML
                    <div id="{$meta['id']}"
                        quid-amount="{$meta['price']}"
                        quid-currency="CAD"
                        quid-product-id="{$meta['id']}"
                        quid-product-url="{$meta['url']}"
                        quid-product-name="{$meta['name']}"
                        quid-product-description="{$meta['description']}"
                        style="display: inline-flex"
                    ></div>
                </div>
            </div>
HTML;
            
            $html .= $quidPaymentsAlreadyPaidButtonJS;

            wp_register_script( 'js_quid_button_'.$meta['id'], plugins_url( 'js/button.js', __FILE__ ) );
            $data = array(
                'post_id' => $post->ID,
                'meta_id' => $meta['id'],
                'meta_type' => $meta['type'],
                'meta_price' => $meta['price'],
                'meta_paid' => $meta['paid'],
            );
            wp_localize_script( 'js_quid_button_'.$meta['id'], 'dataButtonJS', $data );
            wp_enqueue_script( 'js_quid_button_'.$meta['id'] );

            if ($meta['type'] == "Required") {
                $nonce = wp_create_nonce( 'quid-cookie-nonce' );
                
                $purchaseCheckURL = admin_url("admin-post.php?action=purchase-check&_wpnonce=".$nonce);
                wp_register_script( 'js_quid_button_required_'.$meta['id'], plugins_url( 'js/buttonRequired.js', __FILE__ ) );
                $data = array(
                    'purchase_check_url' => $purchaseCheckURL,
                    'post_id' => $post->ID,
                    'meta_id' => $meta['id'],
                    'meta_type' => $meta['type'],
                    'meta_price' => $meta['price'],
                    'meta_paid' => $meta['paid'],
                );
                wp_localize_script( 'js_quid_button_required_'.$meta['id'], 'dataButtonJS', $data );
                wp_enqueue_script( 'js_quid_button_required_'.$meta['id'] );
            }

            return $html;
        }

        function quidSlider($atts) {
            global $post;
            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);
            $html = "";

            $html .= <<<HTML
            <div style="width: 100%;" id="post-content-{$meta['id']}">
                <p id="{$post->ID}-excerpt">$post->post_excerpt</p>
                <div class="quid-pay-error-container" style="text-align: center; margin: 0px; display: none;">
                    <div id="quid-error-{$post->ID}" class="quid-pay-error" style="display: inline-flex;">
                        <img class="quid-pay-error-image" src="https://js.quid.works/v1/assets/quid.png" />
                        Payment validation failed
                    </div>
                </div>

                <div class="quid-pay-buttons">
                    <div
                        id="{$meta['id']}"
                        class="quid-slider"
                        quid-currency="CAD"
                        quid-product-id="{$meta['id']}"
                        quid-product-url="{$meta['url']}"
                        quid-product-name="{$meta['name']}"
                        quid-product-description="{$meta['description']}"
                        quid-text="{$meta['text']}"
                        quid-text-paid="{$meta['paid']}"
                    ></div>
                </div>
            </div>
HTML;

            wp_register_style( 'css_quid_init', plugins_url( 'css/init.css', __FILE__ ) );
            wp_enqueue_style( 'css_quid_init' );

            wp_register_script( 'js_quid_slider', plugins_url( 'js/slider.js', __FILE__ ) );
            $data = array(
                'post_id' => $post->ID,
                'meta_id' => $meta['id'],
                'meta_initial' => $meta['initial'],
                'meta_type' => $meta['type'],
                'meta_text' => $meta['text'],
                'meta_price' => $meta['price'],
                'meta_paid' => $meta['paid'],
                'meta_description' => $meta['description'],
                'meta_name' => $meta['name'],
                'meta_url' => $meta['url'],
                'meta_min' => $meta['min'],
                'meta_max' => $meta['max'],
            );
            wp_localize_script( 'js_quid_slider', 'dataSliderJS', $data );
            wp_enqueue_script( 'js_quid_slider' );

            if ($meta['type'] == "Required") {
                $nonce = wp_create_nonce( 'quid-cookie-nonce' );
                
                $purchaseCheckURL = admin_url("admin-post.php?action=purchase-check&_wpnonce=".$nonce);

                wp_register_script( 'js_quid_button_required', plugins_url( 'js/sliderRequired.js', __FILE__ ) );
                $data = array(
                    'purchase_check_url' => $purchaseCheckURL,
                    'post_id' => $post->ID,
                    'meta_id' => $meta['id'],
                    'meta_type' => $meta['type'],
                    'meta_price' => $meta['price'],
                    'meta_paid' => $meta['paid'],
                    'meta_description' => $meta['description'],
                    'meta_name' => $meta['name'],
                    'meta_url' => $meta['url'],
                    'purchase_check_url' => $purchaseCheckURL,
                );
                wp_localize_script( 'js_quid_button_required', 'dataSliderJS', $data );
                wp_enqueue_script( 'js_quid_button_required' );
            }

            return $html;
        }

    }

}

?>