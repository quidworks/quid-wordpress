console.log('HELLO');
quidPaymentsButton = quid.createButton({
  amount: "0",
  currency: "CAD",
  theme: "quid",
  palette: "default",
  text: "Already Paid",
});

quidPaymentsButton.setAttribute("onclick", "quidPay(this, true)");
document.getElementById(`${dataButtonJS.meta_id}_free`).appendChild(quidPaymentsButton);

(function (dataButtonJS) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (xhttp.responseText !== '') {
        document.getElementById(`post-content-${dataButtonJS.meta_id}`).innerHTML = xhttp.responseText;
      }
    }
  }
  xhttp.open('POST', dataButtonJS.purchase_check_url, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send(`postID=${dataButtonJS.post_id}&productID=${dataButtonJS.meta_id}`);
})(dataButtonJS);