{{--
    Komponen Konfirmasi Logout (SweetAlert2)
    Digunakan bersama oleh semua role: Admin, Vendor, Client.

    Cara pakai:
    1. Tambahkan <x-logout-confirmation /> di layout sebelum </body>
    2. Pada tombol/link logout, ganti type="submit" / onclick menjadi:
       onclick="confirmLogout(event)"
    3. Pastikan form logout memiliki id="logout-form"
--}}

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmLogout(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Konfirmasi Keluar',
            text: 'Apakah Anda yakin ingin keluar dari akun ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'swal-logout-popup',
                title: 'swal-logout-title',
                confirmButton: 'swal-logout-confirm',
                cancelButton: 'swal-logout-cancel'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>

<style>
    .swal-logout-popup {
        border-radius: 16px !important;
        font-family: inherit;
    }
    .swal-logout-title {
        font-size: 20px !important;
        font-weight: 700 !important;
    }
    .swal-logout-confirm {
        border-radius: 8px !important;
        padding: 10px 24px !important;
        font-weight: 600 !important;
    }
    .swal-logout-cancel {
        border-radius: 8px !important;
        padding: 10px 24px !important;
        font-weight: 600 !important;
    }
</style>
