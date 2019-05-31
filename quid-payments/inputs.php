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

            if (isset($quidUserHash)) $userCookie = $quidUserHash;
            else { echo ''; return; }

            if ($database->hasPurchasedAlready($userCookie, $productID)) $purchased = true;
            else { echo ''; return; }

            if ($purchased) echo do_shortcode(get_post_field('post_content', $postID));
            else { echo ''; return; }
        }

        private function buttonAlignment($shortcodeArg) {
            if (isset($shortcodeArg)) {
                if ($shortcodeArg == 'center') return 'center';
                if ($shortcodeArg == 'left') return 'flex-start';
                if ($shortcodeArg == 'right') return 'flex-end';
            }

            $alignOption = get_option('quid-align');
            if ($alignOption != "") {
                return $alignOption;
            }

            return 'flex-end';
        }

        function quidButton($meta) {
            global $post;
            $microtimeIdentifier = microtime();
            $requiredFields = ['id', 'price', 'description', 'name', 'url'];

            if (!isset($meta['type'])) $meta['type'] = 'Optional';
            if (!isset($meta['paid'])) $meta['paid'] = 'Thanks!';

            $meta['dom-id'] = $meta['id'].$microtimeIdentifier;

            foreach ($requiredFields as $field) {
                if (!isset($meta[$field])) return "";
            }

            $html = <<<HTML
                <div class="quid-pay-error-container" style="text-align: center; margin: 0px; display: none;">
                    <div id="quid-error-{$post->ID}" class="quid-pay-error" style="display: inline-flex;">
                        <img class="quid-pay-error-image" src="https://js.quid.works/v1/assets/quid.png" />
                        Payment validation failed
                    </div>
                </div>
                <div id="quid-pay-buttons-{$meta['dom-id']}" class="quid-pay-buttons for-product-{$meta['id']}" style="display: flex; justify-content: {$this->buttonAlignment($meta['align'])};">
HTML;
                    if ($meta['type'] == "Required") {
                        $html .= <<<HTML
                        <div id="{$meta['dom-id']}_free"
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
                    <div id="{$meta['dom-id']}"
                        quid-amount="{$meta['price']}"
                        quid-currency="CAD"
                        quid-product-id="{$meta['id']}"
                        quid-product-url="{$meta['url']}"
                        quid-product-name="{$meta['name']}"
                        quid-product-description="{$meta['description']}"
                        style="display: inline-flex"
                    ></div>
                </div>
HTML;

            $this->enqueueJS(
                'js_quid_button_'.$microtimeIdentifier,
                plugins_url( 'js/button.js', __FILE__ ),
                array(
                    'post_id' => $post->ID,
                    'meta_name' => $meta['name'],
                    'meta_id' => $meta['id'],
                    'meta_domID' => $meta['dom-id'],
                    'meta_type' => $meta['type'],
                    'meta_price' => $meta['price'],
                    'meta_paid' => $meta['paid'],
                )
            );

            # If required, add already paid button beside the pay button.
            if ($meta['type'] == "Required") {
                $nonce = wp_create_nonce( 'quid-cookie-nonce' );
                $purchaseCheckURL = admin_url("admin-post.php?action=purchase-check&_wpnonce=".$nonce);

                $this->enqueueJS(
                    'js_quid_button_required_'.$microtimeIdentifier,
                    plugins_url( 'js/buttonRequired.js', __FILE__ ),
                    array(
                        'purchase_check_url' => $purchaseCheckURL,
                        'post_id' => $post->ID,
                        'meta_name' => $meta['name'],
                        'meta_id' => $meta['id'],
                        'meta_domID' => $meta['dom-id'],
                        'meta_type' => $meta['type'],
                        'meta_price' => $meta['price'],
                        'meta_paid' => $meta['paid'],
                    )
                );
            }

            return $html;
        }

        function quidSlider($meta) {
            global $post;
            $microtimeIdentifier = microtime();
            $requiredFields = ['id', 'price', 'description', 'name', 'url'];

            if (!isset($meta['type'])) $meta['type'] = 'Optional';
            if (!isset($meta['min'])) $meta['min'] = '0.01';
            if (!isset($meta['max'])) $meta['max'] = '2.00';
            if (!isset($meta['text'])) $meta['text'] = 'Give';
            if (!isset($meta['initial'])) $meta['initial'] = '1.00';
            if (!isset($meta['paid'])) $meta['paid'] = 'Thanks!';

            $meta['dom-id'] = $meta['id'].$microtimeIdentifier;

            foreach ($requiredFields as $field) {
                if (!isset($meta[$field])) return "";
            }

            $html = <<<HTML
                <div class="quid-pay-error-container" style="text-align: center; margin: 0px; display: none;">
                    <div id="quid-error-{$post->ID}" class="quid-pay-error" style="display: inline-flex;">
                        <img class="quid-pay-error-image" src="https://js.quid.works/v1/assets/quid.png" />
                        Payment validation failed
                    </div>
                </div>

                <div id="quid-pay-buttons-{$meta['dom-id']}" class="quid-pay-buttons  for-product-{$meta['id']}" style="display: flex; justify-content: {$this->buttonAlignment($meta['align'])};">
                    <div
                        id="{$meta['dom-id']}"
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
HTML;

            wp_register_style( 'css_quid_init', plugins_url( 'css/init.css', __FILE__ ) );
            wp_enqueue_style( 'css_quid_init' );

            $this->enqueueJS(
                'js_quid_slider'.$microtimeIdentifier,
                plugins_url( 'js/slider.js', __FILE__ ),
                array(
                    'post_id' => $post->ID,
                    'meta_id' => $meta['id'],
                    'meta_domID' => $meta['dom-id'],
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
                )
            );


            # If required, add already paid button beside the pay button.
            if ($meta['type'] == "Required") {
                $nonce = wp_create_nonce( 'quid-cookie-nonce' );
                $purchaseCheckURL = admin_url("admin-post.php?action=purchase-check&_wpnonce=".$nonce);
                $this->enqueueJS(
                    'js_quid_button_required'.$microtimeIdentifier,
                    plugins_url( 'js/sliderRequired.js', __FILE__ ),
                    array(
                        'purchase_check_url' => $purchaseCheckURL,
                        'post_id' => $post->ID,
                        'meta_id' => $meta['id'],
                        'meta_domID' => $meta['dom-id'],
                        'meta_type' => $meta['type'],
                        'meta_price' => $meta['price'],
                        'meta_paid' => $meta['paid'],
                        'meta_description' => $meta['description'],
                        'meta_name' => $meta['name'],
                        'meta_url' => $meta['url'],
                    )
                );
            }

            return $html;
        }

        function enqueueJS($fileIdentifier, $path, $data) {
            wp_register_script( $fileIdentifier, $path );
            wp_localize_script( $fileIdentifier, 'dataJS', $data );
            wp_enqueue_script( $fileIdentifier );
        }
    }

}

?>