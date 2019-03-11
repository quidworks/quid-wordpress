<?php

add_action( 'admin_post_nopriv_purchase-check', 'returnUserCookie' );
add_action( 'admin_post_purchase-check', 'returnUserCookie' );

function returnUserCookie() {
    
    $purchased = false;
    $userCookie = '';

    if (isset($_COOKIE["quidUserHash"])) {
        $userCookie = $_COOKIE["quidUserHash"];
    } else {
        echo '';
        return;
    }

    if (hasPurchasedAlready($userCookie, $_POST["productID"])) {
        $purchased = true;
    } else {
        echo '';
        return;
    }

    if ($purchased) {
        echo wpautop(get_post_field('post_content', $_POST["postID"]));
    } else {
        echo '';
        return;
    }
}

function quidButton() {
    global $post;
    global $wpRoot;
    $meta = getMetaFields($post);
    $html = "";

    $html .= <<<HTML
        <div style="width: 100%;" id="post-content-{$meta->id}">
HTML;

    if ($meta->type == "Required") {
        $html .= <<<HTML
            <p id="{$post->ID}-excerpt">$post->post_excerpt</p>
HTML;
    }

    $alreadyPaidButtonHTML = '';
    $alreadyPaidButtonJS = '';

    if ($meta->type == "Required") {
        $alreadyPaidButtonHTML .= <<<HTML
        <div id="{$meta->id}_free"
            class="already-paid"
            quid-amount="0"
            quid-currency="CAD"
            quid-product-id="{$meta->id}"
            quid-product-url="{$meta->url}"
            quid-product-name="{$meta->name}"
            quid-product-description="{$meta->description}"
            style="display: inline-flex"
        ></div>
HTML;
    }

    if ($meta->type == "Required") {
        $alreadyPaidButtonJS = <<<HTML
        <script>
            qButton = quid.createButton({
                amount: "0",
                currency: "CAD",
                theme: "quid",
                palette: "default",
                text: "Already Paid",
            });

            qButton.setAttribute("onclick", "quidPay(this, true)");
            document.getElementById("{$meta->id}_free").appendChild(qButton);
        </script>
HTML;
    }

    $html .= <<<HTML
        <div class="quid-pay-error" style="text-align: center; margin: 0px; display: none;">
            <div id="quid-error-{$post->ID}" class="wp-quid-error" style="display: inline-flex;">
                <img class="wp-quid-error-image" src="https://js.quid.works/v1/assets/quid.png" />
                Payment validation failed
            </div>
        </div>
        <div class="quid-pay-buttons">
HTML;
            $html .= $alreadyPaidButtonHTML;

            $html .= <<<HTML
            <div id="{$meta->id}"
                quid-amount="{$meta->price}"
                quid-currency="CAD"
                quid-product-id="{$meta->id}"
                quid-product-url="{$meta->url}"
                quid-product-name="{$meta->name}"
                quid-product-description="{$meta->description}"
                style="display: inline-flex"
            ></div>
        </div>
    </div>

    <script>
        _quid_wp_global["{$meta->id}"] = {
            postid: "{$post->ID}",
            required: "{$meta->type}",
            paidText: "{$meta->paid}",
            target: "post-content-{$meta->id}"
        };

        qButton = quid.createButton({
            amount: "{$meta->price}",
            currency: "CAD",
            theme: "quid",
            palette: "default",
            text: "Pay $$meta->price",
        });

        qButton.setAttribute("onclick", "quidPay(this)");
        document.getElementById("{$meta->id}").appendChild(qButton);
    </script>
HTML;

    if ($meta->type == "Required") {
        $html .= <<<HTML
            <script>
                (function() {
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            if (xhttp.responseText !== '') {
                                document.getElementById(`post-content-{$meta->id}`).innerHTML = xhttp.responseText;
                            }
                        }
                    }
                    xhttp.open('POST', '{$wpRoot}/wp-admin/admin-post.php?action=purchase-check', true);
                    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhttp.send("postID={$post->ID}&productID={$meta->id}");
                })();
            </script>
