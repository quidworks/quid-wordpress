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
        print_r("validation failed");
        return;
    };
    setcookie( "quidUserHash", $json->paymentResponse->userHash, time() + (86400 * 30), "/" );

    $cost = (float)$json->paymentResponse->amount;
    if ($cost != 0.000000000) {
        if (!storePurchase(
            $json->paymentResponse->userHash,
            $json->paymentResponse->productID
        )) {
            print_r("database error");
            return;
        };
    } else {
        if (!checkIfPurchasedAlready(
            $json->paymentResponse->userHash,
            $json->paymentResponse->productID
        )) {
            print_r("unpurchased");
            return;
        }
    }
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

function checkIfPurchasedAlready($userHash, $productID) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT user FROM {$wpdb->dbname}.{$wpdb->prefix}quidPurchases WHERE user = '%s' AND `product-id` = '%s' ORDER BY ID DESC LIMIT 1", $userHash, $productID);
    $results = $wpdb->get_results( $sql, ARRAY_N );
    return sizeof($results) > 0;
}

function storePurchase($userHash, $productID) {
    global $wpdb;
    $table_name = $wpdb->prefix . "quidPurchases";

    $wpdb->insert( 
        $table_name, 
        array(
            'time' => current_time( 'mysql' ), 
            'user' => $userHash,
            'product-id' => $productID,
        ) 
    );
    if($wpdb->last_error !== '') {
        echo $wpdb->last_error;
        return false;
    }
    return true;
}

function fetchContent($postTitle) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT post_content FROM {$wpdb->dbname}.{$wpdb->prefix}posts WHERE post_status = 'private' AND post_title = '%s' ORDER BY ID DESC LIMIT 1", $postTitle);
    $results = $wpdb->get_results( $sql, ARRAY_N );
    return $results[0][0];
}

/* --------------------- DATABASE --------------------- */

function hasPurchasedAlready($userHash, $productID) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT `product-id` FROM {$wpdb->dbname}.{$wpdb->prefix}quidPurchases WHERE user = '%s' AND `product-id`='%s' LIMIT 1", $userHash, $productID);
    $results = $wpdb->get_results( $sql, ARRAY_N );
    return sizeof($results) > 0;
}

// https://codex.wordpress.org/Creating_Tables_with_Plugins
register_activation_hook( __FILE__, 'createPurchaseDatabase' );

