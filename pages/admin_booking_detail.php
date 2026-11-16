<?php
$bookingId = (int) get('id');
$booking   = null;

if ($tm && $bookingId > 0) {
    $booking = $tm->getBookingById($bookingId);
}

if ($booking === null) {
    flash('error', 'Booking not found.');
    redirect('admin_dashboard');
}

// POST: update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $newStatus = post('status');
    if ($tm && in_array($newStatus, ['pending', 'confirmed', 'cancelled'], true)) {
        $tm->updateBookingStatus($bookingId, $newStatus);
        flash('success', 'Status updated to ' . ucfirst($newStatus) . '.');
    }
    redirect('admin_booking_detail', ['id' => $bookingId]);
}

$highlights   = array_filter(explode('|', $booking['highlights'] ?? ''));
$daysToDepart = (int) ceil((strtotime($booking['travel_date']) - time()) / 86400);
$isDeparted   = strtotime($booking['travel_date']) < time();
$ref          = 'MH-' . str_pad((string)$bookingId, 5, '0', STR_PAD_LEFT);
?>

<div class="admin-wrapper">
  <div class="admin-header">
    <div class="admin-header__inner">
      <div>
        <nav style="font-size:.8rem;color:var(--text-muted);margin-bottom:.5rem;display:flex;align-items:center;gap:.4rem;">
          <a href="/?page=admin_dashboard" style="color:var(--text-muted);">Dashboard</a>
          <span aria-hidden="true">›</span>
          <span>Booking <?= h($ref) ?></span>
        </nav>
        <h1 style="margin-bottom:.35rem;">Booking <?= h($ref) ?></h1>
        <p style="color:var(--text-muted);font-size:.88rem;margin:0;">
          <?= h($booking['destination_name']) ?> &bull; <?= h($booking['user_name']) ?> &bull;
          <span class="badge badge--<?= h($booking['status']) ?>" style="vertical-align:middle;"><?= ucfirst(h($booking['status'])) ?></span>
        </p>
      </div>
      <div style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;">
        <?php if ($booking['status'] === 'pending'): ?>
        <form method="post" action="/?page=admin_booking_detail&id=<?= $bookingId ?>">
          <?= csrfField() ?>
          <input type="hidden" name="status" value="confirmed">
          <button type="submit" class="btn btn--gold">Confirm Booking</button>
        </form>
        <?php endif; ?>
        <?php if ($booking['status'] !== 'cancelled'): ?>
        <form method="post" action="/?page=admin_booking_detail&id=<?= $bookingId ?>">
          <?= csrfField() ?>
          <input type="hidden" name="status" value="cancelled">
          <button type="submit" class="btn btn--outline-danger btn--sm"
                  onclick="return confirm('Cancel booking <?= h($ref) ?>?')">Cancel Booking</button>
        </form>
        <?php endif; ?>
        <a href="/?page=admin_dashboard" class="btn btn--outline btn--sm">&#8592; All Bookings</a>
      </div>
    </div>
  </div>

  <div class="admin-body">
    <nav class="admin-subnav" aria-label="Admin navigation">
      <a href="/?page=admin_dashboard" class="active">Dashboard</a>
      <a href="/?page=admin_manage_listings">Listings</a>
      <a href="/?page=admin_view_users">Users</a>
    </nav>

    <?php renderFlash(); ?>

    <div class="booking-detail-grid">

      <!-- LEFT: trip info -->
      <div style="display:flex;flex-direction:column;gap:1.5rem;">

        <!-- Destination hero card -->
        <div class="table-wrap" style="overflow:hidden;">
          <div style="position:relative;height:210px;">
            <img src="<?= h($booking['image_url']) ?>" alt="<?= h($booking['destination_name']) ?>"
                 style="width:100%;height:100%;object-fit:cover;" loading="lazy">
            <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(10,26,42,.72) 0%,transparent 55%);"></div>
            <div style="position:absolute;bottom:1.25rem;left:1.5rem;">
              <span style="font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:.3rem;"><?= h($booking['country']) ?></span>
              <h2 style="font-size:1.45rem;color:var(--white);font-family:var(--font-serif);margin:0;line-height:1.2;"><?= h($booking['destination_name']) ?></h2>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;border-bottom:1px solid var(--border-light);">
            <?php
            $cells = [
                ['Travel Date',  formatDate($booking['travel_date']),
                 !$isDeparted && $daysToDepart > 0 ? $daysToDepart . ' days away' : ($isDeparted ? 'Departed' : '')],
                ['Travellers',   (int)$booking['passengers'] . ' ' . ((int)$booking['passengers'] === 1 ? 'person' : 'people'), ''],
                ['Duration',     (int)$booking['duration_days'] . ' nights', ''],
            ];
            foreach ($cells as $i => [$label, $value, $sub]):
            ?>
            <div style="padding:1.25rem 1.5rem;<?= $i < 2 ? 'border-right:1px solid var(--border-light);' : '' ?>">
              <div style="font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.35rem;"><?= $label ?></div>
              <div style="font-size:.95rem;font-weight:700;color:var(--navy);"><?= $value ?></div>
              <?php if ($sub): ?><div style="font-size:.72rem;color:var(--text-muted);margin-top:.15rem;"><?= h($sub) ?></div><?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>

          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;">
            <div style="padding:1.25rem 1.5rem;border-right:1px solid var(--border-light);">
              <div style="font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.35rem;">Base Price pp</div>
              <div style="font-size:.95rem;font-weight:700;color:var(--navy);"><?= formatPrice((float)$booking['base_price']) ?></div>
            </div>
            <div style="padding:1.25rem 1.5rem;border-right:1px solid var(--border-light);">
              <div style="font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.35rem;">Total Value</div>
              <div style="font-size:1.1rem;font-weight:800;color:var(--gold);"><?= formatPrice((float)$booking['total_price']) ?></div>
            </div>
            <div style="padding:1.25rem 1.5rem;">
              <div style="font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.35rem;">Status</div>
              <span class="badge badge--<?= h($booking['status']) ?>"><?= ucfirst(h($booking['status'])) ?></span>
            </div>
          </div>
        </div>

        <!-- What's included -->
        <?php if (!empty($highlights)): ?>
        <div class="table-wrap" style="padding:1.5rem;">
          <h3 style="font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);margin-bottom:1rem;">What's Included</h3>
          <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.6rem;">
            <?php foreach ($highlights as $hl): ?>
            <li style="display:flex;align-items:flex-start;gap:.75rem;font-size:.88rem;color:var(--text-muted);">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--success-text)" stroke-width="2.5" style="flex-shrink:0;margin-top:2px;" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
              <?= h($hl) ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <!-- Customer notes -->
        <div class="table-wrap" style="padding:1.5rem;">
          <h3 style="font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.75rem;">Customer Notes</h3>
          <?php if (!empty($booking['notes'])): ?>
          <p style="font-size:.9rem;color:var(--text);line-height:1.75;white-space:pre-line;margin:0;"><?= h($booking['notes']) ?></p>
          <?php else: ?>
          <p style="font-size:.88rem;color:var(--text-light);font-style:italic;margin:0;">No special notes provided.</p>
          <?php endif; ?>
        </div>

      </div>

      <!-- RIGHT: customer + actions -->
      <div style="display:flex;flex-direction:column;gap:1.5rem;">

        <!-- Customer -->
        <div class="table-wrap" style="padding:1.5rem;">
          <h3 style="font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);margin-bottom:1.25rem;">Customer</h3>
          <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;padding-bottom:1.25rem;border-bottom:1px solid var(--border-light);">
            <div style="width:46px;height:46px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <span style="font-family:var(--font-serif);font-size:1.1rem;color:var(--gold);font-weight:700;"><?= h(initial((string)$booking['user_name'])) ?></span>
            </div>
            <div>
              <div style="font-weight:700;font-size:1rem;color:var(--navy);"><?= h($booking['user_name']) ?></div>
              <div style="font-size:.8rem;color:var(--text-muted);"><?= h($booking['user_email']) ?></div>
            </div>
          </div>
          <a href="/?page=admin_view_users&edit=<?= (int)$booking['user_id'] ?>" class="btn btn--sm btn--outline" style="width:100%;justify-content:center;text-align:center;display:flex;">
            View &amp; Edit Account
          </a>
        </div>

        <!-- Booking record -->
        <div class="table-wrap" style="padding:1.5rem;">
          <h3 style="font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);margin-bottom:1rem;">Booking Record</h3>
          <div style="display:flex;flex-direction:column;gap:.8rem;">
            <?php
            $meta = [
                ['Reference',     '<span style="font-family:monospace;font-weight:700;letter-spacing:.06em;color:var(--navy);">' . h($ref) . '</span>'],
                ['Date Created',  h(date('j M Y, H:i', strtotime($booking['created_at'])))],
                ['Destination',   '<a href="/?page=booking_details&id=' . (int)$booking['destination_id'] . '" target="_blank" rel="noopener" style="color:var(--navy);font-weight:600;">' . h($booking['destination_name']) . ' &#8599;</a>'],
            ];
            foreach ($meta as [$label, $val]):
            ?>
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:.84rem;">
              <span style="color:var(--text-muted);"><?= $label ?></span>
              <span><?= $val ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Status update -->
        <div class="table-wrap" style="padding:1.5rem;">
          <h3 style="font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--text-muted);margin-bottom:1rem;">Update Status</h3>
          <div style="display:flex;flex-direction:column;gap:.6rem;">
            <?php foreach (['pending', 'confirmed', 'cancelled'] as $s):
              $isCurrent = $booking['status'] === $s;
              $cls = $isCurrent ? 'btn--outline' : ($s === 'confirmed' ? 'btn--gold' : ($s === 'cancelled' ? 'btn--outline-danger' : 'btn--navy'));
            ?>
            <form method="post" action="/?page=admin_booking_detail&id=<?= $bookingId ?>">
              <?= csrfField() ?>
              <input type="hidden" name="status" value="<?= h($s) ?>">
              <button type="submit"
                      <?= $isCurrent ? 'disabled' : '' ?>
                      <?= $s === 'cancelled' && !$isCurrent ? "onclick=\"return confirm('Cancel booking " . h($ref) . "?')\"" : '' ?>
                      class="btn btn--sm <?= $cls ?>"
                      style="width:100%;justify-content:center;display:flex;<?= $isCurrent ? 'opacity:.45;cursor:not-allowed;' : '' ?>">
                <?= $isCurrent ? '&#10003;&nbsp;' : '' ?><?= ucfirst($s) ?>
              </button>
            </form>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
