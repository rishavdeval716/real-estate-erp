<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Portfolio Statement - <?= esc($owner['owner_code']) ?></title>
    <link rel="stylesheet" href="/assets/css/erp-style.css">
    <style>
        body { background: #fff; padding: 2rem; color: #1e293b; font-family: 'Plus Jakarta Sans', sans-serif; }
        .statement-wrapper { max-width: 900px; margin: 0 auto; border: 1px solid #cbd5e1; border-radius: 8px; padding: 2.5rem; }
        @media print {
            body { padding: 0; }
            .statement-wrapper { border: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="max-width: 900px; margin: 0 auto 1.5rem; display: flex; justify-content: space-between;">
        <a href="/owners/view/<?= $owner['id'] ?>" class="btn btn-secondary">&larr; Return to Profile</a>
        <button onclick="window.print()" class="btn btn-primary">Print Statement</button>
    </div>

    <div class="statement-wrapper">
        <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 1.5rem; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0; color: #0f172a;">REAL ESTATE ERP ENTERPRISE</h1>
                <p style="margin: 0.25rem 0 0; color: #64748b; font-size: 0.85rem;">Official Landlord Portfolio & Asset Statement</p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.85rem; color: #64748b;">Statement Date:</div>
                <div style="font-weight: 700;"><?= date('d F Y') ?></div>
                <div style="font-size: 0.82rem; color: #2563eb; font-weight: 700; margin-top: 0.25rem;"><?= esc($owner['owner_code']) ?></div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; gap: 2rem; margin-bottom: 2rem;">
            <div style="flex: 1;">
                <h4 style="font-size: 0.8rem; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem;">Landlord Details</h4>
                <div style="font-size: 1.1rem; font-weight: 700;"><?= esc($owner['first_name'] . ' ' . $owner['last_name']) ?></div>
                <?php if ($owner['company_name']): ?>
                <div style="color: #475569; font-size: 0.9rem;"><?= esc($owner['company_name']) ?></div>
                <?php endif; ?>
                <div style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem;">
                    PAN: <?= esc($owner['pan_number'] ?: 'N/A') ?> &bull; Phone: <?= esc($owner['phone']) ?>
                </div>
            </div>
            <div style="flex: 1; text-align: right;">
                <h4 style="font-size: 0.8rem; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem;">Disbursement Bank</h4>
                <div style="font-size: 1rem; font-weight: 700;"><?= esc($owner['bank_name'] ?: 'Not Specified') ?></div>
                <div style="font-size: 0.85rem; color: #475569;">A/C: <?= esc($owner['bank_account_number'] ?: '—') ?></div>
                <div style="font-size: 0.85rem; color: #475569;">IFSC: <?= esc($owner['bank_ifsc'] ?: '—') ?></div>
            </div>
        </div>

        <h3 style="font-size: 1rem; font-weight: 700; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem; margin-bottom: 1rem;">Asset Portfolio Summary</h3>
        <table class="table" style="width: 100%; margin-bottom: 2rem;">
            <thead>
                <tr style="background: #f8fafc;">
                    <th style="padding: 0.6rem; text-align: left;">Property Code</th>
                    <th style="padding: 0.6rem; text-align: left;">Title & Location</th>
                    <th style="padding: 0.6rem; text-align: left;">Type</th>
                    <th style="padding: 0.6rem; text-align: right;">Area (Sq.Ft.)</th>
                    <th style="padding: 0.6rem; text-align: right;">Asset Valuation</th>
                </tr>
            </thead>
            <tbody>
                <?php $totalValuation = 0; ?>
                <?php foreach ($owner['properties'] as $p): ?>
                <?php $totalValuation += (float)$p['price']; ?>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 0.6rem;"><strong><?= esc($p['property_code']) ?></strong></td>
                    <td style="padding: 0.6rem;"><?= esc($p['title']) ?>, <?= esc($p['location_city']) ?></td>
                    <td style="padding: 0.6rem;"><?= esc($p['property_type_name']) ?></td>
                    <td style="padding: 0.6rem; text-align: right;"><?= number_format((float)$p['area'], 0) ?></td>
                    <td style="padding: 0.6rem; text-align: right; font-weight: 700;">₹<?= number_format((float)$p['price'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr style="font-weight: 800; background: #f8fafc;">
                    <td colspan="4" style="padding: 0.75rem;">Total Portfolio Assets (<?= count($owner['properties']) ?> Properties)</td>
                    <td style="padding: 0.75rem; text-align: right; color: #2563eb;">₹<?= number_format($totalValuation, 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div style="background: #f1f5f9; padding: 1.5rem; border-radius: 8px; margin-bottom: 2.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span>Total Gross Rental Income Realized:</span>
                <strong style="color: #10b981;">₹<?= number_format($owner['rental_income'], 2) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span>Total Maintenance & Operational Expenses Incurred:</span>
                <strong style="color: #ef4444;">- ₹<?= number_format($owner['expenses'], 2) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-top: 1px solid #cbd5e1; padding-top: 0.5rem; font-size: 1.1rem; font-weight: 800;">
                <span>Net Portfolio Yield Balance:</span>
                <span style="color: #2563eb;">₹<?= number_format($owner['rental_income'] - $owner['expenses'], 2) ?></span>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 4rem; padding-top: 1rem; border-top: 1px dashed #cbd5e1;">
            <div style="text-align: center; width: 220px;">
                <div style="border-bottom: 1px solid #64748b; height: 40px;"></div>
                <div style="font-size: 0.8rem; margin-top: 0.5rem; color: #64748b;">Prepared By (Accounts)</div>
            </div>
            <div style="text-align: center; width: 220px;">
                <div style="border-bottom: 1px solid #64748b; height: 40px;"></div>
                <div style="font-size: 0.8rem; margin-top: 0.5rem; color: #64748b;">Authorized Signatory / Seal</div>
            </div>
        </div>
    </div>
</body>
</html>
