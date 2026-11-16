<?php
$currentUserId = currentUser()['id'];
$editId        = (int) get('edit');
$editUser      = ($editId > 0 && $tm) ? $tm->getUserById($editId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action   = post('action');
    $targetId = (int) post('user_id');

    // Prevent self-modification via the panel (except password resets are blocked too)
    if ($targetId === $currentUserId) {
        flash('error', 'You cannot modify your own account from this panel.');
        redirect('admin_view_users');
    }

    switch ($action) {
        case 'create_user':
            $name  = post('name');
            $email = post('email');
            $pass  = post('password');
            $role  = post('role') === 'admin' ? 'admin' : 'user';
            if ($name === '' || $email === '' || $pass === '') {
                flash('error', 'Name, email and password are required.');
                redirect('admin_view_users', ['action' => 'add']);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('error', 'Please enter a valid email address.');
                redirect('admin_view_users', ['action' => 'add']);
            }
            if (strlen($pass) < 8) {
                flash('error', 'Password must be at least 8 characters.');
                redirect('admin_view_users', ['action' => 'add']);
            }
            if ($tm && $tm->adminCreateUser($name, $email, $pass, $role)) {
                flash('success', htmlspecialchars($name, ENT_QUOTES) . '\'s account created.');
            } else {
                flash('error', 'Could not create account — that email may already be in use.');
            }
            redirect('admin_view_users');

        case 'edit_user':
            $name  = post('name');
            $email = post('email');
            $role  = post('role') === 'admin' ? 'admin' : 'user';
            if ($name === '' || $email === '') {
                flash('error', 'Name and email are required.');
                redirect('admin_view_users', ['edit' => $targetId]);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('error', 'Please enter a valid email address.');
                redirect('admin_view_users', ['edit' => $targetId]);
            }
            if ($tm && $tm->updateUser($targetId, $name, $email, $role)) {
                flash('success', 'Account updated.');
            } else {
                flash('error', 'Update failed — email may already be taken by another account.');
            }
            redirect('admin_view_users');

        case 'reset_password':
            $pass    = post('password');
            $confirm = post('password_confirm');
            if (strlen($pass) < 8) {
                flash('error', 'New password must be at least 8 characters.');
                redirect('admin_view_users', ['edit' => $targetId]);
            }
            if ($pass !== $confirm) {
                flash('error', 'Passwords do not match.');
                redirect('admin_view_users', ['edit' => $targetId]);
            }
            if ($tm) $tm->resetUserPassword($targetId, $pass);
            flash('success', 'Password reset successfully.');
            redirect('admin_view_users');

        case 'delete_user':
            if ($tm) $tm->deleteUser($targetId);
            flash('success', 'User deleted.');
            redirect('admin_view_users');
    }
}

$users       = $tm ? $tm->getAllUsers() : [];
$showAddForm = (get('action') === 'add');
?>

