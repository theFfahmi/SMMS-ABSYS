<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="<?= base_url('campaigns') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Campaign
        </a>
        <span class="badge <?= $campaign['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?> fs-6 text-uppercase px-3 py-2">
            <?= $campaign['status'] ?>
        </span>
    </div>

    <div class="cp-card p-4 mb-4" style="border-radius:20px;">
        <h3 class="fw-bold text-dark mb-2"><?= esc($campaign['name']) ?></h3>
        <p class="text-muted mb-3"><?= esc($campaign['description'] ?: 'Tidak ada deskripsi.') ?></p>
        
        <div class="row g-3 bg-light rounded-4 p-3" style="font-size:13px; background-color: var(--cp-bg) !important; border: 1.5px solid var(--cp-border);">
            <div class="col-md-3">
                <div class="text-muted">Tujuan / Objective:</div>
                <div class="fw-bold text-dark"><?= esc($campaign['objective'] ?: '-') ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Target Audience:</div>
                <div class="fw-bold text-dark"><?= esc($campaign['target_audience'] ?: '-') ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Periode Campaign:</div>
                <div class="fw-bold text-dark"><?= $campaign['start_date'] ?> s/d <?= $campaign['end_date'] ?: 'Selesai' ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Anggaran:</div>
                <div class="fw-bold text-primary fs-6" style="color: var(--cp-purple) !important;">Rp <?= number_format((float)$campaign['budget'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <div class="cp-card p-4" style="border-radius:20px;">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-collection-play text-primary me-2"></i> Konten dalam Campaign ini</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Judul Konten</th>
                        <th>Platform</th>
                        <th>Jenis</th>
                        <th>Tanggal Rencana</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($campaign['contents'])): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada konten yang dihubungkan ke campaign ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($campaign['contents'] as $c): ?>
                            <tr>
                                <td><strong><?= esc($c['title']) ?></strong></td>
                                <td>
                                    <?php if (!empty($c['platforms'])): ?>
                                        <?php foreach ($c['platforms'] as $p): ?>
                                            <span class="badge bg-light text-dark border me-1"><i class="bi <?= $p['icon'] ?>"></i> <?= $p['name'] ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($c['type_name'] ?: '-') ?></td>
                                <td><?= $c['planned_date'] ?></td>
                                <td><span class="badge" style="background-color:<?= $c['status_color'] ?>"><?= esc($c['status_name']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
