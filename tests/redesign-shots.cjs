const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();

  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('demo@lamaran.test');
  await page.locator('input[type=password]').first().fill('DemoLamaran2026!');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  // Dashboard redesain
  await page.goto(`${BASE}/admin`);
  await page.waitForTimeout(3000);
  await page.screenshot({ path: `${SHOT_DIR}/20-dashboard-redesign.png`, fullPage: true });

  // Kegiatan
  await page.goto(`${BASE}/admin/activities`);
  await page.waitForTimeout(2500);
  await page.screenshot({ path: `${SHOT_DIR}/21-activities-redesign.png`, fullPage: true });

  // Anggaran
  await page.goto(`${BASE}/admin/budget-items`);
  await page.waitForTimeout(2500);
  await page.screenshot({ path: `${SHOT_DIR}/22-budget-redesign.png`, fullPage: true });

  // Pembayaran
  await page.goto(`${BASE}/admin/payments`);
  await page.waitForTimeout(2500);
  await page.screenshot({ path: `${SHOT_DIR}/23-payments-redesign.png`, fullPage: true });

  // Mobile 360
  const mpage = await ctx.newPage();
  await mpage.setViewportSize({ width: 360, height: 740 });
  await mpage.goto(`${BASE}/admin`);
  await mpage.waitForTimeout(3000);
  await mpage.screenshot({ path: `${SHOT_DIR}/24-dashboard-mobile-redesign.png` });

  await browser.close();
  console.log('REDESIGN SHOTS DONE');
})();