<div class="admin-wrapper">
  <div class="admin-header">
    <div class="admin-header__inner">
      <div>
        <h1>Manage Users</h1>
        <p style="color:var(--text-muted);font-size:.88rem;margin-top:.25rem;"><?= count($users) ?> registered account<?= count($users) !== 1 ? 's' : '' ?></p>
      </div>
      <?php if (!$showAddForm && !$editUser): ?>
      <a href="/?page=admin_view_users&action=add" class="btn btn--gold">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add User
      </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="admin-body">
    <nav class="admin-subnav">
      <a href="/?page=admin_dashboard">Dashboard</a>
      <a href="/?page=admin_manage_listings">Listings</a>
      <a href="/?page=admin_view_users" class="active">Users</a>
    </nav>

    <?php renderFlash(); ?>

    <!-- ── Add User panel ── -->
    <?php if ($showAddForm): ?>
    <div class="admin-form-panel">
      <div class="admin-form-panel__header">
        <h3>Create New Account</h3>
        <a href="/?page=admin_view_users" class="admin-form-panel__close" aria-label="Close">&#10005;</a>
      </div>
      <form method="post" action="/?page=admin_view_users" class="admin-form-panel__body">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="create_user">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="new-name">Full Name <span class="required-star">*</span></label>
            <input class="form-input" id="new-name" type="text" name="name" required autocomplete="off" placeholder="Jane Smith">
          </div>
          <div class="form-group">
            <label class="form-label" for="new-email">Email Address <span class="required-star">*</span></label>
            <input class="form-input" id="new-email" type="email" name="email" required autocomplete="off" placeholder="jane@example.com">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="new-pass">Temporary Password <span class="required-star">*</span></label>
            <input class="form-input" id="new-pass" type="password" name="password" required minlength="8" placeholder="Min. 8 characters" autocomplete="new-password">
          </div>
          <div class="form-group">
            <label class="form-label" for="new-role">Role</label>
            <select class="form-select" id="new-role" name="role">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <div style="display:flex;gap:.75rem;margin-top:.5rem;">
          <button type="submit" class="btn btn--gold">Create Account</button>
          <a href="/?page=admin_view_users" class="btn btn--outline">Cancel</a>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <!-- ── Edit User panel ── -->
    <?php if ($editUser && (int)$editUser['id'] !== $currentUserId): ?>
    <div class="admin-form-panel">
      <div class="admin-form-panel__header">
        <h3>Edit Account &mdash; <?= h($editUser['name']) ?></h3>
        <a href="/?page=admin_view_users" class="admin-form-panel__close" aria-label="Close">&#10005;</a>
      </div>
      <div class="admin-form-panel__body">

        <!-- Edit details -->
        <form method="post" action="/?page=admin_view_users" style="margin-bottom:2rem;">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="edit_user">
          <input type="hidden" name="user_id" value="<?= (int)$editUser['id'] ?>">
          <p style="font-size:.78rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-bottom:1rem;">Account Details</p>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="edit-name">Full Name <span class="required-star">*</span></label>
              <input class="form-input" id="edit-name" type="text" name="name" required value="<?= h($editUser['name']) ?>">
            </div>
            <div class="form-group">
              <label class="form-label" for="edit-email">Email Address <span class="required-star">*</span></label>
              <input class="form-input" id="edit-email" type="email" name="email" required value="<?= h($editUser['email']) ?>">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="edit-role">Role</label>
              <select class="form-select" id="edit-role" name="role">
                <option value="user"  <?= $editUser['role'] === 'user'  ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $editUser['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
              </select>
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end;">
              <button type="submit" class="btn btn--navy" style="width:100%;">Save Changes</button>
            </div>
          </div>
        </form>

        <hr class="divider">

        <!-- Reset password -->
        <p style="font-size:.78rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-bottom:1rem;margin-top:1.5rem;">Reset Password</p>
        <form method="post" action="/?page=admin_view_users">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="reset_password">
          <input type="hidden" name="user_id" value="<?= (int)$editUser['id'] ?>">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="reset-pass">New Password <span class="required-star">*</span></label>
              <input class="form-input" id="reset-pass" type="password" name="password" required minlength="8"
                     placeholder="Min. 8 characters" autocomplete="new-password">
            </div>
            <div class="form-group">
              <label class="form-label" for="reset-confirm">Confirm Password <span class="required-star">*</span></label>
              <input class="form-input" id="reset-confirm" type="password" name="password_confirm" required minlength="8"
                     placeholder="Repeat password" autocomplete="new-password">
            </div>
          </div>
          <button type="submit" class="btn btn--outline-danger btn--sm"
                  onclick="return confirm('Reset password for <?= h(addslashes($editUser['name'])) ?>?')">
            Reset Password
          </button>
        </form>

      </div>
    </div>
    <?php endif; ?>

    <!-- ── Users table ── -->
    <?php if (empty($users)): ?>
    <div style="background:var(--white);border:1px solid var(--border-light);border-radius:var(--r-lg);padding:3rem;text-align:center;color:var(--text-muted);">
      <p>No users registered yet.</p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
      <table class="data-table" aria-label="Users list">
        <thead>
          <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u):
            $isSelf    = ((int)$u['id'] === $currentUserId);
            $isEditing = ($editId === (int)$u['id'] && !$isSelf);
          ?>
          <tr <?= $isEditing ? 'style="background:rgba(200,169,110,.07);"' : '' ?>>
            <td style="color:var(--text-muted);font-size:.78rem;font-weight:600;"><?= (int)$u['id'] ?></td>
            <td>
              <strong style="font-size:.9rem;"><?= h($u['name']) ?></strong>
              <?php if ($isSelf): ?>
              <span style="font-size:.68rem;margin-left:.4rem;color:var(--text-light);background:var(--off-white);padding:1px 8px;border-radius:var(--r-full);border:1px solid var(--border);">you</span>
              <?php endif; ?>
            </td>
            <td style="font-size:.87rem;"><?= h($u['email']) ?></td>
            <td><span class="badge badge--<?= h($u['role']) ?>"><?= ucfirst(h($u['role'])) ?></span></td>
            <td style="white-space:nowrap;font-size:.82rem;color:var(--text-muted);"><?= h(date('j M Y', strtotime($u['created_at']))) ?></td>
            <td>
              <?php if (!$isSelf): ?>
              <div class="actions">
                <a href="/?page=admin_view_users&edit=<?= (int)$u['id'] ?>" class="btn btn--sm btn--navy" style="font-size:.75rem;padding:5px 14px;">Edit</a>
                <form method="post" action="/?page=admin_view_users"
                      onsubmit="return confirm('Permanently delete <?= h(addslashes($u['name'])) ?>?');">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="delete_user">
                  <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                  <button type="submit" class="btn btn--sm btn--danger" style="font-size:.75rem;padding:5px 14px;">Delete</button>
                </form>
              </div>
              <?php else: ?>
              <span style="font-size:.8rem;color:var(--text-light);">Current session</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
