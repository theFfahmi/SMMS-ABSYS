const baseUrl = window.location.origin + '/';
let allAssets = [];
let deleteAssetTargetId = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchAssets();
    setupDropzone();
});

function fetchAssets() {
    fetch(`${baseUrl}asset-library/getAssets`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        allAssets = data;
        renderAssets();
    })
    .catch(err => console.error(err));
}

function renderAssets() {
    const grid = document.getElementById('asset-grid');
    grid.innerHTML = '';

    if (allAssets.length === 0) {
        grid.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-images"></i>
                <h4>Belum ada aset</h4>
                <p>Seret dan lepas file ke sini atau klik tombol Browse Files.</p>
            </div>
        `;
        return;
    }

    allAssets.forEach(asset => {
        const sizeKB = (asset.file_size / 1024).toFixed(1);
        const ext = asset.file_name.split('.').pop().toUpperCase();
        
        let thumbContent = '';
        if (asset.file_type === 'image') {
            thumbContent = `<img src="${baseUrl}${asset.file_path}" alt="${asset.name}" onerror="this.src='https://placehold.co/400x300?text=File+Not+Found'">`;
        } else if (asset.file_type === 'video') {
            thumbContent = `<div style="text-align:center;color:#6b7280"><i class="bi bi-play-circle-fill" style="font-size:40px"></i><div style="font-size:12px;margin-top:5px;font-weight:bold">VIDEO</div></div>`;
        } else {
            thumbContent = `<div style="text-align:center;color:#6b7280"><i class="bi bi-file-earmark-text-fill" style="font-size:40px"></i><div style="font-size:12px;margin-top:5px;font-weight:bold">DOCUMENT</div></div>`;
        }

        grid.innerHTML += `
            <div class="asset-card" id="asset-${asset.id}">
                <div class="asset-actions">
                    <a href="${baseUrl}${asset.file_path}" target="_blank" class="asset-btn" title="View"><i class="bi bi-eye"></i></a>
                    <button class="asset-btn" onclick="confirmDeleteAsset(${asset.id})" title="Hapus"><i class="bi bi-trash"></i></button>
                </div>
                <div class="asset-thumb">
                    ${thumbContent}
                </div>
                <div class="asset-info">
                    <div class="asset-name" title="${asset.name}">${asset.name}</div>
                    <div class="asset-meta">${ext} &bull; ${sizeKB} KB</div>
                </div>
            </div>
        `;
    });
}

function setupDropzone() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('upload-input');
    
    // Browse button hook
    document.getElementById('btn-upload').addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            uploadFile(e.target.files[0]);
            fileInput.value = ''; // reset
        }
    });

    // Drag events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.add('drag-active');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.remove('drag-active');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            uploadFile(files[0]);
        }
    }, false);
}

function uploadFile(file) {
    const formData = new FormData();
    formData.append('asset', file);
    formData.append('name', file.name);

    showToast(`Mengunggah ${file.name}...`);
    
    fetch(`${baseUrl}asset-library/upload`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            fetchAssets();
            showToast('Aset media berhasil diunggah!');
        } else {
            showToast('Gagal mengunggah: ' + (res.message || 'Unknown error'), true);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Gagal mengunggah karena kesalahan jaringan.', true);
    });
}

function confirmDeleteAsset(id) {
    deleteAssetTargetId = id;
    const asset = allAssets.find(a => a.id == id);
    const name = asset ? asset.name : 'Aset Media';
    
    if (document.getElementById('del-asset-name')) {
        document.getElementById('del-asset-name').innerText = name;
    }
    
    const m = document.getElementById('m-confirm-delete-asset');
    if (m) m.style.display = 'flex';
}

function executeDeleteAsset() {
    if (!deleteAssetTargetId) return;
    
    const card = document.getElementById(`asset-${deleteAssetTargetId}`);
    if (card) card.style.opacity = '0.5';

    fetch(`${baseUrl}asset-library/delete/${deleteAssetTargetId}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(res => {
        const m = document.getElementById('m-confirm-delete-asset');
        if (m) m.style.display = 'none';
        
        if (res.status === 'success') {
            fetchAssets();
            showToast('Aset media berhasil dihapus!');
        } else {
            if (card) card.style.opacity = '1';
            showToast('Gagal menghapus aset.', true);
        }
    })
    .catch(err => {
        const m = document.getElementById('m-confirm-delete-asset');
        if (m) m.style.display = 'none';
        if (card) card.style.opacity = '1';
        showToast('Terjadi kesalahan jaringan.', true);
    });
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
