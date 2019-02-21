<?php

function quidButton() {
    global $post;
    $meta = getMetaFields($post);
    $html = "";
    $purchased = false;

    if (isset($_COOKIE["quidUserHash"]) && $meta->type == 'Required') {
        if (hasPurchasedAlready($_COOKIE["quidUserHash"], $meta->id)) {
            $purchased = true;
        }
    }

    if ($purchased) {
        return wpautop(get_post_field('post_content', $post->ID));
    } else if ($meta->type == 'Required') {
        $html .= '<p id="'.$post->ID.'-excerpt">'.$post->post_excerpt.'</p>';
    }

    $html .= '
    <div style="width: 100%;" id="post-content-'.$meta->id.'"></div>

    <div class="quid-pay-error" style="text-align: center; margin: 0px; display: none;">
        <div id="quid-error-'.$post->ID.'" class="wp-quid-error" style="display: inline-flex;">
            <img class="wp-quid-error-image" src="https://js.quid.works/v1/assets/quid.png" />
            Payment validation failed
        </div>
    </div>

    <div class="quid-pay-buttons">
        <div id="'.$meta->id.'"
            quid-amount="'.$meta->price.'"
            quid-currency="CAD"
            quid-product-id="'.$meta->id.'"
            quid-product-url="'.$meta->url.'"
            quid-product-name="'.$meta->name.'"
            quid-product-description="'.$meta->description.'"
            style="display: inline-flex"
        ></div>';

    if ($meta->type == "Required") {
        $html .= '
        <div id="'.$meta->id.'_free"
            quid-amount="0"
            quid-currency="CAD"
            quid-product-id="'.$meta->id.'"
            quid-product-url="'.$meta->url.'"
            quid-product-name="'.$meta->name.'"
            quid-product-description="'.$meta->description.'"
            style="display: inline-flex"
        ></div>';
    }

    $html .= '
    </div>

    <script>
        qButton = quid.createButton({
            amount: "'.$meta->price.'",
            currency: "CAD",
            theme: "quid",
            palette: "default",
            text: "Pay $'.$meta->price.'",
        });

        qButton.setAttribute("onclick", "quidPay(this)");
        document.getElementById("'.$meta->id.'").appendChild(qButton);';

        if ($meta->type == "Required") {
            $html .= '
            qButton = quid.createButton({
                amount: "0",
                currency: "CAD",
                theme: "quid",
                palette: "default",
                text: "Already Paid",
            });
    
            qButton.setAttribute("onclick", "quidPay(this)");
            document.getElementById("'.$meta->id.'_free").appendChild(qButton);
            ';
        }

        $html .= '_quid_wp_global["'.$meta->id.'"] = {postid: "'.$post->ID.'", required: "'.$meta->type.'", paidText: "'.$meta->paid.'", target: "post-content-'.$meta->id.'"};
    </script>
    ';

    return $html;
}

function quidSlider($atts) {
    global $post;
    $meta = getMetaFields($post);
    $html = "";
    $purchased = false;

    if (isset($_COOKIE["quidUserHash"]) && $meta->type == 'Required') {
        if (hasPurchasedAlready($_COOKIE["quidUserHash"], $meta->id)) {
            $purchased = true;
        }
    }

    if ($purchased) {
        return wpautop(get_post_field('post_content', $post->ID));
    } else if ($meta->type == 'Required') {
        $html .= '<p id="'.$post->ID.'-excerpt">'.$post->post_excerpt.'</p>';
    }

    $html .= '
    <div style="width: 100%;" id="post-content-'.$meta->id.'"></div>

    <div class="quid-pay-error" style="text-align: center; margin: 0px; display: none;">
        <div id="quid-error-'.$post->ID.'" class="wp-quid-error" style="display: inline-flex;">
            <img class="wp-quid-error-image" src="https://js.quid.works/v1/assets/quid.png" />
            Payment validation failed
        </div>
    </div>

    <div
        id="'.$meta->id.'"
        class="quid-slider"
        quid-currency="CAD"
        quid-product-id="'.$meta->id.'"
        quid-product-url="'.$meta->url.'"
        quid-product-name="'.$meta->name.'"
        quid-product-description="'.$meta->description.'"
        quid-text="'.$meta->text.'"
        quid-text-paid="'.$meta->paid.'"
    ></div>
    <script>
        baseElement = document.getElementById("'.$meta->id.'");
        qSlider = quid.createSlider({
            minAmount: "'.$meta->min.'",
            maxAmount: "'.$meta->max.'",
            element: baseElement,
            theme: "quid",
            text: "'.$meta->text.'",
            currency: "CAD",
            amount: "'.$meta->initial.'",
        });
        qSlider.getElementsByClassName("quid-pay-button")[0].setAttribute("onclick", "quidPay(baseElement)");';

    if ($meta->type == 'Required') {
        $html .= '
            alreadyPaid = document.createElement("DIV");
            alreadyPaid.setAttribute("id", "'.$meta->id.'_free");
            alreadyPaid.setAttribute("quid-amount", "0");
            alreadyPaid.setAttribute("quid-currency", "CAD");
            alreadyPaid.setAttribute("quid-product-id", "'.$meta->id.'");
            alreadyPaid.setAttribute("quid-product-url", "'.$meta->url.'");
            alreadyPaid.setAttribute("quid-product-name", "'.$meta->name.'");
            alreadyPaid.setAttribute("quid-product-description", "'.$meta->description.'");
            console.log(alreadyPaid);

            qSlider.getElementsByClassName("quid-slider-button-flex")[0].appendChild(alreadyPaid);

            qButton = quid.createButton({
                amount: "0",
                currency: "CAD",
                theme: "quid",
                palette: "default",
                text: "Already Paid",
            });
    
            qButton.setAttribute("onclick", "quidPay(this)");
            document.getElementById("'.$meta->id.'_free").appendChild(qButton);
            let alreadyPaidButton = alreadyPaid.getElementsByClassName("quid-pay-button")[0];
            alreadyPaidButton.style.display = "block";
            alreadyPaidButton.style.marginLeft = "5px";
        ';
    }

    $html .= '_quid_wp_global["'.$meta->id.'"] = {postid: "'.$post->ID.'", required: "'.$meta->type.'", paidText: "'.$meta->paid.'", target: "post-content-'.$meta->id.'"};
    </script>
    ';
    return $html;
}

?>