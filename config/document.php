<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Document Numbering Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi penomoran dokumen resmi perusahaan.
    |
    | Format: SEQ/KODE_DOKUMEN-KODE_INSTANSI/BULAN_ROMawi/TAHUN
    | Contoh: 001/SPK-ALPH/VII/2026
    |
    */

    'default_company_code' => env('DOCUMENT_COMPANY_CODE', 'ALPH'),
];