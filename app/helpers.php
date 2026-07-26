<?php

if (! function_exists('terbilang')) {
    function terbilang(float $angka): string {
        $angka  = (int) abs($angka);
        $satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
                    'sepuluh', 'sebelas'];
        if ($angka < 12) return $satuan[$angka];
        if ($angka < 20) return $satuan[$angka - 10] . ' belas';
        if ($angka < 100) return $satuan[(int)($angka / 10)] . ' puluh ' . terbilang($angka % 10);
        if ($angka < 200) return 'seratus ' . terbilang($angka % 100);
        if ($angka < 1000) return $satuan[(int)($angka / 100)] . ' ratus ' . terbilang($angka % 100);
        if ($angka < 2000) return 'seribu ' . terbilang($angka % 1000);
        if ($angka < 1000000) return terbilang((int)($angka / 1000)) . ' ribu ' . terbilang($angka % 1000);
        if ($angka < 1000000000) return terbilang((int)($angka / 1000000)) . ' juta ' . terbilang($angka % 1000000);
        return terbilang((int)($angka / 1000000000)) . ' miliar ' . terbilang($angka % 1000000000);
    }
}