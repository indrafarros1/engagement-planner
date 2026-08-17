const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 360, height: 740 } });
  const page = await ctx.newPage();

  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('demo@lamaran.test');
  await page.locator('input[type=password]').first().fill('DemoLamaran2026!');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  // Scroll ke tabel kegiatan di mobile
  await page.goto(`${BASE}/admin/activities`);
  await page.waitForTimeout(2500);
  await page.evaluate(() => window.scrollBy(0, 800));
  await page.waitForTimeout(1000);
  await page.screenshot({ path: `${SHOT_DIR}/12-activities-mobile-table.png` });

  // Scroll lebih jauh (status badge)
  await page.evaluate(() => window.scrollBy(0, 600));
  await page.waitForTimeout(1000);
  await page.screenshot({ path: `${SHOT_DIR}/13-activities-mobile-status.png` });

  // Profil acara
  await page.setViewportSize({ width: 1280, height: 900 });
  await page.goto(`${BASE}/admin/event-profiles`);
  await page.waitForTimeout(2500);
  await page.screenshot({ path: `${SHOT_DIR}/14-event-profile-list.png`, fullPage: true });

  await browser.close();
  console.log('DONE');
})();