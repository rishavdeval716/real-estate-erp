<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Real Estate ERP - Management Software') ?></title>
    
    <!-- Meta & CSRF -->
    <?= csrf_meta() ?>
    <meta name="description" content="Enterprise Real Estate Management Software & CRM Phase 1 Foundation">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Modern ERP Stylesheet -->
    <link rel="stylesheet" href="/assets/css/erp-style.css">

    <?= $this->renderSection('styles') ?>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Mobile Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Sidebar Component -->
        <?= $this->include('layouts/partials/sidebar') ?>

        <!-- Main Content Wrapper -->
        <div class="app-main">
            <!-- Top Navbar Component -->
            <?= $this->include('layouts/partials/header') ?>

            <!-- Page Content Body -->
            <main class="app-content">
                <!-- Alerts / Flash Messages -->
                <?= $this->include('layouts/partials/alerts') ?>

                <!-- Page Injected Content -->
                <?= $this->renderSection('content') ?>
            </main>

            <!-- Footer Component -->
            <?= $this->include('layouts/partials/footer') ?>
        </div>
    </div>

    <!-- Reusable Confirmation Modal -->
    <?= $this->include('layouts/partials/confirm_modal') ?>

    <!-- Master Scripts -->
    <script src="/assets/js/erp-app.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
