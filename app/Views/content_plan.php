<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#2d6cdf,#7c3aed);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(45,108,223,.35);flex-shrink:0">
                    <i class="bi bi-calendar3" style="font-size:20px;color:#fff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                        Content Plan & Calendar
                    </h4>
                    <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                        <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;font-size:11px"></i>
                        Perencanaan jadwal tayang & kalender konten media sosial
                    </p>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="cpb cpb-pri" onclick="openAdd()">
                <i class="bi bi-plus-lg"></i> Tambah Konten Baru
            </button>
        </div>
    </div>

    <!-- STATS -->
    <div class="cp-stats">
        <div class="cp-stat">
            <div class="cp-stat-icon blue"><i class="bi bi-collection-fill"></i></div>
            <div>
                <div class="cp-stat-val" id="s-total">0</div>
                <div class="cp-stat-lbl">Total Konten</div>
            </div>
        </div>
        <div class="cp-stat">
            <div class="cp-stat-icon orange"><i class="bi bi-pencil-square"></i></div>
            <div>
                <div class="cp-stat-val" id="s-draft">0</div>
                <div class="cp-stat-lbl">Draft / Idea</div>
            </div>
        </div>
        <div class="cp-stat">
            <div class="cp-stat-icon purple"><i class="bi bi-patch-check-fill"></i></div>
            <div>
                <div class="cp-stat-val" id="s-acc">0</div>
                <div class="cp-stat-lbl">Review / Approved</div>
            </div>
        </div>
        <div class="cp-stat">
            <div class="cp-stat-icon green"><i class="bi bi-send-check-fill"></i></div>
            <div>
                <div class="cp-stat-val" id="s-pub">0</div>
                <div class="cp-stat-lbl">Published / Scheduled</div>
            </div>
        </div>
    </div>

    <!-- MAIN CALENDAR CARD -->
    <div class="cp-card">
        <!-- TOOLBAR ROW 1: NAVIGATION & VIEW TOGGLES -->
        <div class="cp-toolbar" style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--cp-border);background:#fff">
            <div class="cp-cal-nav" style="display:flex;align-items:center;gap:12px">
                <button class="cp-navbtn" id="cp-prev" style="width:34px;height:34px;border:1.5px solid var(--cp-border);background:#fff;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="bi bi-chevron-left"></i></button>
                <span class="cp-mlbl" id="cp-mlbl" style="font-size:16px;font-weight:800;color:var(--cp-text);min-width:140px;text-align:center">Juli 2026</span>
                <button class="cp-navbtn" id="cp-next" style="width:34px;height:34px;border:1.5px solid var(--cp-border);background:#fff;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="bi bi-chevron-right"></i></button>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select class="cp-sel" id="cp-msel">
                    <option value="0">Januari</option>
                    <option value="1">Februari</option>
                    <option value="2">Maret</option>
                    <option value="3">April</option>
                    <option value="4">Mei</option>
                    <option value="5">Juni</option>
                    <option value="6">Juli</option>
                    <option value="7">Agustus</option>
                    <option value="8">September</option>
                    <option value="9">Oktober</option>
                    <option value="10">November</option>
                    <option value="11">Desember</option>
                </select>
                <select class="cp-sel" id="cp-ysel"></select>
                <button class="cpb cpb-out btn-sm" id="cp-today"><i class="bi bi-calendar-check me-1"></i> Hari Ini</button>
                
                <div class="cp-views" style="display:flex;gap:4px;background:#f1f5f9;padding:4px;border-radius:10px">
                    <button class="cp-vbtn active" id="tog-cal"><i class="bi bi-calendar3"></i> Kalender</button>
                    <button class="cp-vbtn" id="tog-lst"><i class="bi bi-list-task"></i> List</button>
                </div>
            </div>
        </div>

        <!-- TOOLBAR ROW 2: KELOLA MASTER DATA -->
        <div style="padding:10px 20px;border-bottom:1px solid var(--cp-border);background:#fafafa;display:flex;align-items:center;gap:12px;font-size:13px;flex-wrap:wrap">
            <span class="fw-bold text-dark">Kelola:</span>
            <button type="button" class="btn btn-sm btn-white border rounded-3 fw-semibold px-3" onclick="document.getElementById('m-manage-platform').style.display='flex'"><i class="bi bi-display me-1 text-primary"></i> Platform</button>
            <button type="button" class="btn btn-sm btn-white border rounded-3 fw-semibold px-3" onclick="document.getElementById('m-manage-type').style.display='flex'"><i class="bi bi-tag me-1 text-danger"></i> Content Type</button>
            <button type="button" class="btn btn-sm btn-white border rounded-3 fw-semibold px-3" onclick="document.getElementById('m-manage-pillar').style.display='flex'"><i class="bi bi-box me-1 text-success"></i> Content Pillar</button>
        </div>

        <!-- TOOLBAR ROW 3: LEGEND & HINT -->
        <div style="padding:10px 20px;border-bottom:1px solid var(--cp-border);background:#fff;display:flex;align-items:center;justify-content:space-between;font-size:12px;flex-wrap:wrap;gap:10px">
            <div class="d-flex align-items-center gap-3 fw-medium">
                <span><i class="bi bi-circle-fill text-success me-1" style="font-size:9px"></i> Published</span>
                <span><i class="bi bi-circle-fill text-primary me-1" style="font-size:9px"></i> Acc</span>
                <span><i class="bi bi-circle-fill text-warning me-1" style="font-size:9px"></i> Draft</span>
                <span class="text-muted"><i class="bi bi-lock me-1"></i> Tanggal lampau (read-only)</span>
            </div>
            <div class="text-muted fw-semibold">
                <i class="bi bi-hand-index-thumb me-1"></i> Klik tanggal untuk lihat / tambah konten
            </div>
        </div>

        <!-- CALENDAR VIEW -->
        <div id="v-cal">
            <div class="cp-grid-hdr">
                <div>MIN</div>
                <div>SEN</div>
                <div>SEL</div>
                <div>RAB</div>
                <div>KAM</div>
                <div>JUM</div>
                <div style="color:var(--cp-red)">SAB</div>
            </div>
            <div class="cp-grid-bdy" id="cal-body"></div>
        </div>

        <!-- LIST VIEW -->
        <div id="v-lst" style="display:none;padding:16px">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Judul Konten & Campaign</th>
                            <th>Platform</th>
                            <th>Jenis</th>
                            <th>Tanggal Rencana</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="lst-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: KELOLA PLATFORM -->
