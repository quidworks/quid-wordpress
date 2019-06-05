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
            $align = sanitize_text_field($_POST['align']);

            if ($public !== '') {
                update_option('quid-publicKey', $public);
            }
            if ($secret !== '') {
                update_option('quid-secretKey', $this->hashKey($secret));
            }

            $align = strtolower($align);
            if ($align == 'centre') {
                $align = 'center';
            }

            update_option('quid-align', $align);

            echo 'success';
        }

        function addMenuPage() {
            add_submenu_page( 'options-general.php', 'QUID Settings', 'QUID Settings', 'manage_options', 'quid_settings', array($this, 'renderSettings') );
        }

        function renderSettings() {
            $quidPublicKey = get_option('quid-publicKey');
            $quidAlign = get_option('quid-align');

            $quidAlignLeft = "";
            $quidAlignRight = "";
            $quidAlignCenter = "";

            switch ($quidAlign) {
                case 'center':
                    $quidAlignCenter = "selected";
                    break;
                case 'left':
                    $quidAlignLeft = "selected";
                    break;
                default:
                    $quidAlignRight = "selected";
                    break;
            }


            $html = <<<HTML
            <div class='quid-pay-settings'>
                <h1 class='quid-pay-settings-page-title'>QUID Settings</h1>

                <div class='quid-pay-settings-subtitle'>API Keys can be found on your <a target='_blank' href='https://app.quid.works/merchant'>QUID merchant page</a></div>
                <input id='quid-publicKey' style='margin-bottom: 10px' value='{$quidPublicKey}' placeholder='Public API Key' /><br />
                <input id='quid-secretKey' type='password' placeholder='Secret API Key' />
                <p>secret key is not displayed to keep it extra safe</p>

                <div class='quid-pay-settings-title'>Default Button Alignment</div>
                <select id='quid-align' class='quid-pay-settings-dropdown'>
HTML;
            
                $html .= '
                    <option value="right" '.$quidAlignRight.'>Right</option>
                    <option value="center" '.$quidAlignCenter.'>Center</option>
                    <option value="left" '.$quidAlignLeft.'>Left</option>
                ';

                $html .= <<<HTML
                </select>

                <div><button class="button button-primary" type='button' onclick='submitQuidSettings()'>Save</button></div>
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