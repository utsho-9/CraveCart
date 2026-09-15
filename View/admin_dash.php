<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin dashboard | CraveCart</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="Controller/JS/adminActions.js"></script>
</head>
<body>
<main class="app-shell">
    <nav class="topbar" aria-label="Primary navigation">
        <a class="brand" href="index.php?action=admin_dash"><span class="brand-mark">✦</span> CraveCart <span class="topbar-context">Admin</span></a>
        <a href="index.php?action=logout" class="btn logout-btn">Log out</a>
    </nav>

    <header class="page-hero">
        <div class="hero-copy">
            <span class="eyebrow">Operations overview</span>
            <h1>A healthier picture of the platform.</h1>
            <p>Manage accounts, settle delivered orders, and keep customer feedback moving through review.</p>
        </div>
        <span class="hero-kicker">Admin workspace</span>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice notice-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="notice notice-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <section class="metric-card" aria-label="System revenue">
        <div>
            <span class="metric-label">System revenue</span>
            <span class="metric-value">$<?php echo number_format($total_revenue, 2); ?></span>
        </div>
        <span class="metric-icon" aria-hidden="true">↗</span>
    </section>

    <section class="panel" aria-labelledby="users-heading">
        <div class="panel-header">
            <div>
                <h2 id="users-heading">User management</h2>
                <p>Search the account directory or create a new user profile.</p>
            </div>
        </div>

        <div class="toolbar">
            <input type="text" id="searchInput" placeholder="Search by name or email..." onkeyup="searchUsers()" aria-label="Search users">
        </div>

        <form action="index.php?action=createUser" method="POST" onsubmit="return validateForm()">
            <input type="text" id="name" name="name" placeholder="Full name" required>
            <input type="email" id="email" name="email" placeholder="Email address" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="restaurant">Manager</option>
                <option value="customer">Customer</option>
                <option value="delivery">Delivery</option>
            </select>
            <button type="submit">Create user</button>
        </form>

        <div class="table-wrap with-top-gap">
            <table>
                <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
                </thead>
                <tbody id="userTableBody">
                <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                    <tr id="row-<?php echo $user['id']; ?>">
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="pill"><?php echo htmlspecialchars($user['role']); ?></span></td>
                        <td><button type="button" onclick="deleteUser(<?php echo $user['id']; ?>)" class="delete-btn">Delete</button></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-grid two-column">
        <section class="panel" aria-labelledby="payments-heading">
            <div class="panel-header">
                <div>
                    <h2 id="payments-heading">Pending payments</h2>
                    <p>Delivered orders that still need payment approval.</p>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Order</th><th>Amount</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php while ($pay = mysqli_fetch_assoc($payments_result)): ?>
                        <tr>
                            <td>#<?php echo $pay['id']; ?></td>
                            <td>$<?php echo number_format($pay['total_price'], 2); ?></td>
                            <td><a href="index.php?action=approvePayment&id=<?php echo $pay['id']; ?>" class="btn btn-success">Mark as paid</a></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel" aria-labelledby="feedback-heading">
            <div class="panel-header">
                <div>
                    <h2 id="feedback-heading">Feedback moderation</h2>
                    <p>Review customer comments and decide whether to show them.</p>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Feedback</th><th>Visibility</th></tr></thead>
                    <tbody>
                    <?php while ($fb = mysqli_fetch_assoc($feedback_result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($fb['name']); ?></strong><span class="feedback-meta"><?php echo htmlspecialchars($fb['message']); ?></span></td>
                            <td>
                                <form action="index.php?action=moderateFeedback" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $fb['id']; ?>">
                                    <select name="admin_action" aria-label="Moderation action for <?php echo htmlspecialchars($fb['name']); ?>">
                                        <option value="Approved" <?php if($fb['admin_action'] == 'Approved') echo 'selected'; ?>>Approve</option>
                                        <option value="Hidden" <?php if($fb['admin_action'] == 'Hidden') echo 'selected'; ?>>Hide</option>
                                    </select>
                                    <button type="submit" class="btn-secondary">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</main>
</body>
</html>
