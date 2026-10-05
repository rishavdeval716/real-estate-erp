<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/expenses">Property Expenses</a> &rsaquo;
            <span>Categories</span>
        </div>
        <h1 class="page-title">Expense Categories</h1>
        <p class="page-subtitle">Configure accounting categories for building upkeep, taxes, utilities, and marketing.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/expenses" class="btn btn-secondary">&larr; Back to Expenses</a>
    </div>
</div>

<div class="form-row">
    <!-- List Categories -->
    <div class="form-col-8">
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Active Expense Categories</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category Code</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $c): ?>
                            <tr>
                                <td><code><?= esc($c['code']) ?></code></td>
                                <td><strong><?= esc($c['name']) ?></strong></td>
                                <td><?= esc($c['description'] ?: '—') ?></td>
                                <td>
                                    <span class="badge badge-success"><?= ucfirst(esc($c['status'])) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Category Form -->
    <div class="form-col-4">
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Add New Category</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <form method="POST" action="/expenses/categories/store">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="form-label">Category Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Landscaping & Horticulture">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category Code <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. EXP-CAT-LAND" style="text-transform: uppercase;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Scope of expenses..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Create Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
