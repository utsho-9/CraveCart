<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider dashboard | CraveCart</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="Controller/JS/deliveryActions.js"></script>
</head>
<body>
<main class="app-shell">
    <nav class="topbar" aria-label="Primary navigation">
        <a class="brand" href="index.php?action=delivery_dash"><span class="brand-mark">✦</span> CraveCart <span class="topbar-context">Delivery rider</span></a>
        <a href="index.php?action=logout" class="btn logout-btn">Log out</a>
    </nav>

    <header class="page-hero">
        <div class="hero-copy">
            <span class="eyebrow">Delivery dashboard</span>
            <h1>Every drop-off, under control.</h1>
            <p>Find active orders by zone, collect payment, and leave clear notes for the delivery record.</p>
        </div>
        <span class="hero-kicker">Rider workspace</span>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice notice-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="notice notice-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <section class="panel" aria-labelledby="deliveries-heading">
        <div class="panel-header">
            <div>
                <h2 id="deliveries-heading">Available deliveries</h2>
                <p>Search by delivery zone to focus on your next handoff.</p>
            </div>
        </div>
        <div class="toolbar">
            <input type="text" id="searchZoneInput" placeholder="Filter by zone (for example, North Zone)" onkeyup="filterByZone()" aria-label="Filter deliveries by zone">
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr><th>Order</th><th>Zone</th><th>Item</th><th>Delivery</th><th>Payment</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody id="ordersTableBody">
                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['zone']); ?></td>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td><?php echo $order['is_express'] ? '<span class="pill pill-express">Express</span>' : '<span class="pill">Standard</span>'; ?></td>
                        <td>
                            <span class="pill <?php echo $order['payment_status'] === 'Unpaid' ? 'pill-unpaid' : 'pill-paid'; ?>"><?php echo htmlspecialchars($order['payment_status']); ?></span>
                            <?php if($order['payment_status'] === 'Unpaid'): ?>
                                <a href="index.php?action=collectCash&id=<?php echo $order['id']; ?>" class="btn btn-success">Collect cash</a>
                            <?php endif; ?>
                        </td>
                        <td><span class="pill"><?php echo htmlspecialchars($order['status']); ?></span></td>
                        <td>
                            <?php if($order['status'] === 'Preparing'): ?>
                                <a href="index.php?action=updateDeliveryStatus&id=<?php echo $order['id']; ?>&status=Out for Delivery" class="btn">Pick up</a>
                            <?php elseif($order['status'] === 'Out for Delivery'): ?>
                                <a href="index.php?action=updateDeliveryStatus&id=<?php echo $order['id']; ?>&status=Delivered" class="btn btn-success">Mark delivered</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-grid two-column">
        <section class="panel" aria-labelledby="notes-heading">
            <div class="panel-header">
                <div>
                    <h2 id="notes-heading">Delivery logs &amp; notes</h2>
                    <p>Record delivery details for the order history.</p>
                </div>
            </div>
            <form action="index.php?action=addNote" method="POST" class="stacked-form" onsubmit="return validateNoteForm()">
                <input type="number" id="order_id" name="order_id" placeholder="Order ID" required>
                <input type="text" id="note" name="note" placeholder="Write a delivery note (for example, Left at door)" required>
                <button type="submit">Add note</button>
            </form>
        </section>

        <section class="panel" aria-labelledby="recent-notes-heading">
            <div class="panel-header">
                <div>
                    <h2 id="recent-notes-heading">Recent notes</h2>
                    <p>Quickly review or remove notes you have created.</p>
                </div>
            </div>
            <ul id="notesList" class="notes-list">
                <?php while ($note = mysqli_fetch_assoc($notes_result)): ?>
                    <li id="note-<?php echo $note['id']; ?>">
                        <strong>Order #<?php echo $note['order_id']; ?></strong>
                        <span class="feedback-meta"><?php echo htmlspecialchars($note['note']); ?></span>
                        <button type="button" onclick="deleteNote(<?php echo $note['id']; ?>)" class="delete-btn">Delete</button>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </section>
</main>
</body>
</html>
