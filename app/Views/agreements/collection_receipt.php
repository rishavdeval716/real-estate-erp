<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Official Rent Payment Receipt') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --emerald: #059669;
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
        .receipt-card {
            max-width: 750px;
            margin: 0 auto;
            background: #fff;
            padding: 2.5rem 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }
        .company-name {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--primary);
            margin: 0 0 0.25rem 0;
            text-transform: uppercase;
        }
        .receipt-badge {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-size: 11px;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }
        .receipt-num {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }
        .receipt-body {
            margin-bottom: 2rem;
        }
        .field-row {
            display: flex;
            margin-bottom: 1rem;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 0.5rem;
        }
        .field-label {
            width: 180px;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .field-val {
            flex: 1;
            font-size: 14px;
            color: var(--slate-800);
        }
        .amount-box {
            background: #f1f5f9;
            border-radius: 6px;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1.5rem 0;
            border-left: 4px solid var(--emerald);
        }
        .amount-val {
            font-size: 24px;
            font-weight: 800;
            color: var(--emerald);
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        .sig-block {
            text-align: center;
            width: 200px;
            border-top: 1px solid #94a3b8;
            padding-top: 0.5rem;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }
        .action-bar {
            max-width: 750px;
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
            .receipt-card {
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
    <button onclick="window.print()" class="btn btn-print">Print Official Receipt</button>
    <button onclick="window.close()" class="btn btn-close">Close</button>
</div>

<div class="receipt-card">
    <div class="header">
        <div>
            <h1 class="company-name"><?= esc($company['name'] ?? 'Real Estate ERP Enterprise Ltd.') ?></h1>
            <div style="color: var(--slate-600); font-size: 12px;">
                <?= esc($company['address'] ?? '') ?>, <?= esc($company['city'] ?? '') ?><br>
                Official Rent Acknowledgment Receipt
            </div>
        </div>
        <div style="text-align: right;">
            <div class="receipt-badge">Payment Received & Verified</div>
            <div class="receipt-num"><?= esc($collection['collection_number']) ?></div>
            <div style="font-size: 12px; color: #64748b; margin-top: 0.25rem;">Date: <?= date('d F Y', strtotime($collection['payment_date'])) ?></div>
        </div>
    </div>

    <div class="receipt-body">
        <div class="field-row">
            <div class="field-label">Received From</div>
            <div class="field-val">
                <strong><?= esc($collection['tenant_name']) ?></strong> (<?= esc($collection['tenant_code']) ?>)<br>
                <span style="font-size: 12px; color: #64748b;"><?= esc($collection['tenant_mobile'] ?? '') ?> &bull; <?= esc($collection['tenant_email'] ?? '') ?></span>
            </div>
        </div>

        <div class="field-row">
            <div class="field-label">Premises & Unit</div>
            <div class="field-val">
                <?= esc($collection['property_title'] ?? 'Real Estate Property') ?>
                <?= !empty($collection['unit_number']) ? ' &mdash; Unit ' . esc($collection['unit_number']) : '' ?>
            </div>
        </div>

        <div class="field-row">
            <div class="field-label">Lease Agreement</div>
            <div class="field-val">
                <?= esc($collection['agreement_number']) ?>
            </div>
        </div>

        <div class="field-row">
            <div class="field-label">Demand Notice Ref</div>
            <div class="field-val">
                <?= esc($collection['demand_number']) ?> &mdash; For Period: <?= esc($collection['billing_period']) ?>
            </div>
        </div>

        <div class="amount-box">
            <div>
                <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Amount Received</div>
                <div style="font-size: 13px; color: var(--slate-600);">Mode: <?= esc($collection['payment_method']) ?> <?= !empty($collection['transaction_reference']) ? '(Ref: ' . esc($collection['transaction_reference']) . ')' : '' ?></div>
            </div>
            <div class="amount-val">₹<?= number_format((float)$collection['amount'], 2) ?></div>
        </div>

        <?php if (!empty($collection['remarks'])): ?>
        <div class="field-row">
            <div class="field-label">Remarks</div>
            <div class="field-val" style="font-size: 12px; color: #64748b;">
                <?= esc($collection['remarks']) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <div style="font-size: 11px; color: #94a3b8; max-width: 400px;">
            This is a computer-generated official rent acknowledgment receipt. Valid without physical signature if verified by bank UTR / corporate ledger.
        </div>
        <div class="sig-block">
            Authorized Accounts Desk<br>
            <?= esc($company['name'] ?? 'Real Estate ERP') ?>
        </div>
    </div>
</div>

</body>
</html>
