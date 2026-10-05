<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Voucher - <?= esc($booking['booking_number']) ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 2rem;
        }
        .voucher-container {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
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
            font-weight: 700;
            color: #1e3a8a;
            margin: 0 0 0.25rem 0;
        }
        .company-meta {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.4;
        }
        .voucher-title-box {
            text-align: right;
        }
        .voucher-badge {
            display: inline-block;
            background: #0284c7;
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.35rem 0.75rem;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .voucher-number {
            font-size: 1.25rem;
            font-weight: 700;
            font-family: monospace;
            color: #0f172a;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .section-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1rem 1.25rem;
        }
        .section-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.35rem;
        }
        .data-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
        }
        .data-label { color: #64748b; }
        .data-val { font-weight: 600; color: #0f172a; }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .table th {
            background: #f1f5f9;
            color: #475569;
            text-align: left;
            padding: 0.65rem 0.75rem;
            border-bottom: 1px solid #cbd5e1;
            font-weight: 600;
        }
        .table td {
            padding: 0.65rem 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .terms-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1rem;
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 2rem;
            background: #ffffff;
        }
        .footer-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
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
        .print-bar {
            max-width: 850px;
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
        .btn-secondary {
            background: #64748b;
        }
        @media print {
            body { background: white; padding: 0; }
            .voucher-container { border: none; box-shadow: none; padding: 0; }
            .print-bar { display: none; }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <button class="btn" onclick="window.print();">Print Voucher / Download PDF</button>
    <a href="/bookings/view/<?= $booking['id'] ?>" class="btn btn-secondary">Back to Booking</a>
</div>

<div class="voucher-container">
    <div class="header">
        <div>
            <h1 class="company-title"><?= esc($company['name']) ?></h1>
            <div class="company-meta">
                <?= esc($company['address']) ?><br>
                Tel: <?= esc($company['phone']) ?> &bull; Email: <?= esc($company['email']) ?>
            </div>
        </div>
        <div class="voucher-title-box">
            <div class="voucher-badge">Official Booking Voucher</div>
            <div class="voucher-number"><?= esc($booking['booking_number']) ?></div>
            <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">
                Date: <strong><?= date('d M Y', strtotime($booking['booking_date'])) ?></strong>
            </div>
            <div style="font-size: 0.85rem; color: #64748b;">
                Status: <strong><?= esc($booking['booking_status']) ?></strong>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="section-box">
            <div class="section-title">Allottee / Customer Details</div>
            <div class="data-row">
                <span class="data-label">Name:</span>
                <span class="data-val"><?= esc($booking['first_name'] . ' ' . $booking['last_name']) ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Customer ID:</span>
                <span class="data-val"><?= esc($booking['customer_code']) ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Phone:</span>
                <span class="data-val"><?= esc($booking['customer_phone']) ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Email:</span>
                <span class="data-val"><?= esc($booking['customer_email'] ?: 'N/A') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Address:</span>
                <span class="data-val" style="text-align: right; max-width: 60%;"><?= esc($booking['customer_city'] ?: '-') ?>, <?= esc($booking['customer_state'] ?: '') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">ID Proof:</span>
                <span class="data-val"><?= esc($booking['id_proof_type'] ?: '-') ?> (<?= esc($booking['id_proof_number'] ?: 'Verified') ?>)</span>
            </div>
        </div>

        <div class="section-box">
            <div class="section-title">Property & Unit Allotted</div>
            <div class="data-row">
                <span class="data-label">Project:</span>
                <span class="data-val"><?= esc($booking['project_name']) ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Property:</span>
                <span class="data-val"><?= esc($booking['property_title'] ?? $booking['project_name']) ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Unit Number:</span>
                <span class="data-val" style="color: #0284c7; font-size: 1rem;">Unit <?= esc($booking['unit_number']) ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Configuration:</span>
                <span class="data-val"><?= esc($booking['flat_type'] ?? 'Standard') ?></span>
            </div>
            <div class="data-row">
                <span class="data-label">Carpet Area:</span>
                <span class="data-val"><?= esc($booking['carpet_area'] ?? '-') ?> sq.ft</span>
            </div>
            <div class="data-row">
                <span class="data-label">Floor:</span>
                <span class="data-val">Floor <?= esc($booking['floor_number'] ?? $booking['floor'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>

    <!-- Pricing & Financial Summary Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Agreed Base Unit Price</td>
                <td style="text-align: right; font-weight: 600;">₹<?= number_format($booking['base_price'], 2) ?></td>
            </tr>
            <?php if ($booking['discount'] > 0): ?>
                <tr>
                    <td>Less: Special Approved Discount</td>
                    <td style="text-align: right; color: #16a34a; font-weight: 600;">- ₹<?= number_format($booking['discount'], 2) ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($booking['tax_amount'] > 0): ?>
                <tr>
                    <td>Add: Applicable GST / Taxes</td>
                    <td style="text-align: right; font-weight: 600;">+ ₹<?= number_format($booking['tax_amount'], 2) ?></td>
                </tr>
            <?php endif; ?>
            <tr style="background: #f8fafc; font-size: 1rem;">
                <td><strong>Total Agreed Consideration</strong></td>
                <td style="text-align: right; font-weight: 700; color: #0f172a;">₹<?= number_format($booking['final_amount'], 2) ?></td>
            </tr>
            <tr>
                <td>Total Verified Collections to Date</td>
                <td style="text-align: right; font-weight: 600; color: #16a34a;">₹<?= number_format($booking['total_paid'], 2) ?></td>
            </tr>
            <tr style="background: #fef2f2;">
                <td><strong>Total Outstanding Balance</strong></td>
                <td style="text-align: right; font-weight: 700; color: #dc2626;">₹<?= number_format($booking['total_outstanding'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="terms-box">
        <strong>Terms & Conditions of Booking:</strong><br>
        1. This booking voucher is subject to timely milestone payments strictly in accordance with the attached payment schedule.<br>
        2. Execution of the standard Agreement for Sale shall take place upon completion of booking advance verification.<br>
        3. Possession handover is subject to force majeure conditions, statutory regulatory clearances, and full financial settlement.<br>
        4. Voluntary cancellation shall be processed under the standard deduction policy of the company.
    </div>

    <div class="footer-signatures">
        <div class="sig-block">
            <div class="sig-line"></div>
            <div style="font-size: 0.85rem; font-weight: 600;">Allottee / Buyer Signature</div>
        </div>
        <div class="sig-block">
            <div class="sig-line"></div>
            <div style="font-size: 0.85rem; font-weight: 600;">Authorized Signatory</div>
            <div style="font-size: 0.75rem; color: #64748b;"><?= esc($booking['executive_name'] ?? 'Authorized Officer') ?></div>
        </div>
    </div>
</div>

</body>
</html>
