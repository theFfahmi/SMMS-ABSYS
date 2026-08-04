<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-header" content="<?= csrf_header() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $title ?? 'ABSYS SMMS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/common.css') ?>">
    <?php if (isset($css)): ?>
        <?php foreach ($css as $cssFile): ?>
            <link rel="stylesheet" href="<?= base_url('assets/css/' . $cssFile . '.css') ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('styles') ?>
</head>
<body>

<?php 
    $userRole = session()->get('role') ?? 'admin';
    $userName = session()->get('full_name') ?? 'Guest';
    
    // Role label mapping
    $roleLabels = [
        'admin' => 'Admin (Superuser)',
        'social_media_manager' => 'Social Media Manager',
        'content_creator' => 'Content Creator',
        'designer' => 'Graphic Designer',
        'reviewer' => 'Content Reviewer'
    ];
    $displayRole = $roleLabels[$userRole] ?? ucfirst($userRole);
?>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-layers-fill" style="color:#2d6cdf"></i>
        <div>ABSYS <span>SMMS</span></div>
    </div>
    
    <div class="user-role-badge px-3 py-2 mx-3 mb-3" style="background: rgba(45, 108, 223, 0.1); border-radius: 10px; border: 1px solid rgba(45, 108, 223, 0.2);">
        <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase;">Role Login</div>
        <div style="font-size: 13px; color: #2d6cdf; font-weight: 700;" title="<?= esc($userName) ?>">
            <i class="bi bi-person-badge-fill me-1"></i> <?= esc($displayRole) ?>
        </div>
    </div>

    <div class="sidebar-menu">
        <!-- 1. Executive & Overview -->
        <a href="<?= base_url('dashboard') ?>" class="nav-item-link <?= $page === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <!-- 2. Content Strategy & Management -->
        <?php if (in_array($userRole, ['admin', 'social_media_manager', 'content_creator', 'reviewer'])): ?>
        <div class="menu-header">1. Strategy & Management</div>
        <a href="<?= base_url('content-plan') ?>" class="nav-item-link <?= $page === 'content-plan' ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i> Content Plan & Calendar
        </a>
        <?php endif; ?>
        
        <?php if (in_array($userRole, ['admin', 'content_creator'])): ?>
        <a href="<?= base_url('content-ideas') ?>" class="nav-item-link <?= $page === 'content-ideas' ? 'active' : '' ?>">
            <i class="bi bi-lightbulb-fill"></i> Content Ideas
        </a>
        <?php endif; ?>

        <?php if (in_array($userRole, ['admin', 'social_media_manager'])): ?>
        <a href="<?= base_url('campaigns') ?>" class="nav-item-link <?= $page === 'campaigns' ? 'active' : '' ?>">
            <i class="bi bi-megaphone-fill"></i> Campaigns
        </a>
        <?php endif; ?>

        <!-- 3. Content Production & Creative -->
        <?php if (in_array($userRole, ['admin', 'content_creator', 'designer'])): ?>
        <div class="menu-header">2. Production & Creative</div>
        <a href="<?= base_url('content-brief') ?>" class="nav-item-link <?= $page === 'content-brief' ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text-fill"></i> Content Brief
        </a>
        <a href="<?= base_url('asset-library') ?>" class="nav-item-link <?= $page === 'asset-library' ? 'active' : '' ?>">
            <i class="bi bi-folder-fill"></i> Asset Library
        </a>
        <?php endif; ?>

        <!-- 4. Review & Workflow Approval -->
        <?php if (in_array($userRole, ['admin', 'social_media_manager', 'reviewer'])): ?>
        <div class="menu-header">3. Review & Approval</div>
        <a href="<?= base_url('content-approval') ?>" class="nav-item-link <?= $page === 'content-approval' ? 'active' : '' ?>">
            <i class="bi bi-patch-check-fill"></i> Approval & Revision
        </a>
        <?php endif; ?>

        <!-- 5. Publishing & Engagement -->
        <?php if (in_array($userRole, ['admin', 'social_media_manager'])): ?>
        <div class="menu-header">4. Publishing & Engagement</div>
        <a href="<?= base_url('publishing') ?>" class="nav-item-link <?= $page === 'publishing' ? 'active' : '' ?>">
            <i class="bi bi-send-fill"></i> Publishing Schedule
        </a>
        <a href="<?= base_url('social-inbox') ?>" class="nav-item-link <?= $page === 'social-inbox' ? 'active' : '' ?>">
            <i class="bi bi-chat-dots-fill"></i> Social Inbox
        </a>
        <?php endif; ?>

        <!-- 6. System & Performance -->
        <div class="menu-header">5. System & Performance</div>
        <?php if (in_array($userRole, ['admin', 'social_media_manager'])): ?>
        <a href="<?= base_url('analytics') ?>" class="nav-item-link <?= $page === 'analytics' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-line-fill"></i> Analytics
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'admin'): ?>
        <a href="<?= base_url('user-management') ?>" class="nav-item-link <?= $page === 'user-management' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> User Management
        </a>
        <?php endif; ?>
        
        <div class="menu-header" style="margin-top:15px">Account</div>
        <a href="<?= base_url('auth/logout') ?>" class="nav-item-link">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>

<div class="main-content">
    <?= $this->renderSection('content') ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
