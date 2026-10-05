<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Lease Agreement Voucher') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --slate-600: #475569;
            --slate-800: #1e293b;
            --border: #cbd5e1;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 2rem;
            background: #f8fafc;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.6;
        }
        .voucher-sheet {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }
        .title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--primary);
            margin: 0 0 0.25rem 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }
        .box {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .box-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.25rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }
        th, td {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        th {
            background: #f1f5f9;
            font-weight: 600;
        }
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-top: 4rem;
            padding-top: 1rem;
        }
        .sig-line {
            border-top: 1px solid #94a3b8;
            text-align: center;
            padding-top: 0.5rem;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }
        .action-bar {
            max-width: 800px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-print {
            background: #0f172a;
            color: #fff;
            border: none;
        }
        .btn-close {
            background: #e2e8f0;
            color: #334155;
            border: none;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .voucher-sheet {
                box-shadow: none;
                border: none;
                padding: 0;
            }
            .action-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <button onclick="window.print()" class="btn btn-print">Print Lease Voucher</button>
    <button onclick="window.close()" class="btn btn-close">Close</button>
</div>

<div class="voucher-sheet">
    <div class="header">
        <div>
            <h1 class="title"><?= esc($company['name'] ?? 'Real Estate ERP Enterprise Ltd.') ?></h1>
            <div style="color: var(--slate-600); font-size: 12px;">
                <?= esc($company['address'] ?? '') ?>, <?= esc($company['city'] ?? '') ?>, <?= esc($company['state'] ?? '') ?><br>
                Email: <?= esc($company['email'] ?? '') ?> &bull; Phone: <?= esc($company['phone'] ?? '') ?>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-weight: 700; color: var(--primary);">LEASE AGREEMENT</div>
            <div style="font-weight: 600; font-size: 14px; margin-top: 0.25rem;"><?= esc($lease['agreement_number']) ?></div>
            <div style="font-size: 12px; color: #64748b;">Date: <?= date('d F Y', strtotime($lease['start_date'])) ?></div>
        </div>
    </div>

    <div class="grid-2">
        <div class="box">
            <div class="box-title">Lessor (Landlord / Company)</div>
            <strong><?= esc($company['name'] ?? 'Real Estate ERP Enterprise Ltd.') ?></strong><br>
            Corporate Offices: Financial District, BKC<br>
            GSTIN: 27AABCR1234F1Z5 &bull; CIN: U70100MH2026PLC000001
        </div>
        <div class="box">
            <div class="box-title">Lessee (Tenant)</div>
            <strong><?= esc($lease['tenant_name']) ?></strong><br>
            Tenant Code: <?= esc($lease['tenant_code']) ?><br>
            Mobile: <?= esc($lease['tenant_mobile'] ?? 'N/A') ?><br>
            Email: <?= esc($lease['tenant_email'] ?? 'N/A') ?>
        </div>
    </div>

    <div class="box" style="margin-bottom: 1.5rem;">
        <div class="box-title">Leased Premises Description</div>
        <div style="font-size: 14px; font-weight: 600;"><?= esc($lease['property_title']) ?></div>
        <div>Unit Assignment: <?= esc($lease['unit_number'] ?? 'Entire Property / Floor') ?></div>
        <div style="font-size: 12px; color: #64748b; margin-top: 0.25rem;">Agreement Type: <?= ucfirst(esc($lease['agreement_type'])) ?> Tenancy</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Particulars</th>
                <th>Tenancy Terms & Specifications</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Commencement & Expiry</strong></td>
                <td><?= date('d M Y', strtotime($lease['start_date'])) ?> to <?= date('d M Y', strtotime($lease['end_date'])) ?></td>
            </tr>
            <tr>
                <td><strong>Lock-in Period</strong></td>
                <td><?= (int)$lease['lock_in_period_months'] ?> Months (Strict non-termination duration)</td>
            </tr>
            <tr>
                <td><strong>Notice Period</strong></td>
                <td><?= (int)$lease['notice_period_days'] ?> Days written notice prior to departure</td>
            </tr>
            <tr>
                <td><strong>Monthly Rent</strong></td>
                <td><strong>₹<?= number_format((float)$lease['monthly_rent'], 2) ?></strong> (Excluding utilities & CAM)</td>
            </tr>
            <tr>
                <td><strong>Security Deposit</strong></td>
                <td><strong>₹<?= number_format((float)$lease['security_deposit'], 2) ?></strong> (Interest-free refundable upon handover)</td>
            </tr>
            <tr>
                <td><strong>Payment Due Day</strong></td>
                <td>Due by Day <?= (int)$lease['payment_due_day'] ?> of every calendar month</td>
            </tr>
            <tr>
                <td><strong>Late Fee Penalty</strong></td>
                <td>₹<?= number_format((float)$lease['late_fee_amount'], 2) ?> per day for late rent receipt</td>
            </tr>
            <tr>
                <td><strong>Rent Escalation</strong></td>
                <td><?= esc($lease['rent_escalation_pct']) ?>% escalation compounding on a <?= esc($lease['escalation_frequency']) ?> basis</td>
            </tr>
            <tr>
                <td><strong>Common Area Maintenance (CAM)</strong></td>
                <td>₹<?= number_format((float)$lease['maintenance_charges'], 2) ?> / month</td>
            </tr>
        </tbody>
    </table>

    <?php if (!empty($lease['terms_conditions'])): ?>
    <div style="margin-top: 1.5rem; font-size: 12px; color: #475569;">
        <strong>Special Covenants:</strong><br>
        <?= nl2br(esc($lease['terms_conditions'])) ?>
    </div>
    <?php endif; ?>

    <div class="signature-grid">
        <div class="sig-line">
            Authorized Signatory<br>
            (For <?= esc($company['name'] ?? 'Lessor') ?>)
        </div>
        <div class="sig-line">
            Signature of Lessee / Tenant<br>
            (<?= esc($lease['tenant_name']) ?>)
        </div>
    </div>
</div>

</body>
</html>
