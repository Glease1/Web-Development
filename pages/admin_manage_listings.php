<?php
$action = get('action');
$editId = (int) get('edit_id');
$editDest = null;
if ($editId > 0 && $tm) {
    $editDest = $tm->getDestinationById($editId);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $postAction = post('action');

    if ($postAction === 'save') {
        $data = [
            'name'          => post('name'),
            'country'       => post('country'),
            'region'        => post('region'),
            'description'   => post('description'),
            'highlights'    => post('highlights'),
            'price'         => (float) post('price'),
            'duration_days' => (int)   post('duration_days'),
            'image_url'     => post('image_url'),
            'featured'      => (int)   (bool) post('featured'),
        ];
        $updateId = (int) post('dest_id');
        if ($updateId > 0 && $tm) {
            $tm->updateDestination($updateId, $data);
            flash('success', 'Destination updated successfully.');
        } elseif ($tm) {
            $tm->createDestination($data);
            flash('success', 'New destination created.');
        }
        redirect('admin_manage_listings');
    }

    if ($postAction === 'delete') {
        $delId = (int) post('dest_id');
        if ($tm && $delId > 0) {
            $tm->deleteDestination($delId);
            flash('success', 'Destination deleted.');
        }
        redirect('admin_manage_listings');
    }
}

$destinations = $tm ? $tm->getAllDestinations() : [];
?>

<div class="admin-wrapper">
  <div class="admin-header">
    <div class="admin-header__inner">
      <div>
        <h1>Manage Listings</h1>
        <p style="color:var(--text-muted);font-size:.88rem;margin-top:.25rem;"><?= count($destinations) ?> destinations in total</p>
      </div>
      <a href="/?page=admin_manage_listings&action=new" class="btn btn--gold">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Destination
      </a>
    </div>
  </div>

  <div class="admin-body">
    <nav class="admin-subnav">
      <a href="/?page=admin_dashboard">Dashboard</a>
      <a href="/?page=admin_manage_listings" class="active">Listings</a>
      <a href="/?page=admin_view_users">Users</a>
    </nav>

    <?php renderFlash(); ?>

    <?php if ($action === 'new' || ($action === 'edit' && $editDest)): ?>
    <!-- Add / Edit form -->
    <div style="background:var(--white);border:1px solid var(--border-light);border-radius:var(--r-xl);padding:var(--sp-8);box-shadow:var(--shadow-sm);margin-bottom:var(--sp-8);">
      <h2 style="font-size:1.25rem;margin-bottom:1.5rem;"><?= $editDest ? 'Edit Destination' : 'Add New Destination' ?></h2>
      <form method="post" action="/?page=admin_manage_listings">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="dest_id" value="<?= $editDest ? (int)$editDest['id'] : 0 ?>">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="al-name">Destination Name</label>
            <input class="form-input" id="al-name" type="text" name="name" required value="<?= h($editDest['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="al-country">Country</label>
            <input class="form-input" id="al-country" type="text" name="country" required value="<?= h($editDest['country'] ?? '') ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="al-region">Region</label>
            <select class="form-select" id="al-region" name="region">
              <option value="">Select region…</option>
              <?php
              $regionList = ['Europe','Asia','Africa','Americas','Oceania','Middle East','Polar &amp; Adventure'];
              foreach ($regionList as $r):
                $sel = ($editDest['region'] ?? '') === $r ? 'selected' : '';
              ?>
              <option value="<?= h($r) ?>" <?= $sel ?>><?= $r ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="al-price">Price Per Person (£)</label>
            <input class="form-input" id="al-price" type="number" name="price" min="0" step="1" required value="<?= h((string)($editDest['price'] ?? '')) ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="al-duration">Duration (nights)</label>
            <input class="form-input" id="al-duration" type="number" name="duration_days" min="1" max="365" required value="<?= h((string)($editDest['duration_days'] ?? '7')) ?>">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="al-desc">Description</label>
          <textarea class="form-textarea" id="al-desc" name="description" rows="4" required><?= h($editDest['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label" for="al-highlights">Highlights (pipe-separated, e.g. Private villa|Guided tour)</label>
          <textarea class="form-textarea" id="al-highlights" name="highlights" rows="3"><?= h($editDest['highlights'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label" for="al-img">Image URL</label>
          <input class="form-input" id="al-img" type="url" name="image_url" value="<?= h($editDest['image_url'] ?? '') ?>" placeholder="https://images.unsplash.com/photo-...">
        </div>
        <div class="form-check" style="margin-bottom:1.5rem;">
          <input type="checkbox" id="al-featured" name="featured" value="1" <?= !empty($editDest['featured']) ? 'checked' : '' ?>>
          <label for="al-featured">Feature this destination on the homepage</label>
        </div>
        <div class="flex-c gap-4">
          <button type="submit" class="btn btn--navy">Save Destination</button>
          <a href="/?page=admin_manage_listings" class="btn btn--outline">Cancel</a>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <!-- Listings table -->
    <?php if (empty($destinations)): ?>
    <div style="background:var(--white);border:1px solid var(--border-light);border-radius:var(--r-lg);padding:3rem;text-align:center;color:var(--text-muted);">
      <p>No destinations yet. <a href="/?page=admin_manage_listings&action=new" style="color:var(--navy);font-weight:700;">Add your first one.</a></p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
      <table class="data-table" aria-label="Destinations list">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Country</th>
            <th>Region</th>
            <th>Price</th>
            <th>Duration</th>
            <th>Featured</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($destinations as $d): ?>
          <tr>
            <td style="color:var(--text-muted);font-size:.8rem;"><?= (int)$d['id'] ?></td>
            <td>
              <strong><?= h($d['name']) ?></strong>
            </td>
            <td><?= h($d['country']) ?></td>
            <td><?= h($d['region']) ?: '<span style="color:var(--text-light);">—</span>' ?></td>
            <td><?= formatPrice((float)$d['price']) ?></td>
            <td><?= (int)$d['duration_days'] ?> nights</td>
            <td>
              <?php if ($d['featured']): ?>
              <span class="badge badge--confirmed">Yes</span>
              <?php else: ?>
              <span style="color:var(--text-light);font-size:.8rem;">No</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="actions">
                <a href="/?page=admin_manage_listings&action=edit&edit_id=<?= (int)$d['id'] ?>" class="btn btn--sm btn--outline" style="font-size:.75rem;padding:5px 12px;">Edit</a>
                <form method="post" action="/?page=admin_manage_listings" onsubmit="return confirm('Delete <?= h(addslashes($d['name'])) ?>? This cannot be undone.');">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="dest_id" value="<?= (int)$d['id'] ?>">
                  <button type="submit" class="btn btn--sm btn--danger" style="font-size:.75rem;padding:5px 12px;">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
