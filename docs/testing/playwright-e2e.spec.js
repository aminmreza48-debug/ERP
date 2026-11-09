// docs/testing/playwright-e2e.spec.js - Automated E2E Testing with Playwright
// Run with: npx playwright test docs/testing/playwright-e2e.spec.js

const { test, expect } = require('@playwright/test');

const BASE_URL = process.env.BASE_URL || 'http://localhost:8080';

test.describe('SIPERMA ERP - End-to-End Functional Test Suite', () => {

  // =========================================================================
  // TEST 1: Mahasiswa Login -> Dashboard -> Melihat Tagihan Aktif
  // =========================================================================
  test('TEST 1: Mahasiswa login -> dashboard -> melihat tagihan', async ({ page }) => {
    // 1. Kunjungi halaman login
    await page.goto(`${BASE_URL}/login`);
    await expect(page).toHaveTitle(/Login - SIPERMA ERP/);

    // 2. Input kredensial mahasiswa
    await page.fill('input[name="identifier"]', 'mhs2024001');
    await page.fill('input[name="password"]', 'mhs123');
    await page.click('button[type="submit"]');

    // 3. Verifikasi mendarat di Dashboard Mahasiswa
    await expect(page).toHaveURL(/.*\/mahasiswa\/dashboard/);
    await expect(page.locator('h3')).toContainText('Ahmad Fauzi');
    await expect(page.locator('text=Total Tagihan')).toBeVisible();

    // 4. Navigasi dan verifikasi daftar tagihan
    await page.click('a[href="/mahasiswa/tagihan"]');
    await expect(page.locator('table')).toBeVisible();
    await expect(page.locator('text=UKT')).toBeVisible();
  });

  // =========================================================================
  // TEST 2: Mahasiswa: Login -> Pilih Tagihan -> Upload Bukti -> Submit
  // =========================================================================
  test('TEST 2: Mahasiswa: login -> pilih tagihan -> upload bukti -> submit', async ({ page }) => {
    // 1. Login
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="identifier"]', 'mhs2024001');
    await page.fill('input[name="password"]', 'mhs123');
    await page.click('button[type="submit"]');

    // 2. Akses halaman tagihan
    await page.goto(`${BASE_URL}/mahasiswa/tagihan`);

    // 3. Cari tombol bayar untuk tagihan yang belum lunas
    const bayarBtn = page.locator('a[href*="/mahasiswa/upload?tagihan_id="]').first();
    if (await bayarBtn.isVisible()) {
      await bayarBtn.click();
      await expect(page).toHaveURL(/.*\/mahasiswa\/upload.*/);

      // 4. Pilih bank & upload file bukti
      await page.selectOption('select[name="metode_pembayaran"]', 'Transfer Bank');
      await page.selectOption('select[name="bank_tujuan"]', 'Bank Mandiri');

      // Set input file payload
      await page.setInputFiles('input[name="bukti_transfer"]', {
        name: 'bukti_transfer.jpg',
        mimeType: 'image/jpeg',
        buffer: Buffer.from('FAKE_IMAGE_DATA')
      });

      // 5. Submit form
      await page.click('button[type="submit"]');

      // 6. Verifikasi diarahkan ke riwayat pembayaran
      await expect(page).toHaveURL(/.*\/mahasiswa\/riwayat.*/);
      await expect(page.locator('text=Menunggu Verifikasi')).toBeVisible();
    }
  });

  // =========================================================================
  // TEST 3: Bendahara: Login -> Verifikasi Pembayaran -> Approve/Reject -> Berikan Catatan
  // =========================================================================
  test('TEST 3: Bendahara: login -> verifikasi pembayaran -> keputusan dan catatan', async ({ page }) => {
    // 1. Kunjungi halaman login
    await page.goto(`${BASE_URL}/login`);

    // 2. Input kredensial Bendahara
    await page.fill('input[name="identifier"]', 'bendahara');
    await page.fill('input[name="password"]', 'bendahara123');
    await page.click('button[type="submit"]');

    // 3. Verifikasi mendarat di Dashboard Bendahara
    await expect(page).toHaveURL(/.*\/bendahara\/dashboard/);
    await expect(page.locator('h3')).toContainText('Dashboard Bendahara Keuangan');

    // 4. Masuk ke halaman verifikasi pembayaran
    await page.goto(`${BASE_URL}/bendahara/verifikasi`);
    await expect(page.locator('table')).toBeVisible();

    // 5. Cek preview bukti transfer jika tombol ada
    const bukaBuktiBtn = page.locator('button:has-text("Buka Bukti")').first();
    if (await bukaBuktiBtn.isVisible()) {
      await bukaBuktiBtn.click();
      await expect(page.locator('.modal-content')).toBeVisible();
      await page.keyboard.press('Escape'); // Tutup modal
    }

    // 6. Evaluasi tombol persetujuan atau penolakan dengan catatan
    const setujuBtn = page.locator('button:has-text("Setuju")').first();
    if (await setujuBtn.isVisible()) {
      page.on('dialog', dialog => dialog.accept());
      await setujuBtn.click();
      await expect(page.locator('.alert-success')).toBeVisible();
    }
  });

});

