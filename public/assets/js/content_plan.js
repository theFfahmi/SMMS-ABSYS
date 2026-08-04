const baseUrl = window.appBaseUrl || (window.location.origin + '/');

let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();
let allContents = [];

const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

document.addEventListener('DOMContentLoaded', function() {
    initCalendar();
    fetchStats();
    
    // Bind events
    document.getElementById('cp-prev').addEventListener('click', () => changeMonth(-1));
    document.getElementById('cp-next').addEventListener('click', () => changeMonth(1));
    document.getElementById('cp-today').addEventListener('click', () => {
        currentDate = new Date();
        currentMonth = currentDate.getMonth();
        currentYear = currentDate.getFullYear();
        initCalendar();
    });
    
    document.getElementById('cp-msel').addEventListener('change', (e) => {
        currentMonth = parseInt(e.target.value);
        initCalendar();
    });
    
    document.getElementById('cp-ysel').addEventListener('change', (e) => {
        currentYear = parseInt(e.target.value);
        initCalendar();
    });
    
    document.getElementById('tog-cal').addEventListener('click', () => toggleView('cal'));
    document.getElementById('tog-lst').addEventListener('click', () => toggleView('lst'));
    
    document.getElementById('f-save').addEventListener('click', saveContent);
});

function initCalendar() {
    document.getElementById('cp-mlbl').innerText = `${monthNames[currentMonth]} ${currentYear}`;
    document.getElementById('cp-msel').value = currentMonth;
    
    const ysel = document.getElementById('cp-ysel');
    if (ysel.options.length === 0) {
        const startY = currentYear - 2;
        for (let i = 0; i <= 5; i++) {
            const opt = document.createElement('option');
            opt.value = startY + i;
            opt.text = startY + i;
            ysel.appendChild(opt);
        }
    }
    ysel.value = currentYear;
    
    fetchContents();
}

function changeMonth(dir) {
    currentMonth += dir;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    initCalendar();
}

function fetchContents() {
    fetch(`${baseUrl}content-plan/getContents?month=${currentMonth + 1}&year=${currentYear}`)
        .then(res => res.json())
        .then(data => {
            allContents = data;
            renderCalendar();
            renderList();
        })
        .catch(err => console.error(err));
}

function fetchStats() {
    fetch(`${baseUrl}content-plan/getStats`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('s-total').innerText = data.total || 0;
            document.getElementById('s-draft').innerText = (data.draft || 0) + (data.idea || 0);
            document.getElementById('s-acc').innerText = (data.approved || 0) + (data.waiting_review || 0);
            document.getElementById('s-pub').innerText = (data.published || 0) + (data.scheduled || 0);
        });
}

function renderCalendar() {
    const calBody = document.getElementById('cal-body');
    calBody.innerHTML = '';
    
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    for (let i = 0; i < firstDay; i++) {
        calBody.innerHTML += `<div class="cp-day empty"></div>`;
    }
    
    const today = new Date();
    const isCurrentMonth = today.getMonth() === currentMonth && today.getFullYear() === currentYear;
    const currentDay = today.getDate();
    
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayContents = allContents.filter(c => c.planned_date === dateStr);
        
        let contentHtml = '';
        dayContents.forEach(c => {
            let cls = 'draft';
            if (c.status_id == 6) cls = 'acc';
            if (c.status_id == 8) cls = 'published';
            if (c.status_id == 4) cls = 'draft';
            if (c.status_id == 7) cls = 'acc';
            
            const campTag = c.campaign_name ? `📢 ` : '';
            
            contentHtml += `
                <div class="cp-epill ${cls}" onclick="openDetail(${c.id}, event)" title="${c.campaign_name ? 'Campaign: ' + c.campaign_name + ' | ' : ''}${c.title}">
                    ${campTag}${c.title}
                </div>
            `;
        });
        
        const isPast = (new Date(currentYear, currentMonth, day)) < new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const cellClass = `cp-day ${isCurrentMonth && day === currentDay ? 'today' : ''} ${isPast ? 'past' : ''}`;
        
        calBody.innerHTML += `
            <div class="${cellClass}" onclick="handleCellClick('${dateStr}')">
                <div class="cp-day-num">${day}</div>
                <div class="cp-day-evs">${contentHtml}</div>
            </div>
        `;
    }
}

