<?php
$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name    = post('name');
    $email   = post('email');
    $subject = post('subject');
    $message = post('message');

    if (strlen($name) < 2)                         $errors['name']    = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']   = 'Please enter a valid email address.';
    if (empty($subject))                            $errors['subject'] = 'Please choose a subject.';
    if (strlen($message) < 20)                      $errors['message'] = 'Please write a message of at least 20 characters.';

    if (empty($errors)) {
        // In production: send email or save to DB
        $success = true;
        flash('success', 'Thank you, ' . $name . '. We’ll be in touch within one business day.');
    }
}
?>

<div class="page-header">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="/?page=home">Home</a>
      <span class="breadcrumb__sep" aria-hidden="true">/</span>
      <span class="breadcrumb__current">Contact</span>
    </nav>
    <span class="eyebrow page-header__eyebrow">Get In Touch</span>
    <h1>Let’s Start Planning</h1>
    <p class="lead">Tell us where you want to go. We’ll handle everything else.</p>
  </div>
</div>

<section class="section">
  <div class="container">
    <?php renderFlash(); ?>
    <div class="contact-grid">

      <!-- Contact info -->
      <div class="contact-info">
        <h2>We’d Love to Hear From You</h2>
        <p>Whether you have a specific destination in mind or simply a blank calendar and an itch to travel, our consultants are here to help you shape something remarkable.</p>

        <div class="contact-detail">
          <div class="contact-detail__icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.9 1.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
          </div>
          <div class="contact-detail__text">
            <span class="label">Telephone</span>
            <a href="tel:+441179000000" class="value">+44 (0)117 900 0000</a>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail__icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
              <polyline points="22,6 12,13 2,6"/>
            </svg>
          </div>
          <div class="contact-detail__text">
            <span class="label">Email</span>
            <a href="mailto:hello@meridianhorizons.co.uk" class="value">hello@meridianhorizons.co.uk</a>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail__icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
          </div>
          <div class="contact-detail__text">
            <span class="label">Office</span>
            <span class="value">12 Queen Square, Bath, BA1 2HN</span>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail__icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
          <div class="contact-detail__text">
            <span class="label">Opening Hours</span>
            <span class="value">Mon–Fri 9am–6pm &bull; Sat 10am–4pm</span>
          </div>
        </div>

        <div style="margin-top:2.5rem;padding:1.5rem;background:var(--gold-pale);border-radius:var(--r-lg);border-left:4px solid var(--gold);">
          <p style="font-size:.88rem;color:var(--text-muted);line-height:1.7;margin:0;">
            <strong style="color:var(--navy);">Emergency line:</strong> If you’re currently travelling with us and need urgent assistance, call <a href="tel:+441179000001" style="color:var(--navy);font-weight:700;">+44 (0)117 900 0001</a> — available 24 hours a day, 365 days a year.
          </p>
        </div>
      </div>

      <!-- Contact form -->
      <div class="contact-form-card">
        <?php if ($success): ?>
        <div style="text-align:center;padding:3rem 1rem;">
          <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--success-text)" stroke-width="1.5" style="margin:0 auto 1.5rem;" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/>
          </svg>
          <h3 style="color:var(--success-text);">Message Sent</h3>
          <p class="lead" style="margin-top:.5rem;">We’ll be back in touch within one business day.</p>
          <a href="/?page=home" class="btn btn--navy" style="margin-top:1.5rem;">Back to Home</a>
        </div>
        <?php else: ?>
        <h3>Send Us a Message</h3>
        <p class="lead">We aim to reply within one business day.</p>

        <form method="post" action="/?page=contact" novalidate>
          <?= csrfField() ?>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="contact-name">Full Name <span aria-hidden="true" style="color:var(--error-text);">*</span></label>
              <input class="form-input<?= isset($errors['name']) ? ' error' : '' ?>" id="contact-name" type="text" name="name"
                     value="<?= h(post('name')) ?>" autocomplete="name" required>
              <?php if (isset($errors['name'])): ?>
              <span class="form-error" role="alert"><?= h($errors['name']) ?></span>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <label class="form-label" for="contact-email">Email Address <span aria-hidden="true" style="color:var(--error-text);">*</span></label>
              <input class="form-input<?= isset($errors['email']) ? ' error' : '' ?>" id="contact-email" type="email" name="email"
                     value="<?= h(post('email')) ?>" autocomplete="email" required>
              <?php if (isset($errors['email'])): ?>
              <span class="form-error" role="alert"><?= h($errors['email']) ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="contact-subject">What can we help with? <span aria-hidden="true" style="color:var(--error-text);">*</span></label>
            <select class="form-select<?= isset($errors['subject']) ? ' error' : '' ?>" id="contact-subject" name="subject" required>
              <option value="">Please select…</option>
              <option value="new-trip" <?= post('subject')==='new-trip'?'selected':'' ?>>Planning a new trip</option>
              <option value="existing" <?= post('subject')==='existing'?'selected':'' ?>>Query about an existing booking</option>
              <option value="honeymoon" <?= post('subject')==='honeymoon'?'selected':'' ?>>Honeymoon or special occasion</option>
              <option value="group" <?= post('subject')==='group'?'selected':'' ?>>Group travel (8+ people)</option>
              <option value="other" <?= post('subject')==='other'?'selected':'' ?>>Something else</option>
            </select>
            <?php if (isset($errors['subject'])): ?>
            <span class="form-error" role="alert"><?= h($errors['subject']) ?></span>
            <?php endif; ?>
          </div>
          <div class="form-group">
            <label class="form-label" for="contact-message">Your Message <span aria-hidden="true" style="color:var(--error-text);">*</span></label>
            <textarea class="form-textarea<?= isset($errors['message']) ? ' error' : '' ?>" id="contact-message" name="message" rows="5" required><?= h(post('message')) ?></textarea>
            <?php if (isset($errors['message'])): ?>
            <span class="form-error" role="alert"><?= h($errors['message']) ?></span>
            <?php endif; ?>
          </div>
          <div class="form-check" style="margin-bottom:1.5rem;">
            <input type="checkbox" id="contact-consent" name="consent" value="1" required>
            <label for="contact-consent">I’m happy for Meridian Horizons to contact me by email regarding my enquiry. I can opt out at any time.</label>
          </div>
          <button type="submit" class="btn btn--gold btn--lg" style="width:100%;justify-content:center;">Send Message</button>
        </form>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>
