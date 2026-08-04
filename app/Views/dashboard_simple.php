<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ABSYS SMMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/common.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-layers-fill" style="color:#2d6cdf"></i>
        <div>ABSYS <span>SMMS</span></div>
    </div>
    <div class="sidebar-menu">
        <a href="<?= base_url('dashboard') ?>" class="nav-item-link active">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <div class="menu-header">Content Management</div>
        <a href="<?= base_url('content-plan') ?>" class="nav-item-link">
            <i class="bi bi-calendar3"></i> Content Plan
        </a>
        <a href="<?= base_url('content-ideas') ?>" class="nav-item-link">
            <i class="bi bi-lightbulb-fill"></i> Content Ideas
        </a>
        <a href="<?= base_url('campaigns') ?>" class="nav-item-link">
            <i class="bi bi-megaphone-fill"></i> Campaigns
        </a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="page-title">Dashboard</h2>
                <p class="text-muted">Ringkasan aktivitas sistem Social Media Management</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Total Content</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Content Ideas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Draft</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-secondary">
                        <i class="bi bi-gear"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>In Production</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-orange">
                        <i class="bi bi-eye"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Waiting Review</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-danger">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Revision</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Approved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-purple">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Scheduled</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-teal">
                        <i class="bi bi-send"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Published</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Upcoming Content</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Belum ada data untuk ditampilkan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>
</body>
</html>
