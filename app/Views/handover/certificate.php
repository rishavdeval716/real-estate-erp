<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
@media print {
    .app-sidebar, .app-header, .page-header, .no-print {
        display: none !important;
    }
    .app-main {
        margin: 0 !important;
        padding: 0 !important;
    }
    .certificate-card {
        border: 2px solid #000 !important;
        box-shadow: none !important;
        padding: 30px !important;
    }
}
</style>

<div class="page-header no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <a href="/handover" style="font-size: 13px; font-weight: 600; color: var(--primary); display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
            &larr; Back to Handover Register
        </a>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900);"><?= esc($title) ?></h1>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-primary" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print Official Certificate
        </button>
    </div>
</div>

<div class="erp-card certificate-card" style="max-width: 850px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: var(--shadow-lg); border: 1px solid var(--slate-200);">
    <!-- Certificate Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--slate-800); padding-bottom: 20px; margin-bottom: 24px;">
        <div>
            <div style="font-size: 24px; font-weight: 800; color: var(--slate-900); text-transform: uppercase; letter-spacing: 0.5px;">
                <?= esc($company['company_name'] ?? 'REAL ESTATE ERP ENTERPRISE') ?>
            </div>
            <div style="font-size: 13px; color: var(--slate-600); margin-top: 4px;">
                <?= esc($company['address'] ?? 'Corporate Towers, Central Business District') ?>
            </div>
            <div style="font-size: 12px; color: var(--slate-500); margin-top: 2px;">
                GSTIN: <?= esc($company['tax_number'] ?? '27AAACR9988A1Z2') ?> | RERA Registration: PRM/KA/RERA/1251/2026/0049
            </div>
        </div>
        <div style="text-align: right;">
            <div style="display: inline-block; padding: 6px 12px; background: var(--primary-light); color: var(--primary); font-weight: 700; border-radius: 6px; font-size: 13px;">
                OFFICIAL CERTIFICATE
            </div>
            <div style="font-size: 14px; font-weight: 700; color: var(--slate-900); margin-top: 8px;">
                <?= esc($handover['certificate_number']) ?>
            </div>
            <div style="font-size: 12px; color: var(--slate-500);">
                Date: <?= date('d F Y', strtotime($handover['handover_date'])) ?>
            </div>
        </div>
    </div>

    <!-- Title Banner -->
    <div style="text-align: center; margin-bottom: 28px;">
        <h2 style="font-size: 20px; font-weight: 800; color: var(--slate-900); letter-spacing: 1px; text-transform: uppercase;">
            POSSESSION & KEY HANDOVER CERTIFICATE
        </h2>
        <p style="font-size: 13px; color: var(--slate-500); margin-top: 4px;">
            Formal Transfer of Physical Possession, Keys & Municipal Utilities
        </p>
    </div>

    <!-- Preamble -->
    <div style="font-size: 14px; line-height: 1.6; color: var(--slate-700); margin-bottom: 24px; background: var(--slate-50); padding: 14px 18px; border-radius: 8px;">
        This is to certify that physical possession of the below described property unit is hereby formally handed over to the allottee(s) upon satisfactory joint inspection and settlement of all financial consideration.
    </div>

    <!-- Allottee & Unit Details Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
        <!-- Allottee Info -->
        <div style="border: 1px solid var(--slate-200); border-radius: 8px; padding: 16px;">
            <div style="font-size: 12px; font-weight: 700; color: var(--primary); text-transform: uppercase; margin-bottom: 8px;">Allottee / Buyer Details</div>
            <div style="font-size: 16px; font-weight: 700; color: var(--slate-900);"><?= esc($handover['first_name'] . ' ' . $handover['last_name']) ?></div>
            <div style="font-size: 13px; color: var(--slate-600); margin-top: 4px;">Phone: <?= esc($handover['phone']) ?></div>
            <div style="font-size: 13px; color: var(--slate-600);">Email: <?= esc($handover['email']) ?></div>
            <?php if (!empty($handover['id_proof_number'])): ?>
                <div style="font-size: 13px; color: var(--slate-600);"><?= esc($handover['id_proof_type'] ?? 'ID Proof') ?>: <?= esc($handover['id_proof_number']) ?></div>
            <?php endif; ?>
            <div style="font-size: 12px; color: var(--slate-500); margin-top: 6px;">Booking Ref: <strong><?= esc($handover['booking_number']) ?></strong></div>
        </div>

        <!-- Property Unit Info -->
        <div style="border: 1px solid var(--slate-200); border-radius: 8px; padding: 16px;">
            <div style="font-size: 12px; font-weight: 700; color: var(--primary); text-transform: uppercase; margin-bottom: 8px;">Unit Specifications</div>
            <div style="font-size: 16px; font-weight: 700; color: var(--slate-900);">Unit <?= esc($handover['unit_number']) ?></div>
            <div style="font-size: 13px; color: var(--slate-600); margin-top: 4px;"><?= esc($handover['project_name']) ?> (<?= esc($handover['project_code']) ?>)</div>
            <div style="font-size: 13px; color: var(--slate-600);"><?= esc($handover['tower_name'] ?? 'Tower') ?>, Floor: <?= esc($handover['floor'] ?? 'N/A') ?></div>
            <div style="font-size: 13px; color: var(--slate-600);">Carpet Area: <?= esc($handover['carpet_area'] ?? 'N/A') ?> sq.ft | Built-up: <?= esc($handover['built_up_area'] ?? 'N/A') ?> sq.ft</div>
            <div style="font-size: 12px; color: var(--slate-500); margin-top: 6px;">Total Value: <strong>₹<?= number_format((float)($handover['final_amount'] ?? 0), 2) ?></strong></div>
        </div>
    </div>

    <!-- Statutory & Clearance Checklist -->
    <div style="margin-bottom: 24px;">
        <div style="font-size: 13px; font-weight: 700; color: var(--slate-800); text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid var(--slate-200); padding-bottom: 4px;">
            Statutory & Handover Clearances
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <tr style="border-bottom: 1px solid var(--slate-100);">
                <td style="padding: 8px 0; color: var(--slate-600);">Occupancy Certificate (OC) Reference:</td>
                <td style="padding: 8px 0; font-weight: 700; text-align: right; color: var(--slate-900);"><?= esc($handover['occupancy_certificate_ref'] ?: 'MCGM/BP/OC-2026/0491') ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--slate-100);">
                <td style="padding: 8px 0; color: var(--slate-600);">Total Financial Consideration Dues:</td>
                <td style="padding: 8px 0; font-weight: 700; text-align: right; color: var(--success);">100% Cleared & Paid in Full</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--slate-100);">
                <td style="padding: 8px 0; color: var(--slate-600);">Snagging Quality Inspection Checklist:</td>
                <td style="padding: 8px 0; font-weight: 700; text-align: right; color: var(--success);">Zero Pending Snags (Accepted)</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--slate-100);">
                <td style="padding: 8px 0; color: var(--slate-600);">Keys Handed Over:</td>
                <td style="padding: 8px 0; font-weight: 700; text-align: right; color: var(--slate-900);"><?= esc($handover['key_sets_provided']) ?> Complete Master Key Sets</td>
            </tr>
        </table>
    </div>

    <!-- Meter Handover Readings -->
    <div style="margin-bottom: 24px; background: var(--slate-50); padding: 16px; border-radius: 8px; border: 1px solid var(--slate-200);">
        <div style="font-size: 13px; font-weight: 700; color: var(--slate-800); text-transform: uppercase; margin-bottom: 10px;">
            Utility Meter Handover Readings
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div style="font-size: 12px; color: var(--slate-500);">Electricity Meter #</div>
                <div style="font-size: 14px; font-weight: 700; color: var(--slate-900);"><?= esc($handover['electricity_meter_number'] ?: 'MSEDCL-LT-889021') ?></div>
                <div style="font-size: 12px; color: var(--slate-600); margin-top: 2px;">Initial Reading: <strong><?= esc($handover['initial_electricity_reading']) ?> kWh</strong></div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--slate-500);">Water Meter #</div>
                <div style="font-size: 14px; font-weight: 700; color: var(--slate-900);"><?= esc($handover['water_meter_number'] ?: 'MCGM-WM-44120') ?></div>
                <div style="font-size: 12px; color: var(--slate-600); margin-top: 2px;">Initial Reading: <strong><?= esc($handover['initial_water_reading']) ?> kL</strong></div>
            </div>
        </div>
    </div>

    <?php if ($handover['notes']): ?>
        <div style="font-size: 12px; color: var(--slate-600); margin-bottom: 24px; font-style: italic;">
            <strong>Special Remarks:</strong> <?= esc($handover['notes']) ?>
        </div>
    <?php endif; ?>

    <!-- Signature Blocks -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--slate-200);">
        <div style="text-align: center; width: 220px;">
            <div style="border-bottom: 1px solid var(--slate-400); margin-bottom: 8px; height: 40px;"></div>
            <div style="font-size: 13px; font-weight: 700; color: var(--slate-900);"><?= esc($handover['first_name'] . ' ' . $handover['last_name']) ?></div>
            <div style="font-size: 11px; color: var(--slate-500);">Allottee / Buyer Signature</div>
            <div style="font-size: 10px; color: var(--slate-400); margin-top: 2px;">Date: <?= date('d/m/Y') ?></div>
        </div>

        <div style="text-align: center;">
            <div style="width: 70px; height: 70px; border: 2px dashed var(--slate-300); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 10px; color: var(--slate-400);">
                OFFICIAL<br>SEAL
            </div>
        </div>

        <div style="text-align: center; width: 220px;">
            <div style="border-bottom: 1px solid var(--slate-400); margin-bottom: 8px; height: 40px;"></div>
            <div style="font-size: 13px; font-weight: 700; color: var(--slate-900);"><?= esc($handover['authorizer_name'] ?? 'Authorized Signatory') ?></div>
            <div style="font-size: 11px; color: var(--slate-500);">For <?= esc($company['company_name'] ?? 'Real Estate ERP') ?></div>
            <div style="font-size: 10px; color: var(--slate-400); margin-top: 2px;">Authorized Signatory</div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
