try {

  _quid_wp_global[dataJS.meta_id] = {
    postid: dataJS.post_id,
    paidText: dataJS.meta_paid,
    target: `post-content-${dataJS.meta_id}`,
    required: dataJS.meta_type,
  };

  quidPaymentsAlreadyPaid = document.createElement("DIV");
  quidPaymentsAlreadyPaid.setAttribute("id", `${dataJS.meta_domID}_free`);
  quidPaymentsAlreadyPaid.setAttribute("quid-amount", "0");
  quidPaymentsAlreadyPaid.setAttribute("class", "quid-pay-already-paid");
  quidPaymentsAlreadyPaid.setAttribute("quid-currency", "CAD");
  quidPaymentsAlreadyPaid.setAttribute("quid-product-id", dataJS.meta_id);
  quidPaymentsAlreadyPaid.setAttribute("quid-product-url", dataJS.meta_url);
  quidPaymentsAlreadyPaid.setAttribute("quid-product-name", dataJS.meta_name);
  quidPaymentsAlreadyPaid.setAttribute("quid-product-description", dataJS.meta_description);

  quidPaymentsSlider.getElementsByClassName("quid-slider-button-flex")[0].prepend(quidPaymentsAlreadyPaid);

  quidPaymentsButton = quid.createButton({
    amount: "0",
    currency: "CAD",
    theme: "quid",
    palette: "default",
    text: "Already Paid",
  });

  quidPaymentsButton.setAttribute("onclick", `quidPay('${dataJS.meta_domID}_free', true)`);
  document.getElementById(`${dataJS.meta_domID}_free`).prepend(quidPaymentsButton);
  let quidPaymentsAlreadyPaidButton = quidPaymentsAlreadyPaid.getElementsByClassName("quid-pay-button")[0];
  quidPaymentsAlreadyPaidButton.style.display = "block";

  (function () {
    const contentDiv = document.getElementById(`post-content-${dataJS.meta_id}`);
    if (!contentDiv) return;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        if (xhttp.responseText !== '') {
          contentDiv.innerHTML = xhttp.responseText;
        }
      }
    }
    xhttp.open('POST', dataJS.purchase_check_url, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send(`postID=${dataJS.post_id}&productID=${dataJS.meta_id}`);
  })();

} catch(e) {}