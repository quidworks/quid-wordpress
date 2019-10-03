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

  onlyHandleMinKeypress(e) {
    if (e.target.value === "") {
      this.outputMessage(
        e.target.nextElementSibling,
        "this field can't be blank"
      );
      return;
    }
    let float = parseFloat(e.target.value);
    if (isNaN(float)) {
      this.outputMessage(
        e.target.nextElementSibling,
        "can't parse number from input"
      );
      return;
    }
    if (float > 2.00) {
      this.outputMessage(
        e.target.nextElementSibling,
        "must be less than $2.00"
      );
      return;
    }
    if (float < 0.01) {
      this.outputMessage(
        e.target.nextElementSibling,
        "can't be less than $0.01"
      );
      return;
    }
    e.target.nextElementSibling.style.display = "none";
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
    const currentTab = this.getCurrentTabID();
    const tabPanel = document.getElementsByClassName(`quid-pay-settings-${currentTab.id}`)[0];
    const tabName = currentTab.name;

    if (tabName === 'quid-categories') {

      const categories = document.getElementsByClassName('quid-pay-settings-category-container');
      const categoriesData = {};

      for(let c = 0; c < categories.length; c++) {
        const categoryData = {};
        const category_fields = categories[c].getElementsByClassName('quid-category-field');
        for (let i = 0; i < category_fields.length; i++) {
          categoryData[category_fields[i].getAttribute("name")] = category_fields[i].value; 
        }

        const categoryCheckboxes = categories[c].getElementsByClassName(
            "quid-pay-settings-category-location-checkbox"
          );
        const locationsData = {};
        for (let k = 0; k < categoryCheckboxes.length; k++) {
          if (categoryCheckboxes[k].checked) {
            locationsData[categoryCheckboxes[k].getAttribute("name")] = "true";
          } else {
            locationsData[categoryCheckboxes[k].getAttribute("name")] = "false";
          }
          categoryData["locations"] = locationsData;
        }

        categoriesData[categories[c].getAttribute('category-slug')] = categoryData;
      }

      data[tabName] = categoriesData;

    } else {

      if (tabName === 'quid-buttons') {
        const fields = tabPanel.getElementsByClassName("quid-fab-field");
        const fabData = {};
        for (let i = 0; i < fields.length; i++) {
          fabData[fields[i].getAttribute("name")] = fields[i].value;
        }
        data['quid-fab'] = fabData;
        this.cleanDollarValues(data['quid-fab']);
      }

      const fields = tabPanel.getElementsByClassName("quid-field");
      const tabData = {};
      for (let i = 0; i < fields.length; i++) {
        tabData[fields[i].getAttribute("name")] = fields[i].value;
      }
      data[tabName] = tabData;
      
    }

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

  getCurrentTabID() {
    var activeTab = document.getElementsByClassName('quid-pay-settings-tab-active')[0];
    return {
      id: activeTab.getAttribute('tab-id'),
      name: activeTab.getAttribute('tab-name'),
    }
  }

  selectTab(e) {
    var tabs = document.getElementsByClassName('quid-pay-settings-tab');
    for (let i = 0; i < tabs.length; i++) {
      tabs[i].classList.remove('quid-pay-settings-tab-active');
    }
    e.classList.add('quid-pay-settings-tab-active');

    var sections = document.getElementsByClassName('quid-pay-settings-tab-content');
    for (let i = 0; i < sections.length; i++) {
      if (sections[i].classList.contains(`quid-pay-settings-${e.getAttribute('tab-id')}`)) {
        sections[i].style.display = 'block';
      } else {
        sections[i].style.display = 'none';
      }
    }
  }
}

quidSettings = new QuidSettings();
