<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sign In - Real Estate ERP') ?></title>
    
    <!-- Meta & CSRF -->
    <?= csrf_meta() ?>
    <meta name="description" content="Sign In - Enterprise Real Estate Management Software">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/erp-style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 10%, #1e293b 0%, #0f172a 100%);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .auth-container::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, rgba(37, 99, 235, 0) 70%);
            top: -100px;
            right: -100px;
            pointer-events: none;
        }
        .auth-container::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0) 70%);
            bottom: -80px;
            left: -80px;
            pointer-events: none;
        }
        .auth-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 30px rgba(37, 99, 235, 0.1);
            width: 100%;
            max-width: 440px;
            padding: 2.75rem 2.25rem;
            color: #ffffff;
            position: relative;
            z-index: 10;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            margin-bottom: 1rem;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        }
        .auth-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
        }
        .auth-subtitle {
            font-size: 0.86rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }
        .auth-card .form-label {
            color: #cbd5e1;
        }
        .auth-card .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }
        .auth-card .form-control:focus {
            background: rgba(15, 23, 42, 0.9);
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }
        .auth-card .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }
        .demo-credentials-box {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-top: 1.75rem;
            font-size: 0.78rem;
            color: #94a3b8;
        }
        .demo-btn-row {
            display: flex;
            gap: 0.4rem;
            margin-top: 0.6rem;
            flex-wrap: wrap;
        }
        .demo-chip {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #60a5fa;
            padding: 0.25rem 0.55rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.74rem;
            font-weight: 600;
            transition: all 0.15s ease;
        }
        .demo-chip:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <?= $this->renderSection('content') ?>
    </div>
    <script src="/assets/js/erp-app.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
