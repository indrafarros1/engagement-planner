const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  const results = [];

  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('demo@lamaran.test');
  await page.locator('input[type=password]').first().fill('DemoLamaran2026!');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  const pages = [
    ['/admin', 'Dashboard'],
    ['/admin/events', 'Event Profil'],
    ['/admin/activities', 'Kegiatan'],
    ['/admin/budget-items', 'Item Anggaran'],
    ['/admin/payments', 'Pembayaran'],
  ];

  for (const [path, name] of pages) {
    const resp = await page.goto(`${BASE}${path === '/admin/events' ? '/admin/event-profiles' : path}`);
    await page.waitForTimeout(2000);
    const status = resp ? resp.status() : 'n/a';
    results.push(`${name} (${path}): ${status}`);
  }

  // Screenshot profil acara
  await page.goto(`${BASE}/admin/event-profiles`);
  await page.waitForTimeout(2000);
  await page.screenshot({ path: `${SHOT_DIR}/10-event-profile.png`, fullPage: true });

  // Uji filter kegiatan: buka filter status
  await page.goto(`${BASE}/admin/activities`);
  await page.waitForTimeout(2000);
  await page.getByRole('button', { name: /filter/i }).first().click();
  await page.waitForTimeout(1000);
  await page.screenshot({ path: `${SHOT_DIR}/11-activities-filter-open.png` });

  console.log(results.join('\n'));
  await browser.close();
})();