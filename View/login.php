<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | CraveCart</title>
    <link rel="stylesheet" type="text/css" href="assets/style.css">
</head>
<body class="auth-page">
<main class="auth-shell">
    <aside class="auth-intro">
        <a class="brand" href="index.php?action=login">
            <span class="brand-mark">✦</span> CraveCart
        </a>
        <div>
            <span class="eyebrow">Restaurant delivery, simplified</span>
            <h1>Good food, delivered with a smoother flow.</h1>
            <p>Order from your favorite local kitchens and keep every delivery on track in one place.</p>
        </div>
        <span class="auth-note">Fresh menus. Clear updates. Faster delivery.</span>
    </aside>

    <section class="auth-card" aria-labelledby="login-heading">
        <h2 id="login-heading">Welcome back</h2>
        <p>Sign in to continue to your CraveCart dashboard.</p>

        <?php if(isset($error)): ?>
            <div class="notice notice-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['success'])): ?>
            <div class="notice notice-success">Registration successful! Please sign in.</div>
        <?php endif; ?>

        <form action="index.php?action=login" method="POST">
            <input type="email" name="email" placeholder="Email address" autocomplete="email" required>
            <input type="password" name="password" placeholder="Password" autocomplete="current-password" required>
            <label><input type="checkbox" name="remember"> Remember me on this device</label>
            <button type="submit">Sign in</button>
        </form>

        <p class="auth-switch">New to CraveCart? <a href="index.php?action=register">Create an account</a></p>
    </section>
</main>
</body>
</html>
