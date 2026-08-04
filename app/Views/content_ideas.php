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
    --cp-blue-l: #eff6ff;
    --cp-blue-m: #dbeafe;
    --cp-green: #16a34a;
    --cp-green-l: #ecfdf5;
    --cp-orange: #ea580c;
    --cp-orange-l: #fff7ed;
    --cp-red: #dc2626;
    --cp-red-l: #fef2f2;
    --cp-purple: #7c3aed;
    --cp-purple-l: #f5f3ff;
    --cp-sh: 0 2px 12px rgba(0, 0, 0, .06);
    --cp-sh-md: 0 8px 32px rgba(0, 0, 0, .12);
}

.cp-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
.cp-stat { background: var(--cp-white); border-radius: 16px; padding: 18px 20px; border: 1.5px solid var(--cp-border); box-shadow: var(--cp-sh); display: flex; align-items: center; gap: 14px; }
.cp-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.cp-stat-icon.purple { background: var(--cp-purple-l); color: var(--cp-purple); }
.cp-stat-icon.orange { background: var(--cp-orange-l); color: var(--cp-orange); }
.cp-stat-icon.green  { background: var(--cp-green-l);  color: var(--cp-green); }
.cp-stat-icon.blue   { background: var(--cp-blue-m);   color: var(--cp-blue); }
.cp-stat-val { font-size: 26px; font-weight: 800; color: var(--cp-text); line-height: 1; }
.cp-stat-lbl { font-size: 12px; color: var(--cp-muted); margin-top: 3px; font-weight: 500; }

