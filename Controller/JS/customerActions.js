function validateOrder(form) {
  let zone = form.zone.value;
  if (zone === '') {
    alert("Validation Error: Please select a delivery zone before ordering.");
    return false;
  }
  return true;
}

function searchMenu() {
  let query = document.getElementById('searchMenuInput').value.toLowerCase();
  let products = document.querySelectorAll('.product-card');

  products.forEach(card => {
    let title = card.querySelector('h3').innerText.toLowerCase();
    if (title.includes(query)) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });

  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      try {
        let data = JSON.parse(this.responseText);
        console.log("JSON Search Results: ", data);
      } catch (e) {
        console.error(e);
      }
    }
  };
  xhttp.open("GET", `index.php?action=searchMenu&q=${encodeURIComponent(query)}`, true);
  xhttp.send();
}

function cancelOrder(id) {
  if (confirm("Are you sure you want to cancel this order?")) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        try {
          let data = JSON.parse(this.responseText);
          if (data.status === 'success') {
            let row = document.getElementById('order-' + id);
            if (row) row.remove();
            alert("Order cancelled successfully.");
          } else {
            alert("Error: Cannot cancel this order.");
          }
        } catch (e) {
          console.error(e);
        }
      }
    };
    xhttp.open("GET", `index.php?action=cancelOrder&id=${id}`, true);
    xhttp.send();
  }
}
