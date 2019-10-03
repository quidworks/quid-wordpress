class QuidPostMeta {
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
    const messages = document.getElementsByClassName('quid-post-meta-message');
    for (let i = 0; i < messages.length; i++) {
      messages[i].style.display = 'none';
    }
  }


  handleInputTypeChange(theSelectElement) {
    let priceInputs = undefined;
    if (theSelectElement.value === 'Slider') {
      priceInputs = document.getElementsByClassName('quid-post-meta-button-only');
      for (let i = 0; i < priceInputs.length; i++) {
        priceInputs[i].setAttribute('readonly', 'readonly');
      }

      priceInputs = document.getElementsByClassName('quid-post-meta-slider-only');
      for (let i = 0; i < priceInputs.length; i++) {
        priceInputs[i].removeAttribute('readonly');
      }
    } else {
      priceInputs = document.getElementsByClassName('quid-post-meta-slider-only');
      for (let i = 0; i < priceInputs.length; i++) {
        priceInputs[i].setAttribute('readonly', 'readonly');
      }
      
      priceInputs = document.getElementsByClassName('quid-post-meta-button-only');
      for (let i = 0; i < priceInputs.length; i++) {
        priceInputs[i].removeAttribute('readonly');
      }
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

quidPostMeta = new QuidPostMeta();