try {
  _quid_wp_global[dataJS.meta_id] = {
    postid: dataJS.post_id,
    required: dataJS.meta_type,
    paidText: dataJS.meta_paid,
    target: `post-content-${dataJS.post_id}`
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

  if (dataJS.post_id !== "") {

    (function () {
      if (dataJS.meta_type === "Required") return;
      const containerDiv = document.getElementById(`post-container-${dataJS.post_id}`);
      const contentDiv = document.getElementById(`post-content-${dataJS.post_id}`);
      const readMore = document.getElementById(`read-more-content-${dataJS.post_id}`);
      const post_id = dataJS.post_id;
      const readMoreDisabled = dataJS.meta_readMore === "false";
      const onThePostsPage = window.location.href.includes(dataJS.meta_url);
      if (!contentDiv) return;
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          if (xhttp.responseText !== '') {
            if (onThePostsPage  || readMoreDisabled) {
              contentDiv.innerHTML = xhttp.responseText;
              return;
            }

            const payButtons = containerDiv.getElementsByClassName('quid-pay-buttons');
            payButtons[0].style.display = 'none';

            const readMoreButton = document.getElementById(`read-more-button-${post_id}`);
            readMoreButton.style.display = 'block';
            readMoreButton.onclick = () => {
              contentDiv.innerHTML = readMore.innerHTML;
              payButtons[payButtons.length - 1].style.display = 'block';
            }
            readMore.innerHTML = xhttp.responseText;
          }
        }
      }
      xhttp.open('POST', dataJS.content_url, true);
      xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhttp.send(`postID=${dataJS.post_id}`);
    })();

  }

} catch(e) {
  if (!e.toString().includes('_quid_wp_global')) console.log(`QUID ERROR: ${e}`);
}