function openAddUser() {
    document.getElementById('m-add-user').style.display = 'flex';
}

function cls(id) {
    document.getElementById(id).style.display = 'none';
}

function editUser(id) {
    // Show loading state if desired (or just disable buttons)
    
    fetch(`${baseUrl}user-management/getUser/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            const u = res.data;
            document.getElementById('eu-full-name').value = u.full_name;
            document.getElementById('eu-username').value = u.username;
            document.getElementById('eu-email').value = u.email;
            document.getElementById('eu-role').value = u.role;
            document.getElementById('eu-is-active').value = u.is_active;
            
            // Set form action
            document.getElementById('form-edit-user').action = `${baseUrl}user-management/update/${id}`;
            
            // Show modal
            document.getElementById('m-edit-user').style.display = 'flex';
        } else {
            alert(res.message || 'Gagal mengambil data user.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan jaringan.');
    });
}

function confirmDeleteUser(url, username) {
    document.getElementById('del-user-name').innerText = `Apakah Anda yakin ingin menghapus user "@${username}"?`;
    document.getElementById('btn-confirm-delete-action').href = url;
    document.getElementById('m-confirm-delete').style.display = 'flex';
}
