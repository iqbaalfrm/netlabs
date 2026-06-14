/**
 * SweetAlert2 Helper Functions — Netlabs Admin
 */

function confirmDelete(formEl, itemName) {
    Swal.fire({
        title: 'Hapus ' + itemName + '?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            if (typeof formEl === 'string') {
                document.getElementById(formEl).submit();
            } else {
                formEl.submit();
            }
        }
    });
}

function confirmReset(formEl, itemName) {
    Swal.fire({
        title: 'Reset Password?',
        text: 'Password ' + itemName + ' akan direset.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            if (typeof formEl === 'string') {
                document.getElementById(formEl).submit();
            } else {
                formEl.submit();
            }
        }
    });
}

function confirmAction(formEl, title, text, confirmText) {
    Swal.fire({
        title: title || 'Konfirmasi',
        text: text || 'Apakah Anda yakin?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4B49AC',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText || 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            if (typeof formEl === 'string') {
                document.getElementById(formEl).submit();
            } else {
                formEl.submit();
            }
        }
    });
}

function showLoading(title) {
    Swal.fire({
        title: title || 'Memproses...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function submitWithLoading(formEl, title) {
    showLoading(title);
    if (typeof formEl === 'string') {
        document.getElementById(formEl).submit();
    } else {
        formEl.submit();
    }
}
