<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                <path d="M9 22v-4h6v4"></path>
                <path d="M8 6h.01"></path>
                <path d="M16 6h.01"></path>
                <path d="M8 10h.01"></path>
                <path d="M16 10h.01"></path>
                <path d="M8 14h.01"></path>
                <path d="M16 14h.01"></path>
            </svg>
        </div>
        <h1 class="auth-title">Real Estate ERP</h1>
        <p class="auth-subtitle">Phase 1 Foundation - Secure Sign In</p>
    </div>

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="alert-close" style="color: #fca5a5;">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.4); color: #86efac;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <div><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="alert-close" style="color: #86efac;">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors') && is_array(session()->getFlashdata('errors'))): ?>
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5;">
            <div>
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <div>&bull; <?= esc($err) ?></div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="alert-close" style="color: #fca5a5;">&times;</button>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST" id="loginForm" autocomplete="on">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email" class="form-label required">Corporate Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="name@company.com" value="<?= esc(old('email')) ?>" required autofocus>
        </div>

        <div class="form-group">
            <label for="password" class="form-label required">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Sign In to Dashboard
        </button>
    </form>

    <div class="demo-credentials-box">
        <div style="font-weight: 700; color: #cbd5e1; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.35rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            Demo Accounts (Click to Autofill):
        </div>
        <div>Default Password: <strong>Password@123</strong></div>
        <div class="demo-btn-row">
            <button type="button" class="demo-chip" onclick="fillDemo('admin@realestate-erp.local')">Super Admin</button>
            <button type="button" class="demo-chip" onclick="fillDemo('admin.ops@realestate-erp.local')">Operations Admin</button>
            <button type="button" class="demo-chip" onclick="fillDemo('manager@realestate-erp.local')">Branch Manager</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function fillDemo(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'Password@123';
}
</script>
<?= $this->endSection() ?>
