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
            $currency = sanitize_text_field($_POST['currency']);

            if ($currency != '') {
                update_option('quid-currency', $currency);
            }

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

        function actionLinks( $links ) {
            $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=quid_settings') ) .'">Settings</a>';
            $links[] = '<a href="https://how.quid.works/en/collections/1780630-quid-wordpress-plugin" target="_blank">Documentation</a>';
            $links[] = '<a href="mailto:support@quid.works?subject=QUID Payments plugin support" target="_blank">Support</a>';
            return $links;
        }

        function renderSettings() {
            $quidPublicKey = get_option('quid-publicKey');
            $quidAlign = get_option('quid-align');
            $quidCurrency = get_option('quid-currency');

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

            switch ($quidCurrency) {
                case 'USD':
                    $quidUSD = "selected";
                    break;
                case 'CAD':
                    $quidCAD = "selected";
                    break;
                default:
                    $quidDefaultCurrency = "selected";
                    break;
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


            $html = <<<HTML
            <div class='quid-pay-settings'>
                <h1 class='quid-pay-settings-page-title'>QUID Settings</h1>

                <div class='quid-pay-settings-title'>Currency of your Merchant Account</div>
                <select id='quid-currency' class='quid-pay-settings-dropdown'>
HTML;
            
                $html .= '
                    <option disabled '.$quidDefaultCurrency.' value> -- Select your currency -- </option>
                    <option value="CAD" '.$quidCAD.'>CAD</option>
                    <option value="USD" '.$quidUSD.'>USD</option>
                ';

                $html .= <<<HTML
                </select>

                <div class='quid-pay-settings-subtitle'>API Keys can be found on your <a target='_blank' href='https://app.quid.works/merchant'>QUID merchant page</a></div>
                <input id='quid-publicKey' style='margin-bottom: 10px' value='{$quidPublicKey}' placeholder='Public API Key' /><br />
                <input id='quid-secretKey' type='password' placeholder='Secret API Key' />
                <p>secret key is not displayed to keep it extra safe</p>

                <div class='quid-pay-settings-title'>Default Button Alignment</div>
                <select id='quid-align' class='quid-pay-settings-dropdown quid-field-margin'>
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
                if(isset($_GET['quid-debug'])) {
            
                    $html .=  <<<HTML
                    <h1 class='quid-pay-settings-page-title'>QUID Payments Debugging Tools</h1>
                    <div class='quid-pay-settings-subtitle'>Test QUID payments database table</div>
HTML;

                    if(isset($_POST['quid-db-test'])){

                        $dbTestResult = testStorePurchase();
                        if($dbTestResult){

                            $html .=  <<<HTML
                            DB Connection Test: <font color="green"><strong>Passed</strong></font>
HTML;

                        } else {

                            global $wpdb;
                            $table_name = $wpdb->prefix . "quidPurchases";
                            $html .=  '
                            DB Connection Test: <font color="red"><strong>Failed</strong></font>
                            <p>The QUID Payments plugin was unable to write a test payment to your WordPress database table '.$table_name.'.</p>';

                        }

                    } else {

                        $html .=  <<<HTML
                        <form  method="post">
                            <input type="submit" name="quid-db-test" value="TEST" class="button button-primary">
                        </form>
HTML;
                    }

                }
            echo $html;
        }

        // This is the format the key needs to be in to verify the payment
        function hashKey($data) {
            return base64_encode(hash('sha256', $data, true));
        }

    }

}

?>
