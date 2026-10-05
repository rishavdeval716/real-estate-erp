<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/customers">Customers</a> &rsaquo;
            <span><?= esc($customer['customer_code']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($customer['first_name'] . ' ' . $customer['last_name']) ?></h1>
        <p class="page-subtitle">
            Customer Code: <strong style="font-family: monospace;"><?= esc($customer['customer_code']) ?></strong>
            <?php if (!empty($customer['lead_code'])): ?>
                &bull; Converted from Lead <a href="/leads/view/<?= $customer['lead_id'] ?>"><?= esc($customer['lead_code']) ?></a>
            <?php endif; ?>
        </p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/bookings/create?customer_id=<?= $customer['id'] ?>" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Booking
        </a>
        <a href="/customers/edit/<?= $customer['id'] ?>" class="btn btn-secondary">
            Edit Profile
        </a>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 1.5rem;">
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Booking Value</div>
            <div class="metric-value">₹<?= number_format($financialSummary['total_booking_value'] ?? 0, 2) ?></div>
            <div class="metric-meta">Across <?= count($customer['bookings'] ?? []) ?> booking(s)</div>
        </div>
    </div>
    <div class="metric-card success">
        <div>
            <div class="metric-label">Total Paid</div>
            <div class="metric-value">₹<?= number_format($financialSummary['total_paid'] ?? 0, 2) ?></div>
            <div class="metric-meta">Verified collections</div>
        </div>
    </div>
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Outstanding Balance</div>
            <div class="metric-value">₹<?= number_format($financialSummary['total_outstanding'] ?? 0, 2) ?></div>
            <div class="metric-meta">Remaining to be paid</div>
        </div>
    </div>
    <div class="metric-card danger">
        <div>
            <div class="metric-label">Overdue Amount</div>
            <div class="metric-value">₹<?= number_format($financialSummary['overdue_amount'] ?? 0, 2) ?></div>
            <div class="metric-meta">Milestones past due</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: flex-start;">
    <!-- Left Column: Profile & KYC Details -->
    <div>
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Customer Information</h3>
                <?php
                $kycClass = 'badge-secondary';
                if ($customer['kyc_status'] === 'Verified') $kycClass = 'badge-success';
                elseif ($customer['kyc_status'] === 'Rejected') $kycClass = 'badge-danger';
                elseif ($customer['kyc_status'] === 'Pending') $kycClass = 'badge-warning';
                ?>
                <span class="badge <?= $kycClass ?>">KYC: <?= esc($customer['kyc_status']) ?></span>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Phone Number</div>
                    <div style="font-size: 0.95rem; font-weight: 600; color: var(--slate-900);"><?= esc($customer['phone']) ?></div>
                    <?php if (!empty($customer['alternate_phone'])): ?>
                        <div style="font-size: 0.8rem; color: var(--slate-600);">Alt: <?= esc($customer['alternate_phone']) ?></div>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Email Address</div>
                    <div style="font-size: 0.9rem; color: var(--slate-900);"><?= esc($customer['email'] ?: 'Not provided') ?></div>
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Residential Address</div>
                    <div style="font-size: 0.85rem; color: var(--slate-800); line-height: 1.4;">
                        <?= nl2br(esc($customer['address'] ?: 'Not provided')) ?>
                        <?php if (!empty($customer['city']) || !empty($customer['state'])): ?>
                            <br><?= esc($customer['city']) ?><?= !empty($customer['state']) ? ', ' . esc($customer['state']) : '' ?> <?= esc($customer['pincode']) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="margin-bottom: 0.75rem; padding-top: 0.5rem; border-top: 1px solid var(--slate-200);">
                    <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Primary ID Document</div>
                    <div style="font-size: 0.9rem; font-weight: 600; color: var(--slate-900);"><?= esc($customer['id_proof_type'] ?: 'Not specified') ?></div>
                    <div style="font-size: 0.85rem; font-family: monospace; color: var(--slate-600);"><?= esc($customer['id_proof_number'] ?: 'N/A') ?></div>
                </div>

                <div style="font-size: 0.75rem; color: var(--slate-400); margin-top: 1rem;">
                    Customer since <?= date('d M Y', strtotime($customer['created_at'])) ?>
                </div>
            </div>
        </div>

        <!-- KYC Documents Section -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">KYC Documents</h3>
                <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('kycUploadModal').style.display = 'block';">
                    + Upload
                </button>
            </div>
            <div class="card-body" style="padding: 0;">
                <?php if (empty($customer['documents'])): ?>
                    <div style="padding: 1.5rem; text-align: center; color: var(--slate-500); font-size: 0.85rem;">
                        No KYC documents uploaded yet.
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <th>Document</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customer['documents'] as $doc): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;"><?= esc($doc['document_type']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--slate-500); font-family: monospace;"><?= esc($doc['document_number'] ?: $doc['file_name']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge <?= $doc['verification_status'] === 'Verified' ? 'badge-success' : ($doc['verification_status'] === 'Rejected' ? 'badge-danger' : 'badge-warning') ?>">
                                                <?= esc($doc['verification_status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.25rem;">
                                                <a href="/customers/kyc/document/<?= $doc['id'] ?>" target="_blank" class="btn btn-sm btn-secondary" title="View Secure Document">View</a>
                                                <?php if ($doc['verification_status'] === 'Pending'): ?>
                                                    <form method="POST" action="/customers/kyc/verify/<?= $doc['id'] ?>" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="status" value="Verified">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Mark Verified">✓</button>
                                                    </form>
                                                    <form method="POST" action="/customers/kyc/verify/<?= $doc['id'] ?>" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="status" value="Rejected">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Reject">✕</button>
                                                    </form>
                                                <?php endif; ?>
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
    </div>

    <!-- Right Column: Tabs for Bookings, Payments, Invoices, Receipts, Agreements -->
    <div>
        <!-- Bookings Section -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Property Bookings (<?= count($customer['bookings'] ?? []) ?>)</h3>
                <a href="/bookings/create?customer_id=<?= $customer['id'] ?>" class="btn btn-sm btn-primary">+ New Booking</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking Number</th>
                                <th>Property / Unit</th>
                                <th>Booking Date</th>
                                <th>Total Value</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customer['bookings'])): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--slate-500);">
                                        No bookings recorded for this customer yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customer['bookings'] as $b): ?>
                                    <tr>
                                        <td>
                                            <a href="/bookings/view/<?= $b['id'] ?>" style="font-weight: 600; color: var(--brand-primary); text-decoration: none; font-family: monospace;">
                                                <?= esc($b['booking_number']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;"><?= esc($b['property_title'] ?? $b['project_name']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--slate-500);">Unit: <?= esc($b['unit_number']) ?> (<?= esc($b['flat_type'] ?? '') ?>)</div>
                                        </td>
                                        <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($b['booking_date'])) ?></td>
                                        <td style="font-weight: 600;">₹<?= number_format($b['final_amount'], 2) ?></td>
                                        <td>
                                            <?php
                                            $stClass = 'badge-secondary';
                                            if ($b['booking_status'] === 'Confirmed') $stClass = 'badge-success';
                                            elseif ($b['booking_status'] === 'Cancelled') $stClass = 'badge-danger';
                                            elseif ($b['booking_status'] === 'Pending Confirmation') $stClass = 'badge-warning';
                                            ?>
                                            <span class="badge <?= $stClass ?>"><?= esc($b['booking_status']) ?></span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="/bookings/view/<?= $b['id'] ?>" class="btn btn-sm btn-secondary">Details</a>
                                            <a href="/bookings/voucher/<?= $b['id'] ?>" target="_blank" class="btn btn-sm btn-secondary" title="View Voucher">Voucher</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payments Section -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Payment History</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Payment #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Status</th>
                                <th style="text-align: right;">Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customer['payments'])): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">
                                        No payments recorded yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customer['payments'] as $p): ?>
                                    <tr>
                                        <td style="font-family: monospace; font-weight: 600;"><?= esc($p['payment_number']) ?></td>
                                        <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                                        <td style="font-weight: 600; color: var(--slate-900);">₹<?= number_format($p['amount'], 2) ?></td>
                                        <td><span class="badge badge-secondary"><?= esc($p['payment_method']) ?></span></td>
                                        <td style="font-size: 0.8rem; font-family: monospace; color: var(--slate-600);"><?= esc($p['transaction_reference'] ?: '-') ?></td>
                                        <td>
                                            <span class="badge <?= $p['status'] === 'Received' ? 'badge-success' : ($p['status'] === 'Cancelled' ? 'badge-danger' : 'badge-warning') ?>">
                                                <?= esc($p['status']) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <?php if (!empty($p['receipt_number'])): ?>
                                                <a href="/receipts/view/<?= $p['receipt_id'] ?>" class="btn btn-sm btn-secondary" target="_blank">
                                                    <?= esc($p['receipt_number']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span style="font-size: 0.75rem; color: var(--slate-400);">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Invoices & Sales Agreements Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Invoices Card -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; font-size: 0.95rem; font-weight: 600;">Invoices</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-container">
                        <table class="table" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($customer['invoices'])): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 1rem; color: var(--slate-500);">No invoices issued.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($customer['invoices'] as $inv): ?>
                                        <tr>
                                            <td>
                                                <a href="/invoices/view/<?= $inv['id'] ?>" style="font-weight: 600; text-decoration: none; font-family: monospace;">
                                                    <?= esc($inv['invoice_number']) ?>
                                                </a>
                                            </td>
                                            <td><?= date('d M Y', strtotime($inv['invoice_date'])) ?></td>
                                            <td style="font-weight: 600;">₹<?= number_format($inv['total_amount'], 2) ?></td>
                                            <td><span class="badge badge-secondary"><?= esc($inv['status']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sales Agreements Card -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; font-size: 0.95rem; font-weight: 600;">Sales Agreements</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-container">
                        <table class="table" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <th>Agreement #</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($customer['agreements'])): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 1rem; color: var(--slate-500);">No agreements created.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($customer['agreements'] as $agr): ?>
                                        <tr>
                                            <td>
                                                <a href="/agreements/view/<?= $agr['id'] ?>" style="font-weight: 600; text-decoration: none; font-family: monospace;">
                                                    <?= esc($agr['agreement_number']) ?>
                                                </a>
                                            </td>
                                            <td><?= esc($agr['agreement_type']) ?></td>
                                            <td><span class="badge <?= $agr['agreement_status'] === 'Signed' ? 'badge-success' : 'badge-warning' ?>"><?= esc($agr['agreement_status']) ?></span></td>
                                            <td>
                                                <a href="/agreements/view/<?= $agr['id'] ?>" class="btn btn-sm btn-secondary">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KYC Document Upload Modal -->
<div id="kycUploadModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; max-width: 500px; width: 90%; margin: 5% auto; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Upload KYC Document</h3>
            <button type="button" onclick="document.getElementById('kycUploadModal').style.display='none';" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="/customers/kyc/upload/<?= $customer['id'] ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">Document Type <span style="color: red;">*</span></label>
                <select name="document_type" class="form-control" required>
                    <option value="PAN Card">PAN Card</option>
                    <option value="Aadhaar Card">Aadhaar Card</option>
                    <option value="Passport">Passport</option>
                    <option value="Voter ID">Voter ID</option>
                    <option value="Driving License">Driving License</option>
                    <option value="Electricity Bill">Electricity Bill (Address Proof)</option>
                    <option value="Bank Statement">Bank Statement</option>
                    <option value="Other Document">Other Document</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">Document Number</label>
                <input type="text" name="document_number" class="form-control" placeholder="e.g. ABCDE1234F">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Select Document File (PDF, PNG, JPG - max 5MB) <span style="color: red;">*</span></label>
                <input type="file" name="kyc_file" class="form-control" required accept=".pdf,.png,.jpg,.jpeg">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('kycUploadModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload & Save</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