function createPurchaseDatabase() {
    global $wpdb;
    $table_name = $wpdb->prefix . "quidPurchases";

    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE ".$table_name." (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            `user` VARCHAR(60) NOT NULL,
            `product-id` VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        );";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

/* --------------------- INITIALIZATION --------------------- */

// This is included in the head of every wordpress page
add_action( 'wp_head', 'quidInit' );

function quidInit() {
    print_r("
        <script src='http://localhost:8082/dist/client.bundle.js'></script>
        <link rel='stylesheet' type='text/css' href='http://localhost:8082/demos/quid.css' />
        <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Barlow:400' />
        <style>
        .wp-quid-error {
            font-family: sans-serif;
            font-size: 15px;
            font-weight: bold;
            display: flex;
            align-items: center;
            color: rgba(0,0,0,0.6);
            border-radius: 4px;
            padding: 10px 20px;
            margin-top: 10px!important;
            background-color: #eee;
            box-shadow: 0px 0px 1px 1px rgba(0,0,0,0.2);
        }
        .wp-quid-error-image {
            height: 25px;
            margin-right: 20px;
        }
        </style>
        <script>_quid_wp_global = {};</script>
    ");
}

/* --------------------- FOOTER ------------------------ */

add_action( 'wp_footer', 'quidFooter' );

function quidFooter() {
    print_r("
        <script>
        function quidFetchContent(res) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    let target = document.getElementById(_quid_wp_global[res.productID].target);
                    let postPayButtons = target.getElementsByClassName('quid-button');
                    let validationErrorNode = target.getElementsByClassName('wp-quid-error')[0];
                    if (validationErrorNode) {
                        target.removeChild(validationErrorNode);
                    }
                    let errorReturned = '';
                    switch (xhttp.responseText) {
                        case 'validation failed':
                            errorReturned = 'Payment failed to go through';
                            break;
                        case 'database error':
                            errorReturned = 'database error';
                            break;
                        case 'unpurchased':
                            errorReturned = 'You have not bought this yet';
                            if (postPayButtons.length > 1) postPayButtons[1].style.display = 'none';
                            break;
                    }
                    if (errorReturned !== '') {
                        validationErrorNode = document.createElement('DIV');
                        validationErrorNode.id = 'paymentValidationError';
                        validationErrorNode.classList.add('wp-quid-error');

                        let validationErrorNodeImage = document.createElement('IMG');
                        validationErrorNodeImage.setAttribute('src', 'data:image/png;base64,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');
                        validationErrorNodeImage.classList.add('wp-quid-error-image');

                        validationErrorNode.appendChild(validationErrorNodeImage);
                        validationErrorNode.innerHTML += errorReturned;
                        target.appendChild(validationErrorNode);
                    } else {
                        for (let x = 0; x < postPayButtons.length; x += 1) {
                            postPayButtons[x].style.display = 'none';
                        }
                        target.innerHTML = target.innerHTML + ' ' + xhttp.responseText;
                    }
                }
            };
            xhttp.open('POST', '/wordpress/wp-admin/admin-post.php?action=quid-article', true);
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhttp.send(JSON.stringify({postTitle: _quid_wp_global[res.productID].title, paymentResponse: res}));
        }
        function quidPay(el) {
            quidInstance.requestPayment({
                productID: el.getAttribute('quid-product-id'),
                productURL: el.getAttribute('quid-product-url'),
                productName: el.getAttribute('quid-product-name'),
                productDescription: el.getAttribute('quid-product-description'),
                price: parseFloat(el.getAttribute('quid-amount')),
                currency: el.getAttribute('quid-currency'),
                successCallback: quidFetchContent,
            });
        }
        const quidInstance = new quid.Quid({onLoad: () => {}, baseURL: 'http://localhost:3000', apiKey: '".get_option('quid-publicKey')."'});
        quidInstance.install();
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
    $sql = $wpdb->prepare("SELECT ID FROM {$wpdb->dbname}.{$wpdb->prefix}posts WHERE post_status = 'private' AND post_title = '%s' ORDER BY ID DESC LIMIT 1", $postTitle);
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
    if (isset($_COOKIE["quidUserHash"])) {
        if (hasPurchasedAlready($_COOKIE["quidUserHash"], $atts["id"])) {
            return fetchContent($atts["title"]);
        }
    }

    return ('
        <div>
            <div style="display: inline-block" 
                class="quid-button"
                onclick="quidPay(this)"
                quid-amount="'.$atts["price"].'"
                quid-currency="CAD"
                quid-product-id="'.$atts["id"].'"
                quid-product-url="'.$atts["url"].'"
                quid-product-name="'.$atts["name"].'"
                quid-product-description="'.$atts["description"].'">
                    <button class="quid-pay-button">
                        <div class="quid-pay-button-flex"><div class="quid-pay-button-icon">
                        <img src="data:image/png;base64,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" />
                        </div><div class="quid-pay-button-price">'.$atts["price"].' CAD</div></div>
                    </button>
            </div>
            <div style="display: inline-block"
                class="quid-button"
                onclick="quidPay(this)"
                quid-amount="0"
                quid-currency="CAD"
                quid-product-id="'.$atts["id"].'"
                quid-product-url="'.$atts["url"].'"
                quid-product-name="'.$atts["name"].'"
                quid-product-description="'.$atts["description"].'">
                <button class="quid-pay-button">
                    <div class="quid-pay-button-flex"><div class="quid-pay-button-icon">
                    <img src="data:image/png;base64,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" />
                    </div><div class="quid-pay-button-price">Already Paid?</div></div>
                </button>
            </div>
        </div>
        <script>
            _quid_wp_global["'.$atts["id"].'"] = {title: "'.$atts["title"].'", target: "'.$atts["target"].'"};
        </script>
    ');
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