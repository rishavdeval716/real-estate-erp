<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/projects">Projects</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <h1 class="page-title">Create Real Estate Project</h1>
        <p class="page-subtitle">Register a new residential or commercial development venture</p>
    </div>
    <div>
        <a href="/projects" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Projects
        </a>
    </div>
</div>

<div class="card" style="max-width: 880px;">
    <div class="card-header">
        <h2 class="card-title">Project Master Information</h2>
    </div>
    <div class="card-body">
        <form action="/projects/store" method="POST">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="name" class="form-label required">Project Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name')) ?>" placeholder="e.g. Skyline Horizon Residences" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="project_code" class="form-label required">Project Code (Unique)</label>
                        <input type="text" name="project_code" id="project_code" class="form-control" value="<?= esc(old('project_code')) ?>" placeholder="e.g. PRJ-MUM-SKY01" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="builder_developer" class="form-label required">Builder / Developer Name</label>
                        <input type="text" name="builder_developer" id="builder_developer" class="form-control" value="<?= esc(old('builder_developer')) ?>" placeholder="e.g. Skyline Luxury Infra Group" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="location_id" class="form-label required">Project Location</label>
                        <select name="location_id" id="location_id" class="form-control" required>
                            <option value="">Select Location</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= esc($loc['id']) ?>" <?= old('location_id') == $loc['id'] ? 'selected' : '' ?>>
                                    <?= esc($loc['city']) ?> - <?= esc($loc['area']) ?> (<?= esc($loc['state']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Project Description & Scope</label>
                <textarea name="description" id="description" rows="3" class="form-control" placeholder="Describe architectural highlights, master plan, and key offerings..."><?= esc(old('description')) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="construction_status" class="form-label required">Construction Status</label>
                        <select name="construction_status" id="construction_status" class="form-control" required>
                            <option value="Pre-Launch" <?= old('construction_status') === 'Pre-Launch' ? 'selected' : '' ?>>Pre-Launch</option>
                            <option value="Under Construction" <?= old('construction_status', 'Under Construction') === 'Under Construction' ? 'selected' : '' ?>>Under Construction</option>
                            <option value="Ready to Move" <?= old('construction_status') === 'Ready to Move' ? 'selected' : '' ?>>Ready to Move</option>
                            <option value="Completed" <?= old('construction_status') === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="On Hold" <?= old('construction_status') === 'On Hold' ? 'selected' : '' ?>>On Hold</option>
                        </select>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="possession_date" class="form-label">Estimated Possession Date</label>
                        <input type="date" name="possession_date" id="possession_date" class="form-control" value="<?= esc(old('possession_date')) ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label required">Project Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="completed" <?= old('status') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="archived" <?= old('status') === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Create Project</button>
                <a href="/projects" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
