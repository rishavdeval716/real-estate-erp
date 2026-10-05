<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leases">Rentals</a> &rsaquo;
            <span>Draft New Lease</span>
        </div>
        <h1 class="page-title">Draft Lease Agreement</h1>
        <p class="page-subtitle">Configure legal tenancy terms, lock-in clauses, rent escalation, and security deposit.</p>
    </div>
    <div>
        <a href="/leases" class="btn btn-secondary">
            &larr; Back to Leases
        </a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-body" style="padding: 2rem;">
        <form method="POST" action="/leases/store">
            <?= csrf_field() ?>

            <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--slate-800); margin-bottom: 1.25rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                1. Parties & Property Allocation
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label" style="font-weight: 500;">Select Tenant <span style="color: var(--rose-500);">*</span></label>
                    <select name="tenant_id" class="form-control" required>
                        <option value="">-- Choose Active Tenant --</option>
                        <?php foreach ($tenants as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= old('tenant_id') == $t['id'] ? 'selected' : '' ?>>
                            <?= esc($t['full_name']) ?> (<?= esc($t['tenant_code']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Agreement Type <span style="color: var(--rose-500);">*</span></label>
                    <select name="agreement_type" class="form-control" required>
                        <option value="residential" <?= old('agreement_type') === 'residential' ? 'selected' : '' ?>>Residential Tenancy</option>
                        <option value="commercial" <?= old('agreement_type') === 'commercial' ? 'selected' : '' ?>>Commercial Lease (18% GST Applicable)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                <div>
                    <label class="form-label" style="font-weight: 500;">Target Property <span style="color: var(--rose-500);">*</span></label>
                    <select name="property_id" class="form-control" required>
                        <option value="">-- Choose Property --</option>
                        <?php foreach ($properties as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= old('property_id') == $p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['title']) ?> (<?= esc($p['property_code']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Available Unit (Optional)</label>
                    <select name="property_unit_id" class="form-control">
                        <option value="">-- Entire Property or Unassigned --</option>
                        <?php foreach ($units as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= old('property_unit_id') == $u['id'] ? 'selected' : '' ?>>
                            Unit <?= esc($u['unit_number']) ?> (<?= esc($u['unit_type'] ?? 'Flat/Space') ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--slate-800); margin-bottom: 1.25rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                2. Tenure & Term Period
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                <div>
                    <label class="form-label" style="font-weight: 500;">Start Date <span style="color: var(--rose-500);">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="<?= old('start_date', date('Y-m-d')) ?>" required>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">End Date <span style="color: var(--rose-500);">*</span></label>
                    <input type="date" name="end_date" class="form-control" value="<?= old('end_date', date('Y-m-d', strtotime('+11 months'))) ?>" required>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Lock-in Period (Months)</label>
                    <input type="number" name="lock_in_period_months" class="form-control" min="0" max="60" value="<?= old('lock_in_period_months', 6) ?>">
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Notice Period (Days)</label>
                    <input type="number" name="notice_period_days" class="form-control" min="0" max="180" value="<?= old('notice_period_days', 30) ?>">
                </div>
            </div>

            <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--slate-800); margin-bottom: 1.25rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                3. Financial Consideration & Escalation
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label" style="font-weight: 500;">Monthly Rent (₹) <span style="color: var(--rose-500);">*</span></label>
                    <input type="number" step="0.01" name="monthly_rent" class="form-control" placeholder="e.g. 25000.00" value="<?= old('monthly_rent') ?>" required>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Security Deposit (₹)</label>
                    <input type="number" step="0.01" name="security_deposit" class="form-control" placeholder="e.g. 50000.00" value="<?= old('security_deposit') ?>">
                    <div style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.2rem;">Auto-generates deposit tracking record</div>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Maintenance Charges (₹)</label>
                    <input type="number" step="0.01" name="maintenance_charges" class="form-control" placeholder="e.g. 2500.00" value="<?= old('maintenance_charges', 0) ?>">
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Payment Due Day of Month</label>
                    <input type="number" name="payment_due_day" class="form-control" min="1" max="28" value="<?= old('payment_due_day', 5) ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                <div>
                    <label class="form-label" style="font-weight: 500;">Rent Escalation (%)</label>
                    <input type="number" step="0.01" name="rent_escalation_pct" class="form-control" min="0" max="100" value="<?= old('rent_escalation_pct', 5.00) ?>">
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Escalation Frequency</label>
                    <select name="escalation_frequency" class="form-control">
                        <option value="annual" <?= old('escalation_frequency') === 'annual' ? 'selected' : '' ?>>Annual (Every 12 Months)</option>
                        <option value="bi-annual" <?= old('escalation_frequency') === 'bi-annual' ? 'selected' : '' ?>>Bi-Annual (Every 6 Months)</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Late Fee / Day Overdue (₹)</label>
                    <input type="number" step="0.01" name="late_fee_amount" class="form-control" min="0" value="<?= old('late_fee_amount', 100.00) ?>">
                </div>
            </div>

            <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--slate-800); margin-bottom: 1.25rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                4. Terms & Special Conditions
            </h3>

            <div style="margin-bottom: 2rem;">
                <label class="form-label" style="font-weight: 500;">Contractual Clauses & Special Terms</label>
                <textarea name="terms_conditions" class="form-control" rows="4" placeholder="Specify maintenance responsibilities, painting covenants, sub-letting prohibition, utility meter readings, etc."><?= old('terms_conditions') ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--slate-200);">
                <a href="/leases" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;">
                    Create Lease Agreement
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
