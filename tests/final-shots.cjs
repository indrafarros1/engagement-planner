const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();

  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('owner@engagement.test');
  await page.locator('input[type=password]').first().fill('Owner2026!Dev');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  const shots = [
    ['/admin', 'FINAL-dashboard'],
    ['/admin/vendors', 'FINAL-vendors'],
    ['/admin/vendor-comparison', 'FINAL-perbandingan'],
    ['/admin/seserahan-items', 'FINAL-seserahan'],
    ['/admin/guests', 'FINAL-tamu'],
    ['/admin/activity-logs', 'FINAL-activity-log'],
    ['/admin/users', 'FINAL-akun'],
    ['/admin/activities', 'FINAL-kegiatan'],
    ['/admin/budget-items', 'FINAL-anggaran'],
    ['/admin/payments', 'FINAL-pembayaran'],
  ];
  for (const [path, name] of shots) {
    await page.goto(`${BASE}${path}`);
    await page.waitForTimeout(1800);
    await page.screenshot({ path: `${SHOT_DIR}/${name}.png`, fullPage: true });
    console.log('shot', name);
  }

  // mobile dashboard + kegiatan
  const mpage = await ctx.newPage();
  await mpage.setViewportSize({ width: 360, height: 740 });
  await mpage.goto(`${BASE}/admin`);
  await mpage.waitForTimeout(2500);
  await mpage.screenshot({ path: `${SHOT_DIR}/FINAL-dashboard-mobile.png` });
  await mpage.goto(`${BASE}/admin/seserahan-items`);
  await mpage.waitForTimeout(2000);
  await mpage.screenshot({ path: `${SHOT_DIR}/FINAL-seserahan-mobile.png` });

  await browser.close();
  console.log('ALL FINAL SHOTS DONE');
})();