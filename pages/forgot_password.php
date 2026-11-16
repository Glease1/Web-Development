<?php
if (isLoggedIn()) {
    redirect('home');
}

$sent   = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = post('email');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (empty($errors)) {
        // In production: generate a secure reset token, save to DB, send email
        // For now we simulate success to not expose whether an email exists
        $sent = true;
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
      <?php if ($sent): ?>
      <div style="text-align:center;padding:1rem 0 2rem;">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--success-text)" stroke-width="1.5" style="margin:0 auto 1.5rem;" aria-hidden="true">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
        </svg>
        <h1 style="font-size:1.75rem;">Check Your Inbox</h1>
        <p class="lead" style="margin-top:.5rem;">If an account exists for that email address, we've sent a password reset link. It may take a couple of minutes to arrive.</p>
        <p style="font-size:.82rem;color:var(--text-muted);margin-top:1rem;">Don't see it? Check your spam folder, or <a href="/?page=forgot_password" style="color:var(--navy);">try again</a>.</p>
        <a href="/?page=login" class="btn btn--navy" style="margin-top:1.5rem;">Back to Sign In</a>
      </div>
      <?php else: ?>
      <h1>Reset Password</h1>
      <p class="lead">Enter the email address on your account and we'll send you a reset link.</p>

      <form method="post" action="/?page=forgot_password" novalidate>
        <?= csrfField() ?>
        <div class="form-group">
          <label class="form-label" for="forgot-email">Email Address</label>
          <input class="form-input" id="forgot-email" type="email" name="email"
                 value="<?= h(post('email')) ?>" autocomplete="email" required autofocus>
          <?php if (isset($errors['email'])): ?>
          <span class="form-error" role="alert"><?= h($errors['email']) ?></span>
          <?php endif; ?>
        </div>
        <button type="submit" class="btn btn--navy btn--lg" style="width:100%;justify-content:center;">Send Reset Link</button>
      </form>

      <p class="auth-switch"><a href="/?page=login">&larr; Back to sign in</a></p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Image panel -->
  <div class="auth-panel auth-panel--image">
    <div class="auth-quote">
      <blockquote>
        &ldquo;Once a year, go somewhere you have never been before.&rdquo;
      </blockquote>
      <cite>Dalai Lama</cite>
    </div>
  </div>
</div>
