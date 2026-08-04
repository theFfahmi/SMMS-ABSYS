const baseUrl = window.location.origin + '/';
let allBriefs = [];
let deleteTargetId = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchBriefs();

    document.getElementById('briefForm').addEventListener('submit', submitBrief);
});

function fetchBriefs() {
    fetch(`${baseUrl}content-brief/getBriefs`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        allBriefs = data;
        renderBriefs();
        
        // Hide detail view by default
        document.getElementById('empty-state').style.display = 'block';
        document.getElementById('detail-content').style.display = 'none';
        document.getElementById('detail-actions').style.display = 'none';
    })
    .catch(err => console.error(err));
}

function renderBriefs() {
    const list = document.getElementById('brief-list');
    list.innerHTML = '';
    
    document.getElementById('brief-count').innerText = allBriefs.length;

    if (allBriefs.length === 0) {
        list.innerHTML = `<div class="brief-empty" style="padding:20px">Belum ada brief.</div>`;
        return;
    }

    allBriefs.forEach(b => {
        const title = b.contentTitle || 'Unknown Content';
        const date = new Date(b.created_at).toLocaleString('id-ID', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'});
        
        list.innerHTML += `
            <div class="brief-item js-brief-item" onclick="showDetail(${b.id}, this)">
                <div class="brief-item-title">${title}</div>
                <div class="brief-item-date">${date}</div>
            </div>
        `;
    });
}

function showDetail(id, element) {
    // Highlight active item
    document.querySelectorAll('.js-brief-item').forEach(el => el.classList.remove('active'));
    if (element) {
        element.classList.add('active');
    }

    const b = allBriefs.find(x => x.id == id);
    if (!b) return;

    document.getElementById('empty-state').style.display = 'none';
    document.getElementById('detail-content').style.display = 'block';
    document.getElementById('detail-actions').style.display = 'flex';

    document.getElementById('dt-title').textContent = b.contentTitle || 'Unknown Content';
    document.getElementById('dt-objective').innerHTML = (b.objective || '-').replace(/\n/g, '<br>');
    document.getElementById('dt-audience').textContent = b.target_audience || '-';
    document.getElementById('dt-message').textContent = b.key_message || '-';
    document.getElementById('dt-cta').textContent = b.call_to_action || '-';
    document.getElementById('dt-tone').textContent = b.tone || '-';
    
    if (b.reference_url) {
        document.getElementById('dt-url').innerHTML = `<a href="${b.reference_url}" target="_blank" style="color:var(--cp-blue)">${b.reference_url}</a>`;
    } else {
        document.getElementById('dt-url').textContent = '-';
    }
    
    document.getElementById('dt-notes').innerHTML = (b.notes || '-').replace(/\n/g, '<br>');

    // Update action buttons
    document.getElementById('btn-edit').setAttribute('onclick', `openBriefForm(${b.id})`);
    document.getElementById('btn-delete').setAttribute('onclick', `confirmDeleteBrief(${b.id})`);
}

function openBriefForm(id = null) {
    document.getElementById('briefForm').reset();
    document.getElementById('f-brief-id').value = id || '';
    
    // Get used content IDs
    const usedContentIds = allBriefs.map(b => b.content_id ? b.content_id.toString() : null);
    const fbContent = document.getElementById('fb-content');
    
    let currentBrief = null;
    if (id) {
        currentBrief = allBriefs.find(x => x.id == id);
    }

    // Disable options for contents that already have a brief
    for (let opt of fbContent.options) {
        if (!opt.value) continue;
        
        if (usedContentIds.includes(opt.value) && (!currentBrief || currentBrief.content_id != opt.value)) {
            opt.disabled = true;
            if (!opt.text.includes('(Sudah ada brief)')) opt.text += ' (Sudah ada brief)';
        } else {
            opt.disabled = false;
            opt.text = opt.text.replace(' (Sudah ada brief)', '');
        }
    }
    
    if (id) {
        document.getElementById('mb-title').innerText = 'Edit Content Brief';
        if (currentBrief) {
            document.getElementById('fb-content').value = currentBrief.content_id;
            document.getElementById('fb-objective').value = currentBrief.objective;
            document.getElementById('fb-audience').value = currentBrief.target_audience;
            document.getElementById('fb-message').value = currentBrief.key_message;
            document.getElementById('fb-cta').value = currentBrief.call_to_action;
            document.getElementById('fb-tone').value = currentBrief.tone;
            document.getElementById('fb-url').value = currentBrief.reference_url;
            document.getElementById('fb-notes').value = currentBrief.notes;
        }
    } else {
        document.getElementById('mb-title').innerText = 'Buat Content Brief Baru';
    }
    
    opn('m-brief');
}

function submitBrief(e) {
    e.preventDefault();
    const id = document.getElementById('f-brief-id').value;
    const formData = new FormData(document.getElementById('briefForm'));
    const url = id ? `${baseUrl}content-brief/update/${id}` : `${baseUrl}content-brief/store`;

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            cls('m-brief');
            fetchBriefs();
            showToast(res.message || 'Brief berhasil disimpan!');
        } else {
            if (res.errors) {
                const errMsgs = Object.values(res.errors).join(', ');
                showToast('Gagal menyimpan: ' + errMsgs, true);
            } else {
                showToast('Gagal menyimpan brief: ' + (res.message || 'Error'), true);
            }
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Terjadi kesalahan koneksi saat menyimpan.', true);
    });
}

