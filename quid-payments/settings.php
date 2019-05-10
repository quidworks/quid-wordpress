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

            $public = sanitize_text_field($_POST['public']);
            $secret = sanitize_text_field($_POST['secret']);

            if ($public !== '') {
                update_option('quid-publicKey', $public);
            }
            if ($secret !== '') {
                update_option('quid-secretKey', $this->hashKey($secret));
            }
            echo 'success';
        }

        function addMenuPage() {
            add_submenu_page( 'options-general.php', 'QUID Settings', 'QUID Settings', 'manage_options', 'quid_settings', array($this, 'renderSettings') );
        }

        function renderSettings() {
            $quidPublicKey = get_option('quid-publicKey');

            $html = <<<HTML
            <div class='quid-pay-settings'>
                <div class='quid-pay-settings-title'>QUID Settings</div>
                <div class='quid-pay-settings-subtitle'>API Keys can be found on your <a target='_blank' href='https://app.quid.works/merchant'>QUID merchant page</a></div>
                <div id='quid-connect-container'></div>
                <input id='quid-publicKey' style='margin-bottom: 10px' value='{$quidPublicKey}' placeholder='Public API Key' /><br />
                <input id='quid-secretKey' type='password' placeholder='Secret API Key' />
                <p>secret key is not displayed to keep it extra safe</p>
                <button type='button' onclick='submitQuidSettings()'>Save</button>
                <span class='quid-pay-settings-response'></span>
            </div>
HTML;
            echo $html;
        }

        // This is the format the key needs to be in to verify the payment
        function hashKey($data) {
            return base64_encode(hash('sha256', $data, true));
        }

    }

}

?>