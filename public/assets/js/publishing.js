const baseUrl = window.location.origin + '/';
let dataApproved = [];
let dataAll = [];
let dataScheduled = [];
let dataPublished = [];
let dataSchedules = [];

document.addEventListener('DOMContentLoaded', () => {
    fetchData();

    document.getElementById('formSchedule').addEventListener('submit', function(e) {
        e.preventDefault();
        submitSchedule(this);
    });
});

function fetchData() {
    fetch(`${baseUrl}publishing/getData`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        dataApproved = data.approved;
        dataAll = data.all;
        dataScheduled = data.scheduled;
        dataPublished = data.published;
        dataSchedules = data.schedules;
        renderData();
    })
    .catch(err => console.error('Fetch error:', err));
}

function renderData() {
    // 1. Update Stats
    document.getElementById('stat-approved').innerText = dataApproved.length;
    document.getElementById('stat-scheduled').innerText = dataScheduled.length;
    document.getElementById('stat-published').innerText = dataPublished.length;

    // 2. Update Select Options in Modal
    const selectContent = document.querySelector('select[name="content_id"]');
    if (selectContent) {
        selectContent.innerHTML = '<option value="">-- Pilih Konten --</option>';
        const targetList = dataApproved.length > 0 ? dataApproved : dataAll;
        targetList.forEach(ac => {
            selectContent.innerHTML += `<option value="${ac.id}">[${ac.status_name}] ${ac.title} (Rencana: ${ac.planned_date})</option>`;
        });
    }

    // 3. Render Schedules Table
    const scheduleList = document.getElementById('schedule-list');
    scheduleList.innerHTML = '';
    
    if (dataSchedules.length === 0) {
        scheduleList.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada konten yang dijadwalkan. Klik <strong>Buat Jadwal Tayang</strong> di atas.</td></tr>`;
    } else {
        dataSchedules.forEach(s => {
            const dateObj = new Date(s.scheduled_at);
            const dateStr = dateObj.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
            const timeStr = dateObj.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
            
            let statusBadge = '';
            let actionBtn = '';

            if (s.status === 'published') {
                statusBadge = '<span class="badge bg-success"><i class="bi bi-check-all me-1"></i> Published</span>';
                actionBtn = '<span class="text-muted small"><i class="bi bi-check-circle-fill text-success"></i> Selesai Rilis</span>';
            } else {
                statusBadge = '<span class="badge bg-teal" style="background:#14b8a6;color:#fff"><i class="bi bi-clock me-1"></i> Scheduled</span>';
                actionBtn = `<button type="button" class="btn btn-sm btn-success fw-semibold rounded-3" onclick="confirmPublish(${s.id}, '${(s.content_title || '').replace(/'/g, "\\'")}')">
                                <i class="bi bi-send-fill me-1"></i> Publish Now
                             </button>`;
            }

            scheduleList.innerHTML += `
                <tr id="sched-${s.id}">
                    <td><strong>${s.content_title || 'Konten #' + s.content_platform_id}</strong></td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <i class="bi ${s.platform_icon || 'bi-share'}" style="color:${s.platform_color}"></i>
                            ${s.platform_name || 'Social Media'}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><i class="bi bi-clock me-1 text-primary"></i> ${dateStr}, ${timeStr}</div>
                    </td>
                    <td><small class="text-muted">${s.notes || '-'}</small></td>
                    <td>${statusBadge}</td>
                    <td>${actionBtn}</td>
                </tr>
            `;
        });
    }
}

let currentScheduleId = null;

function openScheduleModal() {
    document.getElementById('formSchedule').reset();
    document.getElementById('m-schedule').style.display = 'flex';
}

function confirmPublish(scheduleId, title) {
    currentScheduleId = scheduleId;
    document.getElementById('pub-content-title').innerText = 'Publikasikan konten "' + title + '" sekarang ke media sosial?';
    document.getElementById('m-confirm-publish').style.display = 'flex';
}

function cls(id) {
    document.getElementById(id).style.display = 'none';
}

function submitSchedule(formElement) {
    const formData = new FormData(formElement);
    
    fetch(`${baseUrl}publishing/store-schedule`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            cls('m-schedule');
            fetchData();
            showToast(res.message);
        } else {
            alert(res.message || 'Gagal menyimpan jadwal');
        }
    })
    .catch(e => alert('Network error'));
}

function executePublish() {
    if(!currentScheduleId) return;
    
    const row = document.getElementById('sched-' + currentScheduleId);
    if(row) row.style.opacity = '0.5';

    fetch(`${baseUrl}publishing/publish-now/${currentScheduleId}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            cls('m-confirm-publish');
            fetchData();
            showToast(res.message);
        } else {
            if(row) row.style.opacity = '1';
            alert('Gagal publikasi konten');
        }
    })
    .catch(e => alert('Network error'));
}

function showToast(msg) {
    let t = document.getElementById('cp-toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'cp-toast';
        t.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#111827;color:#fff;padding:12px 20px;border-radius:10px;z-index:9999;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);font-size:14px;font-weight:600;display:none;';
        document.body.appendChild(t);
    }
    t.innerText = msg;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3000);
}
