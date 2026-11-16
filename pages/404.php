<?php http_response_code(404); ?>
<div class="error-page">
  <span class="error-page__code" aria-hidden="true">404</span>
  <div class="error-page__content">
    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <h1>Page Not Found</h1>
    <p>The page you’re looking for doesn’t exist, may have moved, or perhaps we’re still building it.</p>
    <div class="flex-c gap-4" style="justify-content:center;flex-wrap:wrap;">
      <a href="/?page=home" class="btn btn--navy">Back to Home</a>
      <a href="/?page=search_results" class="btn btn--outline">Browse Destinations</a>
    </div>
    <p style="margin-top:2rem;font-size:.82rem;color:var(--text-light);">If you think this is an error, <a href="/?page=contact" style="color:var(--navy);">let us know</a>.</p>
  </div>
</div>
