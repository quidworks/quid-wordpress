try {
  _quid_wp_global[dataJS.meta_id] = {
    postid: dataJS.post_id,
    paidText: dataJS.meta_paid,
    target: `post-content-${dataJS.post_id}`,
    required: dataJS.meta_type
  };

  quidPaymentsAlreadyPaid = document.createElement("DIV");
  quidPaymentsAlreadyPaid.setAttribute("id", `${dataJS.meta_domID}_free`);
  quidPaymentsAlreadyPaid.setAttribute("quid-amount", "0");
  quidPaymentsAlreadyPaid.setAttribute("class", "quid-pay-already-paid");
  quidPaymentsAlreadyPaid.setAttribute("quid-currency", dataJS.meta_currency);
  quidPaymentsAlreadyPaid.setAttribute("quid-product-id", dataJS.meta_id);
  quidPaymentsAlreadyPaid.setAttribute("quid-product-url", dataJS.meta_url);
  quidPaymentsAlreadyPaid.setAttribute("quid-product-name", dataJS.meta_name);
  quidPaymentsAlreadyPaid.setAttribute(
    "quid-product-description",
    dataJS.meta_description
  );

  quidSliderContainer = quidPaymentsSlider.getElementsByClassName(
    "quid-slider-button-flex"
  )[0];

  if (!quidSliderContainer) {
    throw "quid-slider-button-flex (slider container) not found";
  }

  quidSliderContainer.prepend(quidPaymentsAlreadyPaid);

  quidPaymentsButton = quid.createButton({
    amount: "0",
    currency: dataJS.meta_currency,
    theme: "quid",
    palette: "default",
    text: "Restore Purchase"
  });

  if (!quidPaymentsButton) {
    throw `createButton returned an invalid element`;
  }

  quidPaymentsButton.setAttribute(
    "onclick",
    `quidPay('${dataJS.meta_domID}_free', true)`
  );

  quidPaymentsBaseElement = document.getElementById(
    `${dataJS.meta_domID}_free`
  );

  if (!quidPaymentsBaseElement) {
    throw `element with ID ${dataJS.meta_domID}_free does not exist`;
  }

  quidPaymentsBaseElement.prepend(quidPaymentsButton);

  let quidPaymentsAlreadyPaidButton = quidPaymentsAlreadyPaid.getElementsByClassName(
    "quid-pay-button"
  )[0];
  quidPaymentsAlreadyPaidButton.style.display = "block";
} catch (e) {
  if (!e.toString().includes("_quid_wp_global"))
    console.log(`QUID ERROR: ${e}`);
}
