const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });
  await page.goto('http://127.0.0.1:8020/admin/login');
  await page.getByLabel('Email').fill('demo@lamaran.test');
  await page.locator('input[type=password]').first().fill('DemoLamaran2026!');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  const info = await page.evaluate(() => {
    const items = document.querySelectorAll('[class*="sidebar-item"]');
    const out = [];
    for (const el of items) {
      out.push({
        cls: String(el.className || '').substring(0, 110),
        text: (el.textContent || '').trim().substring(0, 30),
        tag: el.tagName,
      });
    }
    return out.slice(0, 12);
  });
  console.log('ITEMS:', JSON.stringify(info, null, 1));

  const active = await page.evaluate(() => {
    const a = document.querySelector('[class*="sidebar-item-active"]');
    if (!a) return 'no active found';
    const cs = getComputedStyle(a);
    return { tag: a.tagName, cls: String(a.className || '').substring(0, 160), bg: cs.backgroundColor, color: cs.color };
  });
  console.log('ACTIVE:', JSON.stringify(active, null, 1));

  await browser.close();
})();