HTML;
    }
    
    $html .= $alreadyPaidButtonJS;

    return $html;
}

function quidSlider($atts) {
    global $post;
    global $wpRoot;
    $meta = getMetaFields($post);
    $html = "";

    $html .= <<<HTML
    <div style="width: 100%;" id="post-content-{$meta->id}">
        <p id="{$post->ID}-excerpt">$post->post_excerpt</p>
        <div class="quid-pay-error" style="text-align: center; margin: 0px; display: none;">
            <div id="quid-error-{$post->ID}" class="wp-quid-error" style="display: inline-flex;">
                <img class="wp-quid-error-image" src="https://js.quid.works/v1/assets/quid.png" />
                Payment validation failed
            </div>
        </div>

        <div class="quid-pay-buttons">
            <div
                id="{$meta->id}"
                class="quid-slider"
                quid-currency="CAD"
                quid-product-id="{$meta->id}"
                quid-product-url="{$meta->url}"
                quid-product-name="{$meta->name}"
                quid-product-description="{$meta->description}"
                quid-text="{$meta->text}"
                quid-text-paid="{$meta->paid}"
            ></div>
        </div>
    </div>
    <script>
        baseElement = document.getElementById("{$meta->id}");
        qSlider = quid.createSlider({
            minAmount: "{$meta->min}",
            maxAmount: "{$meta->max}",
            element: baseElement,
            theme: "quid",
            text: "{$meta->text}",
            currency: "CAD",
            amount: "{$meta->initial}",
        });
        qSlider.getElementsByClassName("quid-pay-button")[0].setAttribute("onclick", "quidPay(baseElement)");
HTML;

    if ($meta->type == 'Required') {
        $html .= <<<HTML
            alreadyPaid = document.createElement("DIV");
            alreadyPaid.setAttribute("id", "{$meta->id}_free");
            alreadyPaid.setAttribute("quid-amount", "0");
            alreadyPaid.setAttribute("class", "already-paid");
            alreadyPaid.setAttribute("quid-currency", "CAD");
            alreadyPaid.setAttribute("quid-product-id", "{$meta->id}");
            alreadyPaid.setAttribute("quid-product-url", "{$meta->url}");
            alreadyPaid.setAttribute("quid-product-name", "{$meta->name}");
            alreadyPaid.setAttribute("quid-product-description", "{$meta->description}");

            qSlider.getElementsByClassName("quid-slider-button-flex")[0].prepend(alreadyPaid);

            qButton = quid.createButton({
                amount: "0",
                currency: "CAD",
                theme: "quid",
                palette: "default",
                text: "Already Paid",
            });
    
            qButton.setAttribute("onclick", "quidPay(this, true)");
            document.getElementById("{$meta->id}_free").prepend(qButton);
            let alreadyPaidButton = alreadyPaid.getElementsByClassName("quid-pay-button")[0];
            alreadyPaidButton.style.display = "block";
HTML;
    }

    $html .= <<<HTML
        _quid_wp_global["{$meta->id}"] = {
            postid: "{$post->ID}",
            required: "{$meta->type}",
            paidText: "{$meta->paid}",
            target: "post-content-{$meta->id}"
        };
        </script>
HTML;

    if ($meta->type == "Required") {
        $html .= <<<HTML
            <script>
                (function() {
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            if (xhttp.responseText !== '') {
                                document.getElementById(`post-content-{$meta->id}`).innerHTML = xhttp.responseText;
                            }
                        }
                    }
                    xhttp.open('POST', '{$wpRoot}/wp-admin/admin-post.php?action=purchase-check', true);
                    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhttp.send("postID={$post->ID}&productID={$meta->id}");
                })();
            </script>
HTML;
    }

    return $html;
}

?>