<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="margin-bottom: 24px;">
    <a href="/construction/daily-logs" style="font-size: 13px; font-weight: 600; color: var(--primary); display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
        &larr; Back to Daily Site Logs
    </a>
    <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900);"><?= esc($title) ?></h1>
    <p style="color: var(--slate-500); font-size: 14px;">Submit daily site progress report, labor headcount and equipment deployment</p>
</div>

<div class="erp-card" style="max-width: 800px;">
    <div class="card-body" style="padding: 24px;">
        <form method="POST" action="/construction/daily-logs/store">
            <?= csrf_field() ?>
            <div style="display: flex; flex-direction: column; gap: 18px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Execution Date *</label>
                        <input type="date" name="log_date" class="form-control" value="<?= esc($today) ?>" required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Weather Condition</label>
                        <select name="weather_condition" class="form-control">
                            <option value="Sunny">Sunny / Clear</option>
                            <option value="Overcast">Overcast / Cloud</option>
                            <option value="Rainy">Rainy / Wet Site</option>
                            <option value="Stormy">Heavy Rain / High Winds</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Project *</label>
                        <select name="project_id" class="form-control" required>
                            <option value="">Select Project</option>
                            <?php foreach ($projects as $prj): ?>
                                <option value="<?= $prj['id'] ?>"><?= esc($prj['name'] ?? $prj['project_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Building Tower (Optional)</label>
                        <select name="tower_id" class="form-control">
                            <option value="">All Towers / Site Wide</option>
                            <?php foreach ($towers as $tw): ?>
                                <option value="<?= $tw['id'] ?>"><?= esc($tw['tower_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Active Milestone Stage (Optional)</label>
                    <select name="milestone_id" class="form-control">
                        <option value="">General Works / Not Milestone-Specific</option>
                        <?php foreach ($milestones as $ms): ?>
                            <option value="<?= $ms['id'] ?>"><?= esc($ms['stage_order']) ?>. <?= esc($ms['milestone_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; background: var(--slate-50); padding: 16px; border-radius: 8px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Skilled Workers (Masons, Fitters, Electricians) *</label>
                        <input type="number" name="skilled_workers" class="form-control" value="20" min="0" required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Unskilled Workers (Helpers, Carriers) *</label>
                        <input type="number" name="unskilled_workers" class="form-control" value="35" min="0" required>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Detailed Work Completed Today *</label>
                    <textarea name="work_completed" class="form-control" rows="3" placeholder="Describe concrete casting, rebar tie-up, brickwork progress, plumbing drops..." required></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Materials Consumed</label>
                        <textarea name="materials_used" class="form-control" rows="2" placeholder="e.g. 50 bags cement, 2.5 MT steel, 1200 AAC blocks..."></textarea>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Heavy Equipment Utilized</label>
                        <textarea name="equipment_deployed" class="form-control" rows="2" placeholder="e.g. Tower crane 8 hrs, concrete pump 4 hrs..."></textarea>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Site Delays / Impediments / Safety Notes</label>
                    <textarea name="delays_or_impediments" class="form-control" rows="2" placeholder="Material shortage, power outage, inspection wait times, zero-incident safety log..."></textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="/construction/daily-logs" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Daily Log</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
