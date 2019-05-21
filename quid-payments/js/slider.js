console.log('JAVASCRIPT');

_quid_wp_global[dataJS.meta_id] = {
  postid: dataJS.post_id,
  required: dataJS.meta_type,
  paidText: dataJS.meta_paid,
  target: `post-content-${dataJS.meta_id}`
};

quidPaymentsBaseElement = document.getElementById(dataJS.meta_id);
quidPaymentsSlider = quid.createSlider({
  minAmount: dataJS.meta_min,
  maxAmount: dataJS.meta_max,
  element: quidPaymentsBaseElement,
  theme: "quid",
  text: dataJS.meta_text,
  currency: "CAD",
  amount: dataJS.meta_initial,
});
quidPaymentsSlider.getElementsByClassName("quid-pay-button")[0].setAttribute("onclick", `quidPay('${dataJS.meta_id}')`);