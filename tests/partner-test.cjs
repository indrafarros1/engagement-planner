const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });

  // ===== PARTNER (tanpa akses nominal) =====
  const page = await ctx.newPage();
  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('partner@lamaran.test');
  await page.locator('input[type=password]').first().fill('PartnerLamaran2026!');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);
  console.log('Partner login URL:', page.url());

  const menu = await page.evaluate(() => {
    return Array.from(document.querySelectorAll('.fi-sidebar-item-label, [class*=sidebar-item-label]'))
      .map(el => el.textContent.trim()).filter(t => t.length > 0);
  });
  console.log('Partner menu:', JSON.stringify(menu));

  await page.screenshot({ path: `${SHOT_DIR}/40-partner-dashboard.png`, fullPage: true });
  const pageContent = await page.content();
  console.log('Partner Lihat Rp di dashboard:', pageContent.includes('Rp 2'));
  console.log('Partner Lihat pesan disembunyikan:', pageContent.includes('Nominal anggaran disembunyikan'));

  // coba akses budget langsung (harus 403/redirect karena policy)
  const resp = await page.goto(`${BASE}/admin/budget-items`);
  await page.waitForTimeout(2000);
  console.log('Partner akses /budget-items:', resp ? resp.status() : 'n/a');

  await browser.close();
})();