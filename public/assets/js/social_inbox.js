const baseUrl = window.location.origin + '/';
let allMessages = [];
let filteredMessages = [];
let currentMsgId = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchData();

    // Event Listeners for filters
    document.getElementById('filter-type').addEventListener('change', applyFilters);
    document.getElementById('filter-platform').addEventListener('change', applyFilters);
    
    // Send reply event
    document.getElementById('btn-send-reply').addEventListener('click', sendReply);
    
    // Input enter event
    document.getElementById('reply-input').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') sendReply();
    });
});

function fetchData() {
    fetch(`${baseUrl}social-inbox/getData`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        allMessages = data;
        applyFilters();
        
        // Refresh active thread if open
        if(currentMsgId) {
            const updatedMsg = allMessages.find(m => m.id == currentMsgId);
            if(updatedMsg) renderThread(updatedMsg);
            else resetThread(); // Deleted
        }
    })
    .catch(err => console.error('Fetch error:', err));
}

function applyFilters() {
    const type = document.getElementById('filter-type').value;
    const platform = document.getElementById('filter-platform').value;

    filteredMessages = allMessages.filter(m => {
        let matchType = type === '' || m.type === type;
        let matchPlatform = platform === '' || m.platform_name.toLowerCase() === platform;
        // Don't show resolved/archived in main list unless we have a specific filter for it (optional)
        return matchType && matchPlatform && m.status !== 'resolved'; 
    });

    renderInboxList();
}

function renderInboxList() {
    const list = document.getElementById('inbox-list');
    list.innerHTML = '';
    
    if (filteredMessages.length === 0) {
        list.innerHTML = '<div style="padding:30px 20px;text-align:center;color:#6b7280;font-size:13px">Tidak ada pesan yang sesuai filter.</div>';
        return;
    }

    filteredMessages.forEach(m => {
        const dateObj = new Date(m.received_at);
        const timeStr = dateObj.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
        
        const isUnread = m.status === 'unread' ? 'unread' : '';
        const isActive = m.id === currentMsgId ? 'active' : '';

        const msgPreview = m.message.replace(/--- \[Replied by Manager\] ---[\s\S]*/, '').trim();

        list.innerHTML += `
            <div class="inbox-item ${isUnread} ${isActive}" onclick="openThread(${m.id})">
                <div class="inbox-item-header">
                    <div class="inbox-sender">${m.sender_name}</div>
                    <div class="inbox-time">${timeStr}</div>
                </div>
                <div class="inbox-preview">${msgPreview}</div>
                <div class="inbox-item-meta">
                    <span class="type-pill type-${m.type}">${m.type}</span>
                    <span class="inbox-platform">
                        <i class="bi ${m.platform_icon}" style="color:${m.platform_color}"></i> ${m.platform_name}
                    </span>
                    ${m.status === 'replied' ? '<i class="bi bi-reply-fill text-success" title="Replied" style="margin-left:auto"></i>' : ''}
                    ${m.status === 'unread' ? '<div style="width:8px;height:8px;background:var(--cp-blue);border-radius:50%;margin-left:auto"></div>' : ''}
                </div>
            </div>
        `;
    });
}

function openThread(id) {
    currentMsgId = id;
    renderInboxList(); // Update active state visually
    
    const msg = allMessages.find(m => m.id == id);
    if(msg) {
        renderThread(msg);
        if(msg.status === 'unread') {
            markAsRead(id);
        }
    }
}

function renderThread(msg) {
    document.getElementById('thread-title').innerText = msg.sender_name + (msg.sender_handle ? ' (' + msg.sender_handle + ')' : '');
    
    // Split message if it contains reply
    const parts = msg.message.split('--- [Replied by Manager] ---');
    const originalMsg = parts[0].trim();
    const replyMsg = parts.length > 1 ? parts[1].trim() : null;

    const dateStr = new Date(msg.received_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'});

    let html = `
        <div class="chat-bubble received">
            ${originalMsg.replace(/\n/g, '<br>')}
            <div class="chat-meta">${dateStr} via ${msg.platform_name}</div>
        </div>
    `;

    if (replyMsg) {
        const replyDateStr = new Date(msg.updated_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'});
        html += `
            <div class="chat-bubble sent">
                ${replyMsg.replace(/\n/g, '<br>')}
                <div class="chat-meta">${replyDateStr}</div>
            </div>
        `;
    }

    document.getElementById('thread-body').innerHTML = html;
    
    // Show footer and update actions
    document.getElementById('thread-footer').style.display = 'flex';
    document.getElementById('btn-archive').setAttribute('onclick', `actionThread('archive', ${msg.id})`);
    document.getElementById('btn-delete').setAttribute('onclick', `actionThread('delete', ${msg.id})`);
}

function resetThread() {
    currentMsgId = null;
    document.getElementById('thread-title').innerText = 'Pilih percakapan';
    document.getElementById('thread-body').innerHTML = `
        <div class="inbox-empty">
            <i class="bi bi-chat-square-text"></i>
            <h4>Pilih percakapan untuk memulai</h4>
            <p>Klik salah satu pesan dari daftar di samping</p>
        </div>
    `;
    document.getElementById('thread-footer').style.display = 'none';
}

function markAsRead(id) {
    fetch(`${baseUrl}social-inbox/markAsRead/${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(() => fetchData());
}

function actionThread(action, id) {
    let confirmMsg = action === 'delete' ? 'Hapus pesan ini secara permanen?' : 'Tandai selesai (Archive) pesan ini?';
    if(!confirm(confirmMsg)) return;

    fetch(`${baseUrl}social-inbox/${action}/${id}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            showToast(res.message);
            resetThread();
            fetchData();
        } else alert('Gagal memproses aksi');
    });
}

function sendReply() {
    if(!currentMsgId) return;
    
    const input = document.getElementById('reply-input');
    const text = input.value.trim();
    if(!text) return;

    const formData = new FormData();
    formData.append('reply_text', text);

    fetch(`${baseUrl}social-inbox/reply/${currentMsgId}`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            input.value = '';
            showToast(res.message);
            fetchData(); // will re-render thread
        } else alert('Gagal mengirim balasan');
    });
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
