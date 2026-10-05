<?php
$session = session();
?>

<?php if ($session->getFlashdata('success')): ?>
<div class="alert alert-success">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <div>
        <strong>Success:</strong> <?= esc($session->getFlashdata('success')) ?>
    </div>
    <button type="button" class="alert-close" aria-label="Close">&times;</button>
</div>
<?php endif; ?>

<?php if ($session->getFlashdata('error')): ?>
<div class="alert alert-danger">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        <strong>Error:</strong> <?= esc($session->getFlashdata('error')) ?>
    </div>
    <button type="button" class="alert-close" aria-label="Close">&times;</button>
</div>
<?php endif; ?>

<?php if ($session->getFlashdata('errors') && is_array($session->getFlashdata('errors'))): ?>
<div class="alert alert-danger">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        <strong>Please correct the following errors:</strong>
        <ul style="margin-top: 0.35rem; padding-left: 1.25rem;">
            <?php foreach ($session->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <button type="button" class="alert-close" aria-label="Close">&times;</button>
</div>
<?php endif; ?>

<?php if ($session->getFlashdata('info')): ?>
<div class="alert alert-info">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
    <div><?= esc($session->getFlashdata('info')) ?></div>
    <button type="button" class="alert-close" aria-label="Close">&times;</button>
</div>
<?php endif; ?>
