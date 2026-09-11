<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager dashboard | CraveCart</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="app-shell">
    <nav class="topbar" aria-label="Primary navigation">
        <a class="brand" href="index.php?action=restaurant_dash"><span class="brand-mark">✦</span> CraveCart <span class="topbar-context">Restaurant manager</span></a>
        <a href="index.php?action=logout" class="btn logout-btn">Log out</a>
    </nav>

    <header class="page-hero">
        <div class="hero-copy">
            <span class="eyebrow">Restaurant operations</span>
            <h1>Keep the kitchen in sync.</h1>
            <p>Maintain your menu and move incoming orders from the counter to preparation without the clutter.</p>
        </div>
        <span class="hero-kicker">Manager workspace</span>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice notice-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="notice notice-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <section class="panel" aria-labelledby="menu-management-heading">
        <div class="panel-header">
            <div>
                <h2 id="menu-management-heading">Menu management</h2>
                <p>Add menu items, monitor stock, and update availability.</p>
            </div>
        </div>

        <div class="toolbar">
            <input type="text" id="searchInput" placeholder="Search menu items..." onkeyup="searchProducts()" aria-label="Search menu items">
        </div>

        <form action="index.php?action=createProduct" method="POST" enctype="multipart/form-data" onsubmit="return validateProduct()">
            <input type="text" id="name" name="name" placeholder="Product name">
            <input type="number" id="price" name="price" placeholder="Price ($)" step="0.01">
            <input type="number" id="stock" name="stock_limit" placeholder="Stock limit">
            <input type="file" name="product_image" accept="image/*" aria-label="Product image">
            <button type="submit">Add to menu</button>
        </form>

        <div class="table-wrap with-top-gap">
            <table>
                <thead>
                <tr><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody id="productTableBody">
                <?php while ($prod = mysqli_fetch_assoc($products_result)): ?>
                    <tr id="row-<?php echo $prod['id']; ?>">
                        <td><img class="image-thumb" src="<?php echo htmlspecialchars($prod['image_path']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>"></td>
                        <td><?php echo htmlspecialchars($prod['name']); ?></td>
                        <td>$<?php echo $prod['price']; ?></td>
                        <td><?php echo $prod['stock_limit']; ?></td>
                        <td><span class="pill <?php echo $prod['is_available'] ? 'pill-available' : 'pill-out-of-stock'; ?>"><?php echo $prod['is_available'] ? 'Available' : 'Out of stock'; ?></span></td>
                        <td>
                            <a href="index.php?action=toggleAvailability&id=<?php echo $prod['id']; ?>" class="btn btn-secondary">Toggle</a>
                            <button type="button" onclick="deleteProduct(<?php echo $prod['id']; ?>)" class="delete-btn">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel" aria-labelledby="kitchen-orders-heading">
        <div class="panel-header">
            <div>
                <h2 id="kitchen-orders-heading">Incoming kitchen orders</h2>
                <p>Prioritize the queue and send each order into preparation.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Order</th><th>Item</th><th>Custom request</th><th>Delivery</th><th>Action</th></tr></thead>
                <tbody>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['custom_request']); ?></td>
                        <td><?php echo $order['is_express'] ? '<span class="pill pill-express">Express</span>' : '<span class="pill">Standard</span>'; ?></td>
                        <td><a href="index.php?action=markPreparing&id=<?php echo $order['id']; ?>" class="btn btn-success">Start preparing</a></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
    function validateProduct() {
        const name = document.getElementById('name').value.trim();
        const price = parseFloat(document.getElementById('price').value);
        const stock = parseInt(document.getElementById('stock').value);

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
            fetch(`index.php?action=deleteProduct&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        document.getElementById('row-' + id).remove();
                    }
                });
        }
    }

    function searchProducts() {
        const query = document.getElementById('searchInput').value;
        fetch(`index.php?action=searchProducts&q=${query}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('productTableBody');
                tbody.innerHTML = '';
                data.forEach(p => {
                    const status = p.is_available == 1 ? 'Available' : 'Out of Stock';
                    tbody.innerHTML += `
                    <tr id="row-${p.id}">
                        <td><img class="image-thumb" src="${p.image_path}" alt="${p.name}"></td>
                        <td>${p.name}</td>
                        <td>$${p.price}</td>
                        <td>${p.stock_limit}</td>
                        <td><span class="pill ${p.is_available == 1 ? 'pill-available' : 'pill-out-of-stock'}">${status}</span></td>
                        <td>
                            <a href="index.php?action=toggleAvailability&id=${p.id}" class="btn btn-secondary">Toggle</a>
                            <button type="button" onclick="deleteProduct(${p.id})" class="delete-btn">Delete</button>
                        </td>
                    </tr>`;
                });
            });
    }
</script>
</body>
</html>
