<?php
if (isLoggedIn()) {
    redirect('home');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email    = post('email');
    $password = post('password');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 1) {
        $errors['password'] = 'Please enter your password.';
    }

    if (empty($errors) && $tm) {
        if ($tm->loginUser($email, $password)) {
            $returnTo = isset($_SESSION['_return_to']) ? $_SESSION['_return_to'] : 'home';
            unset($_SESSION['_return_to']);
            flash('success', 'Welcome back, ' . currentUser()['name'] . '!');
            redirect($returnTo);
        } else {
            $errors['general'] = 'Those details don’t match our records. Please try again.';
        }
    } elseif (empty($errors) && !$tm) {
        $errors['general'] = 'The service is temporarily unavailable. Please try again shortly.';
    }
}
?>

<div class="auth-wrapper">
  <!-- Form panel -->
  <div class="auth-panel">
    <a href="/?page=home" class="auth-logo-link">
      <span class="site-logo">
        <span class="site-logo__primary" style="color:var(--navy);">Meridian</span>
        <span class="site-logo__secondary">Horizons</span>
      </span>
    </a>

    <div class="auth-form">
      <h1>Welcome Back</h1>
      <p class="lead">Sign in to access your bookings and saved trips.</p>

      <?php renderFlash(); ?>

      <?php if (!empty($errors['general'])): ?>
      <div class="flash flash--error" role="alert">
        <span class="flash__icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
        </span>
        <span class="flash__message"><?= h($errors['general']) ?></span>
      </div>
      <?php endif; ?>

      <form method="post" action="/?page=login" novalidate>
        <?= csrfField() ?>
        <div class="form-group">
          <label class="form-label" for="login-email">Email Address</label>
          <input class="form-input" id="login-email" type="email" name="email"
                 value="<?= h(post('email')) ?>" autocomplete="email" required autofocus>
          <?php if (isset($errors['email'])): ?>
          <span class="form-error" role="alert"><?= h($errors['email']) ?></span>
          <?php endif; ?>
        </div>
        <div class="form-group">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <label class="form-label" for="login-password">Password</label>
            <a href="/?page=forgot_password" style="font-size:.78rem;color:var(--text-muted);text-decoration:underline;text-underline-offset:3px;">Forgot password?</a>
          </div>
          <input class="form-input" id="login-password" type="password" name="password"
                 autocomplete="current-password" required>
          <?php if (isset($errors['password'])): ?>
          <span class="form-error" role="alert"><?= h($errors['password']) ?></span>
          <?php endif; ?>
        </div>
        <div class="form-check" style="margin-bottom:1.5rem;">
          <input type="checkbox" id="remember-me" name="remember_me" value="1">
          <label for="remember-me">Keep me signed in</label>
        </div>
        <button type="submit" class="btn btn--navy btn--lg" style="width:100%;justify-content:center;">Sign In</button>
      </form>

      <p class="auth-switch">Don’t have an account? <a href="/?page=register">Create one for free</a></p>
    </div>
  </div>

  <!-- Image panel -->
  <div class="auth-panel auth-panel--image">
    <div class="auth-quote">
      <blockquote>
        &ldquo;The world is a book, and those who do not travel read only one page.&rdquo;
      </blockquote>
      <cite>Saint Augustine</cite>
    </div>
  </div>
</div>
