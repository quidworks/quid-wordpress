<?php

add_action( 'admin_post_nopriv_quid-settings', 'saveSettings' );
add_action( 'admin_post_quid-settings', 'saveSettings' );

add_action('admin_menu', 'addMenuPage');

function saveSettings() {
    if ($_POST['public'] !== '') {
        update_option('quid-publicKey', $_POST['public']);
    }
    if ($_POST['secret'] !== '') {
        update_option('quid-secretKey', hashKey($_POST['secret']));
    }
    echo 'success';
}

function addMenuPage() {
    add_submenu_page( 'options-general.php', 'QUID Settings', 'QUID Settings', 'manage_options', 'quid_settings', 'renderSettings' );
}

function renderSettings() {
    global $wpRoot;
    $html = "
    <style>
        .quid-settings {
            margin-top: 30px;
        }
        .quid-settings-response {
            padding-left: 5px;
        }
        .quid-settings input {
            width: 400px;
            max-width: 100%;
            padding: 5px; border-radius: 4px; 
            border: solid 1px rgba(0,0,0,0.2);
        }
        .quid-settings p {
            margin-top: 0px;
            padding: 0px 5px;
            opacity: 0.7;
            font-size: 12px;
        }
        .quid-settings-title {
            font-size: 24px;
        }
        .quid-settings-subtitle {
            font-size: 15px;
            margin: 25px 0px;
        }
    </style>
    <div class='quid-settings'>
        <div class='quid-settings-title'>QUID Settings</div>
        <div class='quid-settings-subtitle'>API Keys can be found on your <a target='_blank' href='https://app.quid.works/merchant'>QUID merchant page</a></div>
        <input id='quid-publicKey' style='margin-bottom: 10px' value='".get_option('quid-publicKey')."' placeholder='Public API Key' /><br />
        <input id='quid-secretKey' type='password' placeholder='Secret API Key' />
        <p>secret key is not displayed to keep it extra safe</p>
        <button type='button' onclick='submitQuidSettings()'>Save</button>
        <span class='quid-settings-response'></span>
    </div>
    <script>
    function submitQuidSettings() {
        let public = document.getElementById('quid-publicKey').value;
        let secret = document.getElementById('quid-secretKey').value;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                const messageOutput = document.getElementsByClassName('quid-settings-response')[0];
                if (xhttp.responseText === 'success') {
                    messageOutput.innerHTML = 'Success';
                } else {
                    messageOutput.innerHTML = 'Something went wrong';
                }
            }
        }
        xhttp.open('POST', '".$wpRoot."/wp-admin/admin-post.php?action=quid-settings', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send('public='+public+'&secret='+secret);
    }
    </script>
    ";
    echo $html;
}

// Gets rendered above the QUID setting fields on the Settings/General page
function quidSettingsCallback() {
    print_r("
        <style>
            .quid-setting {}
            .quid-setting input {
                width: 100%; padding: 5px; border-radius: 4px; 
                border: solid 1px rgba(0,0,0,0.2); margin-bottom: 10px;
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

?>