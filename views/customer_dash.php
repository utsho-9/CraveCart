<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer dashboard | CraveCart</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="app-shell">
    <nav class="topbar" aria-label="Primary navigation">
        <a class="brand" href="index.php?action=customer_dash"><span class="brand-mark">✦</span> CraveCart <span class="topbar-context">Customer</span></a>
        <a href="index.php?action=logout" class="btn logout-btn">Log out</a>
    </nav>

    <header class="page-hero">
        <div class="hero-copy">
            <span class="eyebrow">Customer dashboard</span>
            <h1>Order something you’ll love.</h1>
            <p>Browse the menu, tailor your meal, and follow every order from kitchen to door.</p>
        </div>
        <span class="hero-kicker">Food, your way</span>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice notice-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="notice notice-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <section class="panel" aria-labelledby="menu-heading">
        <div class="panel-header">
            <div>
                <h2 id="menu-heading">Explore the menu</h2>
                <p>Choose a dish and add a note for the kitchen if you need to.</p>
            </div>
        </div>
        <div class="toolbar">
            <input type="text" id="searchMenuInput" placeholder="Search for food..." onkeyup="searchMenu()" aria-label="Search the menu">
        </div>

        <div id="menuContainer" class="product-grid">
            <?php while ($item = mysqli_fetch_assoc($menu_result)): ?>
                <article class="product-card" id="product-<?php echo $item['id']; ?>">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="product-card-body">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="product-price">$<?php echo $item['price']; ?></p>

                        <form action="index.php?action=placeOrder" method="POST" onsubmit="return validateOrder(this)">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="text" name="custom_request" placeholder="Custom request (optional)">
                            <select name="zone">
                                <option value="">Select delivery zone</option>
                                <option value="North Zone">North Zone</option>
                                <option value="South Zone">South Zone</option>
                            </select>
                            <label><input type="checkbox" name="is_express"> Express delivery (+$5)</label>
                            <button type="submit">Place order</button>
                        </form>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="panel" aria-labelledby="history-heading">
        <div class="panel-header">
            <div>
                <h2 id="history-heading">My order history</h2>
                <p>Update a pending request or cancel it before preparation begins.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr><th>Order</th><th>Item</th><th>Request</th><th>Zone</th><th>Status</th><th>Total</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr id="order-<?php echo $order['id']; ?>">
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td>
                            <?php if($order['status'] === 'Pending'): ?>
                                <form action="index.php?action=updateRequest" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="text" name="custom_request" value="<?php echo htmlspecialchars($order['custom_request'] ?? ''); ?>" aria-label="Custom request for order #<?php echo $order['id']; ?>">
                                    <button type="submit" class="btn-secondary">Update</button>
                                </form>
                            <?php else: ?>
                                <?php echo htmlspecialchars($order['custom_request'] ?? 'None'); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['zone']); ?> <?php echo $order['is_express'] ? '<span class="pill pill-express">Express</span>' : ''; ?></td>
                        <td><span class="pill"><?php echo $order['status']; ?></span></td>
                        <td>$<?php echo $order['total_price']; ?></td>
                        <td>
                            <?php if($order['status'] === 'Pending'): ?>
                                <button type="button" onclick="cancelOrder(<?php echo $order['id']; ?>)" class="delete-btn">Cancel</button>
                            <?php else: ?>
                                <span class="empty-copy">Cannot cancel</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-grid two-column">
        <section class="panel" aria-labelledby="feedback-heading">
            <div class="panel-header">
                <div>
                    <h2 id="feedback-heading">Share feedback</h2>
                    <p>Tell us about your food or delivery experience.</p>
                </div>
            </div>
            <form action="index.php?action=submitFeedback" method="POST" class="stacked-form">
                <textarea name="message" placeholder="How was your food?" required></textarea>
                <button type="submit">Submit feedback</button>
            </form>
        </section>

        <section class="panel" aria-labelledby="previous-feedback-heading">
            <div class="panel-header">
                <div>
                    <h2 id="previous-feedback-heading">Previous feedback</h2>
                    <p>Your submitted comments and their review status.</p>
                </div>
            </div>
            <ul class="feedback-list">
                <?php while ($fb = mysqli_fetch_assoc($feedback_result)): ?>
                    <li>
                        “<?php echo htmlspecialchars($fb['message']); ?>”
                        <span class="feedback-meta">Status: <?php echo $fb['admin_action']; ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </section>
</main>

<script>
    function validateOrder(form) {
        const zone = form.zone.value;
        if (zone === '') {
            alert("Frontend Validation Error: Please select a delivery zone before ordering.");
            return false;
        }
        return true;
    }

    function searchMenu() {
        const query = document.getElementById('searchMenuInput').value.toLowerCase();
        const products = document.querySelectorAll('.product-card');

        products.forEach(card => {
            const title = card.querySelector('h3').innerText.toLowerCase();
            if (title.includes(query)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        fetch(`index.php?action=searchMenu&q=${query}`)
            .then(res => res.json())
            .then(data => console.log("JSON Search Results: ", data));
    }

    function cancelOrder(id) {
        if (confirm("Are you sure you want to cancel this order?")) {
            fetch(`index.php?action=cancelOrder&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        document.getElementById('order-' + id).remove();
                        alert("Order cancelled successfully.");
                    } else {
                        alert("Error: Cannot cancel this order.");
                    }
                });
        }
    }
</script>
</body>
</html>
