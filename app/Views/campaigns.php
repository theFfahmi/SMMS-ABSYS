<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#ea580c,#dc2626);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(234,88,12,.35);flex-shrink:0">
                    <i class="bi bi-megaphone-fill" style="font-size:20px;color:#fff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                        Campaign Management
                    </h4>
                    <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                        <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;font-size:11px"></i>
                        Kelola dan pantau kampanye promosi & pemasaran
                    </p>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <button type="button" class="cpb cpb-primary" onclick="showAiCampaignModal()" style="background: linear-gradient(135deg, #7c3aed, #4f46e5); border:none; color: #fff;">
                <i class="bi bi-magic me-1"></i> AI Campaign Assistant
            </button>
            <button type="button" class="cpb cpb-pri" onclick="openAddCampaign()">
                <i class="bi bi-plus-lg"></i> Buat Campaign Baru
            </button>
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

    <!-- STATS -->
    <div class="row g-3 mb-4">
        <?php 
            $activeCount = count(array_filter($campaigns, fn($c) => $c['status'] === 'active'));
            $draftCount = count(array_filter($campaigns, fn($c) => $c['status'] === 'draft'));
            $completedCount = count(array_filter($campaigns, fn($c) => $c['status'] === 'completed'));
            $totalBudget = array_sum(array_column($campaigns, 'budget'));
        ?>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#fef2f2;color:#dc2626;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-flag-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1"><?= $activeCount ?></div>
                    <div class="text-muted small fw-medium">Active Campaigns</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#eff6ff;color:#2d6cdf;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1"><?= $draftCount ?></div>
                    <div class="text-muted small fw-medium">Draft Campaigns</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#ecfdf5;color:#16a34a;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-4 line-height-1"><?= $completedCount ?></div>
                    <div class="text-muted small fw-medium">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div style="width:48px;height:48px;background:#fff7ed;color:#ea580c;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-5 line-height-1">Rp <?= number_format($totalBudget, 0, ',', '.') ?></div>
                    <div class="text-muted small fw-medium">Total Anggaran</div>
                </div>
            </div>
        </div>
    </div>

    <!-- CAMPAIGNS GRID -->
    <div class="card border-0 shadow-sm rounded-4 p-3">
        <div class="row g-3">
            <?php if (empty($campaigns)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-megaphone fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada kampanye pemasaran. Klik <strong>Buat Campaign Baru</strong> di atas untuk memulai.
                </div>
            <?php else: ?>
                <?php foreach ($campaigns as $c): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border h-100 rounded-4 shadow-sm p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge <?= $c['status'] === 'active' ? 'bg-success' : ($c['status'] === 'completed' ? 'bg-secondary' : 'bg-warning text-dark') ?> text-uppercase px-2 py-1" style="font-size:11px">
                                    <?= $c['status'] ?>
                                </span>
                                <small class="text-muted fw-semibold">
                                    <i class="bi bi-calendar-range"></i> <?= $c['start_date'] ?>
                                </small>
                            </div>
                            <h5 class="fw-bold text-dark mb-1"><?= esc($c['name']) ?></h5>
                            <p class="text-muted small mb-3 flex-grow-1" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                                <?= esc($c['description'] ?: 'Tidak ada deskripsi.') ?>
                            </p>
                            
                            <div class="bg-light rounded-3 p-2 mb-3" style="font-size:12px">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Total Konten:</span>
                                    <span class="fw-bold text-primary"><?= $c['content_count'] ?> Konten</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Budget:</span>
                                    <span class="fw-bold text-dark">Rp <?= number_format((float)$c['budget'], 0, ',', '.') ?></span>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="<?= base_url('campaigns/detail/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary w-100 fw-semibold rounded-3">
                                    <i class="bi bi-eye-fill me-1"></i> Detail Konten
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger px-2 rounded-3" onclick="confirmDeleteCampaign('<?= base_url('campaigns/delete/' . $c['id']) ?>', '<?= esc($c['name'], 'js') ?>')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- NATIVE OVERLAY MODAL FORM CREATE CAMPAIGN -->
<div class="cp-back" id="m-add-campaign">
    <div class="cp-modal" style="max-width:550px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt">Buat Campaign Baru</div>
                <div class="cp-ms">Isi formulir untuk membuat kampanye promosi baru</div>
            </div>
            <button class="cp-mcls" onclick="cls('m-add-campaign')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="<?= base_url('campaigns/store') ?>" method="post">
            <div style="padding:20px">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Campaign <span class="text-danger">*</span></label>
                    <input type="text" class="cp-inp" style="width:100%" name="name" id="f-camp-name" placeholder="Contoh: Promo Ramadhan 2026" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Deskripsi</label>
                    <textarea class="cp-inp" style="width:100%" name="description" id="f-camp-desc" rows="2" placeholder="Penjelasan singkat campaign..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Tujuan (Objective)</label>
                    <input type="text" class="cp-inp" style="width:100%" name="objective" id="f-camp-obj" placeholder="Contoh: Meningkatkan konversi penjualan 20%">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Target Audience</label>
                    <input type="text" class="cp-inp" style="width:100%" name="target_audience" id="f-camp-aud" placeholder="Contoh: Pria & Wanita 18-35 tahun">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" class="cp-inp" style="width:100%" name="start_date" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Tanggal Selesai</label>
                        <input type="date" class="cp-inp" style="width:100%" name="end_date">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Anggaran (Rp)</label>
                        <input type="number" class="cp-inp" style="width:100%" name="budget" id="f-camp-budget" placeholder="10000000">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Status</label>
                        <select class="cp-sel" style="width:100%" name="status">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="padding:15px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1.5px solid var(--cp-border);background:#fafafa;border-radius:0 0 20px 20px">
                <button type="button" class="cpb cpb-out" onclick="cls('m-add-campaign')">Batal</button>
                <button type="submit" class="cpb cpb-pri"><i class="bi bi-check-lg me-1"></i> Simpan Campaign</button>
            </div>
        </form>
    </div>
</div>

<!-- CUSTOM CONFIRM DELETE OVERLAY MODAL -->
<div class="cp-back" id="m-confirm-delete">
    <div class="cp-modal" style="max-width:440px;text-align:center;padding:24px">
        <div style="width:58px;height:58px;background:#fef2f2;color:#dc2626;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 16px">
            <i class="bi bi-trash-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Hapus Campaign Ini?</h5>
        <p class="text-muted small mb-4" id="del-camp-name">Apakah Anda yakin ingin menghapus campaign ini?</p>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" class="cpb cpb-out" onclick="cls('m-confirm-delete')">Batal</button>
            <a id="btn-confirm-delete-action" href="#" class="cpb cpb-pri" style="background:#dc2626;border-color:#dc2626;text-decoration:none">
                <i class="bi bi-trash me-1"></i> Ya, Hapus Campaign
            </a>
        </div>
    </div>
</div>
<!-- AI CAMPAIGN MODAL -->
<div class="cp-back" id="m-ai-campaign">
    <div class="cp-modal" style="max-width:500px">
        <div class="cp-mh">
            <div class="cp-mt"><i class="bi bi-magic text-warning me-2"></i> AI Campaign Assistant</div>
            <button type="button" class="cp-mcls" onclick="cls('m-ai-campaign')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb" style="padding:20px;">
            <p style="font-size: 13px; color: var(--cp-muted); margin-bottom: 15px;">Punya ide kasar? Ceritakan topik atau tujuan kampanye Anda. AI akan merancangnya menjadi struktur kampanye yang terarah!</p>
            <div class="cp-field mb-3">
                <input type="text" class="cp-inp" id="ai-camp-prompt" style="width:100%" placeholder="Contoh: Promo akhir tahun untuk sepatu lari">
            </div>
            <div id="ai-camp-loading" style="display:none; text-align:center; padding: 20px 0;">
                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                <div style="font-size:12px; color:var(--cp-muted);">AI sedang menyusun masterplan kampanye Anda...</div>
            </div>
        </div>
        <div class="cp-mf" style="padding:15px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1.5px solid var(--cp-border);background:#fafafa;border-radius:0 0 20px 20px">
            <button type="button" class="cpb cpb-out" onclick="cls('m-ai-campaign')">Batal</button>
            <button type="button" class="cpb cpb-primary" id="btn-ai-camp-gen" onclick="generateAiCampaign()" style="background: linear-gradient(135deg, #7c3aed, #4f46e5); border:none; color:#fff;">
                <i class="bi bi-magic me-1"></i> Rancang Campaign
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/campaigns.js?v=' . time()) ?>"></script>
<script>
function openAddCampaign() {
    document.getElementById('m-add-campaign').style.display = 'flex';
}
function confirmDeleteCampaign(deleteUrl, campName) {
    document.getElementById('btn-confirm-delete-action').href = deleteUrl;
    document.getElementById('del-camp-name').innerText = 'Apakah Anda yakin ingin menghapus campaign "' + campName + '"? Data yang dihapus tidak dapat dikembalikan.';
    document.getElementById('m-confirm-delete').style.display = 'flex';
}
function cls(id) {
    document.getElementById(id).style.display = 'none';
}
</script>
<?= $this->endSection() ?>
