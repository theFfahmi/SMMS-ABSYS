const baseUrl = window.location.origin + '/';
let dataWaiting = [];
let dataHistory = [];

document.addEventListener('DOMContentLoaded', () => {
    fetchData();

    document.getElementById('formRevision').addEventListener('submit', function(e) {
        e.preventDefault();
        submitRevision(this);
    });
});

function fetchData() {
    fetch(`${baseUrl}content-approval/getData`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        dataWaiting = data.waiting;
        dataHistory = data.history;
        renderData();
    })
    .catch(err => console.error('Fetch error:', err));
}

function renderData() {
    // 1. Update Stats
    document.getElementById('count-waiting').innerText = dataWaiting.length;
    document.getElementById('count-approved').innerText = dataHistory.filter(a => a.status === 'approved').length;
    document.getElementById('count-revision').innerText = dataHistory.filter(a => a.status === 'revision').length;

    // 2. Render Waiting List
    const waitingList = document.getElementById('waiting-list');
    waitingList.innerHTML = '';
    
    if (dataWaiting.length === 0) {
        waitingList.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="bi bi-check-all fs-1 d-block mb-2 text-success"></i>
                Tidak ada konten yang sedang menunggu persetujuan. Semua konten aman!
            </div>
        `;
    } else {
        let html = '<div class="row g-3">';
        dataWaiting.forEach(w => {
            html += `
                <div class="col-md-6" id="cw-${w.id}">
                    <div class="card border rounded-4 p-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-purple text-white px-2 py-1" style="background:#8b5cf6">
                                <i class="bi bi-clock me-1"></i> Waiting Review
                            </span>
                            <small class="text-muted"><i class="bi bi-calendar"></i> Rencana: ${w.planned_date}</small>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">${w.title || 'Untitled'}</h5>
                        <p class="text-muted small mb-3">${w.description || 'Tidak ada deskripsi caption.'}</p>
                        
                        <div class="bg-light rounded-3 p-2 mb-3" style="font-size:12px">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Content Type:</span>
                                <span class="fw-bold text-dark">${w.type_name || '-'} (${w.format_name || '-'})</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Creator:</span>
                                <span class="fw-bold text-dark">${w.created_user_name || 'Creator'}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Campaign:</span>
                                <span class="fw-bold text-primary">${w.campaign_name || 'Non-Campaign'}</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-auto">
                            ${parseInt(w.status_id) === 4 ? `
                                <button type="button" class="btn btn-success btn-sm w-100 fw-semibold rounded-3" onclick="confirmApprove(${w.id}, '${(w.title || '').replace(/'/g, "\\'")}')">
                                    <i class="bi bi-check-lg me-1"></i> Approve 1-Klik
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-semibold rounded-3" onclick="openRevisionModal(${w.id}, '${(w.title || '').replace(/'/g, "\\'")}')">
                                    <i class="bi bi-pencil-square me-1"></i> Minta Revisi
                                </button>
                            ` : `
                                <span class="badge bg-secondary w-100 py-2 fs-6">Bukan Waiting Review</span>
                            `}
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        waitingList.innerHTML = html;
    }

    // 3. Render History
    const historyList = document.getElementById('history-list');
    historyList.innerHTML = '';
    
    if (dataHistory.length === 0) {
        historyList.innerHTML = `<tr><td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat review.</td></tr>`;
    } else {
        dataHistory.forEach(a => {
            let badge = '';
            if (a.status === 'approved') badge = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Approved</span>';
            else if (a.status === 'revision') badge = '<span class="badge bg-danger"><i class="bi bi-arrow-repeat me-1"></i> Revision</span>';
            else badge = `<span class="badge bg-warning text-dark">${a.status}</span>`;

            historyList.innerHTML += `
                <tr>
                    <td><strong>${a.content_title || 'Content #' + a.content_id}</strong></td>
                    <td>${a.reviewer_name || 'Reviewer'}</td>
                    <td>${badge}</td>
                    <td>${a.comment || '-'}</td>
                    <td>${a.reviewed_at || a.created_at}</td>
                </tr>
            `;
        });
    }
}

// Helper CSRF Token
function getCsrfTokenInfo() {
    const headerMeta = document.querySelector('meta[name="csrf-header"]');
    const nameMeta = document.querySelector('meta[name="csrf-name"]');
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    return {
        header: headerMeta ? headerMeta.getAttribute('content') : 'X-CSRF-TOKEN',
        name: nameMeta ? nameMeta.getAttribute('content') : 'csrf_test_name',
        hash: tokenMeta ? tokenMeta.getAttribute('content') : ''
    };
}

