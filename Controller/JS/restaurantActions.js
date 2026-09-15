function validateProduct() {
  let name = document.getElementById('name').value.trim();
  let price = parseFloat(document.getElementById('price').value);
  let stock = parseInt(document.getElementById('stock').value);

  if (name === '' || isNaN(price) || isNaN(stock)) {
    alert("Please fill out all fields correctly.");
    return false;
  }
  if (price <= 0 || stock < 0) {
    alert("Price must be > 0 and stock cannot be negative.");
    return false;
  }
  return true;
}

function deleteProduct(id) {
  if (confirm("Remove this item from the menu?")) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        try {
          let data = JSON.parse(this.responseText);
          if (data.status === 'success') {
            let row = document.getElementById('row-' + id);
            if (row) row.remove();
          } else {
            alert("Failed to delete product.");
          }
        } catch (e) {
          console.error(e);
        }
      }
    };
    xhttp.open("GET", `index.php?action=deleteProduct&id=${id}`, true);
    xhttp.send();
  }
}

function searchProducts() {
  let query = document.getElementById('searchInput').value;
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      try {
        let products = JSON.parse(this.responseText);
        let tbody = document.getElementById('productTableBody');
        tbody.innerHTML = '';
        products.forEach(p => {
          let statusText = p.is_available == 1 ? 'Available' : 'Out of stock';
          let statusClass = p.is_available == 1 ? 'pill-available' : 'pill-out-of-stock';
          tbody.innerHTML += `
            <tr id="row-${p.id}">
                <td><img class="image-thumb" src="${escapeHtml(p.image_path)}" alt="${escapeHtml(p.name)}"></td>
                <td>${escapeHtml(p.name)}</td>
                <td>$${p.price}</td>
                <td>${p.stock_limit}</td>
                <td><span class="pill ${statusClass}">${statusText}</span></td>
                <td>
                    <a href="index.php?action=toggleAvailability&id=${p.id}" class="btn btn-secondary">Toggle</a>
                    <button type="button" onclick="deleteProduct(${p.id})" class="delete-btn">Delete</button>
                </td>
            </tr>`;
        });
      } catch (e) {
        console.error(e);
      }
    }
  };
  xhttp.open("GET", `index.php?action=searchProducts&q=${encodeURIComponent(query)}`, true);
  xhttp.send();
}

function escapeHtml(str) {
  if (!str) return '';
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
