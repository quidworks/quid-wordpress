_quid_wp_global[dataSliderJS.meta_id] = {
  postid: dataSliderJS.post_id,
  required: dataSliderJS.meta_type,
  paidText: dataSliderJS.meta_paid,
  target: `post-content-${dataSliderJS.meta_id}`
};

quidPaymentsBaseElement = document.getElementById(dataSliderJS.meta_id);
quidPaymentsSlider = quid.createSlider({
  minAmount: dataSliderJS.meta_min,
  maxAmount: dataSliderJS.meta_max,
  element: quidPaymentsBaseElement,
  theme: "quid",
  text: dataSliderJS.meta_text,
  currency: "CAD",
  amount: dataSliderJS.meta_initial,
});
quidPaymentsSlider.getElementsByClassName("quid-pay-button")[0].setAttribute("onclick", "quidPay(quidPaymentsBaseElement)");