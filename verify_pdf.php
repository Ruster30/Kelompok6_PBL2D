<?php
require "vendor/autoload.php";
use Illuminate\Support\Facades\Storage;

$path = "proposals/surat-penawaran-ut-veniam-voluptas-est-v1.pdf";
$raw = Storage::disk("public")->get($path);

$count = 0;
$found = false;
$offset = 0;
while (($s = strpos($raw, "stream", $offset)) !== false) {
    $e = strpos($raw, "endstream", $s);
    if ($e === false) break;
    $stream = substr($raw, $s + 6, $e - $s - 6);
    $stream = ltrim($stream, "\r\n");
    $dec = @gzuncompress($stream);
    if ($dec !== false) {
        if (strpos($dec, "PRO-TEST-001") !== false) { $found = true; }
        $count++;
    }
    $offset = $e + 9;
}
echo "Decompressed streams: $count\n";
echo "PRO-TEST-001 in decompressed PDF: " . ($found ? "YES" : "NO") . "\n";
echo "Raw PDF contains /Subtype /Image: " . (strpos($raw, "/Subtype /Image") !== false ? "YES" : "NO") . "\n";
echo "Raw PDF starts with %PDF: " . (strpos($raw, "%PDF-") === 0 ? "YES" : "NO") . "\n";
