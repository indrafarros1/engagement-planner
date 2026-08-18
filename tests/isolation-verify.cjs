const { chromium } = require('playwright');
const BASE = 'http://127.0.0.1:8020';
const SHOT_DIR = '/opt/data/projects/engagement-planner/screenshots';

const login = async (browser, email, pass) => {
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(`${BASE}/admin/login`);
  await page.waitForTimeout(1200);
  await page.getByLabel('Email').fill(email);
  await page.locator('input[type=password]').first().fill(pass);
  await page.getByRole('button').filter({ hasText: /masuk|login|sign in/i }).first().click();
  await page.waitForTimeout(3000);
  return { ctx, page };
};

(async () => {
  const browser = await chromium.launch();
  const ts = Date.now();
  const newEmail = `bosbaru${ts}@lamaran.test`;

  // ===== SKENARIO A: Register akun baru → data KOSONG =====
  const ctxA = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const pageA = await ctxA.newPage();
  await pageA.goto(`${BASE}/admin/register`);
  await pageA.waitForTimeout(1500);
  await pageA.locator('input[type=text], input:not([type])').first().fill('Akun Baru Bos');
  await pageA.getByLabel('Email').fill(newEmail);
  await pageA.locator('input[type=password]').nth(0).fill('Baru2026!Bos');
  await pageA.locator('input[type=password]').nth(1).fill('Baru2026!Bos');
  await pageA.getByRole('button', { name: /Buat akun/i }).first().click();
  await pageA.waitForTimeout(3500);
  const aDashboard = await pageA.content();
  const aEmpty = !aDashboard.includes('Raka & Nadia') && !aDashboard.includes('Total Anggaran');
  console.log('A. register → dashboard KOSONG (tanpa data owner):', aEmpty);
  await pageA.screenshot({ path: `${SHOT_DIR}/60-isolasi-akun-baru.png`, fullPage: true });
  await ctxA.close();

  // ===== SKENARIO B: Login Owner → data lama tetap =====
  const { page: pageB } = await login(browser, 'owner@engagement.test', 'Owner2026!Dev');
  const bDashboard = await pageB.content();
  console.log('B. owner lihat Raka & Nadia:', bDashboard.includes('Raka & Nadia'));
  console.log('B. owner lihat nominal anggaran:', bDashboard.includes('Total Anggaran') && bDashboard.includes('Rp'));
  await pageB.screenshot({ path: `${SHOT_DIR}/61-isolasi-owner.png`, fullPage: true });
  await pageB.goto(`${BASE}/admin/activities`);
  await pageB.waitForTimeout(1800);
  const bAct = await pageB.content();
  console.log('B. owner kegiatan (Fitting baju):', (bAct.match(/Fitting baju adat CPP/g) || []).length > 0);
  await ctxA; // noop

  // ===== SKENARIO C: Partner → data owner sesuai izin =====
  const { page: pageC } = await login(browser, 'partner@lamaran.test', 'PartnerLamaran2026!');
  const cDashboard = await pageC.content();
  console.log('C. partner lihat data owner (Raka):', cDashboard.includes('Raka & Nadia'));
  console.log('C. partner nominal disembunyikan:', cDashboard.includes('Nominal anggaran disembunyikan'));
  await pageC.screenshot({ path: `${SHOT_DIR}/62-isolasi-partner.png`, fullPage: true });
  const cResp = await pageC.goto(`${BASE}/admin/budget-items`);
  await pageC.waitForTimeout(1500);
  console.log('C. partner akses /budget-items:', cResp ? cResp.status() : 'n/a');

  await browser.close();
  console.log('EMAIL_BARU=' + newEmail);
})();