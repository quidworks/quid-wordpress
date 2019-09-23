class QuidSettings {
  constructor() {
    this.minPrice = 0.01;
    this.maxPrice = 2.0;
    this.alertTimeout = null;

    this.toggleSwitch = this.toggleSwitch.bind(this);
  }

  setAlertElement() {
    if (this.alertTimeout !== null) window.clearTimeout(this.alertTimeout);
    this.errorContainer = document.getElementById(
      "quidSettingsMessageContainer"
    );
  }

  setAlertTimeout() {
    window.scrollTo(0, 0);
    this.alertTimeout = window.setTimeout(() => {
      this.clearAlert();
    }, 5000);
  }

  showError() {
    this.errorContainer.classList.add(
      "quid-settings-message-show",
      "notice-error"
    );
    this.errorContainer.innerHTML = "Something went wrong";
    this.setAlertTimeout();
  }

  showSuccess() {
    this.errorContainer.classList.add(
      "quid-settings-message-show",
      "notice-success"
    );
    this.errorContainer.innerHTML = "Success";
    this.setAlertTimeout();
  }

  clearAlert() {
    this.errorContainer.classList.remove(
      "quid-settings-message-show",
      "notice-success",
      "notice-error"
    );
  }

  getElements() {
    this.switch = document.getElementById("quidFabSwitch");
    this.switchText = this.switch.getElementsByClassName(
      "quid-fab-switch-text"
    )[0];
    this.switchInput = this.switch.getElementsByTagName("input")[0];
  }

  toggleSwitch() {
    this.getElements();
    if (this.switchText.innerHTML === "ON") this.switchOff();
    else this.switchOn();
  }

  switchOn() {
    this.switchInput.value = "true";
    this.switch.classList.remove("quid-fab-switched-off");
    this.switch.classList.add("quid-fab-switched-on");
    this.switchText.innerHTML = "ON";
  }

  switchOff() {
    this.switchInput.value = "false";
    this.switch.classList.remove("quid-fab-switched-on");
    this.switch.classList.add("quid-fab-switched-off");
    this.switchText.innerHTML = "OFF";
  }

  outputMessage(el, message) {
    if (el) {
      el.innerHTML = message;
      el.style.display = "table-cell";
    }
  }

  closeAllMessages() {
    const messages = document.getElementsByClassName(
      "quid-fab-setting-message"
    );
    for (let i = 0; i < messages.length; i++) {
      messages[i].style.display = "none";
    }
  }

  handleMinKeypress(e) {
    if (e.target.value === "") {
      this.outputMessage(
        e.target.nextElementSibling,
        "this field can't be blank"
      );
      return;
    }
    let float = parseFloat(e.target.value);
    if (float < 0.01) {
      this.outputMessage(
        e.target.nextElementSibling,
        "can't be less than $0.01"
      );
    } else if (float >= this.maxPrice) {
      this.outputMessage(
        e.target.nextElementSibling,
        "must be less than max price"
      );
    } else if (isNaN(float)) {
    } else {
      this.minPrice = float;
      e.target.nextElementSibling.style.display = "none";
    }
  }

  handleMaxKeypress(e) {
    if (e.target.value === "") {
      this.outputMessage(
        e.target.nextElementSibling,
        "this field can't be blank"
      );
      return;
    }
    let float = parseFloat(e.target.value);
    if (float > 2.0) {
      this.outputMessage(
        e.target.nextElementSibling,
        "can't be more than $2.00"
      );
    } else if (float <= this.minPrice) {
      this.outputMessage(
        e.target.nextElementSibling,
        "must be more than min price"
      );
    } else if (isNaN(float)) {
    } else {
      this.maxPrice = float;
      e.target.nextElementSibling.style.display = "none";
    }
  }

  handlePriceKeypress(e) {
    if (e.target.value === "") {
      this.outputMessage(
        e.target.nextElementSibling,
        "this field can't be blank"
      );
      return;
    }
    let float = parseFloat(e.target.value);
    if (float > 2.0) {
      this.outputMessage(
        e.target.nextElementSibling,
        "can't be more than $2.00"
      );
    } else if (float < 0.01) {
      this.outputMessage(
        e.target.nextElementSibling,
        "can't be less than $0.01"
      );
    } else {
      e.target.nextElementSibling.style.display = "none";
    }
  }

  setMinPrice(price) {
    this.minPrice = price;
  }

  setMaxPrice(price) {
    this.maxPrice = price;
  }

  cleanDollarValues(data) {
    let minPriceAsFloat = parseFloat(data["quid-fab-min"]);
    let maxPriceAsFloat = parseFloat(data["quid-fab-max"]);
    const initialPriceAsFloat = parseFloat(data["quid-fab-initial"]);

    if (minPriceAsFloat < 0.01) {
      data["quid-fab-min"] = "0.01";
    }

    if (maxPriceAsFloat > 2.0) {
      data["quid-fab-max"] = "2.00";
    }

    if (maxPriceAsFloat < minPriceAsFloat) {
      if (minPriceAsFloat !== 2.0) {
        data["quid-fab-max"] = "2.00";
      } else {
        data["quid-fab-max"] = "2.00";
        data["quid-fab-min"] = "1.99";
      }
    }

    minPriceAsFloat = parseFloat(data["quid-fab-min"]);
    maxPriceAsFloat = parseFloat(data["quid-fab-max"]);

    if (
      initialPriceAsFloat > minPriceAsFloat &&
      initialPriceAsFloat < maxPriceAsFloat
    )
      return;

    const medianPriceValue = (maxPriceAsFloat + minPriceAsFloat) / 2.0;
    data["quid-fab-initial"] = medianPriceValue.toFixed(2);
  }

  submitQuidSettings() {
    const data = {};
    const fields = document.getElementsByClassName("quid-field");
    for (let i = 0; i < fields.length; i++) {
      data[fields[i].getAttribute("name")] = fields[i].value;
    }
    const category_fields = document.getElementsByClassName(
      "quid-category-field"
    );
    console.log("Category fields");
    console.log(category_fields);
    for (let i = 0; i < category_fields.length; i++) {
      data[category_fields[i].getAttribute("name")] = category_fields[i].value;
    }

    this.cleanDollarValues(data);

    var json = JSON.stringify(data);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        quidSettings.setAlertElement();
        if (xhttp.responseText === "success") quidSettings.showSuccess();
        else quidSettings.showError();
      }
    };
    xhttp.open("POST", dataSettingsJS.settings_url, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("data=" + json);
  }
}

quidSettings = new QuidSettings();
