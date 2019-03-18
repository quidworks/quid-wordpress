_quid_wp_global[dataButtonJS.meta_id] = {
  postid: dataButtonJS.post_id,
  required: dataButtonJS.meta_type,
  paidText: dataButtonJS.meta_paid,
  target: `post-content-${dataButtonJS.meta_id}`,
};

quidPaymentsButton = quid.createButton({
  amount: dataButtonJS.meta_price,
  currency: "CAD",
  theme: "quid",
  palette: "default",
  text: `Pay ${dataButtonJS.meta_price}`,
});

quidPaymentsButton.setAttribute("onclick", "quidPay(this)");
document.getElementById(dataButtonJS.meta_id).appendChild(quidPaymentsButton);