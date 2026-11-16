<?php
// Session-based basket — no login required
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}
$items = $_SESSION['basket'];
$lineTotal = static function (array $i): float {
    return ((float) $i['price']
        + (float) ($i['group_surcharge'] ?? 0)
        + (float) ($i['addon_per_person'] ?? 0)) * (int) $i['passengers'];
};
$total = array_sum(array_map($lineTotal, $items));
$totalDeposit = array_sum(array_map(
    static fn(array $i): float => $lineTotal($i) * ((int) ($i['deposit_pct'] ?? 10) / 100),
    $items
));

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = post('action');

    if ($action === 'remove') {
        $key = post('item_key');
        $_SESSION['basket'] = array_values(
            array_filter($_SESSION['basket'], fn($i) => $i['key'] !== $key)
        );
        flash('info', 'Item removed from your basket.');
        redirect('basket');
    }

    if ($action === 'clear') {
        $_SESSION['basket'] = [];
        flash('info', 'Your basket has been cleared.');
        redirect('basket');
    }

    if ($action === 'checkout') {
        if (empty($items)) {
            flash('warning', 'Your basket is empty.');
            redirect('basket');
        }

        // A booking must belong to an account. Previously a logged-out
        // checkout cleared the basket and reported success without saving
        // anything; now the basket is preserved and the user is sent to login.
        if (!isLoggedIn()) {
            $_SESSION['_return_to'] = 'basket';
            flash('error', 'Please sign in or create an account to complete your booking.');
            redirect('login');
        }
        if (!$tm) {
            flash('error', 'The booking service is temporarily unavailable. Your basket has been kept.');
            redirect('basket');
        }

        $user = currentUser();
        if (!$tm->createBookings((int) $user['id'], $items)) {
            flash('error', 'We could not save your booking. Your basket has been kept; please try again.');
            redirect('basket');
        }

        $_SESSION['basket'] = [];
        flash('success', 'Your booking request has been received! A consultant will be in touch within one business day to confirm details and arrange payment.');
        redirect('home');
    }
}
?>

<div class="page-header">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="/?page=home">Home</a>
      <span class="breadcrumb__sep" aria-hidden="true">/</span>
      <span class="breadcrumb__current">Your Basket</span>
    </nav>
    <span class="eyebrow page-header__eyebrow">Almost There</span>
    <h1>Your Basket</h1>
    <p class="lead"><?= count($items) ?> trip<?= count($items) !== 1 ? 's' : '' ?> saved</p>
  </div>
</div>

