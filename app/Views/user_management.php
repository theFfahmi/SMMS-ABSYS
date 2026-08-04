<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#111827,#374151);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(0,0,0,.35);flex-shrink:0">
                    <i class="bi bi-people-fill" style="font-size:20px;color:#fff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="font-size:19px;letter-spacing:-.4px;color:#111827;line-height:1.2">
                        User Management
                    </h4>
                    <p class="mb-0" style="font-size:12.5px;color:#6b7280;margin-top:2px;font-weight:500">
                        <i class="bi bi-lightning-charge-fill" style="color:#f59e0b;font-size:11px"></i>
                        Kelola akses dan peran pengguna sistem
                    </p>
                </div>
            </div>
        </div>
        <button class="cpb cpb-pri" onclick="openAddUser()">
            <i class="bi bi-person-plus-fill"></i> Tambah User
        </button>
    </div>

    <div class="cp-card">
        <div class="cp-toolbar">
            <div class="tab-nav-wrap">
                <button class="tab-nav active">Users</button>
                <button class="tab-nav">Roles</button>
                <button class="tab-nav">Permissions</button>
            </div>
            <div class="cp-filters">
                <input type="text" class="cp-search-inp" placeholder="Cari user..." style="width:200px">
                <select class="cp-sel">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="social_media_manager">Social Media Manager</option>
                    <option value="content_creator">Content Creator</option>
                    <option value="designer">Designer</option>
                    <option value="reviewer">Reviewer</option>
                </select>
                <select class="cp-sel">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
        </div>
        <table class="cp-ltbl">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Active</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="users-table">
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:30px;color:var(--cp-muted)">Belum ada pengguna.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td style="font-weight:600;color:var(--cp-dark)"><?= esc($u['full_name']) ?><br><span style="font-size:12px;color:var(--cp-muted);font-weight:400">@<?= esc($u['username']) ?></span></td>
                        <td style="color:var(--cp-muted)"><?= esc($u['email']) ?></td>
                        <td>
                            <?php 
                                $rc = 'bg-secondary';
                                if ($u['role'] === 'admin') $rc = 'bg-danger';
                                else if ($u['role'] === 'social_media_manager') $rc = 'bg-primary';
                                else if ($u['role'] === 'content_creator') $rc = 'bg-success';
                                else if ($u['role'] === 'designer') $rc = 'bg-warning text-dark';
                                else if ($u['role'] === 'reviewer') $rc = 'bg-info text-dark';
                            ?>
                            <span class="badge <?= $rc ?>"><?= esc($u['role']) ?></span>
                        </td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;color:var(--cp-muted)"><?= $u['last_login_at'] ?? 'Never' ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editUser(<?= $u['id'] ?>)"><i class="bi bi-pencil"></i></button>
                                <?php if ($u['id'] !== session()->get('user_id')): ?>
                                <button type="button" class="btn btn-sm <?= $u['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" onclick="window.location.href='<?= base_url('user-management/toggleStatus/' . $u['id']) ?>'">
                                    <i class="bi bi-power"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteUser('<?= base_url('user-management/delete/' . $u['id']) ?>', '<?= esc($u['username'], 'js') ?>')"><i class="bi bi-trash"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD USER MODAL -->
<div class="cp-back" id="m-add-user">
    <div class="cp-modal" style="max-width:500px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt">Tambah User Baru</div>
            </div>
            <button class="cp-mcls" onclick="cls('m-add-user')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="<?= base_url('user-management/store') ?>" method="post">
            <div style="padding:20px">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="cp-inp" style="width:100%" name="full_name" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                        <input type="text" class="cp-inp" style="width:100%" name="username" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                        <input type="email" class="cp-inp" style="width:100%" name="email" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                        <input type="password" class="cp-inp" style="width:100%" name="password" required minlength="8">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Role <span class="text-danger">*</span></label>
                        <select class="cp-sel" style="width:100%" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="social_media_manager">Social Media Manager</option>
                            <option value="content_creator" selected>Content Creator</option>
                            <option value="designer">Designer</option>
                            <option value="reviewer">Reviewer</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Status Akses</label>
                    <select class="cp-sel" style="width:100%" name="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="cp-mf" style="padding:15px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1.5px solid var(--cp-border);background:#fafafa;border-radius:0 0 20px 20px">
                <button type="button" class="cpb cpb-out" onclick="cls('m-add-user')">Batal</button>
                <button type="submit" class="cpb cpb-pri">Simpan User</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div class="cp-back" id="m-edit-user">
    <div class="cp-modal" style="max-width:500px">
        <div class="cp-mh">
            <div>
                <div class="cp-mt">Edit User</div>
            </div>
            <button class="cp-mcls" onclick="cls('m-edit-user')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="form-edit-user" method="post">
            <div style="padding:20px">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="cp-inp" style="width:100%" name="full_name" id="eu-full-name" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                        <input type="text" class="cp-inp" style="width:100%" name="username" id="eu-username" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                        <input type="email" class="cp-inp" style="width:100%" name="email" id="eu-email" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Password (Opsional)</label>
                        <input type="password" class="cp-inp" style="width:100%" name="password" placeholder="Kosongkan jika tak diubah">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Role <span class="text-danger">*</span></label>
                        <select class="cp-sel" style="width:100%" name="role" id="eu-role" required>
                            <option value="admin">Admin</option>
                            <option value="social_media_manager">Social Media Manager</option>
                            <option value="content_creator">Content Creator</option>
                            <option value="designer">Designer</option>
                            <option value="reviewer">Reviewer</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Status Akses</label>
                    <select class="cp-sel" style="width:100%" name="is_active" id="eu-is-active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="cp-mf" style="padding:15px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1.5px solid var(--cp-border);background:#fafafa;border-radius:0 0 20px 20px">
                <button type="button" class="cpb cpb-out" onclick="cls('m-edit-user')">Batal</button>
                <button type="submit" class="cpb cpb-pri">Perbarui User</button>
            </div>
        </form>
    </div>
</div>

<!-- CONFIRM DELETE MODAL -->
<div class="cp-back" id="m-confirm-delete">
    <div class="cp-modal" style="max-width:400px;text-align:center;padding:24px">
        <div style="width:58px;height:58px;background:#fef2f2;color:#dc2626;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 16px">
            <i class="bi bi-trash-fill"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Hapus User Ini?</h5>
        <p class="text-muted small mb-4" id="del-user-name">Apakah Anda yakin ingin menghapus user ini?</p>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" class="cpb cpb-out" onclick="cls('m-confirm-delete')">Batal</button>
            <a id="btn-confirm-delete-action" href="#" class="cpb cpb-pri" style="background:#dc2626;border-color:#dc2626;text-decoration:none">
                Ya, Hapus
            </a>
        </div>
    </div>
</div>

<div id="cp-toast"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/user_management.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
