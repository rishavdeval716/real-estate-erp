<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/projects">Projects</a>
            <span class="separator">/</span>
            <span>Edit</span>
        </div>
        <h1 class="page-title">Edit Project</h1>
        <p class="page-subtitle">Update project information and construction timelines</p>
    </div>
    <div>
        <a href="/projects/view/<?= esc($project['id']) ?>" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Project
        </a>
    </div>
</div>

<div class="card" style="max-width: 880px;">
    <div class="card-header">
        <h2 class="card-title">Edit: <?= esc($project['name']) ?></h2>
    </div>
    <div class="card-body">
        <form action="/projects/update/<?= esc($project['id']) ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="name" class="form-label required">Project Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name', $project['name'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="project_code" class="form-label required">Project Code</label>
                        <input type="text" name="project_code" id="project_code" class="form-control" value="<?= esc(old('project_code', $project['project_code'])) ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="builder_developer" class="form-label required">Builder / Developer Name</label>
                        <input type="text" name="builder_developer" id="builder_developer" class="form-control" value="<?= esc(old('builder_developer', $project['builder_developer'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="location_id" class="form-label required">Project Location</label>
                        <select name="location_id" id="location_id" class="form-control" required>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= esc($loc['id']) ?>" <?= old('location_id', $project['location_id']) == $loc['id'] ? 'selected' : '' ?>>
                                    <?= esc($loc['city']) ?> - <?= esc($loc['area']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Project Description</label>
                <textarea name="description" id="description" rows="3" class="form-control"><?= esc(old('description', $project['description'])) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="construction_status" class="form-label required">Construction Status</label>
                        <select name="construction_status" id="construction_status" class="form-control" required>
                            <?php foreach (['Pre-Launch', 'Under Construction', 'Ready to Move', 'Completed', 'On Hold'] as $st): ?>
                                <option value="<?= esc($st) ?>" <?= old('construction_status', $project['construction_status']) === $st ? 'selected' : '' ?>>
                                    <?= esc($st) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="possession_date" class="form-label">Estimated Possession Date</label>
                        <input type="date" name="possession_date" id="possession_date" class="form-control" value="<?= esc(old('possession_date', $project['possession_date'])) ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label required">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <?php foreach (['active', 'inactive', 'completed', 'archived'] as $st): ?>
                        <option value="<?= esc($st) ?>" <?= old('status', $project['status']) === $st ? 'selected' : '' ?>>
                            <?= ucfirst(esc($st)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Update Project</button>
                <a href="/projects/view/<?= esc($project['id']) ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
