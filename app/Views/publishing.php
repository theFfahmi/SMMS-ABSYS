<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#16a34a,#2d6cdf);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(22,163,74,.35);flex-shrink:0">
                    <i class="bi bi-send-fill" style="font-size:20px;color:#fff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                        Publishing Schedule & Execution
                    </h4>
                    <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                        <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;font-size:11px"></i>
                        Atur tanggal & jam tayang publikasi serta rilis konten ke media sosial
                    </p>
                </div>
            </div>
        </div>
        <button type="button" class="cpb cpb-pri" style="background:#16a34a;border-color:#16a34a" onclick="openScheduleModal()">
            <i class="bi bi-calendar-plus"></i> Buat Jadwal Tayang
        </button>
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

    <!-- STATS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#eff6ff;color:#2d6cdf;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1" id="stat-approved">0</div>
                    <div class="text-muted small fw-medium">Siap Dijadwalkan (Approved)</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#ccfbf1;color:#0d9488;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1" id="stat-scheduled">0</div>
                    <div class="text-muted small fw-medium">Terjadwal (Scheduled)</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#ecfdf5;color:#16a34a;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-send-check-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1" id="stat-published">0</div>
                    <div class="text-muted small fw-medium">Sudah Rilis (Published)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCHEDULE TIMELINE & ACTION TABLE -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-event me-2 text-primary"></i> Jadwal Tayang Konten</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Judul Konten</th>
                        <th>Platform</th>
                        <th>Waktu Tayang</th>
                        <th>Catatan</th>
                        <th>Status</th>
                        <th>Aksi Manager</th>
                    </tr>
                </thead>
                <tbody id="schedule-list">
                    <!-- Rendered by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- NATIVE OVERLAY MODAL SCHEDULE CONTENT -->
<div class="cp-back" id="m-schedule">
    <div class="cp-modal" style="max-width:550px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt">Jadwalkan Publikasi Konten</div>
                <div class="cp-ms">Tetapkan waktu dan tanggal rilis konten</div>
            </div>
            <button class="cp-mcls" onclick="cls('m-schedule')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="formSchedule" action="" method="post">
            <div style="padding:20px">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Pilih Konten <span class="text-danger">*</span></label>
                    <select class="cp-sel" style="width:100%" name="content_id" required>
                        <option value="">-- Pilih Konten --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Waktu Tayang (Tanggal & Jam) <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="cp-inp" style="width:100%" name="scheduled_at" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Catatan Publikasi</label>
                    <textarea class="cp-inp" style="width:100%" name="notes" rows="2" placeholder="Catatan jam tayang prime time, dsb..."></textarea>
                </div>
            </div>
            <div style="padding:15px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1.5px solid var(--cp-border);background:#fafafa;border-radius:0 0 20px 20px">
                <button type="button" class="cpb cpb-out" onclick="cls('m-schedule')">Batal</button>
                <button type="submit" class="cpb cpb-pri" style="background:#16a34a;border-color:#16a34a"><i class="bi bi-check-lg me-1"></i> Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- CUSTOM CONFIRM PUBLISH OVERLAY MODAL -->
<div class="cp-back" id="m-confirm-publish">
    <div class="cp-modal" style="max-width:440px;text-align:center;padding:24px">
        <div style="width:58px;height:58px;background:#ecfdf5;color:#16a34a;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 16px">
            <i class="bi bi-send-check-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Rilis Publikasi Konten?</h5>
        <p class="text-muted small mb-4" id="pub-content-title">Publikasikan konten ini sekarang ke media sosial?</p>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" class="cpb cpb-out" onclick="cls('m-confirm-publish')">Batal</button>
            <button type="button" onclick="executePublish()" class="cpb cpb-pri" style="background:#16a34a;border-color:#16a34a;">
                <i class="bi bi-send-fill me-1"></i> Ya, Rilis Sekarang
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/publishing.js') ?>"></script>
<?= $this->endSection() ?>
