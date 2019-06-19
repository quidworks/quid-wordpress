try {
  
  _quid_wp_global[dataJS.meta_id] = {
    postid: dataJS.post_id,
    required: dataJS.meta_type,
    paidText: dataJS.meta_paid,
    target: `post-content-${dataJS.meta_id}`,
  };

  quidPaymentsButton = quid.createButton({
    amount: dataJS.meta_price,
    currency: dataJS.meta_currency,
    theme: "quid",
    palette: "default",
    text: `Pay ${dataJS.meta_price}`,
  });

  quidPaymentsButton.getElementsByClassName("quid-pay-button")[0].setAttribute("onclick", `quidPay('${dataJS.meta_domID}')`);
  document.getElementById(dataJS.meta_domID).appendChild(quidPaymentsButton);

} catch(e) {}