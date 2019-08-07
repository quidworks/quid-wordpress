<?php

namespace QUIDPaymentsFooter {

    use QUIDHelperFunctions as Helpers;

    class Footer {

        function js() {
            global $baseURL;
            global $quidPluginVersion;

            $publicKey = get_option("quid-publicKey");
            $nonce = wp_create_nonce( 'quid-payment-nonce' );
            $quidArticleURL = admin_url('admin-post.php?action=quid-article&_wpnonce='.$nonce);
            $quidTipURL = admin_url('admin-post.php?action=quid-tip&_wpnonce='.$nonce);

            wp_register_script( 'quid_index', plugins_url( 'js/index.js?quid-plugin='.$quidPluginVersion, __FILE__ ));
            $data = array(
                'public_key' => get_option("quid-publicKey"),
                'article_url' => $quidArticleURL,
                'tip_url' => $quidTipURL,
                'base_url' => $baseURL,
            );
            wp_localize_script( 'quid_index', 'dataIndexJS', $data );
            wp_enqueue_script( 'quid_index' );

            if (get_option('quid-fab-enabled') === "true") {

                $fabOptions = json_decode(get_option('quid-fab-options'), true);
                
                wp_register_script( 'js_quid_fab', plugins_url( 'js/fab.js?quid-plugin='.$quidPluginVersion, __FILE__ ) );
                wp_localize_script( 'js_quid_fab', 'dataJS', array(
                    'tip_url' => $quidTipURL,
                    'baseURL' => $baseURL,
                    'apiKey' => get_option("quid-publicKey"),
                    'amount' => $fabOptions['quid-fab-initial'],
                    'text' => $fabOptions['quid-fab-text'],
                    'paid' => $fabOptions['quid-fab-paid'],
                    'id' => 'id_'.Helpers\getSiteTitle(),
                    'description' => get_option("quid-fab-description"),
                    'name' => Helpers\getSiteTitle(),
                    'url' => site_url(),
                    'min' => $fabOptions['quid-fab-min'],
                    'max' => $fabOptions['quid-fab-max'],
                    'currency' => get_option('quid-currency'),
                    'demo' => $fabOptions['quid-fab-demo'],
                    'palette' => $fabOptions['quid-fab-palette'],
                    'reminder' => $fabOptions['quid-fab-reminder'],
                    'position' => $fabOptions['quid-fab-position'],
                ));
                wp_enqueue_script( 'js_quid_fab' );

            }
        }

    }

}

?>