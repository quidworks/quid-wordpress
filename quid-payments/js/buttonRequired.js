try {
  _quid_wp_global[dataJS.meta_id] = {
    postid: dataJS.post_id,
    paidText: dataJS.meta_paid,
    target: `post-content-${dataJS.content_id}`,
    required: dataJS.meta_type,
  };

  quidPaymentsButton = quid.createButton({
    amount: "0",
    currency: dataJS.meta_currency,
    theme: "quid",
    palette: "default",
    text: "Restore Purchase",
  });

  if (!quidPaymentsButton) {
    throw `createButton returned an invalid element`;
  }

  quidPaymentsButton.setAttribute("onclick", `quidPay('${dataJS.meta_domID}_free', true)`);

  quidPaymentsBaseElement = document.getElementById(`${dataJS.meta_domID}_free`);

  if (!quidPaymentsBaseElement) {
    throw `element with ID ${dataJS.meta_domID}_free does not exist`;
  }

  quidPaymentsBaseElement.appendChild(quidPaymentsButton);

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

} catch(e) {
  if (!e.toString().includes('_quid_wp_global')) console.log(`QUID ERROR: ${e}`);
}