<?php

add_action( 'wp_footer', 'quidFooter' );

function quidFooter() {
    global $baseURL;
    global $wpRoot;
    print_r("
        <script>
        function quidSubmitTip(res) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    let slider = document.getElementById(res.productID);
                    let target = document.getElementById(_quid_wp_global[res.productID].target);
                    let payError = target.parentNode.getElementsByClassName('quid-pay-error')[0];
                    if (xhttp.responseText !== 'error') {
                        if (_quid_wp_global[res.productID].required === 'Required') {
                            document.getElementById(_quid_wp_global[res.productID].postid + '-excerpt').style.display = 'none';
                            target.innerHTML = xhttp.responseText;
                            slider.style.display = 'none';
                            payError.style.display = 'none';
                        } else {
                            payError.style.display = 'none';
                            slider.getElementsByTagName('button')[0].getElementsByClassName('quid-pay-button-price')[0].innerHTML = _quid_wp_global[res.productID].paidText;
                            setTimeout(() => {
                                slider.style.display = 'none';
                            }, 2000);
                        }
                    } else {
                        payError.style.display = 'block';
                    }
                }
            };
            if (_quid_wp_global[res.productID].required === 'Required') {
                xhttp.open('POST', '".$wpRoot."/wp-admin/admin-post.php?action=quid-article', true);
            } else {
                xhttp.open('POST', '".$wpRoot."/wp-admin/admin-post.php?action=quid-tip', true);
            }
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhttp.send(JSON.stringify({postid: _quid_wp_global[res.productID].postid, paymentResponse: res}));
        }
        function quidFetchContent(res) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    let target = document.getElementById(_quid_wp_global[res.productID].target);
                    let payButton = target.parentNode.getElementsByClassName('quid-pay-button');
                    let payButtons = target.parentNode.getElementsByClassName('quid-pay-buttons')[0];
                    let payError = target.parentNode.getElementsByClassName('quid-pay-error')[0];
                    let validationErrorNode = document.getElementById('quid-error-'+_quid_wp_global[res.productID].postid);
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
                            if (payButton.length > 1) payButton[1].style.display = 'none';
                            break;
                    }
                    if (errorReturned !== '') {
                        payError.style.display = 'block';
                        validationErrorNode.innerHTML += errorReturned;
                    } else {
                        if (_quid_wp_global[res.productID].required === 'Required') {
                            payButtons.style.display = 'none';
                            payError.style.display = 'none';
                            document.getElementById(_quid_wp_global[res.productID].postid + '-excerpt').style.display = 'none';
                            target.innerHTML = xhttp.responseText;
                        } else {
                            payButton[0].getElementsByClassName('quid-pay-button-price')[0].innerHTML = _quid_wp_global[res.productID].paidText;
                            setTimeout(() => {
                                payButtons.style.display = 'none';
                                payError.style.display = 'none';
                            }, 2000);
                        }
                    }
                }
            };
            if (_quid_wp_global[res.productID].required === 'Required') {
                xhttp.open('POST', '".$wpRoot."/wp-admin/admin-post.php?action=quid-article', true);
            } else {
                xhttp.open('POST', '".$wpRoot."/wp-admin/admin-post.php?action=quid-tip', true);
            }
            xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhttp.send(JSON.stringify({postid: _quid_wp_global[res.productID].postid, paymentResponse: res}));
        }
        function quidPay(element) {
            let el = element;
            let amount = 0;
            let quidCallback;
            if (element.tagName === 'BUTTON') {
                el = element.parentNode;
                amount = parseFloat(el.getAttribute('quid-amount'));
                quidCallback = quidFetchContent;
            } else {
                amount = parseFloat(element.getElementsByClassName('noUi-handle')[0].getAttribute('aria-valuetext'));
                quidCallback = quidSubmitTip;
            }
            quidInstance.requestPayment({
                productID: el.getAttribute('quid-product-id'),
                productURL: el.getAttribute('quid-product-url'),
                productName: el.getAttribute('quid-product-name'),
                productDescription: el.getAttribute('quid-product-description'),
                price: amount,
                currency: el.getAttribute('quid-currency'),
                successCallback: quidCallback,
            });
        }
        const quidInstance = new quid.Quid({
            onLoad: () => {
                const quidButtons = document.getElementsByClassName('quid-pay-button');
                const quidSliders = document.getElementsByClassName('quid-pay-slider');
                for (let i = 0; i < quidButtons.length; i += 1) {
                    quidButtons[i].disabled = false;
                }
                for (let i = 0; i < quidSliders.length; i += 1) {
                    quidSliders[i].removeAttribute('disabled');
                }
            },
            baseURL: '".$baseURL."',
            apiKey: '".get_option('quid-publicKey')."',
        });
        quidInstance.install();
        </script>
    ");
}

?>