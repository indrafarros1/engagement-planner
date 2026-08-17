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
  await page.goto(`${BASE}/admin/budget-items`);
  await page.waitForTimeout(2500);

  // render semua button & teks
  const btns = await page.evaluate(() => {
    return Array.from(document.querySelectorAll('button, a'))
      .map(el => (el.textContent || '').trim())
      .filter(t => t.length > 0 && t.length < 50);
  });
  console.log('BUTTONS:', JSON.stringify(btns.slice(0, 30), null, 1));
  await page.screenshot({ path: '/opt/data/projects/engagement-planner/screenshots/39-budget-header.png' });
  await browser.close();
})();