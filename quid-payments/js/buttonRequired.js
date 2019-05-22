_quid_wp_global[dataJS.meta_id] = {
  postid: dataJS.post_id,
  paidText: dataJS.meta_paid,
  target: `post-content-${dataJS.meta_id}`,
  required: dataJS.meta_type,
};

quidPaymentsButton = quid.createButton({
  amount: "0",
  currency: "CAD",
  theme: "quid",
  palette: "default",
  text: "Already Paid",
});

quidPaymentsButton.setAttribute("onclick", `quidPay('${dataJS.meta_id}_free', true)`);
document.getElementById(`${dataJS.meta_id}_free`).appendChild(quidPaymentsButton);

(function () {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (xhttp.responseText !== '') {
        document.getElementById(`post-content-${dataJS.meta_id}`).innerHTML = xhttp.responseText;
      }
    }
  }
  xhttp.open('POST', dataJS.purchase_check_url, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send(`postID=${dataJS.post_id}&productID=${dataJS.meta_id}`);
})();