function renderList() {
    const lstBody = document.getElementById('lst-body');
    lstBody.innerHTML = '';
    
    allContents.forEach(c => {
        const platformNames = Array.isArray(c.platforms) ? c.platforms.map(p => p.name).join(', ') : '-';
        const badgeColor = c.status_color || '#6b7280';

        lstBody.innerHTML += `
            <tr>
                <td>
                    <strong>${c.title}</strong>
                    ${c.campaign_name ? `<br><small class="text-primary fw-semibold"><i class="bi bi-megaphone"></i> ${c.campaign_name}</small>` : ''}
                </td>
                <td>${platformNames || '-'}</td>
                <td>${c.type_name || '-'}</td>
                <td>${c.planned_date}</td>
                <td><span class="badge" style="background-color: ${badgeColor}">${c.status_name || 'Draft'}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary rounded-3" onclick="openDetail(${c.id})">Detail</button>
                    <button class="btn btn-sm btn-outline-danger rounded-3" onclick="deleteContent(${c.id})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
    });
}

function toggleView(view) {
    document.getElementById('tog-cal').classList.remove('active');
    document.getElementById('tog-lst').classList.remove('active');
    document.getElementById(`tog-${view}`).classList.add('active');
    
    document.getElementById('v-cal').style.display = view === 'cal' ? 'block' : 'none';
    document.getElementById('v-lst').style.display = view === 'lst' ? 'block' : 'none';
}

function handleCellClick(dateStr) {
    if(event.target.closest('.cp-epill')) return;
    openAdd(dateStr);
}

function openAdd(dateStr) {
    document.getElementById('f-id').value = '';
    document.getElementById('f-judul').value = '';
    document.getElementById('f-desk').value = '';
    document.getElementById('f-tgl').value = dateStr || '';
    if (document.getElementById('f-campaign')) {
        document.getElementById('f-campaign').value = '';
    }
    document.getElementById('mf-title').innerText = 'Tambah Konten Baru';
    
    document.getElementById('m-form').style.display = 'flex';
}

function openDetail(id, e) {
    if (e) e.stopPropagation();
    
    const c = allContents.find(x => x.id == id);
    if (!c) return;
    
    document.getElementById('m-det-title').innerText = c.title;
    document.getElementById('m-det-sub').innerText = 'Tanggal Rencana: ' + c.planned_date;
    
    const platformNames = Array.isArray(c.platforms) ? c.platforms.map(p => p.name).join(', ') : '-';
    
    let html = `
        <div style="margin-bottom:12px">
            <span class="badge" style="background-color:${c.status_color || '#6b7280'};font-size:12px">${c.status_name || 'Draft'}</span>
            ${c.campaign_name ? `<span class="badge bg-primary text-white ms-1"><i class="bi bi-megaphone"></i> ${c.campaign_name}</span>` : ''}
        </div>
        <p><strong>Deskripsi / Caption:</strong><br>${c.description || '-'}</p>
        <p><strong>Platform:</strong> ${platformNames || '-'}</p>
        <p><strong>Content Type:</strong> ${c.type_name || '-'}</p>
        <p><strong>Content Format:</strong> ${c.format_name || '-'}</p>
        <p><strong>Content Pillar:</strong> ${c.pillar_name || '-'}</p>
        <p><strong>Assigned To:</strong> ${c.assigned_user_name || 'Unassigned'}</p>
    `;
    
    document.getElementById('m-det-body').innerHTML = html;
    document.getElementById('m-detail').style.display = 'flex';
}

function deleteContent(id) {
    if (!confirm('Hapus konten ini dari kalender?')) return;

    fetch(`${baseUrl}content-plan/delete/${id}`, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            fetchContents();
            fetchStats();
            showToast(res.message);
        } else {
            alert('Gagal menghapus konten');
        }
    });
}

function cls(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function saveContent() {
    const id = document.getElementById('f-id').value;
    const data = new FormData();
    data.append('title', document.getElementById('f-judul').value);
    data.append('description', document.getElementById('f-desk').value);
    data.append('planned_date', document.getElementById('f-tgl').value);
    
    const campId = document.getElementById('f-campaign') ? document.getElementById('f-campaign').value : '';
    if (campId) {
        data.append('campaign_id', campId);
    }

    const typeId = document.getElementById('f-type').value;
    const jenisId = document.getElementById('f-jenis').value;
    
    data.append('content_type_id', typeId || 1);
    data.append('content_format_id', jenisId || 1);
    data.append('content_pillar_id', 1);
    
    const platformCheckboxes = document.querySelectorAll('input[name="platforms[]"]:checked');
    platformCheckboxes.forEach(cb => {
        data.append('platforms[]', cb.value);
    });
    
    const url = id ? `${baseUrl}content-plan/update/${id}` : `${baseUrl}content-plan/store`;
    
    fetch(url, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            cls('m-form');
            fetchContents();
            fetchStats();
            showToast(res.message);
        } else {
            alert('Error: ' + JSON.stringify(res.errors));
        }
    })
    .catch(err => console.error(err));
}

function showToast(msg) {
    const t = document.getElementById('cp-toast');
    if (!t) return;
    t.innerText = msg;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3000);
}