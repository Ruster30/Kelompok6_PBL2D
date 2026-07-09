const { chromium } = require('playwright');
(async () => {
  const html = `<!DOCTYPE html>
<html>
<head>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { width: 1100px; font-family: "DejaVu Sans", sans-serif; background: #f1f5f9; padding: 20px; color: #1e293b; }
  .page { width: 1060px; background: #fff; padding: 30px 35px; border-radius: 8px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
  .header { display: flex; align-items: center; margin-bottom: 14px; }
  .header-left { width: 18%; }
  .logo-box { width: 80px; height: 80px; background: #0f172a; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #14b8a6; font-size: 32px; font-weight: 800; }
  .header-center { width: 50%; text-align: center; }
  .header-center h1 { font-size: 20px; font-weight: 700; letter-spacing: 2px; color: #0f172a; margin-bottom: 2px; }
  .header-center p { font-size: 9px; color: #64748b; }
  .header-right { width: 32%; text-align: right; }
  .icard { display: inline-block; border: 1px solid #e2e8f0; border-radius: 8px; padding: 5px 12px; text-align: left; font-size: 7pt; }
  .icard .r { display: flex; align-items: center; gap: 6px; padding: 1px 0; }
  .icard .d { width: 14px; height: 14px; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 6pt; }
  .icard .l { color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-size: 5pt; }
  .icard .v { font-weight: 700; color: #0f172a; font-size: 7pt; }
  .div1 { border: none; border-top: 1.5px solid #14b8a6; }
  .div2 { border: none; border-top: 1px solid #e2e8f0; margin-bottom: 14px; }

  .sectitle { font-size: 10px; font-weight: 700; padding: 5px 12px; margin-bottom: 8px; border-left: 4px solid #14b8a6; background: #f1f5f9; border-radius: 4px; }

  .grid { display: grid; grid-template-columns: repeat(8, 1fr); gap: 5px; margin-bottom: 14px; }

  .card {
    height: 72px; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
    display: flex; align-items: center; padding: 0 10px 0 14px; gap: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  }
  .iwrap { flex: 0 0 auto; display: flex; align-items: center; justify-content: center; }
  .ibox {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: #fff; font-weight: 700; line-height: 1;
  }
  .tx { display: flex; flex-direction: column; justify-content: center; }
  .tl { font-size: 5.5pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 1px; }
  .tv { font-size: 10pt; font-weight: 700; color: #0f172a; line-height: 1.2; }
  .tvh { font-size: 11pt; font-weight: 800; color: #0d9488; line-height: 1.2; }

  .cgrid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
  .ccard { border: 1px solid #e2e8f0; border-radius: 10px; padding: 6px; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.04); }
  .ccard .ctitle { font-size: 10px; font-weight: 700; margin-bottom: 4px; padding-left: 2px; }
  .cph { height: 95px; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 9px; }

  .label-row { display: flex; justify-content: space-between; font-size: 7px; color: #94a3b8; padding: 0 2px; margin-top: 14px; }
  .ch { color: #10b981; font-weight: 700; }
</style>
</head>
<body>
<div class="page">
  <div class="header">
    <div class="header-left"><div class="logo-box">A</div></div>
    <div class="header-center"><h1>EVENT ANALYTICS REPORT</h1><p>Ringkasan Performa Bisnis &amp; Operasional</p></div>
    <div class="header-right">
      <div class="icard">
        <div class="r"><div class="d" style="background:#14b8a6;">★</div><div><div class="l">Periode Laporan</div><div class="v">Tahun 2026</div></div></div>
        <hr style="border:none;border-top:1px solid #e2e8f0;margin:0;">
        <div class="r"><div class="d" style="background:#0f172a;">●</div><div><div class="l">Tanggal Cetak</div><div class="v">09 Jul 2026</div></div></div>
      </div>
    </div>
  </div>
  <hr class="div1"><hr class="div2">

  <div style="margin-bottom:8px;"><div class="sectitle">◆ Ringkasan Data</div></div>

  <div class="grid">
    <div class="card"><div class="iwrap"><div class="ibox" style="background:#14b8a6;">#</div></div><div class="tx"><div class="tl">Total Event</div><div class="tv">12</div></div></div>
    <div class="card"><div class="iwrap"><div class="ibox" style="background:#f59e0b;">▶</div></div><div class="tx"><div class="tl">Event Berjalan</div><div class="tv">3</div></div></div>
    <div class="card"><div class="iwrap"><div class="ibox" style="background:#10b981;">✓</div></div><div class="tx"><div class="tl">Event Selesai</div><div class="tv">7</div></div></div>
    <div class="card"><div class="iwrap"><div class="ibox" style="background:#6366f1;">◆</div></div><div class="tx"><div class="tl">Total Client</div><div class="tv">24</div></div></div>
    <div class="card"><div class="iwrap"><div class="ibox" style="background:#8b5cf6;">★</div></div><div class="tx"><div class="tl">Total Vendor</div><div class="tv">15</div></div></div>
    <div class="card"><div class="iwrap"><div class="ibox" style="background:#06b6d4;">▣</div></div><div class="tx"><div class="tl">Total Invoice</div><div class="tv">87</div></div></div>
    <div class="card"><div class="iwrap"><div class="ibox" style="background:#0d9488;">⟡</div></div><div class="tx"><div class="tl">Total Pendapatan</div><div class="tvh">Rp 2.45 M</div></div></div>
    <div class="card"><div class="iwrap"><div class="ibox" style="background:#d97706;">✔</div></div><div class="tx"><div class="tl">Pembayaran Lunas</div><div class="tv">65</div></div></div>
  </div>

  <div style="margin-bottom:6px;"><div class="sectitle">◆ Analitik Grafik</div></div>

  <div class="cgrid">
    <div class="ccard"><div class="ctitle">● Pendapatan per Bulan</div><div class="cph">Line Chart</div></div>
    <div class="ccard"><div class="ctitle">● Event per Bulan</div><div class="cph">Bar Chart</div></div>
    <div class="ccard"><div class="ctitle">● Status Event</div><div class="cph">Pie Chart</div></div>
    <div class="ccard"><div class="ctitle">● Jenis Event</div><div class="cph">Donut Chart</div></div>
  </div>

  <div class="label-row">
    <span><span class="ch">✓</span> Icon 48px flex + line-height:1</span>
    <span>Gap 12px icon -- teks</span>
    <span>Padding kiri 14px</span>
  </div>
</div>
</body>
</html>`;

  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1150, height: 850 } });
  await page.setContent(html, { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'D:\\Kelompok6_PBL2D\\preview-proposed-layout.png', fullPage: true });
  await browser.close();
  console.log('Done');
})();
