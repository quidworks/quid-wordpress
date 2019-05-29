function submitQuidSettings() {
  let public = document.getElementById('quid-publicKey').value;
  let secret = document.getElementById('quid-secretKey').value;
  let align = document.getElementById('quid-align').value;
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
  xhttp.send('public=' + public + '&secret=' + secret + '&align=' + align);
}