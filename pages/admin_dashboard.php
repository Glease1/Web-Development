<?php
// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'update_booking_status') {
    verifyCsrf();
    if ($tm) $tm->updateBookingStatus((int)post('booking_id'), post('status'));
    flash('success', 'Booking status updated.');
    redirect('admin_dashboard');
}

$stats    = $tm ? $tm->getDashboardStats() : ['total_users'=>0,'total_destinations'=>0,'total_bookings'=>0,'total_revenue'=>0.0];
$bookings = $tm ? $tm->getAllBookings() : [];

$counts = [
    'all'       => count($bookings),
    'pending'   => count(array_filter($bookings, fn($b) => $b['status'] === 'pending')),
    'confirmed' => count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed')),
    'cancelled' => count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled')),
];
?>

<div class="admin-wrapper">
  <div class="admin-header">
    <div class="admin-header__inner">
      <div>
        <h1>Dashboard</h1>
        <p style="color:var(--text-muted);font-size:.88rem;margin-top:.25rem;">Overview of Meridian Horizons operations</p>
      </div>
      <a href="/?page=admin_manage_listings&action=new" class="btn btn--gold">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Destination
      </a>
    </div>
  </div>

  <div class="admin-body">
    <nav class="admin-subnav" aria-label="Admin navigation">
      <a href="/?page=admin_dashboard"        class="active">Dashboard</a>
      <a href="/?page=admin_manage_listings">Listings</a>
      <a href="/?page=admin_view_users">Users</a>
    </nav>

    <?php renderFlash(); ?>

    <!-- Stat cards -->
    <div class="stat-cards">
      <div class="stat-card">
        <div class="stat-card__info">
          <div class="stat-card__label">Total Bookings</div>
          <div class="stat-card__value"><?= number_format((int)$stats['total_bookings']) ?></div>
        </div>
        <div class="stat-card__icon" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card__info">
          <div class="stat-card__label">Revenue (confirmed)</div>
          <div class="stat-card__value"><?= formatPrice((float)$stats['total_revenue']) ?></div>
        </div>
        <div class="stat-card__icon" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card__info">
          <div class="stat-card__label">Pending Actions</div>
          <div class="stat-card__value" style="<?= $counts['pending'] > 0 ? 'color:var(--warning-text)' : '' ?>"><?= $counts['pending'] ?></div>
        </div>
        <div class="stat-card__icon" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card__info">
          <div class="stat-card__label">Registered Users</div>
          <div class="stat-card__value"><?= number_format((int)$stats['total_users']) ?></div>
        </div>
        <div class="stat-card__icon" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
      </div>
    </div>

    <!-- Bookings header -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1rem;">
      <h2 style="font-size:1.25rem;margin:0;">All Bookings</h2>
      <input type="search" id="booking-search" placeholder="Search customer or destination…" aria-label="Search bookings"
             style="border:1.5px solid var(--border);border-radius:var(--r-full);padding:7px 16px;font-size:.83rem;color:var(--text);background:var(--white);outline:none;width:240px;transition:border-color var(--t-fast);"
             onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--border)'">
    </div>

    <!-- Status filter tabs -->
    <div class="booking-filter-tabs" role="tablist" aria-label="Filter by status" style="margin-bottom:1.25rem;">
      <button class="bft bft--active" data-filter="all"       role="tab" aria-selected="true">All <span class="bft__count"><?= $counts['all'] ?></span></button>
      <button class="bft"             data-filter="confirmed" role="tab">Confirmed <span class="bft__count bft__count--confirmed"><?= $counts['confirmed'] ?></span></button>
      <button class="bft"             data-filter="pending"   role="tab">Pending <span class="bft__count bft__count--pending"><?= $counts['pending'] ?></span></button>
      <button class="bft"             data-filter="cancelled" role="tab">Cancelled <span class="bft__count bft__count--cancelled"><?= $counts['cancelled'] ?></span></button>
    </div>

    <?php if (empty($bookings)): ?>
    <div style="background:var(--white);border:1px solid var(--border-light);border-radius:var(--r-lg);padding:3rem;text-align:center;color:var(--text-muted);">
      <p>No bookings yet.</p>
    </div>
    <?php else: ?>
    <div class="table-wrap" id="bookings-wrap">
      <table class="data-table" id="bookings-table" aria-label="All bookings">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Destination</th>
            <th>Customer</th>
            <th>Travel Date</th>
            <th>Booked</th>
            <th>Pax</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $b):
            $ref = 'MH-' . str_pad((string)$b['id'], 5, '0', STR_PAD_LEFT);
          ?>
          <tr class="booking-row"
              data-status="<?= h($b['status']) ?>"
              data-search="<?= h(strtolower($b['destination_name'] . ' ' . $b['user_name'] . ' ' . $b['email'] . ' ' . $ref)) ?>"
              onclick="location.href='/?page=admin_booking_detail&id=<?= (int)$b['id'] ?>'"
              title="Open booking <?= h($ref) ?>">
            <td style="font-size:.78rem;font-weight:700;font-family:monospace;letter-spacing:.05em;color:var(--text-muted);"><?= h($ref) ?></td>
            <td>
              <strong style="font-size:.88rem;"><?= h($b['destination_name']) ?></strong><br>
              <span style="font-size:.74rem;color:var(--text-muted);"><?= h($b['country']) ?></span>
            </td>
            <td>
              <span style="font-size:.88rem;"><?= h($b['user_name']) ?></span><br>
              <span style="font-size:.74rem;color:var(--text-muted);"><?= h($b['email']) ?></span>
            </td>
            <td style="white-space:nowrap;font-size:.84rem;"><?= h(formatDate($b['travel_date'])) ?></td>
            <td style="white-space:nowrap;font-size:.76rem;color:var(--text-muted);"><?= h(date('j M Y', strtotime($b['created_at']))) ?></td>
            <td style="font-size:.88rem;"><?= (int)$b['passengers'] ?></td>
            <td style="font-weight:700;font-size:.9rem;"><?= formatPrice((float)$b['total_price']) ?></td>
            <td><span class="badge badge--<?= h($b['status']) ?>"><?= ucfirst(h($b['status'])) ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div id="no-results" style="display:none;padding:2.5rem;text-align:center;color:var(--text-muted);font-size:.9rem;">No bookings match your search.</div>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
(function () {
  var rows   = document.querySelectorAll('#bookings-table .booking-row');
  var tabs   = document.querySelectorAll('.bft');
  var search = document.getElementById('booking-search');
  var noRes  = document.getElementById('no-results');
  var filter = 'all', query = '';

  function apply() {
    var visible = 0;
    rows.forEach(function (r) {
      var ok = (filter === 'all' || r.dataset.status === filter)
            && (query === '' || r.dataset.search.indexOf(query) !== -1);
      r.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });
    if (noRes) noRes.style.display = visible === 0 ? '' : 'none';
  }

  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      tabs.forEach(function (x) { x.classList.remove('bft--active'); x.setAttribute('aria-selected','false'); });
      t.classList.add('bft--active'); t.setAttribute('aria-selected','true');
      filter = t.dataset.filter;
      apply();
    });
  });

  if (search) {
    search.addEventListener('input', function () { query = search.value.toLowerCase().trim(); apply(); });
  }
})();
</script>

