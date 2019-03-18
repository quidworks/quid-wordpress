quidPaymentsAlreadyPaid = document.createElement("DIV");
quidPaymentsAlreadyPaid.setAttribute("id", `${dataSliderJS.meta_id}_free`);
quidPaymentsAlreadyPaid.setAttribute("quid-amount", "0");
quidPaymentsAlreadyPaid.setAttribute("class", "quid-pay-already-paid");
quidPaymentsAlreadyPaid.setAttribute("quid-currency", "CAD");
quidPaymentsAlreadyPaid.setAttribute("quid-product-id", dataSliderJS.meta_id);
quidPaymentsAlreadyPaid.setAttribute("quid-product-url", dataSliderJS.meta_url);
quidPaymentsAlreadyPaid.setAttribute("quid-product-name", dataSliderJS.meta_name);
quidPaymentsAlreadyPaid.setAttribute("quid-product-description", dataSliderJS.meta_description);

quidPaymentsSlider.getElementsByClassName("quid-slider-button-flex")[0].prepend(quidPaymentsAlreadyPaid);

quidPaymentsButton = quid.createButton({
  amount: "0",
  currency: "CAD",
  theme: "quid",
  palette: "default",
  text: "Already Paid",
});

quidPaymentsButton.setAttribute("onclick", "quidPay(this, true)");
document.getElementById(`${dataSliderJS.meta_id}_free`).prepend(quidPaymentsButton);
let quidPaymentsAlreadyPaidButton = quidPaymentsAlreadyPaid.getElementsByClassName("quid-pay-button")[0];
quidPaymentsAlreadyPaidButton.style.display = "block";

(function () {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (xhttp.responseText !== '') {
        document.getElementById(`post-content-${dataSliderJS.meta_id}`).innerHTML = xhttp.responseText;
      }
    }
  }
  xhttp.open('POST', dataSliderJS.purchase_check_url, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send(`postID=${dataSliderJS.post_id}&productID=${dataSliderJS.meta_id}`);
})();