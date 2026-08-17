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
  await page.waitForTimeout(2000);

  // klik tombol File
  const fileBtn = page.locator('button').filter({ hasText: /^File/ }).first();
  console.log('File btn count:', await page.locator('button').filter({ hasText: /^File/ }).count());
  await fileBtn.click();
  await page.waitForTimeout(1200);

  // cek elemen menu yang muncul
  const menus = await page.evaluate(() => {
    return Array.from(document.querySelectorAll('button, [role="menuitem"], a'))
      .filter(el => el.textContent && el.textContent.trim().length > 0 && el.offsetParent !== null)
      .map(el => el.tagName + ':' + el.textContent.trim().substring(0, 40))
      .filter(t => /Impor|Export|Template|File/i.test(t))
      .slice(0, 20);
  });
  console.log('Menu items:', JSON.stringify(menus));

  // coba klik langsung apa yang terlihat bertulisan Impor
  const impor = page.locator('text=/Impor Buku/i').first();
  if (await impor.count()) {
    console.log('Impor found, clicking');
    await impor.click();
    await page.waitForTimeout(2000);
  }
  await browser.close();
})();