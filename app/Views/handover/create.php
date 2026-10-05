<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="margin-bottom: 24px;">
    <a href="/handover" style="font-size: 13px; font-weight: 600; color: var(--primary); display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
        &larr; Back to Possession Handover
    </a>
    <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900);"><?= esc($title) ?></h1>
    <p style="color: var(--slate-500); font-size: 14px;">Verify financial and snagging clearances, record meter readings, and generate formal possession certificate</p>
</div>

<div class="erp-card" style="max-width: 800px;">
    <div class="card-body" style="padding: 24px;">
        <form method="POST" action="/handover/store">
            <?= csrf_field() ?>
            <div style="display: flex; flex-direction: column; gap: 18px;">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Select Confirmed Booking *</label>
                        <select name="booking_id" class="form-control" required>
                            <option value="">Select Confirmed Booking</option>
                            <?php foreach ($bookings as $bk): ?>
                                <option value="<?= $bk['id'] ?>">Booking <?= esc($bk['booking_number']) ?> - <?= esc($bk['first_name'] . ' ' . $bk['last_name']) ?> (Unit <?= esc($bk['unit_number']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Handover Execution Date *</label>
                        <input type="date" name="handover_date" class="form-control" value="<?= esc($today) ?>" required>
                    </div>
                </div>

                <!-- Clearance Gates Checkboxes -->
                <div style="background: var(--slate-50); padding: 16px; border-radius: 8px; border: 1px solid var(--slate-200);">
                    <div style="font-size: 14px; font-weight: 700; color: var(--slate-900); margin-bottom: 10px;">Mandatory Clearance Checkpoints</div>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--slate-800); cursor: pointer;">
                            <input type="checkbox" name="financial_clearance" value="1" checked required style="width: 16px; height: 16px;">
                            <span><strong>1. Financial Clearance:</strong> All milestone demand invoices and taxes cleared in full (100% consideration settled).</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--slate-800); cursor: pointer;">
                            <input type="checkbox" name="snagging_clearance" value="1" checked required style="width: 16px; height: 16px;">
                            <span><strong>2. Quality / Snagging Clearance:</strong> Pre-possession inspection signed off by client with zero pending critical defects.</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--slate-800); cursor: pointer;">
                            <input type="checkbox" name="customer_acknowledged" value="1" checked required style="width: 16px; height: 16px;">
                            <span><strong>3. Customer Joint Walkthrough:</strong> Buyer has verified fittings, plumbing pressure, electrical switches, and fixtures.</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Occupancy Certificate (OC) / Municipal Reference #</label>
                    <input type="text" name="occupancy_certificate_ref" class="form-control" placeholder="e.g. MCGM/BP/OC-2026/0491" value="MCGM/BP/OC-2026/0491">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Electricity Meter Number</label>
                        <input type="text" name="electricity_meter_number" class="form-control" placeholder="e.g. MSEDCL-LT-889021">
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Initial Electricity Reading (kWh)</label>
                        <input type="number" name="initial_electricity_reading" class="form-control" placeholder="0.00" step="0.01" value="10.00">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Water Meter Number</label>
                        <input type="text" name="water_meter_number" class="form-control" placeholder="e.g. MCGM-WM-44120">
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Initial Water Reading (kL)</label>
                        <input type="number" name="initial_water_reading" class="form-control" placeholder="0.00" step="0.01" value="2.00">
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Number of Key Sets Provided *</label>
                    <input type="number" name="key_sets_provided" class="form-control" value="3" min="1" max="10" required>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Possession Handover Remarks / Warranty Documents</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Handed over 3 sets of keys, manufacturer warranty cards for sanitary fittings, electrical distribution board schematic..."></textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="/handover" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Execute Handover & Generate Certificate</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
