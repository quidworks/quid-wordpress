class quidSliderPayCallback {
  constructor(domID) {
    this.domID = domID;
    this.xhttp = new XMLHttpRequest();

    this.paymentCallback = this.paymentCallback.bind(this);
    this.requestCallback = this.requestCallback.bind(this);
  }

  getRelevantElements() {
    this.paymentContainer = document.getElementById(`quid-pay-buttons-${this.domID}`);
    this.target = document.getElementById(_quid_wp_global[this.paymentResponse.productID].target);
    this.payError = this.paymentContainer.previousElementSibling;
    this.excerptContainer = document.getElementById(_quid_wp_global[this.paymentResponse.productID].postid + '-excerpt');
    this.buttonPrice = this.paymentContainer.getElementsByClassName('quid-pay-button-price')[0];
  }

  handleRequestError() {
    if (this.xhttp.responseText === 'error') {
      this.payError.style.display = 'block';
      return true;
    }
    return false;
  }

  finalizeRequiredPayment() {
    this.excerptContainer.style.display = 'none';
    this.target.innerHTML = this.xhttp.responseText;
    this.paymentContainer.style.display = 'none';
    this.payError.style.display = 'none';
  }

  finalizeOptionalPayment() {
    this.payError.style.display = 'none';
    this.buttonPrice.innerHTML = _quid_wp_global[this.paymentResponse.productID].paidText;

    setTimeout(() => {
      this.paymentContainer.style.display = 'none';
    }, 2000);
  }

  paymentCallback(response) {
    this.paymentResponse = response;
    this.paymentRequired = _quid_wp_global[response.productID].required === 'Required';
    this.sendRequest();
  }

  requestCallback() {
    if (this.xhttp.readyState != 4 || this.xhttp.status != 200) return;

    this.getRelevantElements();

    if (this.handleRequestError()) return;

    if (this.paymentRequired) this.finalizeRequiredPayment();
    else this.finalizeOptionalPayment();
  }

  sendRequest() {
    this.xhttp.onreadystatechange = this.requestCallback;

    if (this.paymentRequired) this.xhttp.open('POST', dataIndexJS.article_url, true);
    else this.xhttp.open('POST', dataIndexJS.tip_url, true);

    this.xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    this.xhttp.send(JSON.stringify({ postid: _quid_wp_global[this.paymentResponse.productID].postid, paymentResponse: this.paymentResponse }));
  }
}

class quidButtonPayCallback {
  constructor(domID) {
    this.domID = domID;
    this.xhttp = new XMLHttpRequest();
    this.paymentResponse = null;

    this.paymentCallback = this.paymentCallback.bind(this);
    this.requestCallback = this.requestCallback.bind(this);
    this.sendRequest = this.sendRequest.bind(this);
  }

  getRelevantElements() {
    const res = this.paymentResponse;
    this.target = document.getElementById(_quid_wp_global[res.productID].target);
    this.buttonsContainer = document.getElementById(`quid-pay-buttons-${this.domID}`);
    this.payButton = this.buttonsContainer.getElementsByClassName('quid-pay-button');
    this.payError = this.buttonsContainer.previousElementSibling;
    this.validationErrorNode = this.payError.getElementsByClassName('quid-pay-error')[0];
  }

  getReturnedError() {
    switch (this.xhttp.responseText) {
      case 'validation failed':
        return 'Payment failed to go through';
      case 'database error':
        return 'database error';
      case 'unpurchased':
        if (this.payButton.length > 1) this.payButton[0].style.display = 'none';
        return 'You have not bought this yet';
      default:
        return '';
    }
  }

  returnedError() {
    const errorReturned = this.getReturnedError();
    if (errorReturned !== '') {
      this.payError.style.display = 'block';
      this.validationErrorNode.innerHTML = errorReturned;
      return true;
    }
    return false;
  }

  finalizeOptionalPayment() {
    this.payButton[0].getElementsByClassName('quid-pay-button-price')[0].innerHTML = _quid_wp_global[this.paymentResponse.productID].paidText;
    
    setTimeout(() => {
      this.buttonsContainer.style.display = 'none';
      this.payError.style.display = 'none';
    }, 2000);
  }

  finalizeRequiredPayment() {
    this.buttonsContainer.style.display = 'none';
    this.payError.style.display = 'none';
    document.getElementById(_quid_wp_global[this.paymentResponse.productID].postid + '-excerpt').style.display = 'none';
    this.target.innerHTML = this.xhttp.responseText;
  }

  paymentCallback(response) {
    this.paymentResponse = response;
    this.paymentRequired = _quid_wp_global[response.productID].required === 'Required';
    this.sendRequest();
  }

  requestCallback() {
    if (this.xhttp.readyState != 4 || this.xhttp.status != 200) return;

    this.getRelevantElements();

    if (this.returnedError()) return;
  
    if (this.paymentRequired) this.finalizeRequiredPayment();
    else this.finalizeOptionalPayment();
  }

  sendRequest() {
    this.xhttp.onreadystatechange = this.requestCallback;

    if (this.paymentRequired) this.xhttp.open('POST', dataIndexJS.article_url, true);
    else this.xhttp.open('POST', dataIndexJS.tip_url, true);

    this.xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    this.xhttp.send(JSON.stringify({ postid: _quid_wp_global[this.paymentResponse.productID].postid, paymentResponse: this.paymentResponse }));
  }
}

function quidPay(paymentContainerInstanceID, forceLogin) {
  let el = document.getElementById(paymentContainerInstanceID);
  let quidCallback = () => {};
  let amount = 0;

  const freeIndex = paymentContainerInstanceID.indexOf('_free');
  if (freeIndex !== -1) {
    paymentContainerInstanceID = paymentContainerInstanceID.substring(0, freeIndex);
  }

  if (el.classList.contains('quid-slider')) {
    amount = parseFloat(el.getElementsByClassName('noUi-handle')[0].getAttribute('aria-valuetext'));
    quidCallback = new quidSliderPayCallback(paymentContainerInstanceID).paymentCallback;
  } else {
    amount = parseFloat(el.getAttribute('quid-amount'));
    quidCallback = new quidButtonPayCallback(paymentContainerInstanceID).paymentCallback;
  }

  quidInstance.requestPayment({
    productID: el.getAttribute('quid-product-id'),
    productURL: el.getAttribute('quid-product-url'),
    productName: el.getAttribute('quid-product-name'),
    productDescription: el.getAttribute('quid-product-description'),
    price: amount,
    currency: el.getAttribute('quid-currency'),
    successCallback: quidCallback,
    forceLogin: forceLogin === true,
  });
}

const quidInstance = new quid.Quid({
  onLoad: function() {
    const quidButtons = document.getElementsByClassName('quid-pay-button');
    const quidSliders = document.getElementsByClassName('quid-pay-slider');
    for (let i = 0; i < quidButtons.length; i += 1) {
      quidButtons[i].disabled = false;
    }
    for (let i = 0; i < quidSliders.length; i += 1) {
      quidSliders[i].removeAttribute('disabled');
    }
  },
  baseURL: dataIndexJS.base_url,
  apiKey: dataIndexJS.public_key,
});

quidInstance.install();