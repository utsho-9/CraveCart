function validateForm() {
  let name = document.getElementById('name').value.trim();
  let email = document.getElementById('email').value.trim();
  let password = document.getElementById('password').value.trim();

  if (name === '' || email === '' || password === '') {
    alert("Validation Failed: All fields are required!");
    return false;
  }
  return true;
}

function deleteUser(id) {
  if (confirm("Are you sure you want to delete this user?")) {
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        try {
          let data = JSON.parse(this.responseText);
          if (data.status === 'success') {
            let row = document.getElementById('row-' + id);
            if (row) row.remove();
          } else {
            alert("Failed to delete user.");
          }
        } catch (e) {
          console.error(e);
        }
      }
    };
    xhttp.open("GET", `index.php?action=deleteUser&id=${id}`, true);
    xhttp.send();
  }
}

function searchUsers() {
  let query = document.getElementById('searchInput').value;
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      try {
        let users = JSON.parse(this.responseText);
        let tbody = document.getElementById('userTableBody');
        tbody.innerHTML = '';
        users.forEach(u => {
          tbody.innerHTML += `
            <tr id="row-${u.id}">
                <td>${u.id}</td>
                <td>${escapeHtml(u.name)}</td>
                <td>${escapeHtml(u.email)}</td>
                <td><span class="pill">${escapeHtml(u.role)}</span></td>
                <td><button type="button" onclick="deleteUser(${u.id})" class="delete-btn">Delete</button></td>
            </tr>`;
        });
      } catch (e) {
        console.error(e);
      }
    }
  };
  xhttp.open("GET", `index.php?action=searchUsers&q=${encodeURIComponent(query)}`, true);
  xhttp.send();
}

function escapeHtml(str) {
  if (!str) return '';
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
