import { chromium } from "playwright";
const html = `<!DOCTYPE html>
<html><head><meta charset="utf-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"DejaVu Sans","Helvetica",Arial,sans-serif; background:#f5f6fa; display:flex; justify-content:center; padding:20px; }
.wrapper { width:1060px; background:white; border-radius:8px; padding:24px 36px; box-shadow:0 4px 20px rgba(0,0,0,0.06); }
.header-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:4px; }
.logo-area { display:flex; align-items:center; gap:10px; }
.logo-box { width:60px; height:60px; background:#0f172a; border-radius:14px; display:flex; align-items:center; justify-content:center; color:#14b8a6; font-size:26px; font-weight:800; }
.logo-text { font-size:10px; font-weight:700; color:#0f172a; }
.title-area { text-align:center; flex:1; }
.title-main { font-size:18px; font-weight:800; color:#0f172a; letter-spacing:2px; text-transform:uppercase; }
.title-sub { font-size:9px; color:#64748b; margin-top:2px; }
.info-card { border:1px solid #e2e8f0; border-radius:6px; padding:8px 12px; text-align:left; min-width:170px; }
.info-label { font-size:7px; color:#94a3b8; letter-spacing:1px; text-transform:uppercase; }
.info-value { font-size:9px; font-weight:700; color:#0f172a; }
.info-divider { border:none; border-top:1px solid #e2e8f0; margin:4px 0; }
.header-divider { border:none; border-top:2px solid #14b8a6; margin-top:8px; margin-bottom:0; }
.header-divider2 { border:none; border-top:1px solid #e2e8f0; margin-top:0; }
.stats-row { display:flex; flex-wrap:wrap; gap:8px; margin:16px 0; }
.stat-card { flex:1; min-width:110px; max-width:125px; border:1px solid #e2e8f0; border-radius:8px; padding:10px; display:flex; align-items:center; gap:8px; background:white; box-shadow:0 1px 4px rgba(0,0,0,0.04); }
.stat-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; }
.stat-label { font-size:6.5px; color:#64748b; letter-spacing:0.5px; text-transform:uppercase; }
.stat-value { font-size:11px; font-weight:700; color:#0f172a; line-height:1.2; }
.icon-blue { background:#dbeafe; color:#1e40af; }
.icon-orange { background:#fed7aa; color:#9a3412; }
.icon-green { background:#d1fae5; color:#065f46; }
.icon-purple { background:#e9d5ff; color:#6b21a8; }
.section-title { font-size:10px; font-weight:700; color:#0f172a; padding:5px 10px; border-left:4px solid #14b8a6; background:#f1f5f9; border-radius:4px; margin-bottom:10px; }
.anno { margin-top:20px; padding:14px; background:#fefce8; border:1px dashed #eab308; border-radius:8px; font-size:11px; color:#713f12; line-height:1.6; }
.anno b { color:#dc2626; }
.anno .green { color:#059669; }
</style></head><body>
<div class="wrapper">
<div class="header-row">
<div class="logo-area"><div class="logo-box">A</div><div class="logo-text">ALPHA.CORP</div></div>
<div class="title-area"><div class="title-main">EVENT ANALYTICS REPORT</div><div class="title-sub">Ringkasan Performa Bisnis &amp; Operasional</div></div>
<div class="info-card"><div class="info-label">PERIODE LAPORAN</div><div class="info-value">Tahun 2025</div><hr class="info-divider"/><div class="info-label">TANGGAL CETAK</div><div class="info-value">09 Juli 2025</div></div>
</div>
<hr class="header-divider"/><hr class="header-divider2"/>
<div class="section-title">&#9670; Ringkasan Statistik</div>
<div class="stats-row">
<div class="stat-card"><div class="stat-icon icon-blue">*</div><div><div class="stat-label">Total Event</div><div class="stat-value">48</div></div></div>
<div class="stat-card"><div class="stat-icon icon-orange">*</div><div><div class="stat-label">Event Berjalan</div><div class="stat-value">12</div></div></div>
<div class="stat-card"><div class="stat-icon icon-green">*</div><div><div class="stat-label">Event Selesai</div><div class="stat-value">28</div></div></div>
<div class="stat-card"><div class="stat-icon icon-purple">*</div><div><div class="stat-label">Total Client</div><div class="stat-value">24</div></div></div>
<div class="stat-card"><div class="stat-icon icon-blue">*</div><div><div class="stat-label">Total Vendor</div><div class="stat-value">18</div></div></div>
<div class="stat-card"><div class="stat-icon icon-orange">*</div><div><div class="stat-label">Total Invoice</div><div class="stat-value">36</div></div></div>
<div class="stat-card"><div class="stat-icon icon-green">*</div><div><div class="stat-label">Total Pendapatan</div><div class="stat-value">Rp 2.1 M</div></div></div>
<div class="stat-card"><div class="stat-icon icon-purple">*</div><div><div class="stat-label">Pembayaran Lunas</div><div class="stat-value">30</div></div></div>
</div>

<div class="anno"><b>&#10003; Perbaikan Layout Header PDF:</b><br/><br/>
1. <b>Logo ALPHA</b> -- kiri, <b>sejajar vertikal</b> dengan baris card statistik<br/>
2. <b>Judul</b> -- digeser ke <span class="green">tengah (center)</span><br/>
3. <b>Info card</b> -- kanan, <b>icon dihapus</b>, hanya teks bersih<br/>
4. <b>Semua konten</b> dalam 1 wrapper center (max-width + margin auto)<br/>
5. <b>Header &amp; body</b> alignment konsisten
</div>
</div>
</body></html>`;
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.setViewportSize({ width: 1200, height: 800 });
  await page.setContent(html, { waitUntil: "networkidle" });
  await page.screenshot({ path: "D:/Kelompok6_PBL2D/public/images/pdf-mockup-layout.png", fullPage: true });
  await browser.close();
  console.log("SUCCESS: Screenshot saved!");
})();