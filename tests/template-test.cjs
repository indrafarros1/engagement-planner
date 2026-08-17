const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 }, acceptDownloads: true });
  const page = await ctx.newPage();

  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('owner@engagement.test');
  await page.locator('input[type=password]').first().fill('Owner2026!Dev');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  // Buka halaman anggaran
  await page.goto(`${BASE}/admin/budget-items`);
  await page.waitForTimeout(2000);

  // Unduh template Buku.xlsx
  await page.getByRole('button', { name: /File/i }).first().click();
  await page.waitForTimeout(800);
  const [dl] = await Promise.all([
    page.waitForEvent('download', { timeout: 15000 }),
    page.getByRole('button', { name: /Download Template/i }).first().click(),
  ]);
  const templatePath = await dl.path();
  console.log('Template downloaded:', dl.suggestedFilename(), 'at', templatePath);
  await require('fs').copyFile(templatePath, SHOT_DIR + '/Buku-Template.xlsx', ()=>{});
  console.log('Template saved to screenshots/');

  await browser.close();
})();