const { chromium } = require('playwright');
const TUN = 'https://joins-amongst-candles-operator.trycloudflare.com';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();

  // Owner login melalui TUNNEL publik
  await page.goto(`${TUN}/admin/login`);
  await page.waitForTimeout(1500);
  await page.getByLabel('Email').fill('owner@engagement.test');
  await page.locator('input[type=password]').first().fill('Owner2026!Dev');
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(4000);
  const url = page.url();
  console.log('URL setelah login:', url);
  const ok = url.includes('/admin') && !url.includes('login');
  console.log('LOGIN OWNER OK:', ok);
  if (ok) {
    await page.screenshot({ path: `${SHOT_DIR}/15-owner-login-via-tunnel.png` });
    const title = await page.title();
    console.log('title:', title);
  }
  await browser.close();
})();