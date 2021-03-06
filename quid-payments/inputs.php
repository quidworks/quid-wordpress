<?php

namespace QUIDPaymentsInputs {

    use QUIDPaymentsDatabase as Database;
    use QUIDPaymentsMeta as Meta;
    use QUIDHelperFunctions as Helpers;

    class Inputs {

        function quidButton($meta, $metaInputAndNotShortcode) {
            global $post;
            $nonce = wp_create_nonce( 'quid-cookie-nonce' );

            $blogTitle = Helpers\getSiteTitle();
            $blogTitleSlug = Helpers\getSiteTitleSlug();
            $productName = "";
            $productID = "";
            $productURL = "";
            $id = "";

            if ($metaInputAndNotShortcode) {
                if (isset($meta['id'])) {
                    $id = $meta['id'];
                    $post = get_post($id);
                } else {
                    $id = $post->ID;
                }
                $productName = Helpers\getPostTitle($post);
                $productID = Helpers\getPostSlug($post);
                $productURL = Helpers\getPostURL($post);
            } else {
                $productName = "Tip for " . $blogTitle;
                $productID = $blogTitleSlug."-tip";
                $productURL = get_site_url();
                $id = $blogTitleSlug;
            }

            $uniqId = uniqid();
            $currencyOption = get_option('quid-currency');
            if (isset($meta['align'])) {
                $alignOption = $meta['align'];
            } else {
                $alignOption = get_option('quid-align');
            }
            $justification = Helpers\buttonAlignment($alignOption);
            $requiredFields = ['price']; // Add required shortcode attributes to array

            if (!$meta) {
                $meta = [];
            }

            if (!isset($meta['type'])) $meta['type'] = 'Optional';
            if (!isset($meta['paid'])) $meta['paid'] = 'Thanks!';

            $meta['dom-id'] = $productID.'-'.$id.'-'.$uniqId;

            foreach ($requiredFields as $field) {
                if (!isset($meta[$field])) return "";
            }

            $quidTipClassAddition = "";
            if (($meta['type'] != "Required" && $metaInputAndNotShortcode) || !$metaInputAndNotShortcode) {
                $quidTipClassAddition = "quid-pay-tip";
            }

            $html = <<<HTML
                <div class="quid-pay-error-container" style="text-align: center; margin: 0px; display: none; justify-content: {$justification};">
                    <div id="quid-error-{$id}" class="quid-pay-error" style="display: inline-flex;">
                        <img class="quid-pay-error-image" src="https://js.quid.works/v1/assets/quid.png" />
                        <span>Payment validation failed</span>
                    </div>
                </div>
                <div id="quid-pay-buttons-{$meta['dom-id']}" class="quid-pay-buttons {$quidTipClassAddition} for-product-{$productID}" style="display: flex; justify-content: {$justification};">
HTML;
                    if ($meta['type'] == "Required") {
                        $html .= <<<HTML
                        <div id="{$meta['dom-id']}_free"
                            class="quid-pay-already-paid"
                            quid-amount="0"
                            quid-currency="{$currencyOption}"
                            quid-product-id="{$productID}"
                            quid-product-url="{$productURL}"
                            quid-product-name="{$productName}"
                            quid-product-description="{$blogTitle}"
                            style="display: inline-flex"
                        ></div>
HTML;
                    }

                    $html .= <<<HTML
                    <div id="{$meta['dom-id']}"
                        quid-amount="{$meta['price']}"
                        quid-currency="{$currencyOption}"
                        quid-product-id="{$productID}"
                        quid-product-url="{$productURL}"
                        quid-product-name="{$productName}"
                        quid-product-description="{$blogTitle}"
                        style="display: inline-flex"
                    ></div>
                </div>
HTML;

            $this->enqueueJS(
                'js_quid_button_'.$id.$uniqId,
                plugins_url( 'js/button.js?'.$id.$uniqId, __FILE__ ),
                array(
                    'content_url' => admin_url("admin-post.php?action=post-content&_wpnonce=".$nonce),
                    'post_id' => $id,
                    'meta_name' => $productName,
                    'meta_url' => $productURL,
                    'meta_id' => $productID,
                    'meta_domID' => $meta['dom-id'],
                    'meta_type' => $meta['type'],
                    'meta_price' => $meta['price'],
                    'meta_paid' => $meta['paid'],
                    'meta_currency' => $currencyOption,
                    'meta_readMore' => get_option('quid-read-more'),
                )
            );

            # If payment is required, add RESTORE PURCHASE button beside the pay button.
            if ($meta['type'] == "Required") {
                $purchaseCheckURL = admin_url("admin-post.php?action=purchase-check&_wpnonce=".$nonce);

                $this->enqueueJS(
                    'js_quid_button_required_'.$id.$uniqId,
                    plugins_url( 'js/buttonRestorePurchases.js?'.$id.$uniqId, __FILE__ ),
                    array(
                        'purchase_check_url' => $purchaseCheckURL,
                        'post_id' => $id,
                        'meta_name' => $productName,
                        'meta_id' => $productID,
                        'meta_domID' => $meta['dom-id'],
                        'meta_type' => $meta['type'],
                        'meta_paid' => $meta['paid'],
                        'meta_currency' => $currencyOption,
                        'meta_readMore' => get_option('quid-read-more'),
                    )
                );
            }

            return $html;
        }

