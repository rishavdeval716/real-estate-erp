<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/tenants">Tenants</a> &rsaquo;
            <span><?= esc($tenant['tenant_code']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($tenant['full_name']) ?></h1>
        <p class="page-subtitle">Tenant Code: <strong style="font-family: monospace;"><?= esc($tenant['tenant_code']) ?></strong> &bull; Registered on <?= date('d M Y', strtotime($tenant['created_at'])) ?></p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/leases/create?tenant_id=<?= $tenant['id'] ?>" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
            Create Lease
        </a>
        <a href="/tenants/edit/<?= $tenant['id'] ?>" class="btn btn-secondary">Edit Profile</a>
        <a href="/tenants" class="btn btn-secondary">Back to Directory</a>
    </div>
</div>

<!-- Tabs Navigation -->
<div style="border-bottom: 1px solid var(--slate-200); margin-bottom: 1.5rem; display: flex; gap: 1rem;">
    <button type="button" class="tab-btn active" onclick="switchTab('overview', this)" style="padding: 0.75rem 1rem; border: none; background: none; font-weight: 600; cursor: pointer; border-bottom: 2px solid var(--primary-600); color: var(--primary-600);">
        Overview & Occupancy
    </button>
    <button type="button" class="tab-btn" onclick="switchTab('kyc', this)" style="padding: 0.75rem 1rem; border: none; background: none; font-weight: 500; cursor: pointer; color: var(--slate-600);">
        KYC Compliance (<?= count($documents) ?>)
    </button>
    <button type="button" class="tab-btn" onclick="switchTab('leases', this)" style="padding: 0.75rem 1rem; border: none; background: none; font-weight: 500; cursor: pointer; color: var(--slate-600);">
        Lease Agreements (<?= count($leases) ?>)
    </button>
    <button type="button" class="tab-btn" onclick="switchTab('history', this)" style="padding: 0.75rem 1rem; border: none; background: none; font-weight: 500; cursor: pointer; color: var(--slate-600);">
        Rental History (<?= count($history) ?>)
    </button>
    <button type="button" class="tab-btn" onclick="switchTab('tickets', this)" style="padding: 0.75rem 1rem; border: none; background: none; font-weight: 500; cursor: pointer; color: var(--slate-600);">
        Maintenance (<?= count($tickets) ?>)
    </button>
</div>

<!-- Tab 1: Overview -->
<div id="tab-overview" class="tab-content">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0; font-size: 1rem;">Tenant Demographics & Contact</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Full Name</div>
                        <div style="font-weight: 600; font-size: 1rem;"><?= esc($tenant['full_name']) ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Tenant Type</div>
                        <div>
                            <span class="badge <?= $tenant['tenant_type'] === 'company' ? 'badge-info' : 'badge-secondary' ?>">
                                <?= ucfirst(esc($tenant['tenant_type'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($tenant['company_name'])): ?>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Company Name</div>
                            <div style="font-weight: 500;"><?= esc($tenant['company_name']) ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Authorized Contact</div>
                            <div style="font-weight: 500;"><?= esc($tenant['contact_person'] ?: '—') ?></div>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Mobile Number</div>
                        <div style="font-weight: 500;"><?= esc($tenant['mobile']) ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Email Address</div>
                        <div style="font-weight: 500;"><?= esc($tenant['email']) ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">ID Document Type</div>
                        <div><?= esc($tenant['id_proof_type'] ?: 'Not Provided') ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">ID / PAN Number</div>
                        <div style="font-family: monospace; font-weight: 600;"><?= esc($tenant['id_proof_number'] ?: '—') ?></div>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Permanent Address</div>
                        <div><?= nl2br(esc($tenant['address'] ?: '—')) ?></div>
                        <div style="font-size: 0.85rem; color: var(--slate-500); margin-top: 0.25rem;">
                            <?= esc($tenant['city']) ?><?= $tenant['state'] ? ', ' . esc($tenant['state']) : '' ?><?= $tenant['pincode'] ? ' - ' . esc($tenant['pincode']) : '' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <!-- Occupancy Card -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 style="margin: 0; font-size: 1rem;">Active Occupancy</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($tenant['property_title'])): ?>
                        <div style="font-weight: 700; color: var(--slate-800); margin-bottom: 0.25rem;">
                            <?= esc($tenant['property_title']) ?>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.75rem;">
                            Code: <?= esc($tenant['property_code']) ?>
                            <?php if (!empty($tenant['unit_number'])): ?>
                                &bull; Unit <?= esc($tenant['unit_number']) ?> (Floor <?= esc($tenant['floor']) ?>)
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($tenant['lease_start_date'])): ?>
                            <div style="padding: 0.5rem; background: var(--slate-50); border-radius: var(--radius-sm); font-size: 0.85rem;">
                                <div><strong>Lease Term:</strong></div>
                                <div><?= date('d M Y', strtotime($tenant['lease_start_date'])) ?> &rarr; <?= date('d M Y', strtotime($tenant['lease_end_date'])) ?></div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div style="color: var(--slate-500); font-size: 0.9rem;">
                            No active property or unit occupancy registered.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- KYC Status Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; font-size: 1rem;">KYC Status</h3>
                </div>
                <div class="card-body">
                    <?php
                    $kycClass = match ($tenant['kyc_status']) {
                        'verified' => 'badge-success',
                        'rejected' => 'badge-danger',
                        default    => 'badge-warning',
                    };
                    ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <span>Verification Status:</span>
                        <span class="badge <?= $kycClass ?>" style="font-size: 0.85rem; padding: 0.35rem 0.6rem;">
                            <?= ucfirst(esc($tenant['kyc_status'])) ?>
                        </span>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" style="width: 100%;" onclick="switchTab('kyc', document.querySelectorAll('.tab-btn')[1])">
                        Manage KYC Documents &rsaquo;
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab 2: KYC Documents -->
<div id="tab-kyc" class="tab-content" style="display: none;">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0; font-size: 1rem;">Uploaded KYC Compliance Documents</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th>Number</th>
                            <th>Status</th>
                            <th>Verified By</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--slate-500);">
                                    No KYC documents uploaded yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;"><?= esc($doc['document_type']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($doc['file_name']) ?></div>
                                    </td>
                                    <td style="font-family: monospace;"><?= esc($doc['document_number'] ?: '—') ?></td>
                                    <td>
                                        <?php
                                        $dClass = match ($doc['verification_status']) {
                                            'verified' => 'badge-success',
                                            'rejected' => 'badge-danger',
                                            default    => 'badge-warning',
                                        };
                                        ?>
                                        <span class="badge <?= $dClass ?>"><?= ucfirst(esc($doc['verification_status'])) ?></span>
                                    </td>
                                    <td>
                                        <?php if (!empty($doc['verified_by_name']) || !empty($doc['first_name'])): ?>
                                            <?= esc($doc['verified_by_name'] ?? ($doc['first_name'] . ' ' . $doc['last_name'])) ?>
                                            <div style="font-size: 0.75rem; color: var(--slate-500);"><?= !empty($doc['verification_date']) ? date('d M Y', strtotime($doc['verification_date'])) : '' ?></div>
                                        <?php else: ?>
                                            <span style="color: var(--slate-400);">Pending review</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                            <a href="/tenants/kyc/document/<?= $doc['id'] ?>" target="_blank" class="btn btn-sm btn-secondary" title="View Document">
                                                View
                                            </a>
                                            <?php if ($doc['verification_status'] === 'pending'): ?>
                                                <form method="POST" action="/tenants/kyc/verify/<?= $doc['id'] ?>" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="verified">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve Document">Approve</button>
                                                </form>
                                                <form method="POST" action="/tenants/kyc/verify/<?= $doc['id'] ?>" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Reject Document">Reject</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0; font-size: 1rem;">Upload KYC Document</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/tenants/kyc/upload/<?= $tenant['id'] ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label">Document Type <span style="color: var(--rose-500);">*</span></label>
                        <select name="document_type" class="form-control" required>
                            <option value="PAN Card">PAN Card</option>
                            <option value="Aadhaar Card">Aadhaar Card</option>
                            <option value="Passport">Passport</option>
                            <option value="Certificate of Incorporation">Certificate of Incorporation</option>
                            <option value="GST Certificate">GST Certificate</option>
                            <option value="Board Resolution">Board Resolution</option>
                            <option value="Electricity Bill / Utility Proof">Electricity Bill / Utility Proof</option>
                            <option value="Police Verification Certificate">Police Verification Certificate</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Document / ID Number</label>
                        <input type="text" name="document_number" class="form-control" placeholder="e.g. ABCDE1234F">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Document File (PDF, JPG, PNG) <span style="color: var(--rose-500);">*</span></label>
                        <input type="file" name="kyc_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Expiry Date (Optional)</label>
                        <input type="date" name="expiry_date" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Upload Document</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tab 3: Lease Agreements -->
