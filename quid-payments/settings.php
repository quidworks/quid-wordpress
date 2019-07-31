<?php

namespace QUIDPaymentsSettings {

    class Settings {

        function addScripts() {
            $nonce = wp_create_nonce( 'quid-settings-nonce' );
            $quidSettingsURL = admin_url('admin-post.php?action=quid-settings&_wpnonce='.$nonce);

            wp_register_style( 'css_quid_settings', plugins_url( 'css/settings.css', __FILE__ ) );
            wp_enqueue_style( 'css_quid_settings' );

            wp_register_script( 'quid_settings', plugins_url( 'js/settings.js', __FILE__ ) );
            $data = array(
                'settings_url' => $quidSettingsURL,
            );
            wp_localize_script( 'quid_settings', 'dataSettingsJS', $data );
            wp_enqueue_script( 'quid_settings' );
        }

        function saveSettings() {
            $nonce = $_REQUEST['_wpnonce'];
            if ( ! wp_verify_nonce( $nonce, 'quid-settings-nonce' ) ) {
                die( 'Security check' ); 
            }

            if (!isset($_POST['data'])) die('data value not set');

            $jsonString = str_replace('\\', '', $_POST['data']);
            $jsonAssoc = json_decode($jsonString, true);

            if (isset($jsonAssoc['quid-currency'])) {
                update_option('quid-currency', sanitize_text_field($jsonAssoc['quid-currency']));
                unset($jsonAssoc['quid-currency']);
            }

            if (isset($jsonAssoc['quid-key-public'])) {
                update_option('quid-publicKey', sanitize_text_field($jsonAssoc['quid-key-public']));
                unset($jsonAssoc['quid-key-public']);
            }

            if (isset($jsonAssoc['quid-key-secret'])) {
                update_option('quid-secretKey', sanitize_text_field($jsonAssoc['quid-key-secret']));
                unset($jsonAssoc['quid-key-secret']);
            }

            if (isset($jsonAssoc['quid-button-position'])) {
                $align = strtolower($jsonAssoc['quid-button-position']);
                if ($align == 'centre') {
                    $align = 'center';
                }
                update_option('quid-align', sanitize_text_field($align));
                unset($jsonAssoc['quid-button-position']);
            }

            update_option('quid-fab-options', sanitize_text_field(json_encode($jsonAssoc)));

            echo 'success';
        }

        function addMenuPage() {
            add_submenu_page( 'options-general.php', 'QUID Settings', 'QUID Settings', 'manage_options', 'quid_settings', array($this, 'renderSettings') );
        }

        function actionLinks( $links ) {
            $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=quid_settings') ) .'">Settings</a>';
            $links[] = '<a href="https://how.quid.works/en/collections/1780630-quid-wordpress-plugin" target="_blank">Documentation</a>';
            $links[] = '<a href="mailto:support@quid.works?subject=QUID Payments plugin support" target="_blank">Support</a>';
            return $links;
        }

        function testStorePurchase() {
            global $wpdb;
            $table_name = $wpdb->prefix . "quidPurchases";

            $wpdb->insert( 
                $table_name, 
                array(
                    'time' => current_time( 'mysql' ), 
                    'user' => 'test-user',
                    'product-id' => 'test-product-id',
                    'tip' => 'true',
                ) 
            );
            if($wpdb->last_error !== '') {
                echo $wpdb->last_error;
                return false;
            }
            return true;
        }

        function renderSettings() {
            $quidPublicKey = get_option('quid-publicKey');
            $quidAlign = get_option('quid-align');
            $quidCurrency = get_option('quid-currency');
            $quidFabSettings = json_decode(get_option('quid-fab-options'), true);
            include('settingshtml.php');
        }

        // This is the format the key needs to be in to verify the payment
        function hashKey($data) {
            return base64_encode(hash('sha256', $data, true));
        }

    }

}

?>
