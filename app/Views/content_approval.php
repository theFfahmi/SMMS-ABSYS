<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#8b5cf6,#1d4ed8);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(139,92,246,.35);flex-shrink:0">
                    <i class="bi bi-patch-check-fill" style="font-size:20px;color:#fff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                        Review & Approval Workflow
                    </h4>
                    <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                        <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;font-size:11px"></i>
                        Tinjau dan beri persetujuan (*Approve*) atau catatan revisi pada konten garapan tim
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- STATS SUMMARY -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#f3e8ff;color:#8b5cf6;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1" id="count-waiting">0</div>
                    <div class="text-muted small fw-medium">Waiting Review</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#eff6ff;color:#1d4ed8;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1" id="count-approved">0</div>
                    <div class="text-muted small fw-medium">Approved</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#fef2f2;color:#ef4444;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1" id="count-revision">0</div>
                    <div class="text-muted small fw-medium">Dalam Revisi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- LIST ITEMS WAITING REVIEW -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-warning"></i> Konten Menunggu Persetujuan Reviewer (Waiting Review)</h5>
        <div id="waiting-list">
            <!-- Rendered by JS -->
        </div>
    </div>

    <!-- APPROVAL HISTORY LOG -->
    <div class="card border-0 shadow-sm rounded-4 p-3">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text me-2 text-primary"></i> Riwayat Review Konten</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Konten</th>
                        <th>Reviewer</th>
                        <th>Status</th>
                        <th>Catatan / Komentar</th>
                        <th>Waktu Review</th>
                    </tr>
                </thead>
                <tbody id="history-list">
                    <!-- Rendered by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- NATIVE OVERLAY MODAL REVISION -->
<div class="cp-back" id="m-revision">
    <div class="cp-modal" style="max-width:500px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt">Minta Revisi Konten</div>
                <div class="cp-ms" id="revContentTitle">Tuliskan catatan perbaikan untuk Creator</div>
            </div>
            <button class="cp-mcls" onclick="cls('m-revision')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="formRevision" action="" method="post">
            <div style="padding:20px">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Catatan Revisi <span class="text-danger">*</span></label>
                    <textarea class="cp-inp" style="width:100%" name="comment" rows="4" placeholder="Tuliskan catatan perbaikan (misal: ganti font, perjelas caption, perbaiki tata letak)..." required></textarea>
                </div>
            </div>
            <div style="padding:15px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1.5px solid var(--cp-border);background:#fafafa;border-radius:0 0 20px 20px">
                <button type="button" class="cpb cpb-out" onclick="cls('m-revision')">Batal</button>
                <button type="submit" class="cpb cpb-pri" style="background:#dc2626;border-color:#dc2626"><i class="bi bi-send me-1"></i> Kirim Catatan Revisi</button>
            </div>
        </form>
    </div>
</div>

<!-- CUSTOM CONFIRM APPROVE OVERLAY MODAL -->
<div class="cp-back" id="m-confirm-approve">
    <div class="cp-modal" style="max-width:440px;text-align:center;padding:24px">
        <div style="width:58px;height:58px;background:#ecfdf5;color:#16a34a;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 16px">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Setujui Konten Ini?</h5>
        <p class="text-muted small mb-4" id="app-content-title">Setujui konten ini untuk lanjut ke jadwal tayang?</p>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" class="cpb cpb-out" onclick="cls('m-confirm-approve')">Batal</button>
            <button type="button" onclick="executeApprove()" class="cpb cpb-pri" style="background:#16a34a;border-color:#16a34a;">
                <i class="bi bi-check-lg me-1"></i> Ya, Approve Konten
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/content_approval.js') ?>"></script>
<?= $this->endSection() ?>
