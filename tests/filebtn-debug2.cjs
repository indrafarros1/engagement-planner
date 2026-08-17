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

  // tombol terlihat di header (semua button dengan w>0)
  const btns = await page.evaluate(() => {
    const out = [];
    document.querySelectorAll('button').forEach(el => {
      const r = el.getBoundingClientRect();
      const t = (el.textContent || '').trim();
      if (r.width > 40 && r.height > 20) {
        out.push({ text: t.substring(0, 30), x: Math.round(r.x), y: Math.round(r.y), w: Math.round(r.width), html: el.outerHTML.substring(0, 150) });
      }
    });
    return out;
  });
  btns.forEach(b => console.log(JSON.stringify(b)));

  // Klik tombol "File" (visible)
  const fileBtn = page.getByRole('button', { name: 'File' });
  console.log('File button count:', await fileBtn.count());
  if (await fileBtn.count()) {
    await fileBtn.first().click();
    await page.waitForTimeout(1000);
    console.log('=== after click ===');
    const vis = await page.evaluate(() => {
      return Array.from(document.querySelectorAll('button'))
        .filter(el => /Impor|Template|Export/i.test(el.textContent || '') && el.getBoundingClientRect().width > 40)
        .map(el => el.textContent.trim());
    });
    console.log(JSON.stringify(vis));
  }
  await browser.close();
})();