<div id="tab-leases" class="tab-content" style="display: none;">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem;">Lease Agreements</h3>
            <a href="/leases/create?tenant_id=<?= $tenant['id'] ?>" class="btn btn-sm btn-primary">Create New Lease</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Agreement Number</th>
                        <th>Type</th>
                        <th>Term Dates</th>
                        <th>Monthly Rent</th>
                        <th>Deposit</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leases)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--slate-500);">
                                No lease agreements found for this tenant.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leases as $l): ?>
                            <tr>
                                <td>
                                    <a href="/leases/view/<?= $l['id'] ?>" style="font-weight: 600; text-decoration: none; color: var(--primary-600);">
                                        <?= esc($l['agreement_number']) ?>
                                    </a>
                                </td>
                                <td><span class="badge badge-secondary"><?= ucfirst(esc($l['agreement_type'])) ?></span></td>
                                <td><?= date('d M Y', strtotime($l['start_date'])) ?> &rarr; <?= date('d M Y', strtotime($l['end_date'])) ?></td>
                                <td style="font-weight: 600;">₹<?= number_format($l['monthly_rent'], 2) ?></td>
                                <td>₹<?= number_format($l['security_deposit'], 2) ?></td>
                                <td>
                                    <?php
                                    $lClass = match ($l['status']) {
                                        'active'        => 'badge-success',
                                        'expiring_soon' => 'badge-warning',
                                        'expired'       => 'badge-danger',
                                        'renewed'       => 'badge-info',
                                        default         => 'badge-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $lClass ?>"><?= ucfirst(esc($l['status'])) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/leases/view/<?= $l['id'] ?>" class="btn btn-sm btn-secondary">View Lease</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab 4: Rental History -->
<div id="tab-history" class="tab-content" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h3 style="margin: 0; font-size: 1rem;">Tenancy & Rent Escalation History</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Property / Unit</th>
                        <th>Agreement</th>
                        <th>Previous Rent</th>
                        <th>Escalated / Active Rent</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--slate-500);">
                                No tenancy history records available.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($h['property_title'] ?? '—') ?></strong>
                                    <?php if (!empty($h['unit_number'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);">Unit <?= esc($h['unit_number']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($h['agreement_number'] ?? '—') ?></td>
                                <td><?= $h['previous_rent'] ? '₹' . number_format($h['previous_rent'], 2) : '—' ?></td>
                                <td style="font-weight: 600; color: var(--emerald-600);">₹<?= number_format($h['current_rent'], 2) ?></td>
                                <td><?= date('d M Y', strtotime($h['start_date'])) ?> &rarr; <?= $h['end_date'] ? date('d M Y', strtotime($h['end_date'])) : 'Present' ?></td>
                                <td><span class="badge badge-info"><?= esc($h['status']) ?></span></td>
                                <td style="font-size: 0.85rem; color: var(--slate-600);"><?= esc($h['notes'] ?? '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab 5: Maintenance Requests -->
<div id="tab-tickets" class="tab-content" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h3 style="margin: 0; font-size: 1rem;">Maintenance Work Orders & Tickets</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Description</th>
                        <th>SLA Due Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--slate-500);">
                                No maintenance requests recorded for this tenant.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $t): ?>
                            <tr>
                                <td>
                                    <a href="/maintenance/view/<?= $t['id'] ?>" style="font-weight: 600; text-decoration: none; color: var(--primary-600);">
                                        <?= esc($t['ticket_number']) ?>
                                    </a>
                                </td>
                                <td><?= ucfirst(esc($t['category'])) ?></td>
                                <td>
                                    <?php
                                    $pClass = match ($t['priority']) {
                                        'urgent' => 'badge-danger',
                                        'high'   => 'badge-warning',
                                        default  => 'badge-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $pClass ?>"><?= ucfirst(esc($t['priority'])) ?></span>
                                </td>
                                <td><?= esc(substr($t['description'], 0, 50)) ?>...</td>
                                <td><?= date('d M Y H:i', strtotime($t['sla_due_date'])) ?></td>
                                <td><span class="badge badge-info"><?= ucfirst(esc($t['status'])) ?></span></td>
                                <td style="text-align: right;">
                                    <a href="/maintenance/view/<?= $t['id'] ?>" class="btn btn-sm btn-secondary">View Ticket</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + tabName).style.display = 'block';

    document.querySelectorAll('.tab-btn').forEach(el => {
        el.style.borderBottom = 'none';
        el.style.color = 'var(--slate-600)';
        el.style.fontWeight = '500';
    });
    btn.style.borderBottom = '2px solid var(--primary-600)';
    btn.style.color = 'var(--primary-600)';
    btn.style.fontWeight = '600';
}
</script>

<?= $this->endSection() ?>
