{{--
    Komponen SweetAlert2 Helper Global
    ════════════════════════════════════════════════════════════════
    Menyediakan helper JS terpusat untuk semua konfirmasi aksi penting.
    Komponen ini sudah meng-include CDN SweetAlert2 (dengan pengecekan
    agar tidak duplikat dengan x-logout-confirmation).

    Cara pakai di layout:
        <x-swal-helper />      ← cukup satu kali, sebelum </body>

    Fungsi yang tersedia:
    ─────────────────────────────────────────────────────────────────
    swalForm(formEl, opts)
        Konfirmasi sebelum submit form. formEl = elemen <form>.

    swalAction(callback, opts)
        Konfirmasi sebelum menjalankan fungsi JS.

    ─── Shortcut Presets ───
    swalDelete(formEl, opts?)          → Hapus Data  (merah)
    swalSend(formEl, title?, text?)    → Kirim       (teal)
    swalApprove(formEl, title?, text?) → Setujui     (hijau)
    swalReject(formEl, title?, text?)  → Tolak       (oranye)
    swalSave(callbackFn, title?, text?)→ Simpan      (biru)
    swalVerify(formEl, title?, text?)  → Verifikasi  (teal)
    swalPublish(formEl, title?, text?) → Publish     (ungu)
    swalReset(callbackFn, title?, text?) → Reset     (merah)

    Contoh penggunaan di template:
        <form onsubmit="return swalDelete(this)"> ... </form>
        <form onsubmit="return swalSend(this, 'Kirim Invoice?', 'Invoice akan dikirim ke client.')"> ... </form>
        <button onclick="swalApprove(document.getElementById('form-approve'))">Setujui</button>
        <button onclick="swalSave(() => submitEditForm())">Simpan</button>
--}}

{{-- SweetAlert2 CDN (hanya load jika belum ada) --}}
<script>
if (typeof Swal === 'undefined') {
    (function() {
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        s.async = false;
        document.head.appendChild(s);
    })();
}
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script>
/* ═══════════════════════════════════════════════════════════════
   swalForm(formEl, opts)
   Tampilkan konfirmasi SweetAlert2 lalu submit form jika dikonfirmasi.
   Mengembalikan false selalu (agar onsubmit tidak langsung submit).
═══════════════════════════════════════════════════════════════ */
function swalForm(formEl, opts) {
    opts = Object.assign({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin?',
        icon: 'question',
        confirmText: 'Ya',
        cancelText: 'Batal',
        confirmColor: '#14b8a6',
        cancelColor: '#6b7280',
    }, opts || {});

    Swal.fire({
        title: opts.title,
        text: opts.text,
        icon: opts.icon,
        showCancelButton: true,
        confirmButtonColor: opts.confirmColor,
        cancelButtonColor: opts.cancelColor,
        confirmButtonText: opts.confirmText,
        cancelButtonText: opts.cancelText,
        reverseButtons: true,
        customClass: { popup: 'swal-alpha-popup' }
    }).then(function(result) {
        if (result.isConfirmed) {
            // Nonaktifkan sementara agar tidak trigger onsubmit lagi
            var origOnsubmit = formEl.onsubmit;
            formEl.onsubmit = null;
            formEl.submit();
            formEl.onsubmit = origOnsubmit;
        }
    });

    return false; // cegah submit langsung
}

