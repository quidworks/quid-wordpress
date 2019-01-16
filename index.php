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
            <button 
                onclick="quidPay(this)"
                quid-amount="'.$atts["price"].'"
                quid-currency="CAD"
                quid-product-id="'.$atts["id"].'"
                quid-product-url="'.$atts["url"].'"
                quid-product-name="'.$atts["name"].'"
                quid-product-description="'.$atts["description"].'"
                class="quid-pay-button"
            >
                <div class="quid-pay-button-flex"><div class="quid-pay-button-icon">
                    <img src="https://js.quid.works/v1/assets/quid-button.png" />
                </div><div class="quid-pay-button-price">'.$atts["price"].' CAD</div></div>
            </button>
            <button 
                onclick="quidPay(this)"
                quid-amount="0"
                quid-currency="CAD"
                quid-product-id="'.$atts["id"].'"
                quid-product-url="'.$atts["url"].'"
                quid-product-name="'.$atts["name"].'"
                quid-product-description="'.$atts["description"].'"
                class="quid-pay-button"
            >
                <div class="quid-pay-button-flex"><div class="quid-pay-button-icon">
                <img src="https://js.quid.works/v1/assets/quid-button.png" />
                </div><div class="quid-pay-button-price">Already Paid?</div></div>
            </button>
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