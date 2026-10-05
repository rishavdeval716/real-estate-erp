<?php
$session = session();
?>
<header class="app-header">
    <div class="header-left">
        <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Navigation Sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        </button>

        <div class="header-branch-pill">
            <span class="branch-dot"></span>
            <span class="header-branch-text"><?= esc($session->get('branch_name') ?? 'Headquarters') ?></span>
        </div>
    </div>

    <div class="header-right">
        <div class="header-time" id="liveClock" title="Current Server/System Time"></div>

        <a href="/notifications" class="header-icon-btn" title="Notification Center" style="position: relative; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; color: var(--slate-600); text-decoration: none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </a>

        <div class="header-dropdown" id="userDropdown">
            <button type="button" class="header-user-btn" id="userDropdownBtn" aria-label="User profile menu">
                <div class="user-avatar-sm" style="width: 32px; height: 32px; font-size: 0.8rem;">
                    <?= strtoupper(substr(esc($session->get('user_name') ?? 'U'), 0, 1)) ?>
                </div>
                <span class="header-user-name" style="font-size: 0.85rem; font-weight: 600; color: var(--slate-700);">
                    <?= esc($session->get('user_name') ?? 'User') ?>
                </span>
                <svg class="header-user-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            <div class="header-dropdown-menu">
                <div style="padding: 0.5rem 0.85rem; border-bottom: 1px solid var(--slate-100);">
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--slate-900);"><?= esc($session->get('user_name')) ?></div>
                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($session->get('user_email')) ?></div>
                </div>

                <a href="/users/edit/<?= esc($session->get('user_id')) ?>" class="dropdown-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profile Settings
                </a>

                <?php if ($session->get('is_super_admin') || in_array('company.view', $session->get('permissions') ?? [])): ?>
                <a href="/company" class="dropdown-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/></svg>
                    Company Setup
                </a>
                <?php endif; ?>

                <div class="dropdown-divider"></div>

                <a href="/logout" class="dropdown-item text-danger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    Sign Out
                </a>
            </div>
        </div>
    </div>
</header>
