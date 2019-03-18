<?php

namespace QUIDPaymentsFooter {

    class Footer {

        function js() {
            global $baseURL;

            $publicKey = get_option("quid-publicKey");
            $nonce = wp_create_nonce( 'quid-payment-nonce' );
            $quidArticleURL = admin_url('admin-post.php?action=quid-article&_wpnonce='.$nonce);
            $quidTipURL = admin_url('admin-post.php?action=quid-tip&_wpnonce='.$nonce);

            wp_register_script( 'quid_index', plugins_url( 'js/index.js', __FILE__ ) );

            $data = array(
                'public_key' => get_option("quid-publicKey"),
                'article_url' => $quidArticleURL,
                'tip_url' => $quidTipURL,
                'base_url' => $baseURL,
            );
            wp_localize_script( 'quid_index', 'dataIndexJS', $data );

            wp_enqueue_script( 'quid_index' );
        }

    }

}

?>