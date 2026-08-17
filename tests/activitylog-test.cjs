const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();

  // Owner — buat kegiatan baru (untuk menguji activity log)
  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('owner@engagement.test');
  await page.locator('input[type=password]').first().fill('Owner2026!Dev');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  await page.goto(`${BASE}/admin/activities/create`);
  await page.waitForTimeout(1500);
  await page.getByLabel('Nama Kegiatan').fill('Uji Activity Log QA');
  await page.getByRole('button', { name: 'Buat' }).first().click();
  await page.waitForTimeout(2500);
  console.log('created activity url:', page.url());

  // cek activity log
  await page.goto(`${BASE}/admin/activity-logs`);
  await page.waitForTimeout(2000);
  const body = await page.content();
  console.log('Activity log menampilkan aksi:', body.includes('Uji Activity Log QA'));
  console.log('Ada aksi created:', body.includes('Dibuat'));

  await browser.close();
})();