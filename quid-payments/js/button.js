try {
  _quid_wp_global[dataJS.meta_id] = {
    postid: dataJS.post_id,
    paidText: dataJS.meta_paid,
    target: `post-content-${dataJS.meta_id}`,
    required: dataJS.meta_type
  };

  quidPaymentsBaseElement = document.getElementById(dataJS.meta_domID);

  if (!quidPaymentsBaseElement) {
    throw `element with ID ${dataJS.meta_domID} does not exist`;
  }

  quidPaymentsButton = quid.createButton({
    amount: dataJS.meta_price,
    currency: dataJS.meta_currency,
    theme: "quid",
    palette: "default",
    text: `Pay ${dataJS.meta_price}`
  });

  if (!quidPaymentsButton) {
    throw `createButton returned an invalid element`;
  }

  payButtonElement = quidPaymentsButton.getElementsByClassName(
    "quid-pay-button"
  )[0];

  if (!payButtonElement) {
    throw `element with class quid-pay-button not found`;
  }

  payButtonElement.setAttribute("onclick", `quidPay('${dataJS.meta_domID}')`);

  quidPaymentsBaseElement.appendChild(quidPaymentsButton);

  if (dataJS.post_id !== "") {
    (function() {
      const post_id = dataJS.post_id;
      const containerDiv = document.getElementById(`post-container-${post_id}`);
      const contentDiv = document.getElementById(`post-content-${post_id}`);
      const readMore = document.getElementById(`read-more-content-${post_id}`);
      const excerptsEnabled = dataJS.meta_readMore === "true";
      const postUrl = dataJS.meta_url;
      const onThePostPage = window.location.href.includes(postUrl);
      const paymentRequired = dataJS.meta_type === "Required";

      if (!contentDiv) {
        return;
      }
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          // if the response is not empty text then we know that payment is not required
          const postContentReturned = xhttp.responseText !== "";
          // the excerpt and the payment option are being displayed and we need
          // to decide if they should be replaced with the content
          if (paymentRequired) {
            if (postContentReturned) {
              if (onThePostPage) {
                // Payment is required, the content was returned (i.e.
                // the content has been previously puchased), the post page
                // IS currently being displayed
                // display content and hide they payment option
                contentDiv.innerHTML = xhttp.responseText;
                const payButtons = containerDiv.getElementsByClassName(
                  "quid-pay-buttons"
                );
                payButtons[0].style.display = "none";
              } else {
                if (!excerptsEnabled) {
                  // Payment is required, the content was returned (i.e.
                  // the content has been previously puchased), the post page
                  // is not currently being displayed, and excepts are NOT enabled
                  // display content
                  contentDiv.innerHTML = xhttp.responseText;
                } else {
                  // Payment is required, the content was returned (i.e.
                  // the content has been previously puchased), the post page
                  // is not currently being displayed, and excepts are enabled
                  const payButtons = containerDiv.getElementsByClassName(
                    "quid-pay-buttons"
                  );
                  payButtons[0].style.display = "none";

                  const readMoreButton = document.getElementById(
                    `read-more-button-${post_id}`
                  );
                  readMoreButton.style.display = "block";
                  readMoreButton.onclick = function() {
                    location.href = postUrl;
                    // contentDiv.innerHTML = readMore.innerHTML;
                    // payButtons[payButtons.length - 1].style.display = "block";
                  };
                  readMore.innerHTML = xhttp.responseText;
                }
              }
            }
          } else {
            // payment is not required
            if (!onThePostPage) {
              if (excerptsEnabled) {
                // excerpts are enabled and not on the post page
                const payButtons = containerDiv.getElementsByClassName(
                  "quid-pay-buttons"
                );
                payButtons[0].style.display = "none";

                const readMoreButton = document.getElementById(
                  `read-more-button-${post_id}`
                );
                readMoreButton.style.display = "block";
                readMoreButton.onclick = function() {
                  location.href = postUrl;
                };
              }
            }
          }
        }
      };
      xhttp.open("POST", dataJS.content_url, true);
      xhttp.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
      );
      xhttp.send(`postID=${dataJS.post_id}&productID=${dataJS.meta_id}`);
    })();
  }
} catch (e) {
  if (!e.toString().includes("_quid_wp_global"))
    console.log(`QUID ERROR: ${e}`);
}
