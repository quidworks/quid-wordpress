<?

/*
Plugin Name: QUID Article
Description: Fetches article once user has paid.
Version: 0.1.0
Author: QUID
Author URI: https://quid.works
*/

/* ^^^^^ The above content is needed to show the plugin on the user's plugin page */

/* --------------------- PAYMENT --------------------- */

// https://codex.wordpress.org/Plugin_API/Action_Reference/admin_post_(action)
add_action( 'admin_post_nopriv_quid-article', 'paymentCallback' );
add_action( 'admin_post_quid-article', 'paymentCallback' );

function paymentCallback() {
    // gets POST content
    $json = json_decode(file_get_contents('php://input'));
    if (!validatePaymentResponse($json->paymentResponse)) {
        print_r("denied");
        return;
    };
    print_r(fetchContent($json->postTitle));
}

// https://how.quid.works/developer/verifying-payments
function validatePaymentResponse($paymentResponse) {
    $infoArray = [
        $paymentResponse->id,
        $paymentResponse->userHash,
        $paymentResponse->merchantID,
        $paymentResponse->productID,
        $paymentResponse->currency,
        $paymentResponse->amount,
        $paymentResponse->tsUnix,
    ];
    $payload = implode(',', $infoArray);
    // $secret must be formatted as follows: base64_encode(hash('sha256', SECRET_KEY_HERE, true));
    // ours is already formatted in the settings section of this file
    $secret = get_option('quid-secretKey');
    $sig = base64_encode(hash_hmac('sha256', $payload, $secret, true));

    return ($sig == $paymentResponse->sig);
}

function fetchContent($postTitle) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT post_content FROM {$wpdb->dbname}.wp_posts WHERE post_status = 'private' AND post_title = '%s' ORDER BY ID DESC LIMIT 1", $postTitle);
    $results = $wpdb->get_results( $sql, ARRAY_N );
    return $results[0][0];
}

/* --------------------- INITIALIZATION --------------------- */

// This is included in the head of every wordpress page
add_action( 'wp_head', 'quidInit' );

function quidInit() {
    print_r("
        <script src='http://localhost:8082/dist/client.bundle.js'></script>
        <link rel='stylesheet' type='text/css' href='http://localhost:8082/demos/quid.css' />
        <style>
        .wp-quid-error {
            font-family: sans-serif;
            font-size: 16px;
            padding: 10px 0px;
            color: rgba(255,0,0,0.8);
        }
        </style>
        <script>
            function fetchContent(res) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        let target = document.getElementById(_quid_wp_global[res.productID].target);
                        let validationErrorNode = document.getElementById('paymentValidationError');
                        if (validationErrorNode) {
                            target.removeChild(validationErrorNode);
                        }
                        if (xhttp.responseText === 'denied') {
                            validationErrorNode = document.createElement('DIV');
                            validationErrorNode.id = 'paymentValidationError';
                            validationErrorNode.classList.add('wp-quid-error');
                            validationErrorNode.innerHTML = 'Payment Validation Failed';
                            target.appendChild(validationErrorNode);
                        } else {
                            target.innerHTML = target.innerHTML + ' ' + xhttp.responseText;
                        }
                    }
                };
                xhttp.open('POST', '/wordpress/wp-admin/admin-post.php?action=quid-article', true);
                xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhttp.send(JSON.stringify({postTitle: _quid_wp_global[res.productID].title, paymentResponse: res}));
            }
            _quid_wp_global = {};
            quid.autoInit({baseURL: 'http://localhost:3000', apiKey: '".get_option('quid-publicKey')."', onPaymentSuccess: fetchContent});
        </script>
    ");
}

/* --------------------- PAY BUTTON --------------------- */

// The shortcode is [quidButton id="uh893h3d" etc etc] that user places on the frontend and is replaced by the function listed
// shortcode name - callback
add_shortcode('quidButton', 'quidButton');

// eg. [quidButton price="0.10" url="/when-darkness-overspreads-my-eyes/" id="udew7hui8" name="It Wasn't a Dream" description="Franz' article on News Post York" target="wasntADream" title="IT WASN'T A DREAM"]
// The target field is the id of the element in which you want the content to be concatenated
// The TITLE FIELD NEEDS TO BE THE TITLE OF THE ADDITIONAL CONTENT POST, otherwise it can't find it in the DB

function isTitleFoundInDB($postTitle) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT post_content FROM {$wpdb->dbname}.wp_posts WHERE post_status = 'private' AND post_title = '%s' ORDER BY ID DESC LIMIT 1", $postTitle);
    $results = $wpdb->get_results( $sql, ARRAY_N );
    return sizeof($results) > 0;
}

function quidButton($atts) {
    global $wp;
    if (!isTitleFoundInDB($atts["title"])) {
        return ("
            <div class='wp-quid-error'>Title in shortcode not found in database</div>
            <script>
                console.log('QUID-PLUGIN: The title field within the shortcode must be the same as the title of the private post that contains the restricted content');
            </script>
        ");
    }
    return (
        '<div 
            class="quid-button"
            quid-amount="'.$atts["price"].'"
            quid-currency="USD"
            quid-product-id="'.$atts["id"].'"
            quid-product-url="'.$atts["url"].'"
            quid-product-name="'.$atts["name"].'"
            quid-product-description="'.$atts["description"].'">
        </div>
        <script>
            _quid_wp_global["'.$atts["id"].'"] = {title: "'.$atts["title"].'", target: "'.$atts["target"].'"};
        </script>'
    );
}

/* --------------------- SETTINGS --------------------- */

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
    print_r("<div class='quid-setting'><input name='quid-publicKey' placeholder='Public API Key' /></div>");
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