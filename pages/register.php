<?php
if (isLoggedIn()) {
    redirect('home');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name     = post('name');
    $email    = post('email');
    $password = post('password');
    $confirm  = post('confirm_password');

    if (strlen($name) < 2)
        $errors['name'] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Please enter a valid email address.';
    if (strlen($password) < 8)
        $errors['password'] = 'Password must be at least 8 characters.';
    elseif ($password !== $confirm)
        $errors['confirm_password'] = 'Passwords do not match.';
    if (empty($_POST['terms']))
        $errors['terms'] = 'You must agree to the terms to create an account.';

    if (empty($errors) && $tm) {
        if ($tm->registerUser($name, $email, $password)) {
            $tm->loginUser($email, $password);
            flash('success', 'Welcome to Meridian Horizons, ' . $name . '! Let’s find your next adventure.');
            redirect('home');
        } else {
            $errors['email'] = 'An account with that email already exists. Try signing in.';
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
      <h1>Create Your Account</h1>
      <p class="lead">Create a demo account to try the booking flow.</p>

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

      <form method="post" action="/?page=register" novalidate>
        <?= csrfField() ?>
        <div class="form-group">
          <label class="form-label" for="reg-name">Full Name</label>
          <input class="form-input" id="reg-name" type="text" name="name"
                 value="<?= h(post('name')) ?>" autocomplete="name" required autofocus>
          <?php if (isset($errors['name'])): ?>
          <span class="form-error" role="alert"><?= h($errors['name']) ?></span>
          <?php endif; ?>
        </div>
        <div class="form-group">
          <label class="form-label" for="reg-email">Email Address</label>
          <input class="form-input" id="reg-email" type="email" name="email"
                 value="<?= h(post('email')) ?>" autocomplete="email" required>
          <?php if (isset($errors['email'])): ?>
          <span class="form-error" role="alert"><?= h($errors['email']) ?></span>
          <?php endif; ?>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="reg-pass">Password</label>
            <input class="form-input" id="reg-pass" type="password" name="password"
                   autocomplete="new-password" required minlength="8">
            <?php if (isset($errors['password'])): ?>
            <span class="form-error" role="alert"><?= h($errors['password']) ?></span>
            <?php else: ?>
            <span class="form-hint">Minimum 8 characters</span>
            <?php endif; ?>
          </div>
          <div class="form-group">
            <label class="form-label" for="reg-confirm">Confirm Password</label>
            <input class="form-input" id="reg-confirm" type="password" name="confirm_password"
                   autocomplete="new-password" required>
            <?php if (isset($errors['confirm_password'])): ?>
            <span class="form-error" role="alert"><?= h($errors['confirm_password']) ?></span>
            <?php endif; ?>
          </div>
        </div>
        <div class="form-check" style="margin-bottom:1.5rem;">
          <input type="checkbox" id="terms" name="terms" value="1">
          <label for="terms">I agree to the <a href="#" style="color:var(--navy);text-decoration:underline;text-underline-offset:3px;">Terms of Service</a> and <a href="#" style="color:var(--navy);text-decoration:underline;text-underline-offset:3px;">Privacy Policy</a></label>
        </div>
        <?php if (isset($errors['terms'])): ?>
        <span class="form-error" role="alert" style="display:block;margin-top:-.75rem;margin-bottom:1rem;"><?= h($errors['terms']) ?></span>
        <?php endif; ?>
        <button type="submit" class="btn btn--gold btn--lg" style="width:100%;justify-content:center;">Create Account</button>
      </form>

      <p class="auth-switch">Already have an account? <a href="/?page=login">Sign in</a></p>
    </div>
  </div>

  <!-- Image panel -->
  <div class="auth-panel auth-panel--image">
    <div class="auth-quote">
      <blockquote>
        &ldquo;Not all those who wander are lost.&rdquo;
      </blockquote>
      <cite>J.R.R. Tolkien</cite>
    </div>
  </div>
</div>
