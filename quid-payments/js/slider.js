try {
  _quid_wp_global[dataJS.meta_id] = {
    postid: dataJS.post_id,
    required: dataJS.meta_type,
    paidText: dataJS.meta_paid,
    target: `post-content-${dataJS.meta_id}`
  };

  quidPaymentsBaseElement = document.getElementById(dataJS.meta_domID);
  
  if (!quidPaymentsBaseElement) {
    throw `element with ID ${dataJS.meta_domID} does not exist`;
  }

  quidPaymentsSlider = quid.createSlider({
    minAmount: dataJS.meta_min,
    maxAmount: dataJS.meta_max,
    element: quidPaymentsBaseElement,
    theme: "quid",
    text: dataJS.meta_text,
    currency: dataJS.meta_currency,
    amount: dataJS.meta_initial,
  });

  if (!quidPaymentsSlider) {
    throw `createSlider returned an invalid element`;
  }

  payButtonElement = quidPaymentsSlider.getElementsByClassName("quid-pay-button")[0];

  if (!payButtonElement) {
    throw `element with class quid-pay-button not found`;
  }

  payButtonElement.setAttribute("onclick", `quidPay('${dataJS.meta_domID}')`);

} catch(e) {
  if (!e.toString().includes('_quid_wp_global')) console.log(`QUID ERROR: ${e}`);
}