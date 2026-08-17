const { chromium } = require('playwright');

const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

const shot = async (page, name, opts = {}) => {
  const path = `${SHOT_DIR}/${name}.png`;
  await page.screenshot({ path, fullPage: opts.fullPage || false });
  console.log('saved', path);
};

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();

  // 1. Login page
  await page.goto(`${BASE}/admin/login`);
  await page.waitForTimeout(1500);
  await shot(page, '01-login-desktop');
  console.log('title:', await page.title());

  // 2. Login
  await page.getByLabel('Email').fill('demo@lamaran.test');
  await page.locator('input[type=password]').first().fill('DemoLamaran2026!');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);
  console.log('after login URL:', page.url());

  // 3. Dashboard
  await page.goto(`${BASE}/admin`);
  await page.waitForTimeout(3000);
  await shot(page, '02-dashboard-desktop', { fullPage: true });

  // 4. Kegiatan list
  await page.goto(`${BASE}/admin/activities`);
  await page.waitForTimeout(2500);
  await shot(page, '03-activities-desktop', { fullPage: true });

  // 5. Budget items
  await page.goto(`${BASE}/admin/budget-items`);
  await page.waitForTimeout(2500);
  await shot(page, '04-budget-desktop', { fullPage: true });

  // 6. Payments
  await page.goto(`${BASE}/admin/payments`);
  await page.waitForTimeout(2500);
  await shot(page, '05-payments-desktop', { fullPage: true });

  // 7. Mobile 360px dashboard
  const mpage = await ctx.newPage();
  await mpage.setViewportSize({ width: 360, height: 740 });
  await mpage.goto(`${BASE}/admin`);
  await mpage.waitForTimeout(3000);
  await shot(mpage, '06-dashboard-mobile');

  // mobile activities
  await mpage.goto(`${BASE}/admin/activities`);
  await mpage.waitForTimeout(2500);
  await shot(mpage, '07-activities-mobile');

  await browser.close();
  console.log('DONE');
})();