function confirmDeleteBrief(id) {
    deleteTargetId = id;
    const brief = allBriefs.find(b => b.id == id);
    const title = brief ? brief.contentTitle : 'Content Brief';
    
    if (document.getElementById('del-brief-name')) {
        document.getElementById('del-brief-name').innerText = title;
    }
    
    opn('m-confirm-delete-brief');
}

function executeDeleteBrief() {
    if (!deleteTargetId) return;
    
    fetch(`${baseUrl}content-brief/delete/${deleteTargetId}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        cls('m-confirm-delete-brief');
        if (res.status === 'success') {
            fetchBriefs();
            showToast('Brief berhasil dihapus!');
        } else {
            showToast('Gagal menghapus brief.', true);
        }
    })
    .catch(err => {
        cls('m-confirm-delete-brief');
        showToast('Terjadi kesalahan koneksi.', true);
    });
}

function opn(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function cls(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('show');
    document.body.style.overflow = '';
}

function showToast(msg, isError = false) {
    let t = document.getElementById('cp-toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'cp-toast';
        t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;padding:12px 20px;border-radius:12px;color:#fff;font-weight:700;font-size:13.5px;box-shadow:0 10px 30px rgba(0,0,0,0.2);display:none;transition:all 0.3s ease;';
        document.body.appendChild(t);
    }
    
    t.innerText = msg;
    t.style.background = isError ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 'linear-gradient(135deg, #10b981, #059669)';
    t.style.display = 'block';
    
    setTimeout(() => {
        t.style.display = 'none';
    }, 3500);
}

// AI Polish Function
function aiPolish(inputId, contextType) {
    const el = document.getElementById(inputId);
    const text = el.value;
    
    if (!text.trim()) {
        showToast('Isi teks terlebih dahulu sebelum melakukan Polish AI!', true);
        return;
    }

    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    btn.disabled = true;

    fetch(`${baseUrl}ai/polishText`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ text: text, context: contextType })
    })
    .then(res => res.json())
    .then(res => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (res.status === 'success') {
            el.value = res.polished;
            el.style.backgroundColor = '#eff6ff';
            setTimeout(() => el.style.backgroundColor = 'var(--cp-white)', 1000);
            showToast('Teks berhasil dipoles oleh AI!');
        } else {
            showToast(res.message || 'Gagal memoles teks', true);
        }
    })
    .catch(err => {
        console.error(err);
        btn.innerHTML = originalText;
        btn.disabled = false;
        showToast('Terjadi kesalahan koneksi saat menghubungi AI.', true);
    });
}

// AI Caption Generator
let currentDetailId = null;

const originalShowDetail = showDetail;
showDetail = function(id, element) {
    currentDetailId = id;
    
    document.getElementById('ai-caption-result').style.display = 'none';
    document.getElementById('ai-caption-loading').style.display = 'none';
    document.getElementById('btn-ai-caption').style.display = 'inline-block';
    
    originalShowDetail(id, element);
};

function generateAiCaption() {
    if (!currentDetailId) return;

    document.getElementById('btn-ai-caption').style.display = 'none';
    document.getElementById('ai-caption-loading').style.display = 'block';
    document.getElementById('ai-caption-result').style.display = 'none';

    fetch(`${baseUrl}ai/generateCaption`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ content_id: currentDetailId })
    })
    .then(res => res.json())
    .then(res => {
        document.getElementById('ai-caption-loading').style.display = 'none';
        
        if (res.status === 'success') {
            document.getElementById('dt-ai-caption').textContent = res.caption;
            document.getElementById('dt-ai-hashtags').textContent = res.hashtags;
            document.getElementById('ai-caption-result').style.display = 'block';
            document.getElementById('btn-ai-caption').innerHTML = '<i class="bi bi-arrow-clockwise"></i> Regenerate';
            document.getElementById('btn-ai-caption').style.display = 'inline-block';
            showToast('Caption draft berhasil dibuat oleh AI!');
        } else {
            document.getElementById('btn-ai-caption').style.display = 'inline-block';
            showToast(res.message || 'Gagal generate caption', true);
        }
    })
    .catch(err => {
        console.error(err);
        document.getElementById('ai-caption-loading').style.display = 'none';
        document.getElementById('btn-ai-caption').style.display = 'inline-block';
        showToast('Terjadi kesalahan koneksi saat menghubungi AI.', true);
    });
}
