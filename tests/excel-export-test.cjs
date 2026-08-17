const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const fs = require('fs');
const os = require('os');
const path = require('path');

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 }, acceptDownloads: true });
  const page = await ctx.newPage();

  await page.goto(`${BASE}/admin/login`);
  await page.getByLabel('Email').fill('owner@engagement.test');
  await page.locator('input[type=password]').first().fill('Owner2026!Dev');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);

  // Export Excel Anggaran
  await page.goto(`${BASE}/admin/budget-items`);
  await page.waitForTimeout(2000);
  await page.getByRole('button', { name: 'File' }).first().click();
  await page.waitForTimeout(800);
  const [dl1] = await Promise.all([
    page.waitForEvent('download', { timeout: 20000 }),
    page.getByRole('button', { name: /Export Excel Anggaran/i }).click(),
  ]);
  const p1 = path.join(os.tmpdir(), dl1.suggestedFilename());
  await dl1.saveAs(p1);
  console.log('Anggaran Excel:', dl1.suggestedFilename(), fs.statSync(p1).size, 'bytes');

  // Export Excel Pembayaran
  await page.goto(`${BASE}/admin/payments`);
  await page.waitForTimeout(2000);
  await page.getByRole('button', { name: 'Export' }).first().click();
  await page.waitForTimeout(800);
  const [dl2] = await Promise.all([
    page.waitForEvent('download', { timeout: 20000 }),
    page.getByRole('button', { name: /Export Excel Pembayaran/i }).click(),
  ]);
  const p2 = path.join(os.tmpdir(), dl2.suggestedFilename());
  await dl2.saveAs(p2);
  console.log('Pembayaran Excel:', dl2.suggestedFilename(), fs.statSync(p2).size, 'bytes');
  console.log('EXPORTS OK');
  await browser.close();
})();