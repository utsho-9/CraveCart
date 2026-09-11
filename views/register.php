<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account | CraveCart</title>
    <link rel="stylesheet" type="text/css" href="assets/style.css">
</head>
<body class="auth-page">
<main class="auth-shell">
    <aside class="auth-intro">
        <a class="brand" href="index.php?action=login">
            <span class="brand-mark">✦</span> CraveCart
        </a>
        <div>
            <span class="eyebrow">One connected platform</span>
            <h1>Bring every order and handoff into focus.</h1>
            <p>Join as a customer, restaurant manager, or delivery rider and manage your part of the journey with ease.</p>
        </div>
        <span class="auth-note">Built for local restaurants and their customers.</span>
    </aside>

    <section class="auth-card" aria-labelledby="register-heading">
        <h2 id="register-heading">Create your account</h2>
        <p>Choose the role that best describes how you’ll use CraveCart.</p>

        <?php if(isset($error)): ?>
            <div class="notice notice-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="index.php?action=register" method="POST">
            <input type="text" name="name" placeholder="Full name" autocomplete="name" required>
            <input type="email" name="email" placeholder="Email address" autocomplete="email" required>
            <input type="password" name="password" placeholder="Create a password" autocomplete="new-password" required>
            <select name="role" required>
                <option value="" disabled selected>Select account type</option>
                <option value="customer">Customer</option>
                <option value="restaurant">Restaurant Manager</option>
                <option value="delivery">Delivery Rider</option>
            </select>
            <button type="submit">Create account</button>
        </form>

        <p class="auth-switch">Already have an account? <a href="index.php?action=login">Sign in</a></p>
    </section>
</main>
</body>
</html>
