class QuidFab {
  constructor() {
    this.xhttp = new XMLHttpRequest();

    this.onSuccessCallback = this.onSuccessCallback.bind(this);
  }

  onSuccessCallback(paymentResponse) {
    this.xhttp.open('POST', dataJS.tip_url, true);
    this.xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    this.xhttp.send(JSON.stringify({ paymentResponse }));

    const inputs = document.getElementsByClassName('quid-pay-tip');
    for (let i = 0; i < inputs.length; i++) {
      inputs[i].style.display = 'none';
    }
  }

  createFab() {
    quid.createFAB({
      // Required
      apiKey: dataJS.apiKey,
      baseURL: dataJS.baseURL,
      currency: dataJS.currency,
      productID: dataJS.id,
      productName: dataJS.name,
      productURL: dataJS.url,
      productDescription: dataJS.description !== "" ? dataJS.description : 'Thanks for the support!',
      // Optional
      position: dataJS.position,
      minAmount: dataJS.min,
      maxAmount: dataJS.max,
      amount: dataJS.amount,
      text: dataJS.text,
      paidText: dataJS.paid,
      palette: dataJS.palette,
      demo: dataJS.demo === "true",
      reminder: dataJS.reminder === "true",
      onSuccessCallback: this.onSuccessCallback,
    });
  }
}

new QuidFab().createFab();