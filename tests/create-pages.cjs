const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });
  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('owner@engagement.test');
  await page.locator('input[type=password]').first().fill('Owner2026!Dev');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  const createPages = [
    ['/admin/activities/create', 'Kegiatan'],
    ['/admin/budget-items/create', 'Item Anggaran'],
    ['/admin/payments/create', 'Pembayaran'],
    ['/admin/vendors/create', 'Vendor'],
    ['/admin/seserahan-items/create', 'Seserahan'],
    ['/admin/guests/create', 'Tamu'],
    ['/admin/users/create', 'Akun'],
  ];
  for (const [path, name] of createPages) {
    const resp = await page.goto(`${BASE}${path}`);
    await page.waitForTimeout(1500);
    const content = await page.content();
    const ok = resp.status() === 200 && !content.includes('Internal Server Error');
    console.log(`${name} create (${path}): ${resp.status()} ${ok ? 'OK' : '⚠️ERROR'}`);
  }
  await browser.close();
})();