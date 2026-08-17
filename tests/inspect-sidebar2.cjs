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
    // cari elemen yang mengandung teks Dashboard Lamaran di sidebar
    const labels = document.querySelectorAll('.fi-sidebar-item-label');
    const out = [];
    for (const l of labels) {
      const li = l.closest('li');
      const a = l.closest('a');
      out.push({
        text: l.textContent.trim(),
        liClass: li ? String(li.className) : '',
        aClass: a ? String(a.className) : '',
        tag: li ? li.tagName : '',
      });
    }
    return out;
  });
  console.log(JSON.stringify(info, null, 1));
  await browser.close();
})();