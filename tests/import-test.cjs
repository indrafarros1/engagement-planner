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

  await page.goto(`${BASE}/admin/budget-items`);
  await page.waitForTimeout(2000);
  await page.screenshot({ path: `${SHOT_DIR}/39-budget-header.png` });

  // klik File grup
  await page.getByRole('button', { name: 'File' }).first().click();
  await page.waitForTimeout(1000);
  // klik Impor Buku.xlsx
  await page.getByRole('button', { name: /Impor Buku\.xlsx/i }).first().click();
  await page.waitForTimeout(1500);
  await page.locator('input[type=file]').first().setInputFiles(`${SHOT_DIR}/Buku-test.xlsx`);
  await page.waitForTimeout(1500);
  await page.screenshot({ path: `${SHOT_DIR}/37-import-modal.png` });

  await page.getByRole('button', { name: /Impor Sekarang/i }).click();
  await page.waitForTimeout(3500);

  const body = await page.content();
  console.log('IMPORT SUCCESS NOTIF:', body.includes('Berhasil impor 4 item anggaran'));
  await page.screenshot({ path: `${SHOT_DIR}/38-after-import.png` });
  await browser.close();
})();