<div class="cp-back" id="m-manage-platform">
    <div class="cp-modal" style="max-width:600px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt"><i class="bi bi-display text-primary me-2"></i>Kelola Platform Social Media</div>
                <div class="cp-ms">Daftar platform media sosial aktif dan tambah platform baru</div>
            </div>
            <button class="cp-mcls" onclick="cls('m-manage-platform')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb" style="padding:20px">
            <h6 class="fw-bold mb-2">Platform Aktif</h6>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php foreach ($platforms as $p): ?>
                    <span class="badge bg-light text-dark border p-2" style="font-size:13px">
                        <i class="bi <?= $p['icon'] ?? 'bi-globe' ?> me-1" style="color:<?= $p['color'] ?? '#2d6cdf' ?>"></i> <?= esc($p['name']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <hr>
            <h6 class="fw-bold mb-2">Tambah Platform Baru</h6>
            <form action="<?= base_url('content-plan/store-platform') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Platform <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Threads, Pinterest" required>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-semibold">Ikon (Bootstrap Icon)</label>
                        <input type="text" name="icon" class="form-control" placeholder="bi-chat-quote" value="bi-globe">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-semibold">Warna Theme</label>
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#2d6cdf">
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="cpb cpb-pri btn-sm"><i class="bi bi-plus-lg"></i> Tambah Platform</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: KELOLA CONTENT TYPE -->
<div class="cp-back" id="m-manage-type">
    <div class="cp-modal" style="max-width:600px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt"><i class="bi bi-tag text-danger me-2"></i>Kelola Content Type</div>
                <div class="cp-ms">Kategori jenis konten (Pendidikan, Promosi, Hiburan, dll)</div>
            </div>
            <button class="cp-mcls" onclick="cls('m-manage-type')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb" style="padding:20px">
            <h6 class="fw-bold mb-2">Content Type Aktif</h6>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php foreach ($contentTypes as $t): ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle p-2" style="font-size:13px">
                        🏷️ <?= esc($t['name']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <hr>
            <h6 class="fw-bold mb-2">Tambah Content Type Baru</h6>
            <form action="<?= base_url('content-plan/store-type') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Content Type <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Behind The Scene, Soft Selling" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Penjelasan singkat mengenai tipe konten ini"></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="cpb cpb-pri btn-sm"><i class="bi bi-plus-lg"></i> Tambah Content Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: KELOLA CONTENT PILLAR -->
<div class="cp-back" id="m-manage-pillar">
    <div class="cp-modal" style="max-width:600px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt"><i class="bi bi-box text-success me-2"></i>Kelola Content Pillar</div>
                <div class="cp-ms">Pilar utama topik strategi pemasaran media sosial</div>
            </div>
            <button class="cp-mcls" onclick="cls('m-manage-pillar')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb" style="padding:20px">
            <h6 class="fw-bold mb-2">Content Pillar Aktif</h6>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php foreach ($contentPillars as $cp): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle p-2" style="font-size:13px">
                        🧊 <?= esc($cp['name']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <hr>
            <h6 class="fw-bold mb-2">Tambah Content Pillar Baru</h6>
            <form action="<?= base_url('content-plan/store-pillar') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Content Pillar <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Product Education, Customer Story" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Penjelasan mengenai pilar topik ini"></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="cpb cpb-pri btn-sm"><i class="bi bi-plus-lg"></i> Tambah Content Pillar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: DETAIL KONTEN -->
<div class="cp-back" id="m-detail">
    <div class="cp-modal" style="max-width:520px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt" id="m-det-title">Detail Konten</div>
                <div class="cp-ms" id="m-det-sub"></div>
            </div>
            <button class="cp-mcls" onclick="cls('m-detail')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb" id="m-det-body" style="padding:20px"></div>
        <div class="cp-mf" style="padding:15px 20px;display:flex;justify-content:flex-end;border-top:1.5px solid var(--cp-border);background:#fafafa;border-radius:0 0 20px 20px">
            <button class="cpb cpb-out" onclick="cls('m-detail')">Tutup</button>
        </div>
    </div>
</div>

<!-- MODAL: FORM ADD/EDIT KONTEN -->
<div class="cp-back" id="m-form">
    <div class="cp-modal" style="max-width:620px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt" id="mf-title">Tambah Konten Baru</div>
                <div class="cp-ms" id="mf-sub">Jadwalkan rencana tayang konten baru pada kalender</div>
            </div>
            <button class="cp-mcls" onclick="cls('m-form')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb" style="padding:20px">
            <input type="hidden" id="f-id">
            <div class="cp-row full mb-3">
                <div class="cp-field">
                    <label class="form-label fw-semibold small">Judul Konten <span style="color:var(--cp-red)">*</span></label>
                    <input type="text" class="cp-inp" id="f-judul" placeholder="Masukkan judul konten...">
                </div>
            </div>
            <div class="cp-row full mb-3">
                <div class="cp-field">
                    <label class="form-label fw-semibold small">Hubungkan ke Campaign <span class="text-muted fw-normal">(Opsional)</span></label>
                    <select class="cp-sel" id="f-campaign" style="width:100%">
                        <option value="">-- Tanpa Campaign (Non-Campaign) --</option>
                    </select>
                </div>
            </div>
            <div class="cp-row full mb-3">
                <div class="cp-field">
                    <label class="form-label fw-semibold small">Deskripsi / Caption</label>
                    <textarea class="cp-inp" id="f-desk" rows="3" placeholder="Deskripsi atau caption konten (opsional)"></textarea>
                </div>
            </div>
            <div class="cp-row mb-3" style="display:flex;gap:12px">
                <div class="cp-field" style="flex:1">
                    <label class="form-label fw-semibold small">Tanggal Rencana Tayang <span style="color:var(--cp-red)">*</span></label>
                    <input type="date" class="cp-inp" id="f-tgl" style="width:100%">
                </div>
                <div class="cp-field" style="flex:1">
                    <label class="form-label fw-semibold small">Format Konten</label>
                    <select class="cp-sel" id="f-jenis" style="width:100%">
                        <option value="">Pilih format konten</option>
                    </select>
                </div>
            </div>
            <div class="cp-row mb-3" style="display:flex;gap:12px">
                <div class="cp-field" style="flex:1">
                    <label class="form-label fw-semibold small">Content Type</label>
                    <select class="cp-sel" id="f-type" style="width:100%">
                        <option value="">Pilih content type</option>
                    </select>
                </div>
                <div class="cp-field" style="flex:1">
                    <label class="form-label fw-semibold small">Platform Media Sosial</label>
                    <div class="cp-plat-wrap" id="f-plat-wrap" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:4px">
                        <!-- Checkbox Platform -->
                    </div>
                </div>
            </div>
        </div>
        <div class="cp-mf" style="padding:15px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1.5px solid var(--cp-border);background:#fafafa;border-radius:0 0 20px 20px">
            <button type="button" class="cpb cpb-out" onclick="cls('m-form')">Batal</button>
            <button type="button" class="cpb cpb-pri" id="f-save">
                <i class="bi bi-check-lg"></i> Simpan Konten
            </button>
        </div>
    </div>
</div>

<div id="cp-toast"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.appBaseUrl = '<?= base_url() ?>';
    // Variables from PHP
    const contentTypes = <?= json_encode($contentTypes) ?>;
    const contentFormats = <?= json_encode($contentFormats) ?>;
    const contentPillars = <?= json_encode($contentPillars) ?>;
    const platforms = <?= json_encode($platforms) ?>;
    const statuses = <?= json_encode($contentStatuses) ?>;
    const campaigns = <?= json_encode($campaigns) ?>;
    
    // Populate dropdowns
    document.addEventListener('DOMContentLoaded', () => {
        const typeSel = document.getElementById('f-type');
        contentTypes.forEach(t => {
            typeSel.innerHTML += `<option value="${t.id}">${t.name}</option>`;
        });
        
        const formatSel = document.getElementById('f-jenis');
        contentFormats.forEach(f => {
            formatSel.innerHTML += `<option value="${f.id}">${f.name}</option>`;
        });

        const campSel = document.getElementById('f-campaign');
        campaigns.forEach(c => {
            campSel.innerHTML += `<option value="${c.id}">📢 ${c.name}</option>`;
        });
        
        const platWrap = document.getElementById('f-plat-wrap');
        platforms.forEach(p => {
            platWrap.innerHTML += `
                <label style="display:flex;align-items:center;gap:6px;font-size:13px">
                    <input type="checkbox" name="platforms[]" value="${p.id}">
                    ${p.name}
                </label>
            `;
        });
    });
</script>
<script src="<?= base_url('assets/js/content_plan.js') ?>"></script>
<?= $this->endSection() ?>
