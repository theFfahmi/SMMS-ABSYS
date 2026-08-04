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
.cp-card { background: var(--cp-white); border-radius: 20px; border: 1.5px solid var(--cp-border); box-shadow: var(--cp-sh); padding: 25px; max-width: 900px; margin: 0 auto 30px; }
.cp-title { font-size: 20px; font-weight: 800; color: var(--cp-text); margin-bottom: 5px; }
.cp-subtitle { font-size: 13px; color: var(--cp-muted); margin-bottom: 25px; }
.cp-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.cp-row.full { grid-template-columns: 1fr; }
.cp-field label { display: block; font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 8px; }
.cp-inp { width: 100%; padding: 11px 15px; border: 1.5px solid var(--cp-border); border-radius: 10px; font-size: 14px; color: var(--cp-text); outline: none; transition: border .15s; background: var(--cp-white); }
.cp-inp:focus { border-color: var(--cp-blue); box-shadow: 0 0 0 3px rgba(45,108,223,.08); }
textarea.cp-inp { resize: vertical; min-height: 100px; }
.cpb { padding: 9px 18px; border: none; border-radius: 9px; font-size: 13.5px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all .15s; text-decoration: none;}
.cpb-pri { background: var(--cp-blue); color: #fff; }
.cpb-pri:hover { opacity: .9; color: #fff;}
.cpb-out { background: var(--cp-white); color: var(--cp-text); border: 1.5px solid var(--cp-border); }
.cpb-out:hover { background: var(--cp-bg); color: var(--cp-text);}
.form-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 1.5px solid var(--cp-border); }
.section-title { font-size: 15px; font-weight: 800; color: var(--cp-text); margin: 30px 0 20px; padding-bottom: 10px; border-bottom: 2px solid var(--cp-bg); }
.cp-plat-wrap { display: flex; gap: 12px; flex-wrap: wrap; }
.cp-plat-lbl { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
</style>

<div class="container-fluid py-4">
    <form action="<?= base_url('content-plan/store') ?>" method="POST">
        <div class="cp-card">
            <div class="cp-title">Buat Konten Baru</div>
            <div class="cp-subtitle">Tambahkan data perencanaan konten secara detail</div>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <!-- Informasi Dasar -->
            <div class="section-title">Informasi Dasar</div>
            <div class="cp-row full">
                <div class="cp-field">
                    <label>Judul Konten <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="cp-inp" value="<?= old('title') ?>" required>
                </div>
            </div>
            
            <div class="cp-row full">
                <div class="cp-field">
                    <label>Deskripsi Konten</label>
                    <textarea name="description" class="cp-inp"><?= old('description') ?></textarea>
                </div>
            </div>

            <div class="cp-row">
                <div class="cp-field">
                    <label>Planned Date <span class="text-danger">*</span></label>
                    <input type="date" name="planned_date" class="cp-inp" value="<?= old('planned_date', date('Y-m-d')) ?>" required>
                </div>
                <div class="cp-field">
                    <label>Status</label>
                    <select name="status_id" class="cp-inp">
                        <?php foreach($contentStatuses as $status): ?>
                            <option value="<?= $status['id'] ?>" <?= old('status_id') == $status['id'] ? 'selected' : '' ?>><?= esc($status['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="cp-row">
                <div class="cp-field">
                    <label>Content Type <span class="text-danger">*</span></label>
                    <select name="content_type_id" class="cp-inp" required>
                        <option value="">-- Pilih Tipe --</option>
                        <?php foreach($contentTypes as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= old('content_type_id') == $type['id'] ? 'selected' : '' ?>><?= esc($type['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="cp-field">
                    <label>Content Format <span class="text-danger">*</span></label>
                    <select name="content_format_id" class="cp-inp" required>
                        <option value="">-- Pilih Format --</option>
                        <?php foreach($contentFormats as $format): ?>
                            <option value="<?= $format['id'] ?>" <?= old('content_format_id') == $format['id'] ? 'selected' : '' ?>><?= esc($format['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="cp-row">
                <div class="cp-field">
                    <label>Content Pillar <span class="text-danger">*</span></label>
                    <select name="content_pillar_id" class="cp-inp" required>
                        <option value="">-- Pilih Pillar --</option>
                        <?php foreach($contentPillars as $pillar): ?>
                            <option value="<?= $pillar['id'] ?>" <?= old('content_pillar_id') == $pillar['id'] ? 'selected' : '' ?>><?= esc($pillar['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="cp-field">
                    <label>Campaign (Opsional)</label>
                    <select name="campaign_id" class="cp-inp">
                        <option value="">-- Tanpa Campaign --</option>
                        <?php foreach($campaigns as $camp): ?>
                            <option value="<?= $camp['id'] ?>" <?= old('campaign_id') == $camp['id'] ? 'selected' : '' ?>><?= esc($camp['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="cp-row full">
                <div class="cp-field">
                    <label>Platforms</label>
                    <div class="cp-plat-wrap">
                        <?php foreach($platforms as $p): ?>
                        <label class="cp-plat-lbl">
                            <input type="checkbox" name="platforms[]" value="<?= $p['id'] ?>"> <?= esc($p['name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="cp-row full">
                <div class="cp-field">
                    <label>Assign PIC</label>
                    <select name="assigned_to" class="cp-inp">
                        <option value="">-- Unassigned --</option>
                        <?php foreach($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= old('assigned_to') == $user['id'] ? 'selected' : '' ?>><?= esc($user['full_name'] ?: $user['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Content Brief -->
            <div class="section-title">Content Brief (Opsional)</div>
            
            <div class="cp-row">
                <div class="cp-field">
                    <label>Objective</label>
                    <input type="text" name="objective" class="cp-inp" placeholder="Cth: Brand awareness" value="<?= old('objective') ?>">
                </div>
                <div class="cp-field">
                    <label>Target Audience</label>
                    <input type="text" name="target_audience" class="cp-inp" placeholder="Cth: Mahasiswa 18-24 tahun" value="<?= old('target_audience') ?>">
                </div>
            </div>
            
            <div class="cp-row">
                <div class="cp-field">
                    <label>Key Message</label>
                    <textarea name="key_message" class="cp-inp" style="min-height:70px" placeholder="Pesan utama yang ingin disampaikan"><?= old('key_message') ?></textarea>
                </div>
                <div class="cp-field">
                    <label>Call to Action (CTA)</label>
                    <textarea name="call_to_action" class="cp-inp" style="min-height:70px" placeholder="Cth: Klik link di bio"><?= old('call_to_action') ?></textarea>
                </div>
            </div>
            
            <div class="cp-row">
                <div class="cp-field">
                    <label>Tone of Voice</label>
                    <input type="text" name="tone" class="cp-inp" placeholder="Cth: Santai, informatif" value="<?= old('tone') ?>">
                </div>
                <div class="cp-field">
                    <label>Reference URL</label>
                    <input type="url" name="reference_url" class="cp-inp" placeholder="https://" value="<?= old('reference_url') ?>">
                </div>
            </div>

            <div class="cp-row full">
                <div class="cp-field">
                    <label>Catatan Tambahan (Notes)</label>
                    <textarea name="notes" class="cp-inp" style="min-height:70px"><?= old('notes') ?></textarea>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?= base_url('content-plan') ?>" class="cpb cpb-out">Batal</a>
                <button type="submit" class="cpb cpb-pri"><i class="bi bi-check-lg"></i> Simpan Konten</button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