.cp-card { background: var(--cp-white); border-radius: 20px; border: 1.5px solid var(--cp-border); box-shadow: var(--cp-sh); overflow: hidden; }
.cp-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1.5px solid var(--cp-border); flex-wrap: wrap; gap: 10px; }
.cp-filters { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.cp-sel, .cp-search-inp { padding: 7px 12px; border: 1.5px solid var(--cp-border); border-radius: 9px; font-size: 13px; font-weight: 600; color: var(--cp-text); background: var(--cp-white); outline: none; font-family: 'DM Sans', sans-serif; transition: border .15s; }
.cp-sel:focus, .cp-search-inp:focus { border-color: var(--cp-blue); }

.cpb { padding: 7.5px 14px; border: none; border-radius: 9px; font-size: 12.5px; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif; display: inline-flex; align-items: center; gap: 6px; transition: all .15s; text-decoration: none;}
.cpb-pri { background: var(--cp-blue); color: #fff; }
.cpb-pri:hover { opacity: .9; color: #fff;}
.cpb-out { background: var(--cp-white); color: var(--cp-text); border: 1.5px solid var(--cp-border); }
.cpb-out:hover { background: var(--cp-bg); color: var(--cp-text);}
.cpb-suc { background: var(--cp-green-l); color: var(--cp-green); border: 1.5px solid #bbf7d0; }
.cpb-suc:hover { background: #d1fae5; }
.cpb-dan { background: var(--cp-red-l); color: var(--cp-red); border: 1.5px solid #fecaca; }
.cpb-dan:hover { background: #fee2e2; }
.cpb-sm { padding: 5px 10px; font-size: 11.5px; border-radius: 7px; }

/* IDEA GRID */
.idea-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; padding: 20px; }
.idea-card { background: var(--cp-white); border: 1.5px solid var(--cp-border); border-radius: 16px; padding: 16px 18px; transition: all .2s ease; display: flex; flex-direction: column; justify-content: space-between; }
.idea-card:hover { border-color: var(--cp-blue-m); box-shadow: 0 6px 20px rgba(45, 108, 223, 0.08); transform: translateY(-2px); }
.idea-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
.idea-title { font-size: 15px; font-weight: 700; color: var(--cp-text); line-height: 1.3; }
.prio-badge { font-size: 10.5px; font-weight: 800; padding: 3px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: .4px; flex-shrink: 0; }
.prio-low { background: #f3f4f6; color: #4b5563; }
.prio-medium { background: var(--cp-blue-l); color: var(--cp-blue); }
.prio-high { background: var(--cp-orange-l); color: var(--cp-orange); }
.prio-urgent { background: var(--cp-red-l); color: var(--cp-red); }
.idea-desc { font-size: 12.5px; color: #4b5563; line-height: 1.5; margin-bottom: 14px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.idea-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
.idea-tag { font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; background: var(--cp-bg); color: var(--cp-muted); }
.idea-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px solid var(--cp-border); }
.user-avatar-sm { width: 24px; height: 24px; border-radius: 50%; background: var(--cp-blue); color: #fff; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

/* MODAL STYLING */
.cp-back { position: fixed; inset: 0; background: rgba(7, 16, 40, .55); backdrop-filter: blur(6px); z-index: 9000; display: none; align-items: center; justify-content: center; padding: 20px; }
.cp-back.show { display: flex; }
.cp-modal { background: var(--cp-white); border-radius: 20px; width: 100%; box-shadow: var(--cp-sh-md); animation: cpUp .25s cubic-bezier(.22, 1, .36, 1) both; max-height: 90vh; display: flex; flex-direction: column; }
.cp-mh { display: flex; align-items: center; justify-content: space-between; padding: 17px 22px 13px; border-bottom: 1.5px solid var(--cp-border); }
.cp-mt { font-size: 16px; font-weight: 800; color: var(--cp-text); }
.cp-ms { font-size: 12px; color: var(--cp-muted); margin-top: 2px; }
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

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success mt-2"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mt-2"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#2d6cdf,#7c3aed);border-radius:14px;
                display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(45,108,223,.35);flex-shrink:0">
                <i class="bi bi-lightbulb-fill" style="font-size:20px;color:#fff"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                    Content Ideas
                </h4>
                <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                    <i class="bi bi-lightning-charge-fill" style="color:#ea580c;font-size:11px"></i>
                    Tampung, saring dan konversi ide kreatif menjadi Content Plan
                </p>
            </div>
        </div>
        <div>
            <button class="cpb cpb-pri" onclick="openIdeaForm()"><i class="bi bi-plus-lg"></i> Tambah Ide Baru</button>
        </div>
    </div>

    <!-- STATS -->
    <div class="cp-stats" style="grid-template-columns: repeat(4, 1fr);">
        <div class="cp-stat">
            <div class="cp-stat-icon purple"><i class="bi bi-lightbulb-fill"></i></div>
            <div>
                <div class="cp-stat-val" id="s-total">0</div>
                <div class="cp-stat-lbl">Total Ide</div>
            </div>
        </div>
        <div class="cp-stat">
            <div class="cp-stat-icon orange"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div class="cp-stat-val" id="s-urgent">0</div>
                <div class="cp-stat-lbl">Prioritas Tinggi/Urgent</div>
            </div>
        </div>
        <div class="cp-stat">
            <div class="cp-stat-icon blue"><i class="bi bi-person-badge-fill"></i></div>
            <div>
                <div class="cp-stat-val" id="s-my">0</div>
                <div class="cp-stat-lbl">Assigned to Me</div>
            </div>
        </div>
        <div class="cp-stat">
            <div class="cp-stat-icon green"><i class="bi bi-arrow-repeat"></i></div>
            <div>
                <div class="cp-stat-val" id="s-conv">0</div>
                <div class="cp-stat-lbl">Terkonversi ke Plan</div>
            </div>
        </div>
    </div>

    <!-- MAIN CARD & TOOLBAR -->
    <div class="cp-card">
        <div class="cp-toolbar">
            <div class="cp-filters">
                <input type="text" class="cp-search-inp" id="search-idea" placeholder="Cari ide..." style="width:200px" onkeyup="filterIdeas()">
                <select class="cp-sel" id="f-assign" onchange="filterIdeas()">
                    <option value="">-- Semua Ide --</option>
                    <option value="me">Assigned to Me</option>
                </select>
                <select class="cp-sel" id="f-prio" onchange="filterIdeas()">
                    <option value="">-- Semua Prioritas --</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div style="font-size:12.5px;color:var(--cp-muted);display:flex;align-items:center;gap:12px;">
                <span>Menampilkan <strong style="color:var(--cp-text)" id="ide-count">...</strong> ide</span>
                <button class="cpb cpb-primary cpb-sm" onclick="showAiModal()" style="background: linear-gradient(135deg, #7c3aed, #4f46e5); border:none; color: #fff;">
                    <i class="bi bi-magic me-1"></i> AI Generate Ideas
                </button>
            </div>
        </div>

        <div class="idea-grid" id="idea-container">
            <!-- Ideas rendered by JS -->
        </div>
    </div>
</div>

<!-- MODAL CONVERT IDEA -->
<div class="cp-back" id="m-convert">
    <div class="cp-modal" style="max-width:500px">
        <form id="convertForm" method="POST" action="">
        <div class="cp-mh">
            <div>
                <div class="cp-mt">Konversi Ide ke Content Plan</div>
                <div class="cp-ms" id="mc-subtitle">Ubah ide ini menjadi draf perencanaan konten</div>
            </div>
            <button type="button" class="cp-mcls" onclick="cls('m-convert')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb">
            <input type="hidden" id="mc-id">
            <div class="cp-row full">
                <div class="cp-field">
                    <label>Judul Konten Target</label>
                    <input type="text" class="cp-inp" id="mc-title" readonly style="background:#f9fafb">
                </div>
            </div>
            <div class="cp-row">
                <div class="cp-field">
                    <label>Rencana Tanggal (Planned Date) <span style="color:var(--cp-red)">*</span></label>
                    <input type="date" class="cp-inp" name="planned_date" required>
                </div>
                <div class="cp-field">
                    <label>Status Konten Awal</label>
                    <select class="cp-inp" name="status">
                        <option value="draft" selected>Draft (Perencanaan)</option>
                        <option value="in-production">In Production</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="cp-mf">
            <button type="button" class="cpb cpb-out" onclick="cls('m-convert')">Batal</button>
            <button type="submit" class="cpb cpb-suc"><i class="bi bi-arrow-repeat"></i> Konversi Sekarang</button>
        </div>
        </form>
    </div>
</div>

<!-- MODAL ADD/EDIT IDEA -->
<div class="cp-back" id="m-idea">
    <div class="cp-modal" style="max-width:600px">
        <form id="ideaForm">
            <input type="hidden" id="f-idea-id" name="id">
            <div class="cp-mh">
                <div>
                    <div class="cp-mt" id="mi-title">Tambah Ide Baru</div>
                </div>
                <button type="button" class="cp-mcls" onclick="cls('m-idea')"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="cp-mb">
                <div class="cp-row full">
                    <div class="cp-field">
                        <label>Judul Ide <span style="color:var(--cp-red)">*</span></label>
                        <input type="text" class="cp-inp" id="fi-title" name="title" required>
                    </div>
                </div>
                <div class="cp-row full">
                    <div class="cp-field">
                        <label>Deskripsi Ide</label>
                        <textarea class="cp-inp" id="fi-desc" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="cp-row">
                    <div class="cp-field">
                        <label>Pillar Konten</label>
                        <select class="cp-inp" id="fi-pillar" name="content_pillar_id">
                            <?php foreach($contentPillars as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cp-field">
                        <label>Prioritas</label>
                        <select class="cp-inp" id="fi-prio" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="cp-row">
                    <div class="cp-field">
                        <label>Tipe Konten</label>
                        <select class="cp-inp" id="fi-type" name="content_type_id">
                            <?php foreach($contentTypes as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cp-field">
                        <label>Format Konten</label>
                        <select class="cp-inp" id="fi-format" name="content_format_id">
                            <?php foreach($contentFormats as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= esc($f['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="cp-row full">
                    <div class="cp-field">
                        <label>Assign to (PIC)</label>
                        <select class="cp-inp" id="fi-assign" name="assigned_to">
                            <option value="">-- Unassigned --</option>
                            <?php foreach($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= esc($u['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="cp-mf">
                <button type="button" class="cpb cpb-out" onclick="cls('m-idea')">Batal</button>
                <button type="submit" class="cpb cpb-pri">Simpan Ide</button>
            </div>
        </form>
    </div>
</div>
<!-- AI MODAL -->
<div class="cp-back" id="m-ai">
    <div class="cp-modal" style="max-width:500px">
        <div class="cp-mh">
            <div class="cp-mt"><i class="bi bi-stars text-warning me-2"></i> AI Idea Generator</div>
            <button type="button" class="cp-mcls" onclick="cls('m-ai')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-mb">
            <p style="font-size: 13px; color: var(--cp-muted); margin-bottom: 15px;">Masukkan topik atau kata kunci, AI kami akan memikirkan 3 ide konten brilian untuk kampanye Anda.</p>
            <div class="cp-field">
                <input type="text" class="cp-inp" id="ai-prompt" placeholder="Contoh: Promo diskon akhir tahun...">
            </div>
            <div id="ai-loading" style="display:none; text-align:center; padding: 20px 0;">
                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                <div style="font-size:12px; color:var(--cp-muted);">AI sedang berpikir keras...</div>
            </div>
        </div>
        <div class="cp-mf">
            <button type="button" class="cpb cpb-out" onclick="cls('m-ai')">Batal</button>
            <button type="button" class="cpb cpb-primary" id="btn-ai-gen" onclick="generateAiIdeas()" style="background: linear-gradient(135deg, #7c3aed, #4f46e5); border:none;">Generate</button>
        </div>
    </div>
</div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.currentUserId = '<?= session()->get("user_id") ?>';
</script>
<script src="<?= base_url('assets/js/content_ideas.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
