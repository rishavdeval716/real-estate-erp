<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leads">Leads</a> &rsaquo;
            <span>Intake</span>
        </div>
        <h1 class="page-title">Add New Lead</h1>
        <p class="page-subtitle">Register a prospective property buyer, assign sales rep, and log initial requirements.</p>
    </div>
    <div>
        <a href="/leads" class="btn btn-secondary">&larr; Back to Leads</a>
    </div>
</div>

<form method="post" action="/leads/store">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
        
        <!-- Left Column: Primary Intake -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Contact Details Card -->
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                    1. Contact Information
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control" required placeholder="e.g. Rahul" value="<?= old('first_name') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="e.g. Sharma" value="<?= old('last_name') ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="phone">Primary Phone <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" required placeholder="+91 98000 00000" value="<?= old('phone') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="alternate_phone">Alternate Phone</label>
                        <input type="text" name="alternate_phone" id="alternate_phone" class="form-control" placeholder="+91 22 2000 0000" value="<?= old('alternate_phone') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="client@example.com" value="<?= old('email') ?>">
                </div>
            </div>

            <!-- Property Requirement Card -->
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                    2. Requirements & Preferences
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="budget_min">Minimum Budget (₹)</label>
                        <input type="number" step="1000" name="budget_min" id="budget_min" class="form-control" placeholder="e.g. 5000000" value="<?= old('budget_min') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="budget_max">Maximum Budget (₹)</label>
                        <input type="number" step="1000" name="budget_max" id="budget_max" class="form-control" placeholder="e.g. 7500000" value="<?= old('budget_max') ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="property_type_id">Property Type</label>
                        <select name="property_type_id" id="property_type_id" class="form-control">
                            <option value="">Any Category</option>
                            <?php foreach ($propTypes as $pt): ?>
                                <option value="<?= esc($pt['id']) ?>" <?= old('property_type_id') == $pt['id'] ? 'selected' : '' ?>><?= esc($pt['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="preferred_location">Preferred Location / Locality</label>
                        <input type="text" name="preferred_location" id="preferred_location" class="form-control" placeholder="e.g. Bandra West, Worli, Whitefield" value="<?= old('preferred_location') ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="project_id">Target Project</label>
                        <select name="project_id" id="project_id" class="form-control">
                            <option value="">Specific Project (Optional)</option>
                            <?php foreach ($projects as $prj): ?>
                                <option value="<?= esc($prj['id']) ?>" <?= old('project_id') == $prj['id'] ? 'selected' : '' ?>><?= esc($prj['name']) ?> (<?= esc($prj['project_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="property_id">Target Property</label>
                        <select name="property_id" id="property_id" class="form-control">
                            <option value="">Specific Property (Optional)</option>
                            <?php foreach ($properties as $prop): ?>
                                <option value="<?= esc($prop['id']) ?>" <?= old('property_id') == $prop['id'] ? 'selected' : '' ?>><?= esc($prop['title']) ?> (₹<?= number_format((float)$prop['price']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="property_unit_id">Specific Unit (Optional)</label>
                    <select name="property_unit_id" id="property_unit_id" class="form-control">
                        <option value="">Select Available Unit</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= esc($u['id']) ?>" <?= old('property_unit_id') == $u['id'] ? 'selected' : '' ?>>Unit <?= esc($u['unit_number']) ?> - <?= esc($u['flat_type']) ?> (₹<?= number_format((float)$u['unit_price']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="remarks">Requirement Notes & Remarks</label>
                    <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Specific client specifications, unit orientation, floor preferences..."><?= old('remarks') ?></textarea>
                </div>
            </div>

            <!-- Demographic / Prospect Card -->
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                    3. Prospect Demographic Profile
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="occupation">Occupation / Company</label>
                        <input type="text" name="occupation" id="occupation" class="form-control" placeholder="e.g. IT Director, Architect, Business Owner" value="<?= old('occupation') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="preferred_contact_method">Preferred Contact Mode</label>
                        <select name="preferred_contact_method" id="preferred_contact_method" class="form-control">
                            <option value="Phone" <?= old('preferred_contact_method') === 'Phone' ? 'selected' : '' ?>>Phone Call</option>
                            <option value="WhatsApp" <?= old('preferred_contact_method') === 'WhatsApp' ? 'selected' : '' ?>>WhatsApp</option>
                            <option value="Email" <?= old('preferred_contact_method') === 'Email' ? 'selected' : '' ?>>Email</option>
                            <option value="In-Person" <?= old('preferred_contact_method') === 'In-Person' ? 'selected' : '' ?>>In-Person</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="address">Postal Address</label>
                    <textarea name="address" id="address" class="form-control" rows="2" placeholder="Street, Flat/House No., Complex..."><?= old('address') ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="city">City</label>
                        <input type="text" name="city" id="city" class="form-control" placeholder="e.g. Mumbai" value="<?= old('city') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="state">State</label>
                        <input type="text" name="state" id="state" class="form-control" placeholder="e.g. Maharashtra" value="<?= old('state') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="pincode">Pincode</label>
                        <input type="text" name="pincode" id="pincode" class="form-control" placeholder="e.g. 400050" value="<?= old('pincode') ?>">
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Qualification, Source & Assignment -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Lead Source & Assignment Card -->
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                    Attribution & Routing
                </h3>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="lead_source_id">Lead Source</label>
                    <select name="lead_source_id" id="lead_source_id" class="form-control">
                        <option value="">Direct / Unspecified</option>
                        <?php foreach ($sources as $src): ?>
                            <option value="<?= esc($src['id']) ?>" <?= old('lead_source_id') == $src['id'] ? 'selected' : '' ?>><?= esc($src['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">Sales Executive Assignment</label>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; cursor: pointer;">
                            <input type="radio" name="assign_option" value="manual" checked onchange="document.getElementById('manual-assign-select').style.display='block'">
                            Assign to Specific Executive
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; cursor: pointer;">
                            <input type="radio" name="assign_option" value="round_robin" onchange="document.getElementById('manual-assign-select').style.display='none'">
                            Auto-Assign via Round-Robin
                        </label>
                    </div>

                    <div id="manual-assign-select">
                        <select name="assigned_user_id" id="assigned_user_id" class="form-control">
                            <option value="">Select Executive</option>
                            <?php foreach ($executives as $ex): ?>
                                <option value="<?= esc($ex['id']) ?>" <?= old('assigned_user_id') == $ex['id'] ? 'selected' : '' ?>><?= esc($ex['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="priority">Lead Priority <span style="color: var(--danger);">*</span></label>
                    <select name="priority" id="priority" class="form-control" required>
                        <?php foreach ($priorities as $pr): ?>
                            <option value="<?= esc($pr) ?>" <?= old('priority', 'Medium') === $pr ? 'selected' : '' ?>><?= esc($pr) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Qualification Criteria Card -->
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                    Qualification Criteria
                </h3>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="purchase_purpose">Purchase Purpose</label>
                    <select name="purchase_purpose" id="purchase_purpose" class="form-control">
                        <option value="End Use / Primary Residence" <?= old('purchase_purpose') === 'End Use / Primary Residence' ? 'selected' : '' ?>>End Use / Primary Residence</option>
                        <option value="Investment / Rental Yield" <?= old('purchase_purpose') === 'Investment / Rental Yield' ? 'selected' : '' ?>>Investment / Rental Yield</option>
                        <option value="Weekend / Vacation Home" <?= old('purchase_purpose') === 'Weekend / Vacation Home' ? 'selected' : '' ?>>Weekend / Vacation Home</option>
                        <option value="Commercial / Office" <?= old('purchase_purpose') === 'Commercial / Office' ? 'selected' : '' ?>>Commercial / Office</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="purchase_timeline">Expected Timeline</label>
                    <select name="purchase_timeline" id="purchase_timeline" class="form-control">
                        <option value="Immediate (< 1 Month)" <?= old('purchase_timeline') === 'Immediate (< 1 Month)' ? 'selected' : '' ?>>Immediate (< 1 Month)</option>
                        <option value="1 - 3 Months" <?= old('purchase_timeline') === '1 - 3 Months' ? 'selected' : '' ?>>1 - 3 Months</option>
                        <option value="3 - 6 Months" <?= old('purchase_timeline') === '3 - 6 Months' ? 'selected' : '' ?>>3 - 6 Months</option>
                        <option value="Exploring / 6+ Months" <?= old('purchase_timeline') === 'Exploring / 6+ Months' ? 'selected' : '' ?>>Exploring / 6+ Months</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="financing_required">Bank Loan Required?</label>
                    <select name="financing_required" id="financing_required" class="form-control">
                        <option value="Undecided" <?= old('financing_required') === 'Undecided' ? 'selected' : '' ?>>Undecided</option>
                        <option value="No" <?= old('financing_required') === 'No' ? 'selected' : '' ?>>No (Self-Funded / Cash)</option>
                        <option value="Yes" <?= old('financing_required') === 'Yes' ? 'selected' : '' ?>>Yes (Home Loan / Mortgage)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="lead_status">Initial Lead Status</label>
                    <select name="lead_status" id="lead_status" class="form-control">
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?= esc($st) ?>" <?= old('lead_status', 'New') === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="card" style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary btn-block" style="flex: 1; padding: 0.75rem;">
                    Register & Create Lead
                </button>
                <a href="/leads" class="btn btn-secondary" style="padding: 0.75rem;">Cancel</a>
            </div>

        </div>

    </div>
</form>

<?= $this->endSection() ?>
