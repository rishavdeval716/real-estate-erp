<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?= esc($receipt['receipt_number']) ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 2rem;
        }
        .receipt-card {
            max-width: 780px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #0284c7;
            border-radius: 8px;
            padding: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .company-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e3a8a;
            margin: 0 0 0.25rem 0;
        }
        .company-meta {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.4;
        }
        .receipt-badge {
            background: #16a34a;
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        .receipt-num {
            font-family: monospace;
            font-size: 1.3rem;
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }
        .amount-hero {
            background: #f0fdf4;
            border: 2px dashed #86efac;
            border-radius: 8px;
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .amount-val {
            font-size: 1.8rem;
            font-weight: 800;
            color: #15803d;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .content-table td {
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .label-cell {
            width: 30%;
            color: #64748b;
            font-weight: 600;
        }
        .val-cell {
            color: #0f172a;
            font-weight: 500;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 3.5rem;
            padding-top: 1rem;
        }
        .sig-block {
            text-align: center;
            width: 200px;
        }
        .sig-line {
            border-top: 1px solid #0f172a;
            margin-bottom: 0.5rem;
        }
        .toolbar {
            max-width: 780px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        .btn {
            background: #0284c7;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .btn-secondary { background: #64748b; }
        @media print {
            body { background: white; padding: 0; }
            .receipt-card { border: 1px solid #000; box-shadow: none; padding: 1.5rem; }
            .toolbar { display: none; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button class="btn" onclick="window.print();">Print Official Receipt</button>
    <a href="/receipts" class="btn btn-secondary">Back to Receipts</a>
</div>

<div class="receipt-card">
    <div class="header">
        <div>
            <h1 class="company-title"><?= esc($company['name']) ?></h1>
            <div class="company-meta">
                <?= esc($company['address']) ?><br>
                Tel: <?= esc($company['phone']) ?> &bull; Email: <?= esc($company['email']) ?>
            </div>
        </div>
        <div style="text-align: right;">
            <div class="receipt-badge">Official Payment Receipt</div>
            <div class="receipt-num"><?= esc($receipt['receipt_number']) ?></div>
            <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">
                Date: <strong><?= date('d M Y', strtotime($receipt['receipt_date'])) ?></strong>
            </div>
        </div>
    </div>

    <div class="amount-hero">
        <div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #166534; text-transform: uppercase;">Amount Received</div>
            <div class="amount-val">₹<?= number_format($receipt['amount'], 2) ?></div>
        </div>
        <div style="text-align: right; font-size: 0.85rem; color: #166534; font-weight: 600;">
            Status: VERIFIED & CLEARED
        </div>
    </div>

    <table class="content-table">
        <tr>
            <td class="label-cell">Received From:</td>
            <td class="val-cell">
                <strong><?= esc($receipt['first_name'] . ' ' . $receipt['last_name']) ?></strong>
                <span style="font-size: 0.85rem; color: #64748b; font-family: monospace;">(Code: <?= esc($receipt['customer_code']) ?>)</span>
            </td>
        </tr>
        <tr>
            <td class="label-cell">Towards Property / Unit:</td>
            <td class="val-cell">
                <strong><?= esc($receipt['project_name']) ?></strong> &bull; Unit <strong><?= esc($receipt['unit_number']) ?></strong>
            </td>
        </tr>
        <tr>
            <td class="label-cell">Booking Reference:</td>
            <td class="val-cell" style="font-family: monospace; font-weight: 600;">
                <?= esc($receipt['booking_number']) ?>
            </td>
        </tr>
        <tr>
            <td class="label-cell">Payment Method:</td>
            <td class="val-cell">
                <strong><?= esc($receipt['payment_method']) ?></strong>
            </td>
        </tr>
        <tr>
            <td class="label-cell">Transaction Reference:</td>
            <td class="val-cell" style="font-family: monospace;">
                <?= esc($receipt['transaction_reference'] ?: ($receipt['cheque_number'] ? 'Cheque #: ' . $receipt['cheque_number'] : 'Verified Direct Transfer')) ?>
                <?php if (!empty($receipt['bank_name'])): ?>
                    (Bank: <?= esc($receipt['bank_name']) ?>)
                <?php endif; ?>
            </td>
        </tr>
        <?php if (!empty($receipt['remarks'])): ?>
            <tr>
                <td class="label-cell">Narration / Remarks:</td>
                <td class="val-cell" style="color: #64748b;"><?= esc($receipt['remarks']) ?></td>
            </tr>
        <?php endif; ?>
    </table>

    <div style="font-size: 0.8rem; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 0.75rem;">
        * This is a system-generated electronic receipt valid subject to bank realization of cheques/drafts.
    </div>

    <div class="signatures">
        <div class="sig-block">
            <div class="sig-line"></div>
            <div style="font-size: 0.85rem; font-weight: 600;">Payer's Signature</div>
        </div>
        <div class="sig-block">
            <div class="sig-line"></div>
            <div style="font-size: 0.85rem; font-weight: 600;">Authorized Accounts Signatory</div>
            <div style="font-size: 0.75rem; color: #64748b;"><?= esc($company['name']) ?></div>
        </div>
    </div>
</div>

</body>
</html>
