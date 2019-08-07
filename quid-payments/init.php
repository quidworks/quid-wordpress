<?php

namespace QUIDPaymentsInit {

    class Init {

        public function addScripts() {
            global $quidPluginVersion;

            wp_register_script( 'js_quid_client', 'http://localhost:8082/dist/client.dev.js?quid-plugin='.$quidPluginVersion );
            wp_enqueue_script( 'js_quid_client' );
            wp_register_script( 'js_quid_init', plugins_url( 'js/init.js?quid-plugin='.$quidPluginVersion, __FILE__ ) );
            wp_enqueue_script( 'js_quid_init' );

            wp_register_style( 'css_quid_client', 'http://localhost:8082/assets/quid.css?quid-plugin='.$quidPluginVersion );
            wp_enqueue_style( 'css_quid_client' );
            wp_register_style( 'css_quid_init', plugins_url( 'css/init.css?quid-plugin='.$quidPluginVersion, __FILE__ ) );
            wp_enqueue_style( 'css_quid_init' );
        }

    }

}

?>