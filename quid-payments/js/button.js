try {
  _quid_wp_global[dataJS.meta_id] = {
    postid: dataJS.post_id,
    required: dataJS.meta_type,
    paidText: dataJS.meta_paid,
    target: `post-content-${dataJS.meta_id}`
  };

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

  quidPaymentsBaseElement = document.getElementById(dataJS.meta_domID);

  if (!quidPaymentsBaseElement) {
    throw `element with ID ${dataJS.meta_domID} does not exist`;
  }

  quidPaymentsBaseElement.appendChild(quidPaymentsButton);

  if (dataJS.post_id !== "") {
    (function() {
      console.log(dataJS);
      if (dataJS.meta_type === "Required") return;
      console.log("meta_type is not Required");
      const containerDiv = document.getElementById(
        `post-container-${dataJS.post_id}`
      );
      console.log(
        "post-content-${dataJS.post_id}: " + `post-content-${dataJS.post_id}`
      );
      const contentDiv = document.getElementById(
        `post-content-${dataJS.post_id}`
      );
      console.log(contentDiv);
      const readMore = document.getElementById(
        `read-more-content-${dataJS.post_id}`
      );
      const post_id = dataJS.post_id;
      const readMoreDisabled = dataJS.meta_readMore === "false";
      const onThePostsPage = window.location.href.includes(dataJS.meta_url);
      console.log("just before contentDiv check");
      if (!contentDiv) {
        console.log("!contentDiv");
        return;
      }
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          if (xhttp.responseText !== "") {
            console.log("onreadystatechange");
            if (onThePostsPage || readMoreDisabled) {
              contentDiv.innerHTML = xhttp.responseText;
              return;
            }

            const readMoreButton = document.getElementById(
              `read-more-button-${post_id}`
            );

            const payButtons = containerDiv.getElementsByClassName(
              "quid-pay-buttons"
            );
            console.log(payButtons);
            payButtons[0].style.display = "none";

            readMoreButton.style.display = "block";
            readMoreButton.onclick = () => {
              contentDiv.innerHTML = readMore.innerHTML;
              payButtons[payButtons.length - 1].style.display = "block";
            };
            readMore.innerHTML = xhttp.responseText;
          }
        }
      };
      xhttp.open("POST", dataJS.content_url, true);
      xhttp.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
      );
      xhttp.send(`postID=${dataJS.post_id}`);
    })();
  }
} catch (e) {
  if (!e.toString().includes("_quid_wp_global"))
    console.log(`QUID ERROR: ${e}`);
}
