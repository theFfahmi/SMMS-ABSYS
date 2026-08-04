const baseUrl = window.location.origin + '/';
let allIdeas = [];

document.addEventListener('DOMContentLoaded', () => {
    fetchIdeas();

    document.getElementById('f-prio').addEventListener('change', renderIdeas);
    document.getElementById('f-assign').addEventListener('change', renderIdeas);
    document.getElementById('search-idea').addEventListener('keyup', renderIdeas);

    document.getElementById('convertForm').addEventListener('submit', submitConvert);
    document.getElementById('ideaForm').addEventListener('submit', submitIdea);
});

function fetchIdeas() {
    fetch(`${baseUrl}content-ideas/getIdeas`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        allIdeas = data;
        updateStats();
        renderIdeas();
    })
    .catch(err => console.error(err));
}

function updateStats() {
    const total = allIdeas.length;
    const urgent = allIdeas.filter(i => ['urgent', 'high'].includes(i.priority)).length;
    
    // session()->get('user_id') would be needed for myTasks. Let's get it from a global JS var injected in view
    const myId = window.currentUserId || '1';
    const myTasks = allIdeas.filter(i => i.assigned_to == myId).length;
    const converted = allIdeas.filter(i => i.status === 'converted').length;

    document.getElementById('s-total').innerText = total;
    document.getElementById('s-urgent').innerText = urgent;
    document.getElementById('s-my').innerText = myTasks;
    document.getElementById('s-conv').innerText = converted;
}

function renderIdeas() {
    const container = document.getElementById('idea-container');
    container.innerHTML = '';

    const q = document.getElementById('search-idea').value.toLowerCase();
    const prio = document.getElementById('f-prio').value;
    const assign = document.getElementById('f-assign').value;
    const myId = window.currentUserId || '1';

    let filtered = allIdeas;

    if (q) filtered = filtered.filter(i => i.title.toLowerCase().includes(q));
    if (prio) filtered = filtered.filter(i => i.priority === prio);
    if (assign === 'me') filtered = filtered.filter(i => i.assigned_to == myId);

    if (filtered.length === 0) {
        container.innerHTML = `
            <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--cp-muted)">
                <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>Tidak ada ide konten.
            </div>`;
        return;
    }

    filtered.forEach(item => {
        const isConverted = item.status === 'converted';
        const pillarName = item.pillar_name || 'General';
        const typeName = item.type_name || 'Post';
        const assignName = item.assign_name || 'Unassigned';
        const initial = assignName.charAt(0).toUpperCase();

        const btnHtml = isConverted
            ? `<span class="cpb cpb-suc cpb-sm"><i class="bi bi-check-circle-fill"></i> Converted</span>`
            : `<button class="cpb cpb-pri cpb-sm" onclick="openConvert(${item.id}, '${item.title.replace(/'/g, "\\'")}')"><i class="bi bi-arrow-repeat"></i> Convert</button>`;

        container.innerHTML += `
            <div class="idea-card">
                <div>
                    <div class="idea-header">
                        <div class="idea-title">${item.title}</div>
                        <span class="prio-badge prio-${item.priority}">${item.priority}</span>
                    </div>
                    <div class="idea-desc">${item.description || ''}</div>
                    <div class="idea-tags">
                        <span class="idea-tag"><i class="bi bi-tag-fill"></i> ${pillarName}</span>
                        <span class="idea-tag"><i class="bi bi-layers-fill"></i> ${typeName}</span>
                    </div>
                </div>
                <div class="idea-footer">
                    <div style="display:flex;align-items:center;gap:6px">
                        <div class="user-avatar-sm">${initial}</div>
                        <span style="font-size:12px;color:var(--cp-muted);font-weight:600">${assignName}</span>
                    </div>
                    <div style="display:flex;gap:4px">
                        <button class="cpb cpb-out cpb-sm" title="Edit" onclick="openIdeaForm(${item.id})"><i class="bi bi-pencil"></i></button>
                        <button class="cpb cpb-dan cpb-sm" title="Delete" onclick="deleteIdea(${item.id})"><i class="bi bi-trash"></i></button>
                        ${btnHtml}
                    </div>
                </div>
            </div>
        `;
    });
}

