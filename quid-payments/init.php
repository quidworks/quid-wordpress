<?php

namespace QUIDPaymentsInit {

    class Init {

        public function addScripts() {
            wp_register_script( 'js_quid_client', 'http://192.168.0.20:8082/dist/client.dev.js' );
            wp_enqueue_script( 'js_quid_client' );
            wp_register_script( 'js_quid_init', plugins_url( 'js/init.js', __FILE__ ) );
            wp_enqueue_script( 'js_quid_init' );

            wp_register_style( 'css_quid_client', 'http://192.168.0.20:8082/assets/quid.css' );
            wp_enqueue_style( 'css_quid_client' );
            wp_register_style( 'css_quid_init', plugins_url( 'css/init.css', __FILE__ ) );
            wp_enqueue_style( 'css_quid_init' );
        }

    }

}

?>