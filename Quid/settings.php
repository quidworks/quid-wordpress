<?php

// https://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
add_action('admin_init', 'quidSettings');

// Gets rendered above the QUID setting fields on the Settings/General page
function quidSettingsCallback() {
    print_r("
        <style>
            .quid-setting {}
            .quid-setting input {
                width: 100%; padding: 5px; border-radius: 4px; 
                border: solid 1px rgba(0,0,0,0.2); margin-bottom: 10px;
                box-shadow
            }
            @media screen and (min-width: 901px) {
                .quid-setting {width: 500px;}
            }
            @media screen and (max-width: 900px) {
                .quid-setting {width: 100%;}
            }
        </style>
        <div>API Keys can be found on your <a target='_blank' href='https://app.quid.works/merchant'>QUID merchant page</a></div>
        <br/>
    ");
}

// This is the actual Setting field, the name must correspond to the first value in the add_settings_field
function renderPublicKeyField() {
    print_r("<div class='quid-setting'><input name='quid-publicKey' value='".get_option('quid-publicKey')."' placeholder='Public API Key' /></div>");
}
function renderSecretKeyField() {
    print_r("<div class='quid-setting'><input name='quid-secretKey' placeholder='Secret API Key' /></div>");
}

// This is the format the key needs to be in to verify the payment
function hashKey($data) {
    return base64_encode(hash('sha256', $data, true));
}

function quidSettings() {
    // section name - section title (displayed on page) - callback (print_r content displayed to user) - settings category
    // https://developer.wordpress.org/reference/functions/add_settings_section/
    add_settings_section('quid-settings-section','QUID Settings','quidSettingsCallback','general');

    // name attribute of rendered element - setting title (displayed on page) - 
    // callback (print_r content displayed to user) - setting category - first value of add_settings_section
    // https://developer.wordpress.org/reference/functions/add_settings_field/
    add_settings_field('quid-publicKey', 'Public Key', 'renderPublicKeyField', 'general', 'quid-settings-section');
    add_settings_field('quid-secretKey', 'Secret Key', 'renderSecretKeyField', 'general', 'quid-settings-section');

    // setting category - first value of add_settings_field - assoc array of options (one being sanitize_callback)
    // Whatever is returned by sanitize_callback is stored in the DB. If sanitize_callback isn't specified, whatever the user submits is saved in the DB
    // https://developer.wordpress.org/reference/functions/register_setting/
    register_setting('general', 'quid-publicKey', array('type' => 'string'));
    register_setting('general', 'quid-secretKey', array('type' => 'string', 'sanitize_callback' => 'hashKey'));
}

?>