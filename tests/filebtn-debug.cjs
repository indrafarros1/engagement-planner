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

  // cari elemen yang mengandung "File" sebagai tombol terlihat
  const info = await page.evaluate(() => {
    const out = [];
    document.querySelectorAll('button, [role=button], a, [role=menuitem], li').forEach(el => {
      const t = (el.textContent || '').trim();
      if (/File|Impor|Export|Template/i.test(t) && t.length < 60) {
        const r = el.getBoundingClientRect();
        out.push({
          tag: el.tagName, role: el.getAttribute('role'), text: t.substring(0, 30),
          visible: r.width > 0 && r.height > 0 && getComputedStyle(el).visibility !== 'hidden',
          x: Math.round(r.x), y: Math.round(r.y), w: Math.round(r.width),
        });
      }
    });
    return out.slice(0, 25);
  });
  console.log(JSON.stringify(info, null, 1));
  await browser.close();
})();