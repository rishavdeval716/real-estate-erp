<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?= esc($invoice['invoice_number']) ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 2rem;
        }
        .invoice-card {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 3rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }
        .company-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e3a8a;
            margin: 0 0 0.25rem 0;
        }
        .company-details {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.5;
        }
        .invoice-title {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.03em;
            margin: 0 0 0.5rem 0;
            text-align: right;
        }
        .invoice-meta {
            text-align: right;
            font-size: 0.875rem;
            color: #475569;
        }
        .bill-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .bill-box {
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .bill-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            font-size: 0.875rem;
        }
        .table th {
            background: #f8fafc;
            color: #475569;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #cbd5e1;
            font-weight: 600;
        }
        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .totals-grid {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2rem;
        }
        .totals-table {
            width: 320px;
            font-size: 0.875rem;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
        }
        .totals-row.grand {
            border-top: 2px solid #0f172a;
            padding-top: 0.75rem;
            font-size: 1.1rem;
            font-weight: 800;
        }
        .payment-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1.25rem;
            font-size: 0.85rem;
            color: #475569;
        }
        .toolbar {
            max-width: 850px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
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
        .btn-danger { background: #dc2626; }
        @media print {
            body { background: white; padding: 0; }
            .invoice-card { border: none; box-shadow: none; padding: 0; }
            .toolbar { display: none; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <div>
        <span style="font-weight: 600;">Status:</span>
        <span style="font-size: 0.85rem; padding: 0.25rem 0.5rem; border-radius: 4px; background: #e2e8f0; font-weight: 600;">
            <?= esc($invoice['status']) ?>
        </span>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button class="btn" onclick="window.print();">Print Invoice</button>
        <?php if ($invoice['status'] !== 'Cancelled'): ?>
            <form method="POST" action="/invoices/cancel/<?= $invoice['id'] ?>" onsubmit="return confirm('Cancel this invoice?');" style="display: inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">Cancel Invoice</button>
            </form>
        <?php endif; ?>
        <a href="/invoices" class="btn btn-secondary">Back to Invoices</a>
    </div>
</div>

<div class="invoice-card">
    <div class="header">
        <div>
            <h1 class="company-name"><?= esc($company['name']) ?></h1>
            <div class="company-details">
                <?= esc($company['address']) ?><br>
                Tel: <?= esc($company['phone']) ?> &bull; Email: <?= esc($company['email']) ?>
            </div>
        </div>
        <div>
            <h2 class="invoice-title">TAX INVOICE</h2>
            <div class="invoice-meta">
                <div>Invoice Number: <strong style="font-family: monospace; font-size: 1rem; color: #0f172a;"><?= esc($invoice['invoice_number']) ?></strong></div>
                <div>Invoice Date: <strong><?= date('d M Y', strtotime($invoice['invoice_date'])) ?></strong></div>
                <div>Due Date: <strong style="color: #dc2626;"><?= date('d M Y', strtotime($invoice['due_date'])) ?></strong></div>
            </div>
        </div>
    </div>

    <div class="bill-grid">
        <div class="bill-box">
            <div class="bill-label">Billed To (Purchaser)</div>
            <div style="font-weight: 700; font-size: 1rem; color: #0f172a;"><?= esc($invoice['first_name'] . ' ' . $invoice['last_name']) ?></div>
            <div>Code: <span style="font-family: monospace;"><?= esc($invoice['customer_code']) ?></span></div>
            <div><?= esc($invoice['customer_phone']) ?> &bull; <?= esc($invoice['customer_email']) ?></div>
            <div><?= esc($invoice['customer_city'] ?: '-') ?>, <?= esc($invoice['customer_state'] ?: '') ?> <?= esc($invoice['customer_pincode'] ?: '') ?></div>
        </div>

        <div class="bill-box">
            <div class="bill-label">Property & Booking Reference</div>
            <div>Booking Ref: <strong><?= esc($invoice['booking_number']) ?></strong></div>
            <div>Project: <strong><?= esc($invoice['project_name']) ?></strong></div>
            <div>Property: <?= esc($invoice['property_title'] ?? $invoice['project_name']) ?></div>
            <div>Unit Allocated: <strong style="color: #0284c7;">Unit <?= esc($invoice['unit_number']) ?> (<?= esc($invoice['flat_type'] ?? '') ?>)</strong></div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Property Consideration Milestone Demand</strong><br>
                    <span style="font-size: 0.8rem; color: #64748b;">Demand against Unit <?= esc($invoice['unit_number']) ?> in <?= esc($invoice['project_name']) ?></span>
                </td>
                <td style="text-align: right; font-weight: 600;">₹<?= number_format($invoice['subtotal'], 2) ?></td>
            </tr>
            <?php if ($invoice['discount'] > 0): ?>
                <tr>
                    <td>Less: Discount / Approved Concession</td>
                    <td style="text-align: right; color: #16a34a; font-weight: 600;">- ₹<?= number_format($invoice['discount'], 2) ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($invoice['tax'] > 0): ?>
                <tr>
                    <td>Add: Goods & Services Tax (GST)</td>
                    <td style="text-align: right; font-weight: 600;">+ ₹<?= number_format($invoice['tax'], 2) ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="totals-grid">
        <div class="totals-table">
            <div class="totals-row">
                <span style="color: #64748b;">Subtotal:</span>
                <span style="font-weight: 600;">₹<?= number_format($invoice['subtotal'], 2) ?></span>
            </div>
            <div class="totals-row grand">
                <span>Invoice Total:</span>
                <span>₹<?= number_format($invoice['total_amount'], 2) ?></span>
            </div>
            <div class="totals-row" style="margin-top: 0.5rem; color: #16a34a;">
                <span>Amount Paid:</span>
                <span style="font-weight: 600;">₹<?= number_format($invoice['paid_amount'], 2) ?></span>
            </div>
            <div class="totals-row" style="color: #dc2626; font-size: 1rem; font-weight: 700; border-top: 1px dashed #cbd5e1; padding-top: 0.5rem;">
                <span>Balance Due:</span>
                <span>₹<?= number_format($invoice['balance_amount'], 2) ?></span>
            </div>
        </div>
    </div>

    <div class="payment-box">
        <strong>Bank Payment Settlement Details:</strong><br>
        Beneficiary: <?= esc($company['name']) ?><br>
        Bank: HDFC Bank Ltd. &bull; Branch: Corporate Finance Branch, Mumbai<br>
        Account Number: 50200012345678 &bull; IFSC Code: HDFC0001234<br>
        <em>Please mention your Booking / Invoice number in the transaction narration when transferring funds via NEFT/RTGS/IMPS.</em>
    </div>
</div>

</body>
</html>
