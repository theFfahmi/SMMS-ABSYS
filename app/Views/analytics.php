<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#2d6cdf,#7c3aed);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(45,108,223,.35);flex-shrink:0">
                    <i class="bi bi-bar-chart-line-fill" style="font-size:20px;color:#fff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                        Analytics & Performance
                    </h4>
                    <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                        <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;font-size:11px"></i>
                        Pantau metrik performa konten, jangkauan (*Reach*), dan *Engagement Rate*
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- STATS CARDS (DYNAMIC METRICS FROM DATABASE) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="cp-card p-3 d-flex flex-row align-items-center gap-3">
                <div class="cp-stat-icon blue" style="width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1" id="stat-reach">0</div>
                    <div class="text-muted small fw-medium">Total Reach (Jangkauan)</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cp-card p-3 d-flex flex-row align-items-center gap-3">
                <div class="cp-stat-icon orange" style="width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1" id="stat-impressions">0</div>
                    <div class="text-muted small fw-medium">Total Impressions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cp-card p-3 d-flex flex-row align-items-center gap-3">
                <div class="cp-stat-icon purple" style="width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-heart-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1" id="stat-engagement">0</div>
                    <div class="text-muted small fw-medium">Total Engagement</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cp-card p-3 d-flex flex-row align-items-center gap-3">
                <div class="cp-stat-icon green" style="width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <div class="fw-bold text-success fs-4 line-height-1" id="stat-engagement-rate">0%</div>
                    <div class="text-muted small fw-medium">Engagement Rate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- METRICS FORMULA BREAKDOWN -->
    <div class="cp-card p-3 mb-4 bg-light">
        <div class="row text-center g-3" style="font-size:13px">
            <div class="col-md-2 col-6 border-end">
                <div class="text-muted">Likes</div>
                <div class="fw-bold text-dark fs-6" id="br-likes">0</div>
            </div>
            <div class="col-md-2 col-6 border-end">
                <div class="text-muted">Comments</div>
                <div class="fw-bold text-dark fs-6" id="br-comments">0</div>
            </div>
            <div class="col-md-2 col-6 border-end">
                <div class="text-muted">Shares</div>
                <div class="fw-bold text-dark fs-6" id="br-shares">0</div>
            </div>
            <div class="col-md-2 col-6 border-end">
                <div class="text-muted">Saves</div>
                <div class="fw-bold text-dark fs-6" id="br-saves">0</div>
            </div>
            <div class="col-md-2 col-6 border-end">
                <div class="text-muted">Link Clicks</div>
                <div class="fw-bold text-dark fs-6" id="br-clicks">0</div>
            </div>
            <div class="col-md-2 col-6">
                <div class="text-muted">Followers Gained</div>
                <div class="fw-bold text-dark fs-6" id="br-followers">+0</div>
            </div>
        </div>
    </div>

    <div class="analytics-grid">
        <!-- CHART CONTAINER -->
        <div class="cp-card">
            <div class="cp-toolbar">
                <h6 class="fw-bold mb-0"><i class="bi bi-graph-up me-2 text-primary"></i> Pertumbuhan Engagement (14 Hari)</h6>
            </div>
            <div class="chart-container">
                <canvas id="analyticsChart"></canvas>
            </div>
        </div>

        <!-- PLATFORM STATS -->
        <div class="cp-card">
            <div class="cp-toolbar">
                <h6 class="fw-bold mb-0">Platform Teratas</h6>
            </div>
            <div class="platform-stats" id="platform-stats-list">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- TOP CONTENT -->
    <div class="cp-card">
        <div class="cp-toolbar">
            <h6 class="fw-bold mb-0"><i class="bi bi-trophy-fill me-2 text-warning"></i> Performa Konten Terpublikasi</h6>
        </div>
        <div class="top-content-list" id="top-content-list">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= base_url('assets/js/analytics.js') ?>"></script>
<?= $this->endSection() ?>
