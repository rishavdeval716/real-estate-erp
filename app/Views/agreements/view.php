<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Agreement - <?= esc($agreement['agreement_number']) ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: "Georgia", Times, serif;
            background: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 2rem;
            line-height: 1.6;
        }
        .doc-container {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 3.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .doc-header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }
        .doc-title {
            font-size: 1.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 0.5rem 0;
            color: #0f172a;
        }
        .doc-meta {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 0.85rem;
            color: #64748b;
        }
        .parties-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 1.25rem;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .clause-title {
            font-weight: 700;
            font-size: 1.05rem;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.25rem;
        }
        .clause-content {
            font-size: 0.95rem;
            text-align: justify;
            white-space: pre-line;
            margin-bottom: 1.5rem;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 0.875rem;
        }
        .schedule-table th, .schedule-table td {
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.85rem;
            text-align: left;
        }
        .schedule-table th { background: #f1f5f9; font-weight: 600; }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 4rem;
            padding-top: 2rem;
        }
        .sig-block {
            text-align: center;
            width: 220px;
        }
        .sig-line {
            border-top: 1px solid #0f172a;
            margin-bottom: 0.5rem;
        }
        .toolbar {
            max-width: 850px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
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
        .btn-success { background: #16a34a; }
        .btn-danger { background: #dc2626; }
        .btn-secondary { background: #64748b; }
        @media print {
            body { background: white; padding: 0; }
            .doc-container { border: none; box-shadow: none; padding: 0; }
            .toolbar { display: none; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <div style="display: flex; gap: 0.5rem; align-items: center;">
        <span style="font-weight: 600;">Status:</span>
        <span style="font-size: 0.85rem; padding: 0.25rem 0.5rem; border-radius: 4px; background: <?= $agreement['agreement_status'] === 'Signed' ? '#dcfce7; color: #166534;' : '#fef3c7; color: #92400e;' ?>">
            <?= esc($agreement['agreement_status']) ?>
        </span>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button class="btn" onclick="window.print();">Print Agreement</button>
        <?php if ($agreement['agreement_status'] !== 'Signed'): ?>
            <form method="POST" action="/agreements/sign/<?= $agreement['id'] ?>" style="display: inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success">Mark as Signed</button>
            </form>
        <?php endif; ?>
        <?php if ($agreement['agreement_status'] !== 'Cancelled'): ?>
            <form method="POST" action="/agreements/cancel/<?= $agreement['id'] ?>" onsubmit="return confirm('Cancel this agreement?');" style="display: inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">Cancel</button>
            </form>
        <?php endif; ?>
        <a href="/agreements" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="doc-container">
    <div class="doc-header">
        <div style="font-size: 1.1rem; color: #64748b; font-family: -apple-system, sans-serif;"><?= esc($company['name']) ?></div>
        <h1 class="doc-title"><?= esc($agreement['agreement_type']) ?></h1>
        <div class="doc-meta">
            Document Reference: <strong style="font-family: monospace;"><?= esc($agreement['agreement_number']) ?></strong>
            &bull; Date of Execution: <strong><?= date('d M Y', strtotime($agreement['agreement_date'])) ?></strong>
        </div>
    </div>

    <div class="parties-box">
        <p><strong>BETWEEN:</strong></p>
        <p>
            <strong><?= esc($company['name']) ?></strong>, having its corporate office at <?= esc($company['address']) ?> (hereinafter referred to as the <strong>"PROMOTER/DEVELOPER"</strong>, which expression shall unless repugnant to the context include its successors and assigns) of the <strong>FIRST PART</strong>.
        </p>
        <p><strong>AND:</strong></p>
        <p>
            <strong><?= esc($agreement['first_name'] . ' ' . $agreement['last_name']) ?></strong>, residing at <?= esc($agreement['customer_address'] ?: 'Not Provided') ?>, <?= esc($agreement['customer_city'] ?: '') ?> <?= esc($agreement['customer_state'] ?: '') ?> <?= esc($agreement['customer_pincode'] ?: '') ?>, bearing <?= esc($agreement['id_proof_type'] ?: 'ID') ?> No. <?= esc($agreement['id_proof_number'] ?: 'Verified') ?> (hereinafter referred to as the <strong>"ALLOTTEE/PURCHASER"</strong>) of the <strong>SECOND PART</strong>.
        </p>
    </div>

    <div class="clause-title">SCHEDULE OF PROPERTY (THE "SAID PREMISES")</div>
    <table class="schedule-table">
        <tr>
            <th>Project Name</th>
            <td><?= esc($agreement['project_name']) ?></td>
            <th>Property Name</th>
            <td><?= esc($agreement['property_title'] ?? $agreement['project_name']) ?></td>
        </tr>
        <tr>
            <th>Unit / Apartment Number</th>
            <td style="font-weight: 700; color: #0284c7;">Unit <?= esc($agreement['unit_number']) ?></td>
            <th>Floor</th>
            <td>Floor <?= esc($agreement['floor'] ?? 'N/A') ?></td>
        </tr>
        <tr>
            <th>Apartment Configuration</th>
            <td><?= esc($agreement['flat_type'] ?? 'Standard') ?></td>
            <th>Carpet Area</th>
            <td><?= esc($agreement['carpet_area'] ?? '-') ?> sq.ft</td>
        </tr>
        <tr>
            <th>Booking Reference</th>
            <td><?= esc($agreement['booking_number']) ?></td>
            <th>Total Consideration</th>
            <td style="font-weight: 700; font-size: 1rem;">₹<?= number_format($agreement['total_value'], 2) ?></td>
        </tr>
    </table>

    <div class="clause-title">TERMS & CONDITIONS OF SALE</div>
    <div class="clause-content"><?= esc($agreement['terms_conditions']) ?></div>

    <?php if (!empty($agreement['special_conditions'])): ?>
        <div class="clause-title">SPECIAL CONDITIONS & MUTUALLY AGREED CLAUSES</div>
        <div class="clause-content"><?= esc($agreement['special_conditions']) ?></div>
    <?php endif; ?>

    <p style="font-size: 0.9rem; margin-top: 2rem;">
        IN WITNESS WHEREOF, the parties hereto have set their hands and seals on this agreement on the day, month, and year first above written.
    </p>

    <div class="signatures">
        <div class="sig-block">
            <div class="sig-line"></div>
            <div style="font-weight: 700; font-size: 0.9rem;">For the Promoter / Developer</div>
            <div style="font-size: 0.8rem; color: #64748b;">Authorized Signatory</div>
        </div>
        <div class="sig-block">
            <div class="sig-line"></div>
            <div style="font-weight: 700; font-size: 0.9rem;"><?= esc($agreement['first_name'] . ' ' . $agreement['last_name']) ?></div>
            <div style="font-size: 0.8rem; color: #64748b;">The Allottee / Purchaser</div>
        </div>
    </div>
</div>

</body>
</html>
