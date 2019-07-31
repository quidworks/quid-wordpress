function submitQuidSettings() {
  const data = {};
  const fields = document.getElementsByClassName('quid-field');
  for (let i = 0; i < fields.length; i++) {
    data[fields[i].getAttribute('name')] = fields[i].value;
  }

  var json = JSON.stringify(data);

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const messageOutput = document.getElementsByClassName('quid-pay-settings-response')[0];
      if (xhttp.responseText === 'success') {
        messageOutput.innerHTML = 'Success';
      } else {
        messageOutput.innerHTML = 'Something went wrong';
      }
    }
  }
  xhttp.open('POST', dataSettingsJS.settings_url, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('data='+json);
}

class QuidSettings {
  constructor() {
    this.minPrice = 0.01;
    this.maxPrice = 2.00;
  }

  outputMessage(el, message) {
    if (el) {
      el.innerHTML = message;
      el.style.display = 'table-cell';
    }
  }

  closeAllMessages() {
    const messages = document.getElementsByClassName('quid-fab-setting-message');
    for (let i = 0; i < messages.length; i++) {
      messages[i].style.display = 'none';
    }
  }

  handleMinKeypress(e) {
    let float = parseFloat(e.target.value);
    if (float < 0.01) {
      this.outputMessage(e.target.nextElementSibling, "can't be less than $0.01");
    } else if (float >= this.maxPrice) {
      this.outputMessage(e.target.nextElementSibling, "must be less than max price");
    } else if (isNaN(float)) {} else {
      this.minPrice = float;
      e.target.nextElementSibling.style.display = 'none';
    }
  }

  handleMaxKeypress(e) {
    let float = parseFloat(e.target.value);
    if (float > 2.00) {
      this.outputMessage(e.target.nextElementSibling, "can't be more than $2.00");
    } else if (float <= this.minPrice) {
      this.outputMessage(e.target.nextElementSibling, "must be more than min price");
    } else if (isNaN(float)) {} else {
      this.maxPrice = float;
      e.target.nextElementSibling.style.display = 'none';
    }
  }

  handlePriceKeypress(e) {
    let float = parseFloat(e.target.value);
    if (float > 2.00) {
      this.outputMessage(e.target.nextElementSibling, "can't be more than $2.00");
    } else if (float < 0.01) {
      this.outputMessage(e.target.nextElementSibling, "can't be less than $0.01");
    } else {
      e.target.nextElementSibling.style.display = 'none';
    }
  }

  setMinPrice(price) {
    this.minPrice = price;
  }

  setMaxPrice(price) {
    this.maxPrice = price;
  }
}

quidSettings = new QuidSettings();