        function quidSlider($meta, $metaInputAndNotShortcode) {
            global $post;
            $nonce = wp_create_nonce( 'quid-cookie-nonce' );

            $blogTitle = Helpers\getSiteTitle();
            $blogTitleSlug = Helpers\getSiteTitleSlug();
            $productName = "";
            $productID = "";
            $productURL = "";
            $id = "";

            if ($metaInputAndNotShortcode) {
                if (isset($meta['id'])) {
                    $id = $meta['id'];
                    $post = get_post($id);
                } else {
                    $id = $post->ID;
                }
                $productName = Helpers\getPostTitle($post);
                $productID = Helpers\getPostSlug($post);
                $productURL = Helpers\getPostURL($post);
            } else {
                $productName = "Tip for " . $blogTitle;
                $productID = $blogTitleSlug."-tip";
                $productURL = get_site_url();
                $id = $blogTitleSlug;
            }
            
            $uniqId = uniqid();
            $currencyOption = get_option('quid-currency');
            if (isset($meta['align'])) {
                $alignOption = $meta['align'];
            } else {
                $alignOption = get_option('quid-align');
            }
            $justification = Helpers\buttonAlignment($alignOption);
            $requiredFields = []; // Add required shortcode attributes to array

            if (!$meta) {
                $meta = [];
            }

            if (!isset($meta['type'])) $meta['type'] = 'Optional';
            if (!isset($meta['min'])) $meta['min'] = '0.01';
            if (!isset($meta['max'])) $meta['max'] = '10.00';
            if (!isset($meta['text'])) $meta['text'] = 'Give';
            if (!isset($meta['initial'])) $meta['initial'] = '1.00';
            if (!isset($meta['paid'])) $meta['paid'] = 'Thanks!';

            $meta['dom-id'] = $productID.'-'.$id.'-'.$uniqId;

            foreach ($requiredFields as $field) {
                if (!isset($meta[$field])) return "";
            }

            $quidTipClassAddition = "";
            if (($meta['type'] != "Required" && $metaInputAndNotShortcode) || !$metaInputAndNotShortcode) {
                $quidTipClassAddition = "quid-pay-tip";
            }

            $html = <<<HTML
                <div class="quid-pay-error-container" style="text-align: center; margin: 0px; display: none; justify-content: {$justification};">
                    <div id="quid-error-{$productID}" class="quid-pay-error" style="display: inline-flex;">
                        <img class="quid-pay-error-image" src="https://js.quid.works/v1/assets/quid.png" />
                        <span>Payment validation failed</span>
                    </div>
                </div>

                <div id="quid-pay-buttons-{$meta['dom-id']}" class="quid-pay-buttons {$quidTipClassAddition} for-product-{$productID}" style="display: flex; justify-content: {$justification};">
                    <div
                        id="{$meta['dom-id']}"
                        class="quid-slider"
                        quid-currency="{$currencyOption}"
                        quid-product-id="{$productID}"
                        quid-product-url="{$productURL}"
                        quid-product-name="{$productName}"
                        quid-product-description="{$blogTitle}"
                        quid-text="{$meta['text']}"
                        quid-text-paid="{$meta['paid']}"
                    ></div>
                </div>
HTML;

            wp_register_style( 'css_quid_init', plugins_url( 'css/init.css', __FILE__ ) );
            wp_enqueue_style( 'css_quid_init' );

            $this->enqueueJS(
                'js_quid_slider'.$id.$uniqId,
                plugins_url( 'js/slider.js?'.$id.$uniqId, __FILE__ ),
                array(
                    'content_url' => admin_url("admin-post.php?action=post-content&_wpnonce=".$nonce),
                    'post_id' => $id,
                    'meta_id' => $productID,
                    'meta_domID' => $meta['dom-id'],
                    'meta_initial' => $meta['initial'],
                    'meta_type' => $meta['type'],
                    'meta_text' => $meta['text'],
                    'meta_paid' => $meta['paid'],
                    'meta_description' => $blogTitle,
                    'meta_name' => $productName,
                    'meta_url' => $productURL,
                    'meta_min' => $meta['min'],
                    'meta_max' => $meta['max'],
                    'meta_currency' => $currencyOption,
                    'meta_readMore' => get_option('quid-read-more'),
                )
            );


            # If payment is required, add RESTORE PURCHASE button beside the pay button.
            if ($meta['type'] == "Required") {
                $purchaseCheckURL = admin_url("admin-post.php?action=purchase-check&_wpnonce=".$nonce);
                $this->enqueueJS(
                    'js_quid_button_required'.$id.$uniqId,
                    plugins_url( 'js/sliderRestorePurchases.js?'.$id.$uniqId, __FILE__ ),
                    array(
                        'purchase_check_url' => $purchaseCheckURL,
                        'post_id' => $id,
                        'meta_id' => $productID,
                        'meta_domID' => $meta['dom-id'],
                        'meta_type' => $meta['type'],
                        'meta_paid' => $meta['paid'],
                        'meta_description' => $blogTitle,
                        'meta_name' => $productName,
                        'meta_url' => $productURL,
                        'meta_currency' => $currencyOption,
                        'meta_readMore' => get_option('quid-read-more'),
                    )
                );
            }

            return $html;
        }

        function enqueueJS($fileIdentifier, $path, $data) {
            global $quidPluginVersion;
            wp_register_script( $fileIdentifier, $path.'&quid-version='.$quidPluginVersion );
            wp_localize_script( $fileIdentifier, 'dataJS', $data );
            wp_enqueue_script( $fileIdentifier );
        }
    }

}

?>