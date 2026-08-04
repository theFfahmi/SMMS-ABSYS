<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<style>
:root {
    --cp-bg: #f4f6fb;
    --cp-white: #fff;
    --cp-border: #e8edf5;
    --cp-text: #111827;
    --cp-muted: #6b7280;
    --cp-blue: #2d6cdf;
    --cp-sh: 0 2px 12px rgba(0, 0, 0, .06);
}

.cpb { padding: 9px 18px; border: none; border-radius: 9px; font-size: 13.5px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all .15s; text-decoration: none;}
.cpb-pri { background: var(--cp-blue); color: #fff; }
.cpb-pri:hover { opacity: .9; color: #fff;}
.cpb-dan { background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; }
.cpb-dan:hover { background: #fee2e2; }

.asset-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; padding: 20px; }
.asset-card { background: var(--cp-white); border-radius: 12px; border: 1.5px solid var(--cp-border); overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: transform 0.2s, box-shadow 0.2s; position: relative; }
.asset-card:hover { transform: translateY(-3px); box-shadow: 0 8px 16px rgba(0,0,0,0.08); }
.asset-thumb { width: 100%; height: 150px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.asset-thumb img { width: 100%; height: 100%; object-fit: cover; }
.asset-info { padding: 12px 15px; }
.asset-name { font-size: 13px; font-weight: 700; color: var(--cp-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
.asset-meta { font-size: 11px; color: var(--cp-muted); }
.asset-actions { position: absolute; top: 10px; right: 10px; opacity: 0; transition: opacity 0.2s; display: flex; gap: 5px; }
.asset-card:hover .asset-actions { opacity: 1; }
.asset-btn { width: 30px; height: 30px; border-radius: 8px; background: rgba(255,255,255,0.9); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--cp-text); box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-decoration: none; }
.asset-btn:hover { background: #fff; color: var(--cp-red); }

.empty-state { grid-column: 1/-1; text-align: center; padding: 60px 20px; color: var(--cp-muted); }
.empty-state i { font-size: 48px; color: #d1d5db; margin-bottom: 15px; display: block; }

/* Dropzone Styles */
.dz-container { background: var(--cp-white); border: 2px dashed #cbd5e1; border-radius: 16px; padding: 40px 20px; text-align: center; cursor: pointer; transition: all 0.2s; margin-bottom: 25px; }
.dz-container:hover, .dz-container.drag-active { border-color: var(--cp-blue); background: #eff6ff; }
.dz-main-icon { font-size: 48px; color: #94a3b8; margin-bottom: 12px; display: block; transition: color 0.2s; }
.dz-container:hover .dz-main-icon, .dz-container.drag-active .dz-main-icon { color: var(--cp-blue); }
.dz-title { font-size: 16px; font-weight: 700; color: var(--cp-text); margin-bottom: 5px; }
.dz-desc { font-size: 13px; color: var(--cp-muted); }
</style>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success mt-2"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mt-2"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#7c3aed,#2d6cdf);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(124,58,237,.35);flex-shrink:0">
                    <i class="bi bi-folder-fill" style="font-size:20px;color:#fff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                        Asset Library
                    </h4>
                    <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                        <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;font-size:11px"></i>
                        Kelola semua aset media dalam satu tempat
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- DROPZONE AREA -->
    <div class="dz-container" id="dropzone">
        <i class="bi bi-cloud-arrow-up-fill dz-main-icon"></i>
        <div class="dz-title">Seret & Lepas File ke Sini</div>
        <div class="dz-desc">Atau klik tombol di bawah untuk memilih file dari komputer Anda.<br>Mendukung gambar (JPG, PNG), video (MP4), dan dokumen (PDF, Word). Maksimal 50MB.</div>
        <div class="mt-3">
            <button type="button" class="cpb cpb-pri" id="btn-upload" style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border-radius:12px;font-size:14px;font-weight:700">
                <i class="bi bi-folder2-open" style="font-size:16px;display:inline-block;margin:0;color:#fff"></i> Browse Files
            </button>
            <input type="file" id="upload-input" style="display:none" accept="image/*,video/mp4,video/webm,.pdf,.doc,.docx,.xls,.xlsx,.txt">
        </div>
    </div>

    <!-- ASSET GRID -->
    <div class="cp-card mt-4" style="background:var(--cp-white); border-radius:16px; border:1.5px solid var(--cp-border); box-shadow:var(--cp-sh);">
        <div class="cp-card-header" style="padding:15px 20px; border-bottom:1.5px solid var(--cp-border); font-weight:700; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-grid-fill" style="color:var(--cp-blue)"></i> Koleksi Aset
        </div>
        <div class="asset-grid" id="asset-grid">
            <!-- Rendered via JS -->
        </div>
    </div>
</div>

<!-- MODAL: CONFIRM DELETE ASSET -->
<div class="cp-back" id="m-confirm-delete-asset">
    <div class="cp-modal" style="max-width:440px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt" style="color:var(--cp-red)"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Aset Media?</div>
            </div>
            <button type="button" class="cp-mcls" onclick="document.getElementById('m-confirm-delete-asset').style.display='none'"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb" style="padding:20px;text-align:center">
            <div style="width:60px;height:60px;background:#fef2f2;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:15px">
                <i class="bi bi-trash-fill" style="font-size:28px;color:#dc2626"></i>
            </div>
            <p class="mb-0" style="font-size:14px;color:#374151;line-height:1.5">Apakah Anda yakin ingin menghapus aset media <strong id="del-asset-name">-</strong>?</p>
            <small class="text-muted" style="font-size:12px">Data aset yang dihapus tidak dapat dikembalikan.</small>
        </div>
        <div class="cp-mf" style="padding:15px 20px;display:flex;justify-content:flex-end;gap:10px;background:#fafafa;border-top:1px solid var(--cp-border)">
            <button type="button" class="cpb cpb-out" onclick="document.getElementById('m-confirm-delete-asset').style.display='none'">Batal</button>
            <button type="button" class="cpb cpb-pri" onclick="executeDeleteAsset()" style="background:#dc2626;box-shadow:0 4px 12px rgba(220,38,38,0.25)"><i class="bi bi-trash"></i> Ya, Hapus Aset</button>
        </div>
    </div>
</div>

<div id="cp-toast"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/asset_library.js') ?>"></script>
<?= $this->endSection() ?>
