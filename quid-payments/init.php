<?php

// <script src='https://js.quid.works/v1/client.js'></script>
// <link rel='stylesheet' type='text/css' href='https://js.quid.works/v1/assets/quid.css' />
// <script src='http://localhost:8082/dist/client.dev.js'></script>
// <link rel='stylesheet' type='text/css' href='http://localhost:8082/assets/quid.css' />

// quid.works.client.js
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