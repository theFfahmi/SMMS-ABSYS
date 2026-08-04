<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
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
                    <h3><?= $stats['total_content'] ?></h3>
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
                    <h3><?= $stats['content_ideas'] ?></h3>
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
                    <h3><?= $stats['draft'] ?></h3>
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
                    <h3><?= $stats['in_production'] ?></h3>
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
                    <h3><?= $stats['waiting_review'] ?></h3>
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
                    <h3><?= $stats['revision'] ?></h3>
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
                    <h3><?= $stats['approved'] ?></h3>
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
                    <h3><?= $stats['scheduled'] ?></h3>
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
                    <h3><?= $stats['published'] ?></h3>
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
<?= $this->endSection() ?>
