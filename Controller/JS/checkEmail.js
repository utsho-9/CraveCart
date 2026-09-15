function checkEmail() {
  let emailInput = document.getElementById("email");
  let responseElement = document.getElementById("emailResponse");
  if (!emailInput || !responseElement) return;

  let email = emailInput.value.trim();
  if (email === "") {
    responseElement.innerHTML = "";
    return;
  }

  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      try {
        let res = JSON.parse(this.responseText);
        if (res.status === "taken") {
          responseElement.style.color = "red";
          responseElement.innerHTML = res.message;
        } else if (res.status === "available") {
          responseElement.style.color = "green";
          responseElement.innerHTML = res.message;
        } else {
          responseElement.innerHTML = "";
        }
      } catch (e) {
        responseElement.innerHTML = this.responseText;
      }
    }
  };
  xhttp.open("POST", "Controller/checkEmail.php", true);
  xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
  xhttp.send("email=" + encodeURIComponent(email));
}
