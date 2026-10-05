<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Maintenance Work Order Sheet') ?></title>
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
            padding: 2.5rem 3rem;
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
            font-size: 22px;
            color: var(--primary);
            margin: 0 0 0.25rem 0;
            text-transform: uppercase;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
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
        .sig-grid {
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
    <button onclick="window.print()" class="btn btn-print">Print Work Order</button>
    <button onclick="window.close()" class="btn btn-close">Close</button>
</div>

<div class="voucher-sheet">
    <div class="header">
        <div>
            <h1 class="title"><?= esc($company['name'] ?? 'Real Estate ERP Enterprise Ltd.') ?></h1>
            <div style="color: var(--slate-600); font-size: 12px;">
                Operations & Facility Engineering Services<br>
                <?= esc($company['address'] ?? '') ?>, <?= esc($company['city'] ?? '') ?>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-weight: 700; color: var(--primary);">WORK ORDER SHEET</div>
            <div style="font-weight: 600; font-size: 14px; margin-top: 0.25rem;"><?= esc($ticket['ticket_number']) ?></div>
            <div style="font-size: 12px; color: #64748b;">Issued: <?= date('d M Y, H:i', strtotime($ticket['created_date'])) ?></div>
        </div>
    </div>

    <div class="grid-2">
        <div class="box">
            <div class="box-title">Premises & Location</div>
            <strong><?= esc($ticket['property_title']) ?></strong><br>
            Location: <?= esc($ticket['unit_number'] ? 'Unit ' . $ticket['unit_number'] : 'Common Facility Area') ?><br>
            Asset: <?= esc($ticket['asset_name'] ?? 'Not applicable') ?>
        </div>
        <div class="box">
            <div class="box-title">Assigned Field Specialist</div>
            <strong><?= esc($ticket['technician_name'] ?? 'Unassigned Specialist') ?></strong><br>
            Skill: <?= esc($ticket['technician_skill'] ?? 'General') ?><br>
            Mobile: <?= esc($ticket['technician_mobile'] ?? 'N/A') ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Parameters</th>
                <th>Specifications</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Category / Trade</strong></td>
                <td><?= ucfirst(esc($ticket['category'])) ?> (<?= esc($ticket['subcategory'] ?? 'General') ?>)</td>
            </tr>
            <tr>
                <td><strong>Priority & SLA Target</strong></td>
                <td>
                    <span style="font-weight: 700; text-transform: uppercase;"><?= esc($ticket['priority']) ?></span> &mdash;
                    Target Resolution by: <strong><?= !empty($ticket['sla_due_date']) ? date('d M Y, H:i', strtotime($ticket['sla_due_date'])) : 'Standard SLA' ?></strong>
                </td>
            </tr>
            <tr>
                <td><strong>Work Description</strong></td>
                <td style="line-height: 1.6;"><?= nl2br(esc($ticket['description'])) ?></td>
            </tr>
            <tr>
                <td><strong>Action Taken / Resolution</strong></td>
                <td style="min-height: 60px;">
                    <?= !empty($ticket['resolution']) ? nl2br(esc($ticket['resolution'])) : '<span style="color: #94a3b8;">[ Pending technician on-site notes ]</span>' ?>
                </td>
            </tr>
            <tr>
                <td><strong>Spares / Materials Consumed</strong></td>
                <td><span style="color: #94a3b8;">[ List parts & serial numbers used ]</span></td>
            </tr>
        </tbody>
    </table>

    <div class="sig-grid">
        <div class="sig-line">
            Field Technician Signature<br>
            Date: ____/____/2026
        </div>
        <div class="sig-line">
            Resident / Facility Manager Sign-off<br>
            Date: ____/____/2026
        </div>
    </div>
</div>

</body>
</html>
