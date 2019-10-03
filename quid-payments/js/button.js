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

  if (!quidPaymentsButton) {
    throw `createButton returned an invalid element`;
  }

  payButtonElement = quidPaymentsButton.getElementsByClassName("quid-pay-button")[0]

  if (!payButtonElement) {
    throw `element with class quid-pay-button not found`;
  }

  payButtonElement.setAttribute("onclick", `quidPay('${dataJS.meta_domID}')`);

  quidPaymentsBaseElement = document.getElementById(dataJS.meta_domID);
  
  if (!quidPaymentsBaseElement) {
    throw `element with ID ${dataJS.meta_domID} does not exist`;
  }

  quidPaymentsBaseElement.appendChild(quidPaymentsButton);

} catch(e) {
  if (!e.toString().includes('_quid_wp_global')) console.log(`QUID ERROR: ${e}`);
}