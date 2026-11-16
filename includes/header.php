<?php
declare(strict_types=1);

$currentPage = currentPage();
$user        = currentUser();

// Pages where the nav starts transparent (hero behind it)
$transparentNavPages = ['home'];
$navClass = in_array($currentPage, $transparentNavPages, true) ? '' : ' always-solid';

$pageTitle = match($currentPage) {
    'home'                  => 'Bespoke Travel Experiences',
    'about'                 => 'About Us',
    'contact'               => 'Contact',
    'search_results'        => 'Search Destinations',
    'booking_details'       => 'Book Your Trip',
    'basket'                => 'Your Basket',
    'login'                 => 'Sign In',
    'register'              => 'Create Account',
    'forgot_password'       => 'Forgot Password',
    'faq'                   => 'Frequently Asked Questions',
    'terms'                 => 'Booking Terms & Conditions',
    'privacy'               => 'Privacy Policy',
    'cookies'               => 'Cookie Policy',
    'admin_dashboard'       => 'Admin Dashboard',
    'admin_manage_listings' => 'Manage Listings',
    'admin_view_users'      => 'Manage Users',
    default                 => 'Page Not Found',
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Meridian Horizons — Award-winning bespoke travel experiences crafted around you. Luxury itineraries to over 120 countries since 2003.">
  <title><?= h($pageTitle) ?> &mdash; Meridian Horizons</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,500&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="/style.css">

  <!-- Favicon placeholder -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#9770;</text></svg>">
</head>
<body>

<nav class="site-nav<?= $navClass ?>" id="site-nav" aria-label="Main navigation">
  <div class="site-nav__inner">

    <!-- Logo -->
    <a href="/?page=home" class="site-logo" aria-label="Meridian Horizons Home">
      <span class="site-logo__primary">Meridian</span>
      <span class="site-logo__secondary">Horizons</span>
    </a>

    <!-- Desktop nav links -->
    <ul class="nav-links" role="list">
      <li><a href="/?page=home"         class="<?= $currentPage === 'home'    ? 'active' : '' ?>">Home</a></li>
      <li><a href="/?page=search_results" class="<?= $currentPage === 'search_results' ? 'active' : '' ?>">Destinations</a></li>
      <li><a href="/?page=about"        class="<?= $currentPage === 'about'   ? 'active' : '' ?>">About</a></li>
      <li><a href="/?page=contact"      class="<?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a></li>
      <?php if ($user && $user['role'] === 'admin'): ?>
      <li><a href="/?page=admin_dashboard" class="<?= str_starts_with($currentPage, 'admin') ? 'active' : '' ?>">Admin</a></li>
      <?php endif; ?>
    </ul>

    <!-- Desktop actions -->
    <div class="nav-actions">
      <?php if ($user): ?>
        <span class="user-greeting">Hi, <?= h(explode(' ', $user['name'])[0]) ?></span>
        <a href="/?page=basket" class="btn btn--outline-white btn--sm" aria-label="View basket">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
          </svg>
          Basket
        </a>
        <a href="/?page=logout" class="btn btn--outline-white btn--sm">Sign Out</a>
      <?php else: ?>
        <a href="/?page=login"    class="btn btn--outline-white btn--sm">Sign In</a>
        <a href="/?page=register" class="btn btn--gold btn--sm">Get Started</a>
      <?php endif; ?>
    </div>

    <!-- Mobile hamburger -->
    <button class="nav-hamburger" id="nav-hamburger" aria-label="Toggle menu" aria-expanded="false" aria-controls="nav-mobile">
      <span></span>
      <span></span>
      <span></span>
    </button>

  </div>
</nav>

<!-- Mobile menu -->
<div class="nav-mobile" id="nav-mobile" role="navigation" aria-label="Mobile navigation">
  <a href="/?page=home">Home</a>
  <a href="/?page=search_results">Destinations</a>
  <a href="/?page=about">About Us</a>
  <a href="/?page=contact">Contact</a>
  <?php if ($user && $user['role'] === 'admin'): ?>
  <a href="/?page=admin_dashboard">Admin Panel</a>
  <?php endif; ?>
  <?php if ($user): ?>
  <a href="/?page=basket">Basket</a>
  <a href="/?page=logout">Sign Out</a>
  <?php else: ?>
  <a href="/?page=register" class="btn btn--gold mt-4">Get Started</a>
  <a href="/?page=login">Sign In</a>
  <?php endif; ?>
</div>

<script>
(function() {
  var nav       = document.getElementById('site-nav');
  var hamburger = document.getElementById('nav-hamburger');
  var mobileNav = document.getElementById('nav-mobile');

  // Scroll: make nav solid
  <?php if (in_array($currentPage, $transparentNavPages, true)): ?>
  var threshold = 60;
  function onScroll() {
    if (window.scrollY > threshold) {
      nav.classList.add('scrolled');
    } else {
      nav.classList.remove('scrolled');
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  <?php endif; ?>

  // Mobile menu toggle
  hamburger.addEventListener('click', function() {
    var open = hamburger.classList.toggle('open');
    mobileNav.classList.toggle('open', open);
    hamburger.setAttribute('aria-expanded', open);
    document.body.style.overflow = open ? 'hidden' : '';
  });

  // Close on outside click
  document.addEventListener('click', function(e) {
    if (!nav.contains(e.target) && !mobileNav.contains(e.target)) {
      hamburger.classList.remove('open');
      mobileNav.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }
  });
})();
</script>

<main id="main-content">