function updateCsrfToken(newToken) {
    if (!newToken) return;
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (tokenMeta) {
        tokenMeta.setAttribute('content', newToken);
    }
}

// Global scope vars for actions
let currentContentId = null;

function openRevisionModal(contentId, title) {
    currentContentId = contentId;
    document.getElementById('formRevision').reset();
    document.getElementById('revContentTitle').innerText = 'Konten: ' + title;
    
    const submitBtn = document.querySelector('#formRevision button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Kirim Catatan Revisi';
    }
    
    document.getElementById('m-revision').style.display = 'flex';
}

function confirmApprove(contentId, title) {
    currentContentId = contentId;
    document.getElementById('app-content-title').innerText = 'Setujui konten "' + title + '" untuk lanjut ke alur tayang publikasi?';
    
    const approveBtn = document.querySelector('#m-confirm-approve button.cpb-pri');
    if (approveBtn) {
        approveBtn.disabled = false;
        approveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Ya, Approve Konten';
    }
    
    document.getElementById('m-confirm-approve').style.display = 'flex';
}

function cls(id) {
    document.getElementById(id).style.display = 'none';
}

// Executing Action via Fetch
function executeApprove() {
    if(!currentContentId) return;
    
    const approveBtn = document.querySelector('#m-confirm-approve button.cpb-pri');
    if (approveBtn) {
        approveBtn.disabled = true;
        approveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';
    }
    
    const cw = document.getElementById('cw-' + currentContentId);
    if(cw) cw.style.opacity = '0.5';

    const csrf = getCsrfTokenInfo();
    const headers = {
        'X-Requested-With': 'XMLHttpRequest'
    };
    if (csrf.header && csrf.hash) {
        headers[csrf.header] = csrf.hash;
    }

    const formData = new FormData();
    if (csrf.name && csrf.hash) {
        formData.append(csrf.name, csrf.hash);
    }

    fetch(`${baseUrl}content-approval/approve/${currentContentId}`, {
        method: 'POST',
        headers: headers,
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.csrf_token) updateCsrfToken(res.csrf_token);

        if (res.status === 'success') {
            cls('m-confirm-approve');
            fetchData();
            showToast(res.message, false);
        } else {
            if(cw) cw.style.opacity = '1';
            showToast(res.message || 'Gagal menyetujui konten', true);
            if (approveBtn) {
                approveBtn.disabled = false;
                approveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Ya, Approve Konten';
            }
        }
    })
    .catch(e => {
        if(cw) cw.style.opacity = '1';
        showToast('Network / Server error', true);
        if (approveBtn) {
            approveBtn.disabled = false;
            approveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Ya, Approve Konten';
        }
    });
}

function submitRevision(formElement) {
    if(!currentContentId) return;

    const submitBtn = formElement.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengirim...';
    }

    const formData = new FormData(formElement);
    const csrf = getCsrfTokenInfo();
    if (csrf.name && csrf.hash) {
        formData.append(csrf.name, csrf.hash);
    }

    const headers = {
        'X-Requested-With': 'XMLHttpRequest'
    };
    if (csrf.header && csrf.hash) {
        headers[csrf.header] = csrf.hash;
    }
    
    fetch(`${baseUrl}content-approval/request-revision/${currentContentId}`, {
        method: 'POST',
        body: formData,
        headers: headers
    })
    .then(res => res.json())
    .then(res => {
        if (res.csrf_token) updateCsrfToken(res.csrf_token);

        if (res.status === 'success') {
            cls('m-revision');
            fetchData();
            showToast(res.message, false);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Kirim Catatan Revisi';
            }
        } else {
            showToast(res.message || 'Gagal mengirim revisi', true);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Kirim Catatan Revisi';
            }
        }
    })
    .catch(e => {
        showToast('Network / Server error', true);
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Kirim Catatan Revisi';
        }
    });
}

function showToast(msg, isError = false) {
    let t = document.getElementById('cp-toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'cp-toast';
        t.style.cssText = 'position:fixed;bottom:20px;right:20px;color:#fff;padding:12px 20px;border-radius:10px;z-index:9999;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);font-size:14px;font-weight:600;display:none;';
        document.body.appendChild(t);
    }
    t.style.background = isError ? '#dc2626' : '#111827';
    t.innerText = msg;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3500);
}