function openIdeaForm(id = null) {
    document.getElementById('ideaForm').reset();
    document.getElementById('f-idea-id').value = id || '';
    
    if (id) {
        document.getElementById('mi-title').innerText = 'Edit Ide Konten';
        const idea = allIdeas.find(i => i.id == id);
        if (idea) {
            document.getElementById('fi-title').value = idea.title;
            document.getElementById('fi-desc').value = idea.description;
            document.getElementById('fi-pillar').value = idea.content_pillar_id;
            document.getElementById('fi-type').value = idea.content_type_id;
            document.getElementById('fi-format').value = idea.content_format_id;
            document.getElementById('fi-prio').value = idea.priority;
            document.getElementById('fi-assign').value = idea.assigned_to;
        }
    } else {
        document.getElementById('mi-title').innerText = 'Tambah Ide Baru';
    }
    
    opn('m-idea');
}

function submitIdea(e) {
    e.preventDefault();
    const id = document.getElementById('f-idea-id').value;
    const formData = new FormData(document.getElementById('ideaForm'));
    const url = id ? `${baseUrl}content-ideas/update/${id}` : `${baseUrl}content-ideas/store`;

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            cls('m-idea');
            fetchIdeas();
            showToast(res.message);
        } else {
            alert('Error: ' + JSON.stringify(res.errors));
        }
    });
}

function openConvert(id, title) {
    document.getElementById('mc-id').value = id;
    document.getElementById('mc-title').value = title;
    opn('m-convert');
}

function submitConvert(e) {
    e.preventDefault();
    const id = document.getElementById('mc-id').value;
    const formData = new FormData(document.getElementById('convertForm'));
    const url = `${baseUrl}content-ideas/convertToContent/${id}`;

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            cls('m-convert');
            fetchIdeas();
            showToast(res.message);
        } else {
            alert(res.message);
        }
    });
}

function deleteIdea(id) {
    if (!confirm('Hapus ide ini?')) return;
    
    fetch(`${baseUrl}content-ideas/delete/${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            fetchIdeas();
            showToast(res.message);
        }
    });
}

function opn(id) { document.getElementById(id).classList.add('show'); document.body.style.overflow = 'hidden'; }
function cls(id) { document.getElementById(id).classList.remove('show'); document.body.style.overflow = ''; }

function showToast(msg) {
    // If you have a toast element globally, use it. Otherwise, alert for now.
    const t = document.getElementById('cp-toast');
    if (t) {
        t.innerText = msg;
        t.style.display = 'block';
        setTimeout(() => t.style.display = 'none', 3000);
    } else {
        alert(msg);
    }
}

// AI Integration Functions
function showAiModal() {
    document.getElementById('ai-prompt').value = '';
    document.getElementById('ai-loading').style.display = 'none';
    document.getElementById('btn-ai-gen').disabled = false;
    opn('m-ai');
}

function generateAiIdeas() {
    const prompt = document.getElementById('ai-prompt').value;
    if (!prompt.trim()) {
        alert('Harap masukkan topik terlebih dahulu!');
        return;
    }

    document.getElementById('ai-loading').style.display = 'block';
    document.getElementById('btn-ai-gen').disabled = true;

    fetch(`${baseUrl}ai/generateIdeas`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ prompt: prompt })
    })
    .then(res => res.json())
    .then(res => {
        document.getElementById('ai-loading').style.display = 'none';
        document.getElementById('btn-ai-gen').disabled = false;
        
        if (res.status === 'success') {
            cls('m-ai');
            showToast('AI berhasil menemukan ide! Menyimpan...');
            
            // Loop through ideas and save them via store endpoint
            let promises = res.data.map(idea => {
                let fd = new FormData();
                fd.append('title', idea.title);
                fd.append('description', idea.description);
                fd.append('priority', 'medium');
                fd.append('content_pillar_id', 1); // default
                fd.append('content_type_id', 1);   // default
                fd.append('content_format_id', 1); // default
                fd.append('assigned_to', window.currentUserId || 1);
                
                return fetch(`${baseUrl}content-ideas/store`, {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
            });

            Promise.all(promises).then(() => {
                fetchIdeas(); // Reload ideas
            });
            
        } else {
            alert(res.message || 'Gagal menghasilkan ide');
        }
    })
    .catch(err => {
        console.error(err);
        document.getElementById('ai-loading').style.display = 'none';
        document.getElementById('btn-ai-gen').disabled = false;
        alert('Terjadi kesalahan koneksi saat menghubungi AI.');
    });
}