<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/agents">Agents & Brokers</a> &rsaquo;
            <span>Edit Agent</span>
        </div>
        <h1 class="page-title">Edit Agent: <?= esc($agent['agent_code']) ?></h1>
        <p class="page-subtitle">Update commission percentage, RERA registration, and agency credentials.</p>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0;">Broker Information</h3>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.25rem;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="/agents/update/<?= $agent['id'] ?>">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Channel Partner Type <span style="color: var(--danger);">*</span></label>
                        <select name="agent_type" class="form-control" required>
                            <option value="External Broker" <?= $agent['agent_type'] === 'External Broker' ? 'selected' : '' ?>>External Broker</option>
                            <option value="Channel Partner" <?= $agent['agent_type'] === 'Channel Partner' ? 'selected' : '' ?>>Channel Partner</option>
                            <option value="Internal Agent" <?= $agent['agent_type'] === 'Internal Agent' ? 'selected' : '' ?>>Internal Agent</option>
                            <option value="Agency" <?= $agent['agent_type'] === 'Agency' ? 'selected' : '' ?>>Agency</option>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Agency / Firm Name</label>
                        <input type="text" name="agency_name" class="form-control" value="<?= old('agency_name', $agent['agency_name']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">First Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="first_name" class="form-control" required value="<?= old('first_name', $agent['first_name']) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Last Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="last_name" class="form-control" required value="<?= old('last_name', $agent['last_name']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Email Address <span style="color: var(--danger);">*</span></label>
                        <input type="email" name="email" class="form-control" required value="<?= old('email', $agent['email']) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Phone / Mobile <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="phone" class="form-control" required value="<?= old('phone', $agent['phone']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">RERA License / Registration Number</label>
                        <input type="text" name="license_number" class="form-control" value="<?= old('license_number', $agent['license_number']) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Income Tax PAN</label>
                        <input type="text" name="pan_number" class="form-control" style="text-transform: uppercase;" value="<?= old('pan_number', $agent['pan_number']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Standard Commission Rate (%) <span style="color: var(--danger);">*</span></label>
                        <input type="number" step="0.01" name="commission_rate" class="form-control" required value="<?= old('commission_rate', $agent['commission_rate']) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Link to System User (Optional)</label>
                        <select name="user_id" class="form-control">
                            <option value="">— No User Login Link —</option>
                            <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $agent['user_id'] == $u['id'] ? 'selected' : '' ?>><?= esc($u['name']) ?> (<?= esc($u['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?= old('city', $agent['city']) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $agent['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $agent['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="suspended" <?= $agent['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2"><?= old('address', $agent['address']) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Notes & Activity Tracking</label>
                <textarea name="notes" class="form-control" rows="2"><?= old('notes', $agent['notes']) ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="/agents/view/<?= $agent['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Agent</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
