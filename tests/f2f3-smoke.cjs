const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  const results = [];

  // login Owner
  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('owner@engagement.test');
  await page.locator('input[type=password]').first().fill('Owner2026!Dev');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  const pages = [
    ['/admin', 'Dashboard'],
    ['/admin/vendors', 'Vendor'],
    ['/admin/vendor-comparison', 'Perbandingan Vendor'],
    ['/admin/seserahan-items', 'Seserahan'],
    ['/admin/guests', 'Tamu & Keluarga'],
    ['/admin/activity-logs', 'Activity Log'],
    ['/admin/users', 'Akun Pengguna'],
    ['/admin/budget-items', 'Item Anggaran'],
    ['/admin/payments', 'Pembayaran'],
    ['/admin/activities', 'Kegiatan'],
  ];

  for (const [path, name] of pages) {
    const resp = await page.goto(`${BASE}${path}`);
    await page.waitForTimeout(1800);
    const status = resp ? resp.status() : 'n/a';
    const hasError = (await page.content()).includes('Internal Server Error');
    results.push(`${name} (${path}): ${status}${hasError ? ' ⚠️ERROR' : ''}`);
  }

  // screenshot halaman baru
  const shots = [
    ['/admin/vendors', '30-vendors'],
    ['/admin/vendor-comparison', '31-vendor-comparison'],
    ['/admin/seserahan-items', '32-seserahan'],
    ['/admin/guests', '33-guests'],
    ['/admin/activity-logs', '34-activity-log'],
    ['/admin/users', '35-users'],
    ['/admin', '36-dashboard-f2f3'],
  ];
  for (const [path, name] of shots) {
    await page.goto(`${BASE}${path}`);
    await page.waitForTimeout(1800);
    await page.screenshot({ path: `${SHOT_DIR}/${name}.png`, fullPage: true });
  }

  console.log(results.join('\n'));
  await browser.close();
})();