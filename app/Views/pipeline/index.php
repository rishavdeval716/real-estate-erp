<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leads">CRM</a> &rsaquo;
            <span>Sales Pipeline</span>
        </div>
        <h1 class="page-title">Visual CRM Sales Pipeline</h1>
        <p class="page-subtitle">Track lead progression from first intake through site visit, negotiation, and token booking.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/leads" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            Leads Table
        </a>
        <a href="/leads/create" class="btn btn-primary">+ Add New Lead</a>
    </div>
</div>

<!-- Pipeline Summary Metric Cards -->
<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 1.5rem;">
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Leads in Pipeline</div>
            <div class="metric-value"><?= number_format($totalPipelineCount) ?></div>
            <div class="metric-meta"><span>Across all lifecycle stages</span></div>
        </div>
    </div>
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Active Opportunities</div>
            <div class="metric-value"><?= number_format($activeOpportunities) ?></div>
            <div class="metric-meta"><span>Excluding Won/Lost</span></div>
        </div>
    </div>
    <div class="metric-card success">
        <div>
            <div class="metric-label">Estimated Pipeline Value</div>
            <div class="metric-value" style="color: var(--success); font-size: 1.5rem;">
                ₹<?= $totalPipelineValue >= 10000000 ? number_format($totalPipelineValue / 10000000, 2) . ' Cr' : number_format($totalPipelineValue / 100000, 2) . ' L' ?>
            </div>
            <div class="metric-meta"><span>Aggregated buyer budgets</span></div>
        </div>
    </div>
</div>

<!-- Kanban Board Container -->
<div style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 2rem; align-items: flex-start;">
    <?php foreach ($stages as $stage): 
        $leadsInStage = $pipeline[$stage] ?? [];
        $count = count($leadsInStage);
    ?>
        <div style="flex: 0 0 280px; background: #f8fafc; border: 1px solid var(--slate-200); border-radius: 8px; display: flex; flex-direction: column; max-height: calc(100vh - 280px);">
            <!-- Column Header -->
            <div style="padding: 0.85rem 1rem; border-bottom: 1px solid var(--slate-200); background: #ffffff; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; font-size: 0.88rem; color: var(--slate-800);"><?= esc($stage) ?></span>
                <span style="background: var(--slate-100); color: var(--slate-600); font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 9999px;">
                    <?= $count ?>
                </span>
            </div>

            <!-- Column Cards Scrollable List -->
            <div style="padding: 0.75rem; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem;">
                <?php if (empty($leadsInStage)): ?>
                    <div style="text-align: center; color: var(--slate-400); font-size: 0.8rem; padding: 2rem 0.5rem; border: 1px dashed var(--slate-200); border-radius: 6px;">
                        No leads in this stage
                    </div>
                <?php else: ?>
                    <?php foreach ($leadsInStage as $lead): ?>
                        <div class="card" style="padding: 0.85rem; margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.06); border-left: 3px solid var(--primary);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                                <a href="/leads/view/<?= esc($lead['id']) ?>" style="font-weight: 700; font-size: 0.95rem; color: var(--slate-900); text-decoration: none;">
                                    <?= esc($lead['first_name'] . ' ' . $lead['last_name']) ?>
                                </a>
                                <?= \App\Libraries\LeadStatus::renderPriorityBadge($lead['priority']) ?>
                            </div>

                            <div style="font-family: monospace; font-size: 0.75rem; color: var(--slate-500); margin-bottom: 0.4rem;">
                                <?= esc($lead['lead_code']) ?> &bull; <?= esc($lead['phone']) ?>
                            </div>

                            <?php if (!empty($lead['property_title'])): ?>
                                <div style="font-size: 0.8rem; font-weight: 600; color: var(--primary); margin-bottom: 0.2rem;">
                                    <?= esc($lead['property_title']) ?>
                                </div>
                            <?php elseif (!empty($lead['project_name'])): ?>
                                <div style="font-size: 0.8rem; font-weight: 600; color: var(--primary); margin-bottom: 0.2rem;">
                                    <?= esc($lead['project_name']) ?>
                                </div>
                            <?php endif; ?>

                            <div style="font-size: 0.85rem; font-weight: 700; color: var(--slate-900); margin-bottom: 0.5rem;">
                                <?php if ((float)$lead['budget_max'] > 0): ?>
                                    ₹<?= (float)$lead['budget_max'] >= 10000000 ? number_format((float)$lead['budget_max'] / 10000000, 2) . ' Cr' : number_format((float)$lead['budget_max'] / 100000, 1) . ' L' ?>
                                <?php else: ?>
                                    <span style="color: var(--slate-400); font-weight: 400; font-size: 0.78rem;">Budget unassigned</span>
                                <?php endif; ?>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--slate-100); padding-top: 0.5rem; font-size: 0.75rem;">
                                <span style="color: var(--slate-600);"><?= esc($lead['assigned_to_name'] ?: 'Unassigned') ?></span>
                                <button type="button" class="btn btn-sm btn-secondary" style="font-size: 0.7rem; padding: 0.2rem 0.45rem;" onclick="openMoveModal(<?= esc($lead['id']) ?>, '<?= esc($lead['lead_code']) ?>', '<?= esc($stage) ?>')">
                                    Move &rarr;
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal: Quick Move Stage -->
<div id="modal-move-stage" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 450px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">
            Move Lead Stage: <span id="modal-lead-code"></span>
        </h3>
        <form method="post" action="/pipeline/stage">
            <?= csrf_field() ?>
            <input type="hidden" name="lead_id" id="modal-lead-id">
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Target Stage</label>
                <select name="lead_stage" id="modal-target-stage" class="form-control" required>
                    <?php foreach ($stages as $sg): ?>
                        <option value="<?= esc($sg) ?>"><?= esc($sg) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Transition Remarks</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Notes on client progression..."></textarea>
            </div>

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-move-stage')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Pipeline</button>
            </div>
        </form>
    </div>
</div>

<script>
function openMoveModal(id, code, currentStage) {
    document.getElementById('modal-lead-id').value = id;
    document.getElementById('modal-lead-code').innerText = code;
    document.getElementById('modal-target-stage').value = currentStage;
    document.getElementById('modal-move-stage').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>

<?= $this->endSection() ?>