<section class="section section--sm">
  <div class="container">
    <?php renderFlash(); ?>

    <?php if (empty($items)): ?>
    <div class="no-results" style="padding:4rem 1rem;">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
      </svg>
      <h3>Your basket is empty</h3>
      <p>Browse our destinations and add a trip to get started.</p>
      <a href="/?page=search_results" class="btn btn--navy" style="margin-top:1.5rem;">Explore Destinations</a>
    </div>
    <?php else: ?>

    <div class="booking-layout">
      <div>
        <div class="booking-card">
          <div class="booking-card__body">
            <h2 class="form-section-title">Saved Trips</h2>
            <?php foreach ($items as $item): ?>
            <div class="basket-item">
              <img
                src="<?= h($item['image_url']) ?>"
                alt="<?= h($item['name']) ?>"
                class="basket-item__img"
                loading="lazy" width="100" height="75"
              >
              <div class="basket-item__info">
                <div class="basket-item__name"><?= h($item['name']) ?></div>
                <div class="basket-item__meta">
                  <?= h($item['country']) ?> &bull; <?= (int)$item['duration_days'] ?> nights &bull;
                  <?= (int)$item['passengers'] ?> <?= (int)$item['passengers'] === 1 ? 'person' : 'people' ?>
                  <?php if (!empty($item['travel_date'])): ?> &bull; Departs <?= date('j M Y', strtotime($item['travel_date'])) ?><?php endif; ?>
                  <?php if (!empty($item['notes'])): ?><br><em style="font-size:.78rem;color:var(--text-light);"><?= h($item['notes']) ?></em><?php endif; ?>
                </div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;">
                <?php $itemTotal = ((float)$item['price'] + (float)($item['group_surcharge'] ?? 0)) * (int)$item['passengers']; ?>
                <span class="basket-item__price"><?= formatPrice($itemTotal) ?></span>
                <?php if (!empty($item['group_surcharge'])): ?>
                <span style="font-size:.72rem;color:var(--text-muted);">(incl. group supplement)</span>
                <?php endif; ?>
                <?php $dpct = (int)($item['deposit_pct'] ?? 10); if ($dpct !== 10): ?>
                <span style="font-size:.72rem;color:var(--gold);font-weight:600;"><?= $dpct ?>% deposit tier</span>
                <?php endif; ?>
                <form method="post" action="/?page=basket">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="remove">
                  <input type="hidden" name="item_key" value="<?= h($item['key']) ?>">
                  <button type="submit" class="btn btn--sm" style="color:var(--error-text);font-size:.75rem;padding:4px 10px;border:1px solid var(--border);border-radius:var(--r-full);">Remove</button>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Order summary + checkout -->
      <aside>
        <div class="booking-summary">
          <h3>Order Summary</h3>
          <?php foreach ($items as $item): ?>
          <?php $summaryItemTotal = $lineTotal($item); ?>
          <div class="summary-line">
            <span class="summary-label" style="max-width:60%;"><?= h($item['name']) ?> &times;<?= (int)$item['passengers'] ?><?php if (!empty($item['group_surcharge'])): ?> <span style="font-size:.7rem;opacity:.65;">(group)</span><?php endif; ?><?php if (!empty($item['addon_ids'])): ?> <span style="font-size:.7rem;opacity:.65;">+ extras</span><?php endif; ?></span>
            <span class="summary-price"><?= formatPrice($summaryItemTotal) ?></span>
          </div>
          <?php endforeach; ?>
          <div class="summary-line total">
            <span class="summary-label">Estimated Total</span>
            <span class="summary-price"><?= formatPrice($total) ?></span>
          </div>
          <div class="summary-line" style="background:rgba(200,169,110,.12);border-radius:var(--r-md);padding:.6rem .5rem;margin-top:.5rem;">
            <span class="summary-label" style="font-weight:700;color:var(--navy);">Deposit Due Today</span>
            <span class="summary-price" style="color:var(--gold);font-weight:700;"><?= formatPrice($totalDeposit) ?></span>
          </div>
          <p style="font-size:.75rem;color:var(--text-light);margin-top:.75rem;line-height:1.5;">Deposit calculated on your departure dates. Balance due 10 weeks before each trip. No payment is taken online — your consultant will arrange this.</p>

          <?php if (!isLoggedIn()): ?>
          <p style="font-size:.75rem;color:var(--text-muted);margin-top:.75rem;line-height:1.5;">You’ll be asked to sign in before the booking is saved. Your basket is kept.</p>
          <?php endif; ?>
          <form method="post" action="/?page=basket" style="margin-top:1.5rem;">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="checkout">
            <button type="submit" class="btn btn--gold btn--lg" style="width:100%;justify-content:center;">
              Request Booking
            </button>
          </form>
        </div>

        <form method="post" action="/?page=basket" style="margin-top:.75rem;">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="clear">
          <button type="submit" class="btn btn--outline" style="width:100%;justify-content:center;color:var(--text-muted);border-color:var(--border);font-size:.82rem;">Clear Basket</button>
        </form>

        <!-- Upsell nudge -->
        <div class="basket-nudge">
          <div class="basket-nudge__header">Guests Also Added</div>
          <div class="basket-nudge__item">
            <span class="basket-nudge__icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
            <span class="basket-nudge__text">
              <span class="basket-nudge__name">Travel Insurance</span>
              <span class="basket-nudge__sub">Medical, cancellation &amp; curtailment — from £79 pp</span>
            </span>
            <a href="/?page=contact" class="basket-nudge__cta">Add &rarr;</a>
          </div>
          <div class="basket-nudge__item">
            <span class="basket-nudge__icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span>
            <span class="basket-nudge__text">
              <span class="basket-nudge__name">Private Airport Transfers</span>
              <span class="basket-nudge__sub">Meet & greet, luggage assist, door-to-door — from £85 pp</span>
            </span>
            <a href="/?page=contact" class="basket-nudge__cta">Add &rarr;</a>
          </div>
          <div class="basket-nudge__item">
            <span class="basket-nudge__icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg></span>
            <span class="basket-nudge__text">
              <span class="basket-nudge__name">Airport Lounge Access</span>
              <span class="basket-nudge__sub">Priority Pass both ways, unlimited food &amp; drinks — from £55 pp</span>
            </span>
            <a href="/?page=contact" class="basket-nudge__cta">Add &rarr;</a>
          </div>
        </div>
      </aside>
    </div>

    <?php endif; ?>
  </div>
</section>
