<?php http_response_code(403); ?>
<div class="error-page">
  <span class="error-page__code" aria-hidden="true">403</span>
  <div class="error-page__content">
    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
      <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    </svg>
    <h1>Access Denied</h1>
    <p>You don’t have permission to view this page. If you believe you should have access, please sign in with an appropriate account.</p>
    <div class="flex-c gap-4" style="justify-content:center;flex-wrap:wrap;">
      <a href="/?page=home"  class="btn btn--navy">Back to Home</a>
      <a href="/?page=login" class="btn btn--outline">Sign In</a>
    </div>
  </div>
</div>
