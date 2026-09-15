function validateNoteForm() {
  let orderId = document.getElementById('order_id').value;
  let note = document.getElementById('note').value.trim();
  if (orderId <= 0 || note === '') {
    alert("Please enter a valid Order ID and note text.");
    return false;
  }
  return true;
}

function deleteNote(id) {
  if (confirm("Remove this delivery note?")) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        try {
          let data = JSON.parse(this.responseText);
          if (data.status === 'success') {
            let item = document.getElementById('note-' + id);
            if (item) item.remove();
          } else {
            alert("Failed to delete note.");
          }
        } catch (e) {
          console.error(e);
        }
      }
    };
    xhttp.open("GET", `index.php?action=deleteNote&id=${id}`, true);
    xhttp.send();
  }
}

function filterByZone() {
  let query = document.getElementById('searchZoneInput').value;
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      try {
        let orders = JSON.parse(this.responseText);
        let tbody = document.getElementById('ordersTableBody');
        tbody.innerHTML = '';
        orders.forEach(o => {
          let paymentAction = o.payment_status === 'Unpaid' ? `<br><a href="index.php?action=collectCash&id=${o.id}" class="btn btn-success">Collect cash</a>` : '';
          let deliveryAction = o.status === 'Preparing' ? `<a href="index.php?action=updateDeliveryStatus&id=${o.id}&status=Out for Delivery" class="btn">Pick up</a>` : `<a href="index.php?action=updateDeliveryStatus&id=${o.id}&status=Delivered" class="btn btn-success">Mark delivered</a>`;
          let express = o.is_express == 1 ? '<span class="pill pill-express">Express</span>' : '<span class="pill">Standard</span>';
          let paymentClass = o.payment_status === 'Unpaid' ? 'pill-unpaid' : 'pill-paid';

          tbody.innerHTML += `
            <tr>
                <td>#${o.id}</td>
                <td>${escapeHtml(o.zone)}</td>
                <td>${escapeHtml(o.product_name)}</td>
                <td>${express}</td>
                <td><span class="pill ${paymentClass}">${escapeHtml(o.payment_status)}</span> ${paymentAction}</td>
                <td><span class="pill">${escapeHtml(o.status)}</span></td>
                <td>${deliveryAction}</td>
            </tr>`;
        });
      } catch (e) {
        console.error(e);
      }
    }
  };
  xhttp.open("GET", `index.php?action=searchByZone&q=${encodeURIComponent(query)}`, true);
  xhttp.send();
}

function escapeHtml(str) {
  if (!str) return '';
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
