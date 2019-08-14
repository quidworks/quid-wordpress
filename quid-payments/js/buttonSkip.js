try {
  quidPaymentsAlreadyPaid = document.createElement("BUTTON");
  quidPaymentsAlreadyPaid.setAttribute("class", "quid-pay-button quid-pay-skip");
  quidPaymentsAlreadyPaid.disabled = true;
  quidPaymentsAlreadyPaid.innerHTML = 'Skip';
  quidPostURL = dataJS.meta_postURL;
  quidPaymentsAlreadyPaid.setAttribute('posturl', quidPostURL);
  quidPaymentsAlreadyPaid.addEventListener('click', (function(quidPostURL) {
    return function() {
      if (quidPostURL === "") return;
      window.location.href = quidPostURL;
    }
  })(quidPostURL));

  quidPaymentsBaseElement.prepend(quidPaymentsAlreadyPaid);
} catch (e) {
  console.log(`QUID ERROR: trouble adding skip button - ${e}`);
}