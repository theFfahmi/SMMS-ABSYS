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
.cpb-out { background: var(--cp-white); color: var(--cp-text); border: 1.5px solid var(--cp-border); }
.cpb-out:hover { background: var(--cp-bg); color: var(--cp-text);}
.cpb-sm { padding: 5px 10px; font-size: 11.5px; border-radius: 7px; }

.brief-container { display: grid; grid-template-columns: 350px 1fr; gap: 20px; margin-top: 15px; align-items: start; }
.cp-card { background: var(--cp-white); border-radius: 16px; border: 1.5px solid var(--cp-border); box-shadow: var(--cp-sh); overflow: hidden; }
.cp-card-header { padding: 15px 20px; border-bottom: 1.5px solid var(--cp-border); background: #fafafa; font-weight: 700; color: var(--cp-text); font-size: 15px; display: flex; justify-content: space-between; align-items: center; }
.cp-card-body { padding: 20px; }

.brief-list { max-height: 600px; overflow-y: auto; }
.brief-item { padding: 15px 20px; border-bottom: 1px solid var(--cp-border); cursor: pointer; transition: background .15s; }
.brief-item:hover { background: var(--cp-bg); }
.brief-item.active { background: #eff6ff; border-left: 4px solid var(--cp-blue); }
.brief-item-title { font-size: 14px; font-weight: 700; color: var(--cp-text); margin-bottom: 4px; }
.brief-item-date { font-size: 11px; color: var(--cp-muted); }

.brief-empty { text-align: center; padding: 40px 20px; color: var(--cp-muted); }
.brief-empty i { font-size: 40px; margin-bottom: 10px; display: block; color: #d1d5db; }

.detail-row { margin-bottom: 15px; }
.detail-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--cp-muted); margin-bottom: 4px; letter-spacing: 0.5px; }
.detail-val { font-size: 14px; color: var(--cp-text); line-height: 1.5; background: #f9fafb; padding: 12px; border-radius: 8px; border: 1px solid var(--cp-border); }

@media (max-width: 768px) {
    .brief-container { grid-template-columns: 1fr; }
}
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
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#2d6cdf,#16a34a);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(45,108,223,.35);flex-shrink:0">
                    <i class="bi bi-file-earmark-text-fill" style="font-size:20px;color:#fff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                        Content Brief
                    </h4>
                    <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                        <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;font-size:11px"></i>
                        Buat brief konten yang detail dan terstruktur
                    </p>
                </div>
            </div>
        </div>
        <button onclick="openBriefForm()" class="cpb cpb-pri">
            <i class="bi bi-plus-lg"></i> Buat Brief
        </button>
    </div>

    <div class="brief-container">
        <!-- Brief List -->
        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <i class="bi bi-list-ul"></i> Daftar Brief (<span id="brief-count">0</span>)
                </div>
            </div>
            <div class="brief-list" id="brief-list">
                <!-- List rendered by JS -->
            </div>
        </div>

        <!-- Brief Form/Detail -->
        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <i class="bi bi-file-text"></i> Detail Brief
                </div>
                <div id="detail-actions" style="display:none; gap:6px;">
                    <button id="btn-edit" class="cpb cpb-out cpb-sm"><i class="bi bi-pencil"></i> Edit</button>
                    <button id="btn-delete" class="cpb cpb-out cpb-sm" style="color:var(--cp-red);border-color:#fecaca"><i class="bi bi-trash"></i> Hapus</button>
                </div>
            </div>
            <div class="cp-card-body" id="brief-detail">
                <div class="brief-empty" id="empty-state">
                    <i class="bi bi-file-earmark"></i>
                    <h4>Pilih brief untuk melihat detail</h4>
                    <p>Klik salah satu brief dari daftar di samping</p>
                </div>
                <div id="detail-content" style="display:none;">
                    <h4 id="dt-title" style="font-weight:800; font-size:18px; margin-bottom:20px; color:var(--cp-text)"></h4>
                    
                    <div class="detail-row">
                        <div class="detail-label">Objective / Tujuan</div>
                        <div class="detail-val" id="dt-objective"></div>
                    </div>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px">
                        <div class="detail-row">
                            <div class="detail-label">Target Audience</div>
                            <div class="detail-val" id="dt-audience"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Key Message</div>
                            <div class="detail-val" id="dt-message"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Call to Action</div>
                            <div class="detail-val" id="dt-cta"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Tone of Voice</div>
                            <div class="detail-val" id="dt-tone"></div>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Reference URL</div>
                        <div class="detail-val" id="dt-url"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Notes</div>
                        <div class="detail-val" id="dt-notes"></div>
                    </div>

                    <!-- AI CAPTION GENERATOR SECTION -->
                    <div style="margin-top:25px; padding:20px; background:#f8fafc; border-radius:12px; border:1px dashed var(--cp-border);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                            <h6 style="margin:0; font-weight:800; color:var(--cp-text);"><i class="bi bi-robot text-primary me-2"></i>AI Copywriter</h6>
                            <button class="cpb cpb-primary cpb-sm" id="btn-ai-caption" onclick="generateAiCaption()" style="background: linear-gradient(135deg, #7c3aed, #4f46e5); border:none;"><i class="bi bi-magic"></i> Generate Draft</button>
                        </div>
                        <div id="ai-caption-result" style="display:none;">
                            <div class="detail-label">Caption Draft</div>
                            <div class="detail-val" id="dt-ai-caption" style="white-space: pre-wrap; font-size:13px; line-height:1.6; margin-bottom:10px; background:#fff; padding:15px; border-radius:8px; border:1px solid #e2e8f0;"></div>
                            
                            <div class="detail-label">Suggested Hashtags</div>
                            <div class="detail-val" id="dt-ai-hashtags" style="color:var(--cp-blue); font-weight:600; background:#eff6ff; padding:10px 15px; border-radius:8px;"></div>
                        </div>
                        <div id="ai-caption-loading" style="display:none; text-align:center; padding: 20px 0;">
                            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                            <div style="font-size:12px; color:var(--cp-muted);">Menganalisa brief dan menyusun kata-kata...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- MODAL ADD/EDIT BRIEF -->
<style>
.cp-back { position: fixed; inset: 0; background: rgba(7, 16, 40, .55); backdrop-filter: blur(6px); z-index: 9000; display: none; align-items: center; justify-content: center; padding: 20px; }
.cp-back.show { display: flex; }
.cp-modal { background: var(--cp-white); border-radius: 20px; width: 100%; box-shadow: var(--cp-sh-md); animation: cpUp .25s cubic-bezier(.22, 1, .36, 1) both; max-height: 90vh; display: flex; flex-direction: column; }
.cp-mh { display: flex; align-items: center; justify-content: space-between; padding: 17px 22px 13px; border-bottom: 1.5px solid var(--cp-border); }
.cp-mt { font-size: 16px; font-weight: 800; color: var(--cp-text); }
.cp-mcls { width: 30px; height: 30px; border: 1.5px solid var(--cp-border); background: var(--cp-white); border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--cp-muted); }
.cp-mcls:hover { background: #fee2e2; color: var(--cp-red); }
.cp-mb { padding: 18px 22px; overflow-y: auto; flex: 1; }
.cp-mf { display: flex; justify-content: flex-end; gap: 8px; padding: 13px 22px; border-top: 1.5px solid var(--cp-border); }
@keyframes cpUp { from { opacity: 0; transform: translateY(20px) scale(.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
.cp-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.cp-row.full { grid-template-columns: 1fr; }
.cp-field label { display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 5px; }
.cp-inp { width: 100%; padding: 9px 12px; border: 1.5px solid var(--cp-border); border-radius: 10px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: var(--cp-text); outline: none; transition: border .15s; background: var(--cp-white); }
.cp-inp:focus { border-color: var(--cp-blue); box-shadow: 0 0 0 3px rgba(45,108,223,.08); }
</style>
<div class="cp-back" id="m-brief">
    <div class="cp-modal" style="max-width:700px">
        <form id="briefForm">
            <input type="hidden" id="f-brief-id" name="id">
            <div class="cp-mh">
                <div>
                    <div class="cp-mt" id="mb-title">Buat Content Brief Baru</div>
                </div>
                <button type="button" class="cp-mcls" onclick="cls('m-brief')"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="cp-mb">
                <div class="cp-row full">
                    <div class="cp-field">
                        <label>Pilih Konten <span style="color:var(--cp-red)">*</span></label>
                        <select class="cp-inp" id="fb-content" name="content_id" required>
                            <option value="">-- Pilih Content Plan --</option>
                            <?php foreach($contents as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= esc($c['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="cp-row full">
                    <div class="cp-field">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                            <label style="margin:0;">Objective / Tujuan <span style="color:var(--cp-red)">*</span></label>
                            <button type="button" class="cpb cpb-sm" onclick="aiPolish('fb-objective', 'objective')" style="font-size:10px; padding:2px 6px; background:linear-gradient(135deg, #7c3aed, #4f46e5); color:#fff; border:none; border-radius:4px;"><i class="bi bi-stars"></i> Polish</button>
                        </div>
                        <textarea class="cp-inp" id="fb-objective" name="objective" rows="2" placeholder="Apa tujuan utama konten ini?" required></textarea>
                    </div>
                </div>
                <div class="cp-row">
                    <div class="cp-field">
                        <label>Target Audience</label>
                        <textarea class="cp-inp" id="fb-audience" name="target_audience" rows="2" placeholder="Siapa target audiensnya?"></textarea>
                    </div>
                    <div class="cp-field">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                            <label style="margin:0;">Key Message</label>
                            <button type="button" class="cpb cpb-sm" onclick="aiPolish('fb-message', 'message')" style="font-size:10px; padding:2px 6px; background:linear-gradient(135deg, #7c3aed, #4f46e5); color:#fff; border:none; border-radius:4px;"><i class="bi bi-stars"></i> Polish</button>
                        </div>
                        <textarea class="cp-inp" id="fb-message" name="key_message" rows="2" placeholder="Pesan utama yang ingin disampaikan?"></textarea>
                    </div>
                </div>
                <div class="cp-row">
                    <div class="cp-field">
                        <label>Call to Action (CTA)</label>
                        <input type="text" class="cp-inp" id="fb-cta" name="call_to_action" placeholder="Contoh: Klik link di bio">
                    </div>
                    <div class="cp-field">
                        <label>Tone of Voice</label>
                        <input type="text" class="cp-inp" id="fb-tone" name="tone" placeholder="Contoh: Santai, Edukatif">
                    </div>
                </div>
                <div class="cp-row full">
                    <div class="cp-field">
                        <label>Reference URL</label>
                        <input type="url" class="cp-inp" id="fb-url" name="reference_url" placeholder="https://...">
                    </div>
                </div>
                <div class="cp-row full">
                    <div class="cp-field">
                        <label>Catatan Tambahan (Notes)</label>
                        <textarea class="cp-inp" id="fb-notes" name="notes" rows="2" placeholder="Tambahkan catatan khusus..."></textarea>
                    </div>
                </div>
            </div>
            <div class="cp-mf">
                <button type="button" class="cpb cpb-out" onclick="cls('m-brief')">Batal</button>
                <button type="submit" class="cpb cpb-pri">Simpan Brief</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: CONFIRM DELETE BRIEF -->
<div class="cp-back" id="m-confirm-delete-brief">
    <div class="cp-modal" style="max-width:440px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt" style="color:var(--cp-red)"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Content Brief?</div>
            </div>
            <button type="button" class="cp-mcls" onclick="cls('m-confirm-delete-brief')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb" style="padding:20px;text-align:center">
            <div style="width:60px;height:60px;background:#fef2f2;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:15px">
                <i class="bi bi-trash-fill" style="font-size:28px;color:#dc2626"></i>
            </div>
            <p class="mb-0" style="font-size:14px;color:#374151;line-height:1.5">Apakah Anda yakin ingin menghapus brief untuk konten <strong id="del-brief-name">-</strong>?</p>
            <small class="text-muted" style="font-size:12px">Tindakan ini tidak dapat dibatalkan.</small>
        </div>
        <div class="cp-mf" style="padding:15px 20px;display:flex;justify-content:flex-end;gap:10px;background:#fafafa;border-top:1px solid var(--cp-border)">
            <button type="button" class="cpb cpb-out" onclick="cls('m-confirm-delete-brief')">Batal</button>
            <button type="button" class="cpb cpb-pri" onclick="executeDeleteBrief()" style="background:#dc2626;box-shadow:0 4px 12px rgba(220,38,38,0.25)"><i class="bi bi-trash"></i> Ya, Hapus Brief</button>
        </div>
    </div>
</div>

<div id="cp-toast"></div>

<script src="<?= base_url('assets/js/content_brief.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
