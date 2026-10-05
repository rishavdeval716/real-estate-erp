<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leads">Leads</a> &rsaquo;
            <a href="/leads/view/<?= esc($lead['id']) ?>"><?= esc($lead['lead_code']) ?></a> &rsaquo;
            <span>Edit</span>
        </div>
        <h1 class="page-title">Edit Lead: <?= esc($lead['lead_code']) ?></h1>
        <p class="page-subtitle">Update contact details, budget specifications, and pipeline attributes.</p>
    </div>
    <div>
        <a href="/leads/view/<?= esc($lead['id']) ?>" class="btn btn-secondary">&larr; Back to Details</a>
    </div>
</div>

<form method="post" action="/leads/update/<?= esc($lead['id']) ?>">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
        
        <!-- Left Column -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                    Contact Information
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control" required value="<?= old('first_name', $lead['first_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="<?= old('last_name', $lead['last_name']) ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="phone">Primary Phone <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" required value="<?= old('phone', $lead['phone']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="alternate_phone">Alternate Phone</label>
                        <input type="text" name="alternate_phone" id="alternate_phone" class="form-control" value="<?= old('alternate_phone', $lead['alternate_phone']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= old('email', $lead['email']) ?>">
                </div>
            </div>

            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                    Requirements & Preferences
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="budget_min">Minimum Budget (₹)</label>
                        <input type="number" step="1000" name="budget_min" id="budget_min" class="form-control" value="<?= old('budget_min', (float)$lead['budget_min']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="budget_max">Maximum Budget (₹)</label>
                        <input type="number" step="1000" name="budget_max" id="budget_max" class="form-control" value="<?= old('budget_max', (float)$lead['budget_max']) ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="property_type_id">Property Type</label>
                        <select name="property_type_id" id="property_type_id" class="form-control">
                            <option value="">Any Category</option>
                            <?php foreach ($propTypes as $pt): ?>
                                <option value="<?= esc($pt['id']) ?>" <?= old('property_type_id', $lead['property_type_id']) == $pt['id'] ? 'selected' : '' ?>><?= esc($pt['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="preferred_location">Preferred Location</label>
                        <input type="text" name="preferred_location" id="preferred_location" class="form-control" value="<?= old('preferred_location', $lead['preferred_location']) ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="project_id">Project</label>
                        <select name="project_id" id="project_id" class="form-control">
                            <option value="">Select Project</option>
                            <?php foreach ($projects as $prj): ?>
                                <option value="<?= esc($prj['id']) ?>" <?= old('project_id', $lead['project_id']) == $prj['id'] ? 'selected' : '' ?>><?= esc($prj['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="property_id">Property</label>
                        <select name="property_id" id="property_id" class="form-control">
                            <option value="">Select Property</option>
                            <?php foreach ($properties as $prop): ?>
                                <option value="<?= esc($prop['id']) ?>" <?= old('property_id', $lead['property_id']) == $prop['id'] ? 'selected' : '' ?>><?= esc($prop['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="remarks">Remarks / Requirement Details</label>
                    <textarea name="remarks" id="remarks" class="form-control" rows="3"><?= old('remarks', $lead['remarks']) ?></textarea>
                </div>
            </div>

            <!-- Prospect Profile Update -->
            <?php if ($prospect): ?>
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                    Demographic Profile
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="occupation">Occupation</label>
                        <input type="text" name="occupation" id="occupation" class="form-control" value="<?= old('occupation', $prospect['occupation']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="preferred_contact_method">Preferred Contact Mode</label>
                        <select name="preferred_contact_method" id="preferred_contact_method" class="form-control">
                            <option value="Phone" <?= old('preferred_contact_method', $prospect['preferred_contact_method']) === 'Phone' ? 'selected' : '' ?>>Phone Call</option>
                            <option value="WhatsApp" <?= old('preferred_contact_method', $prospect['preferred_contact_method']) === 'WhatsApp' ? 'selected' : '' ?>>WhatsApp</option>
                            <option value="Email" <?= old('preferred_contact_method', $prospect['preferred_contact_method']) === 'Email' ? 'selected' : '' ?>>Email</option>
                            <option value="In-Person" <?= old('preferred_contact_method', $prospect['preferred_contact_method']) === 'In-Person' ? 'selected' : '' ?>>In-Person</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="address">Address</label>
                    <textarea name="address" id="address" class="form-control" rows="2"><?= old('address', $prospect['address']) ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="city">City</label>
                        <input type="text" name="city" id="city" class="form-control" value="<?= old('city', $prospect['city']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="state">State</label>
                        <input type="text" name="state" id="state" class="form-control" value="<?= old('state', $prospect['state']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="pincode">Pincode</label>
                        <input type="text" name="pincode" id="pincode" class="form-control" value="<?= old('pincode', $prospect['pincode']) ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Right Column -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                    Status & Priority
                </h3>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="lead_status">Lead Status</label>
                    <select name="lead_status" id="lead_status" class="form-control">
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?= esc($st) ?>" <?= old('lead_status', $lead['lead_status']) === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="lead_stage">Pipeline Stage</label>
                    <select name="lead_stage" id="lead_stage" class="form-control">
                        <?php foreach ($stages as $sg): ?>
                            <option value="<?= esc($sg) ?>" <?= old('lead_stage', $lead['lead_stage']) === $sg ? 'selected' : '' ?>><?= esc($sg) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control">
                        <?php foreach ($priorities as $pr): ?>
                            <option value="<?= esc($pr) ?>" <?= old('priority', $lead['priority']) === $pr ? 'selected' : '' ?>><?= esc($pr) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="lead_source_id">Lead Source</label>
                    <select name="lead_source_id" id="lead_source_id" class="form-control">
                        <option value="">Direct</option>
                        <?php foreach ($sources as $src): ?>
                            <option value="<?= esc($src['id']) ?>" <?= old('lead_source_id', $lead['lead_source_id']) == $src['id'] ? 'selected' : '' ?>><?= esc($src['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="card" style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary btn-block" style="flex: 1; padding: 0.75rem;">
                    Update Lead
                </button>
                <a href="/leads/view/<?= esc($lead['id']) ?>" class="btn btn-secondary" style="padding: 0.75rem;">Cancel</a>
            </div>

        </div>

    </div>
</form>

<?= $this->endSection() ?>