/* ═══════════════════════════════════════════════════════════════
   swalAction(callback, opts)
   Tampilkan konfirmasi SweetAlert2 lalu jalankan callback JS.
═══════════════════════════════════════════════════════════════ */
function swalAction(callback, opts) {
    opts = Object.assign({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin?',
        icon: 'question',
        confirmText: 'Ya',
        cancelText: 'Batal',
        confirmColor: '#14b8a6',
        cancelColor: '#6b7280',
    }, opts || {});

    Swal.fire({
        title: opts.title,
        text: opts.text,
        icon: opts.icon,
        showCancelButton: true,
        confirmButtonColor: opts.confirmColor,
        cancelButtonColor: opts.cancelColor,
        confirmButtonText: opts.confirmText,
        cancelButtonText: opts.cancelText,
        reverseButtons: true,
        customClass: { popup: 'swal-alpha-popup' }
    }).then(function(result) {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}

/* ═══════════════════════════════════════════════════════════════
   PRESET SHORTCUTS
═══════════════════════════════════════════════════════════════ */

/** Hapus Data → warning merah */
function swalDelete(formEl, opts) {
    return swalForm(formEl, Object.assign({
        title: 'Hapus Data?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        confirmText: 'Ya, Hapus',
        confirmColor: '#ef4444',
    }, opts || {}));
}

/** Kirim / Send → question teal */
function swalSend(formEl, title, text) {
    return swalForm(formEl, {
        title: title || 'Kirim Data?',
        text:  text  || 'Data akan dikirim.',
        icon: 'question',
        confirmText: 'Ya, Kirim',
        confirmColor: '#14b8a6',
    });
}

/** Setujui / Approve → question hijau */
function swalApprove(formEl, title, text) {
    return swalForm(formEl, {
        title: title || 'Setujui?',
        text:  text  || 'Tindakan ini tidak dapat dibatalkan.',
        icon: 'question',
        confirmText: 'Ya, Setujui',
        confirmColor: '#16a34a',
    });
}

/** Tolak / Reject → warning oranye */
function swalReject(formEl, title, text) {
    return swalForm(formEl, {
        title: title || 'Tolak?',
        text:  text  || 'Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        confirmText: 'Ya, Tolak',
        confirmColor: '#f59e0b',
    });
}

/** Simpan / Save → question biru */
function swalSave(callbackFn, title, text) {
    return swalAction(callbackFn, {
        title: title || 'Simpan Perubahan?',
        text:  text  || 'Perubahan akan disimpan.',
        icon: 'question',
        confirmText: 'Ya, Simpan',
        confirmColor: '#3b82f6',
    });
}

/** Verifikasi → question teal */
function swalVerify(formEl, title, text) {
    return swalForm(formEl, {
        title: title || 'Verifikasi?',
        text:  text  || 'Data akan diverifikasi.',
        icon: 'question',
        confirmText: 'Ya, Verifikasi',
        confirmColor: '#14b8a6',
    });
}

/** Publish → question ungu */
function swalPublish(formEl, title, text) {
    return swalForm(formEl, {
        title: title || 'Publikasikan?',
        text:  text  || 'Data akan dipublikasikan.',
        icon: 'question',
        confirmText: 'Ya, Publish',
        confirmColor: '#8b5cf6',
    });
}

/** Reset → warning merah */
function swalReset(callbackFn, title, text) {
    return swalAction(callbackFn, {
        title: title || 'Reset Data?',
        text:  text  || 'Data akan direset. Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        confirmText: 'Ya, Reset',
        confirmColor: '#ef4444',
    });
}

/** Generate → question ungu */
function swalGenerate(formEl, title, text) {
    return swalForm(formEl, {
        title: title || 'Generate Dokumen?',
        text:  text  || 'Dokumen akan di-generate.',
        icon: 'question',
        confirmText: 'Ya, Generate',
        confirmColor: '#8b5cf6',
    });
}

/** Logout → warning merah */
function swalLogout(formEl, title, text) {
    return swalForm(formEl, {
        title: title || 'Keluar dari Sistem?',
        text:  text  || 'Anda akan keluar dari akun saat ini.',
        icon: 'warning',
        confirmText: 'Ya, Keluar',
        confirmColor: '#dc2626',
    });
}
</script>

<style>
.swal-alpha-popup {
    border-radius: 16px !important;
    font-family: inherit !important;
    padding-bottom: 28px !important;
}
.swal2-title {
    font-size: 1.2rem !important;
    font-weight: 700 !important;
}
.swal2-html-container,
.swal2-content {
    font-size: 0.95rem !important;
    color: #64748b !important;
}
.swal2-confirm, .swal2-cancel {
    border-radius: 8px !important;
    padding: 10px 24px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
}
.swal2-actions {
    gap: 10px !important;
}
</style>
