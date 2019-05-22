<?php

namespace QUIDPaymentsInit {

    class Init {

        public function addScripts() {
            wp_register_script( 'js_quid_client', 'https://js.quid.works/v1/client.js' );
            wp_enqueue_script( 'js_quid_client' );
            wp_register_script( 'js_quid_init', plugins_url( 'js/init.js', __FILE__ ) );
            wp_enqueue_script( 'js_quid_init' );

            wp_register_style( 'css_quid_client', 'https://js.quid.works/v1/assets/quid.css' );
            wp_enqueue_style( 'css_quid_client' );
            wp_register_style( 'css_quid_init', plugins_url( 'css/init.css', __FILE__ ) );
            wp_enqueue_style( 'css_quid_init' );
        }

    }

}

?>