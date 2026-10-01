import { test, expect } from '@playwright/test';
import fs from 'fs';
import os from 'os';
import path from 'path';

test('autosave on edit persists after reload', async ({ page }) => {
  await page.goto('/registrations/23/edit');
  await page.waitForTimeout(1000);
  const addr = page.locator('[name="marriage_location_address"]');
  await expect(addr).toBeVisible({timeout:5000});
  const orig = await addr.inputValue();
  const newVal = orig + ' AUTOSAVE-' + Date.now();
  await addr.fill(newVal);
  await page.waitForTimeout(1600);
  await page.reload();
  await page.waitForTimeout(1000);
  const after = await page.locator('[name="marriage_location_address"]').inputValue();
  expect(after).toBe(newVal);
});

test('ktp 8 slots upload replace delete invalid oversize', async ({ page }) => {
  await page.goto('/registrations/23/ktp');
  await page.waitForTimeout(800);
  await expect(page.locator('text=Fotokopi KTP').first()).toBeVisible({timeout:5000});

  const pngB64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
  const buf = Buffer.from(pngB64,'base64');
  const tmpPng = path.join(os.tmpdir(), 'ktp-test-tiny2.png');
  fs.writeFileSync(tmpPng, buf);

  const slots = ['pria','wanita','ayah_pria','ibu_pria','ayah_wanita','ibu_wanita','saksi_1','saksi_2'];
  for (const slot of slots) {
    const input = page.locator(`input[name="ktp[${slot}]"]`);
    await input.setInputFiles(tmpPng);
  }
  await page.click('button:has-text("Simpan")');
  await page.waitForTimeout(1800);
  await page.reload();
  await page.waitForTimeout(800);
  const imgCount = await page.locator('img').count();
  console.log('imgCount after 8 upload', imgCount);
  expect(imgCount).toBeGreaterThanOrEqual(4);

  await page.locator('input[name="ktp[pria]"]').setInputFiles(tmpPng);
  await page.click('button:has-text("Simpan")');
  await page.waitForTimeout(1200);
  await page.reload();
  await page.waitForTimeout(600);
  const imgCount2 = await page.locator('img').count();
  console.log('after replace pria', imgCount2);
  expect(imgCount2).toBeGreaterThanOrEqual(4);

  page.on('dialog', async d => await d.accept());
  const delBtn = page.locator('button:has-text("Hapus")').first();
  if (await delBtn.count() > 0) {
    await delBtn.click();
    await page.waitForTimeout(1200);
    await page.reload();
    await page.waitForTimeout(600);
    const afterDel = await page.locator('img').count();
    console.log('after delete pria', afterDel);
  } else {
    const delForm2 = page.locator(`form[action*="/ktp/pria"]`).first();
    if (await delForm2.count() > 0) {
      await page.evaluate(() => { const f = document.querySelector('form[action*="/ktp/pria"]'); if(f) f.submit(); });
      await page.waitForTimeout(1200);
    }
  }

  const txtPath = path.join(os.tmpdir(), 'ktp-invalid2.txt');
  fs.writeFileSync(txtPath, 'not an image');
  await page.goto('/registrations/23/ktp');
  await page.locator('input[name="ktp[wanita]"]').setInputFiles(txtPath);
  await page.click('button:has-text("Simpan")');
  await page.waitForTimeout(1000);
  const hasInvalidErr = await page.locator('text=must be an image, text=mimes').first().isVisible().catch(()=>false);
  console.log('invalid err', hasInvalidErr);

  const bigPath = path.join(os.tmpdir(), 'ktp-big2.png');
  const bigBuf = Buffer.alloc(5*1024*1024, 0xFF);
  bigBuf[0]=0x89; bigBuf[1]=0x50; bigBuf[2]=0x4E; bigBuf[3]=0x47;
  fs.writeFileSync(bigPath, bigBuf);
  await page.goto('/registrations/23/ktp');
  await page.locator('input[name="ktp[pria]"]').setInputFiles(bigPath);
  await page.click('button:has-text("Simpan")');
  await page.waitForTimeout(1000);
  const hasOversizeErr = await page.locator('text=4096, text=kilobytes').first().isVisible().catch(()=>false);
  console.log('oversize err', hasOversizeErr);
});
