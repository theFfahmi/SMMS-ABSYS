document.addEventListener('DOMContentLoaded', () => {
    // Initial setup if needed
});

function showAiCampaignModal() {
    document.getElementById('ai-camp-prompt').value = '';
    document.getElementById('ai-camp-loading').style.display = 'none';
    document.getElementById('btn-ai-camp-gen').disabled = false;
    document.getElementById('m-ai-campaign').style.display = 'flex';
}

function generateAiCampaign() {
    const prompt = document.getElementById('ai-camp-prompt').value;
    if (!prompt.trim()) {
        alert('Harap masukkan ide kampanye terlebih dahulu!');
        return;
    }

    document.getElementById('ai-camp-loading').style.display = 'block';
    document.getElementById('btn-ai-camp-gen').disabled = true;

    fetch(`${baseUrl}ai/generateCampaign`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ prompt: prompt })
    })
    .then(res => res.json())
    .then(res => {
        document.getElementById('ai-camp-loading').style.display = 'none';
        document.getElementById('btn-ai-camp-gen').disabled = false;
        
        if (res.status === 'success') {
            cls('m-ai-campaign');
            
            // Auto-fill the form
            document.getElementById('f-camp-name').value = res.data.name;
            document.getElementById('f-camp-desc').value = res.data.description;
            document.getElementById('f-camp-obj').value = res.data.objective;
            document.getElementById('f-camp-aud').value = res.data.target_audience;
            document.getElementById('f-camp-budget').value = res.data.budget;
            
            // Open the form
            openAddCampaign();
            
            // Optional: highlight the fields briefly to show they were auto-filled
            const fields = ['f-camp-name', 'f-camp-desc', 'f-camp-obj', 'f-camp-aud', 'f-camp-budget'];
            fields.forEach(f => {
                const el = document.getElementById(f);
                if (el) {
                    el.style.backgroundColor = '#eff6ff';
                    setTimeout(() => el.style.backgroundColor = '#ffffff', 1500);
                }
            });
            
        } else {
            alert(res.message || 'Gagal merancang campaign');
        }
    })
    .catch(err => {
        console.error(err);
        document.getElementById('ai-camp-loading').style.display = 'none';
        document.getElementById('btn-ai-camp-gen').disabled = false;
        alert('Terjadi kesalahan koneksi saat menghubungi AI.');
    });
}
