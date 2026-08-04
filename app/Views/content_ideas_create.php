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

.cp-card { background: var(--cp-white); border-radius: 20px; border: 1.5px solid var(--cp-border); box-shadow: var(--cp-sh); padding: 25px; max-width: 800px; margin: 0 auto; }
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
</style>

<div class="container-fluid py-4">
    <div class="cp-card">
        <div class="cp-title">Tambah Ide Konten</div>
        <div class="cp-subtitle">Catat ide konten sebelum masuk tahap produksi</div>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form action="<?= base_url('content-ideas/store') ?>" method="POST">
            <div class="cp-row full">
                <div class="cp-field">
                    <label>Judul Ide Konten <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="cp-inp" placeholder="Contoh: 5 Tips Seduh Kopi Tubruk Pasir" value="<?= old('title') ?>" required>
                </div>
            </div>
            
            <div class="cp-row full">
                <div class="cp-field">
                    <label>Deskripsi & Konsep Ide</label>
                    <textarea name="description" class="cp-inp" placeholder="Jelaskan storyline, angle, atau referensi ide..."><?= old('description') ?></textarea>
                </div>
            </div>

            <div class="cp-row">
                <div class="cp-field">
                    <label>Content Pillar</label>
                    <select name="content_pillar_id" class="cp-inp">
                        <option value="">-- Pilih Pillar --</option>
                        <?php foreach($contentPillars as $pillar): ?>
                            <option value="<?= $pillar['id'] ?>" <?= old('content_pillar_id') == $pillar['id'] ? 'selected' : '' ?>><?= esc($pillar['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="cp-field">
                    <label>Content Type</label>
                    <select name="content_type_id" class="cp-inp">
                        <option value="">-- Pilih Tipe --</option>
                        <?php foreach($contentTypes as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= old('content_type_id') == $type['id'] ? 'selected' : '' ?>><?= esc($type['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="cp-row">
                <div class="cp-field">
                    <label>Content Format</label>
                    <select name="content_format_id" class="cp-inp">
                        <option value="">-- Pilih Format --</option>
                        <?php foreach($contentFormats as $format): ?>
                            <option value="<?= $format['id'] ?>" <?= old('content_format_id') == $format['id'] ? 'selected' : '' ?>><?= esc($format['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="cp-field">
                    <label>Prioritas <span class="text-danger">*</span></label>
                    <select name="priority" class="cp-inp" required>
                        <option value="low" <?= old('priority') == 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= old('priority', 'medium') == 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= old('priority') == 'high' ? 'selected' : '' ?>>High</option>
                        <option value="urgent" <?= old('priority') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    </select>
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

            <div class="form-footer">
                <a href="<?= base_url('content-ideas') ?>" class="cpb cpb-out">Batal</a>
                <button type="submit" class="cpb cpb-pri"><i class="bi bi-check-lg"></i> Simpan Ide</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
