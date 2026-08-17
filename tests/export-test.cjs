const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 }, acceptDownloads: true });
  const page = await ctx.newPage();

  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('demo@lamaran.test');
  await page.locator('input[type=password]').first().fill('DemoLamaran2026!');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  // Export budget CSV
  await page.goto(`${BASE}/admin/budget-items`);
  await page.waitForTimeout(2000);
  const [dl1] = await Promise.all([
    page.waitForEvent('download', { timeout: 15000 }),
    page.getByRole('button', { name: /Export CSV Anggaran/i }).first().click(),
  ]);
  const path1 = await dl1.path();
  console.log('Budget CSV downloaded:', dl1.suggestedFilename());

  // Export payments CSV
  await page.goto(`${BASE}/admin/payments`);
  await page.waitForTimeout(2000);
  const [dl2] = await Promise.all([
    page.waitForEvent('download', { timeout: 15000 }),
    page.getByRole('button', { name: /Export CSV Pembayaran/i }).first().click(),
  ]);
  console.log('Payments CSV downloaded:', dl2.suggestedFilename());

  await page.screenshot({ path: `${SHOT_DIR}/09-payments-export.png` });
  await browser.close();
  console.log('EXPORTS OK');
})();