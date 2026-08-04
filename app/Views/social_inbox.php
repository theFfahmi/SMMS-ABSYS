<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#7c3aed,#ea580c);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(124,58,237,.35);flex-shrink:0">
                    <i class="bi bi-chat-dots-fill" style="font-size:20px;color:#fff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                        Social Inbox
                    </h4>
                    <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                        <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;font-size:11px"></i>
                        Kelola semua pesan dan interaksi sosial media
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- INBOX -->
    <div class="inbox-layout">
        <div class="cp-card">
            <div class="cp-toolbar">
                <div class="cp-filters">
                    <select class="cp-sel" id="filter-type">
                        <option value="">Semua Tipe</option>
                        <option value="comment">Comments</option>
                        <option value="message">Messages</option>
                        <option value="mention">Mentions</option>
                    </select>
                    <select class="cp-sel" id="filter-platform">
                        <option value="">Semua Platform</option>
                        <?php foreach($platforms as $p): ?>
                            <option value="<?= esc(strtolower($p['name'])) ?>"><?= esc($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="inbox-list" id="inbox-list">
                <!-- Inbox items will be loaded here -->
            </div>
        </div>

        <div class="cp-card">
            <div class="thread-header">
                <div class="thread-title" id="thread-title">Pilih percakapan</div>
                <div class="thread-actions">
                    <button class="cpb cpb-out cpb-sm" id="btn-archive" title="Resolve/Archive"><i class="bi bi-archive"></i></button>
                    <button class="cpb cpb-out cpb-sm" id="btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
                </div>
            </div>
            <div class="thread-body" id="thread-body">
                <div class="inbox-empty">
                    <i class="bi bi-chat-square-text"></i>
                    <h4>Pilih percakapan untuk memulai</h4>
                    <p>Klik salah satu pesan dari daftar di samping</p>
                </div>
            </div>
            <div class="thread-footer" id="thread-footer" style="display:none">
                <input type="text" class="thread-input" id="reply-input" placeholder="Tulis balasan... (Tekan Enter untuk mengirim)">
                <button class="thread-send-btn" id="btn-send-reply"><i class="bi bi-send"></i></button>
            </div>
        </div>
    </div>
</div>

<div id="cp-toast"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/social_inbox.js') ?>"></script>
<?= $this->endSection() ?>
