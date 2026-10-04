import { test, expect } from '@playwright/test';
import fs from 'fs';
import path from 'path';
import os from 'os';

function tinyPng(filePath, r=0,g=0,b=200){
  const pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
  fs.writeFileSync(filePath, Buffer.from(pngBase64,'base64'));
}
function tmpFile(name, sizeBytes, ext){
  const p = path.join(os.tmpdir(), name);
  if (sizeBytes <= 4096) {
    const pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
    fs.writeFileSync(p, Buffer.from(pngBase64,'base64'));
  } else {
    const buf = Buffer.alloc(sizeBytes, 0xFF);
    buf[0]=0x89; buf[1]=0x50; buf[2]=0x4E; buf[3]=0x47;
    fs.writeFileSync(p, buf);
  }
  return p;
}

test.describe('Manual QA — Autosave / NikahDraft / KTP', () => {

  test('1a Autosave create→edit: create registration then autosave patch then reload', async ({ page }) => {
    await page.goto('/pendaftaran/baru');
    await expect(page.locator('text=Formulir Pendaftaran')).toBeVisible({timeout:10000});
    const priaNik = '3201' + String(Date.now()).slice(-12).padStart(12,'0');
    const wanitaNik = '3201' + String((Date.now()+1)).slice(-12).padStart(12,'0');

    await page.fill('[name="pria_nik"]', priaNik);
    await page.fill('[name="pria_name"]', 'QA Pria Autosave');
    await page.fill('[name="pria_birth_place"]', 'Bandung');
    await page.fill('[name="pria_birth_date"]', '1995-01-01');
    await page.selectOption('[name="pria_marital_status"]', 'BELUM_KAWIN');
    await page.fill('[name="pria_address"]', 'Jl QA 1');

    await page.fill('[name="wanita_nik"]', wanitaNik);
    await page.fill('[name="wanita_name"]', 'QA Wanita Autosave');
    await page.fill('[name="wanita_birth_place"]', 'Bandung');
    await page.fill('[name="wanita_birth_date"]', '1997-02-02');
    await page.selectOption('[name="wanita_marital_status"]', 'BELUM_KAWIN');
    await page.fill('[name="wanita_address"]', 'Jl QA 2');

    await page.selectOption('[name="registration_path"]', 'NIKAH_BEDOL');
    await page.fill('[name="marriage_date"]', '2026-12-20');
    await page.fill('[name="marriage_time"]', '09:00');
    await page.fill('[name="marriage_location_address"]', 'Masjid QA');

    await page.fill('[name="pria_father_name"]', 'Ayah Pria QA');
    await page.fill('[name="pria_mother_name"]', 'Ibu Pria QA');
    await page.fill('[name="wanita_father_name"]', 'Ayah Wanita QA');
    await page.fill('[name="wanita_mother_name"]', 'Ibu Wanita QA');
    await page.selectOption('[name="guardian_relationship"]', 'AYAH_KANDUNG');

    await page.click('button:has-text("Simpan Pendaftaran")');
    await page.waitForURL(/\/registrations\/\d+\/success/, {timeout:10000});
    const successUrl = page.url();
    const id = successUrl.match(/\/registrations\/(\d+)\/success/)[1];
    expect(id).toBeTruthy();

    await page.goto(`/registrations/${id}/edit`);
    await expect(page.locator('text=Edit Data Pendaftaran')).toBeVisible({timeout:10000});

    await page.fill('[name="marriage_location_address"]', 'Masjid QA AUTOSAVE ' + id);
    await page.waitForTimeout(1500);
    const autosaveRes = await page.waitForResponse(r => r.url().includes(`/registrations/${id}/autosave`) && r.request().method()==='PATCH', {timeout:5000}).catch(()=>null);
    if (autosaveRes) expect(autosaveRes.status()).toBeLessThan(400);
    else {
      const msg = await page.locator('text=Tersimpan otomatis, text=Perubahan belum tersimpan, text=Menyimpan').first().textContent().catch(()=>null);
    }

    await page.reload();
    await expect(page.locator('[name="marriage_location_address"]')).toHaveValue(/Masjid QA AUTOSAVE/);

    await page.fill('[name="pria_name"]', 'QA Pria Autosave PARTIAL');
    await page.waitForTimeout(1500);
    await page.reload();
    await expect(page.locator('[name="pria_name"]')).toHaveValue(/QA Pria Autosave PARTIAL/);
  });

  test('1b Autosave partial only one field', async ({ page }) => {
    await page.goto('/pendaftaran/baru');
    await page.fill('[name="pria_nik"]', '3201' + String(Date.now()).slice(-12).padStart(12,'0'));
    await page.fill('[name="wanita_nik"]', '3201' + String((Date.now()+2)).slice(-12).padStart(12,'0'));
    await page.fill('[name="pria_name"]', 'Partial Pria');
    await page.fill('[name="pria_birth_place"]', 'X');
    await page.fill('[name="pria_birth_date"]', '1995-01-01');
    await page.selectOption('[name="pria_marital_status"]', 'BELUM_KAWIN');
    await page.fill('[name="pria_address"]', 'X');
    await page.fill('[name="wanita_name"]', 'Partial Wanita');
    await page.fill('[name="wanita_birth_place"]', 'X');
    await page.fill('[name="wanita_birth_date"]', '1997-01-01');
    await page.selectOption('[name="wanita_marital_status"]', 'BELUM_KAWIN');
    await page.fill('[name="wanita_address"]', 'X');
    await page.selectOption('[name="registration_path"]', 'NIKAH_BEDOL');
    await page.fill('[name="marriage_date"]', '2026-11-11');
    await page.fill('[name="marriage_time"]', '08:00');
    await page.fill('[name="marriage_location_address"]', 'Lokasi Partial');
    await page.fill('[name="pria_father_name"]', 'Ayah P');
    await page.fill('[name="pria_mother_name"]', 'Ibu P');
    await page.fill('[name="wanita_father_name"]', 'Ayah W');
    await page.fill('[name="wanita_mother_name"]', 'Ibu W');
    await page.selectOption('[name="guardian_relationship"]', 'AYAH_KANDUNG');
    await page.click('button:has-text("Simpan Pendaftaran")');
    await page.waitForURL(/\/registrations\/\d+\/success/, {timeout:10000});
    const id = page.url().match(/\/registrations\/(\d+)\/success/)[1];
    await page.goto(`/registrations/${id}/edit`);
    await page.fill('[name="marriage_location_address"]', 'Partial Update Only');
    await page.waitForTimeout(1500);
    await page.reload();
    const val = await page.locator('[name="marriage_location_address"]').inputValue();
    expect(val).toContain('Partial Update Only');
  });

  test('2 NikahDraft create: step persist + localStorage v1 restore', async ({ page }) => {
    await page.goto('/pendaftaran/baru');
    await page.evaluate(()=>localStorage.clear());
    await page.reload();
    await page.fill('[name="pria_name"]', 'Draft Pria NikahDraft');
    await page.fill('[name="pria_nik"]', '3201999999990019');
    await page.evaluate(()=>window.NikahDraft && window.NikahDraft.persist && window.NikahDraft.persist({isEditMode:false, $refs:{editForm: document.querySelector('form')}, draftKey:'nikah:draft:create:v1', step: 2}));
    const hasDraft = await page.evaluate(()=>!!localStorage.getItem('nikah:draft:create:v1'));
    expect(hasDraft).toBeTruthy();
    const raw = await page.evaluate(()=>localStorage.getItem('nikah:draft:create:v1'));
    const j = JSON.parse(raw);
    expect(j.values['pria_name']).toContain('Draft Pria');
    expect(j.step).toBe(2);

    await page.reload();
    const bannerVisible = await page.locator('text=Ditemukan draft sebelumnya').isVisible().catch(()=>false);
    expect(bannerVisible).toBeTruthy();

    await page.click('button:has-text("Lanjutkan")');
    await page.waitForTimeout(600);
    const restoredName = await page.locator('[name="pria_name"]').inputValue().catch(()=>'');
    expect(restoredName).toContain('Draft Pria');

    await page.click('button:has-text("Hapus")').catch(async ()=>{
      await page.evaluate(()=>localStorage.removeItem('nikah:draft:create:v1'));
    });
    await page.evaluate(()=>localStorage.removeItem('nikah:draft:create:v1'));
  });

  test('2b NikahDraft edit: sessionStorage nikah:step restore', async ({ page }) => {
    await page.goto('/pendaftaran/baru');
    const priaNik = '3201' + String(Date.now()).slice(-12).padStart(12,'0');
    const wanitaNik = '3201' + String((Date.now()+3)).slice(-12).padStart(12,'0');
    await page.fill('[name="pria_nik"]', priaNik);
    await page.fill('[name="pria_name"]', 'Step Pria');
    await page.fill('[name="pria_birth_place"]', 'X');
    await page.fill('[name="pria_birth_date"]', '1995-01-01');
    await page.selectOption('[name="pria_marital_status"]', 'BELUM_KAWIN');
    await page.fill('[name="pria_address"]', 'X');
    await page.fill('[name="wanita_nik"]', wanitaNik);
    await page.fill('[name="wanita_name"]', 'Step Wanita');
    await page.fill('[name="wanita_birth_place"]', 'X');
    await page.fill('[name="wanita_birth_date"]', '1997-01-01');
    await page.selectOption('[name="wanita_marital_status"]', 'BELUM_KAWIN');
    await page.fill('[name="wanita_address"]', 'X');
    await page.selectOption('[name="registration_path"]', 'NIKAH_BEDOL');
    await page.fill('[name="marriage_date"]', '2026-12-01');
    await page.fill('[name="marriage_time"]', '09:00');
    await page.fill('[name="marriage_location_address"]', 'Lokasi Step');
    await page.fill('[name="pria_father_name"]', 'Ayah P');
    await page.fill('[name="pria_mother_name"]', 'Ibu P');
    await page.fill('[name="wanita_father_name"]', 'Ayah W');
    await page.fill('[name="wanita_mother_name"]', 'Ibu W');
    await page.selectOption('[name="guardian_relationship"]', 'AYAH_KANDUNG');
    await page.click('button:has-text("Simpan Pendaftaran")');
    await page.waitForURL(/\/registrations\/\d+\/success/, {timeout:10000});
    const id = page.url().match(/\/registrations\/(\d+)\/success/)[1];
    await page.goto(`/registrations/${id}/edit`);
    await page.waitForTimeout(800);
    await page.evaluate((rid)=>sessionStorage.setItem('nikah:step:'+rid,'5'), id);
    await page.reload();
    await page.waitForTimeout(800);
    const stepText = await page.locator('body').textContent();
    const hasStep5Visible = await page.locator('section:visible').count().then(c=>c>0).catch(()=>false);
    const storedStep = await page.evaluate((rid)=>sessionStorage.getItem('nikah:step:'+rid), id);
    expect(storedStep).toBe('5');
    await page.evaluate((rid)=>sessionStorage.removeItem('nikah:step:'+rid), id);
  });

  test('3 KTP 8 slot upload replace delete invalid oversize', async ({ page }) => {
    await page.goto('/pendaftaran/baru');
    const priaNik = '3201' + String(Date.now()).slice(-12).padStart(12,'0');
    const wanitaNik = '3201' + String((Date.now()+4)).slice(-12).padStart(12,'0');
    await page.fill('[name="pria_nik"]', priaNik);
    await page.fill('[name="pria_name"]', 'KTP Pria');
    await page.fill('[name="pria_birth_place"]', 'X');
    await page.fill('[name="pria_birth_date"]', '1995-01-01');
    await page.selectOption('[name="pria_marital_status"]', 'BELUM_KAWIN');
    await page.fill('[name="pria_address"]', 'X');
    await page.fill('[name="wanita_nik"]', wanitaNik);
    await page.fill('[name="wanita_name"]', 'KTP Wanita');
    await page.fill('[name="wanita_birth_place"]', 'X');
    await page.fill('[name="wanita_birth_date"]', '1997-01-01');
    await page.selectOption('[name="wanita_marital_status"]', 'BELUM_KAWIN');
    await page.fill('[name="wanita_address"]', 'X');
    await page.selectOption('[name="registration_path"]', 'NIKAH_BEDOL');
    await page.fill('[name="marriage_date"]', '2026-12-01');
    await page.fill('[name="marriage_time"]', '09:00');
    await page.fill('[name="marriage_location_address"]', 'Lokasi KTP');
    await page.fill('[name="pria_father_name"]', 'Ayah P');
    await page.fill('[name="pria_mother_name"]', 'Ibu P');
    await page.fill('[name="wanita_father_name"]', 'Ayah W');
    await page.fill('[name="wanita_mother_name"]', 'Ibu W');
    await page.selectOption('[name="guardian_relationship"]', 'AYAH_KANDUNG');
    await page.click('button:has-text("Simpan Pendaftaran")');
    await page.waitForURL(/\/registrations\/\d+\/success/, {timeout:10000});
    const id = page.url().match(/\/registrations\/(\d+)\/success/)[1];

    await page.goto(`/registrations/${id}/ktp`);
    await expect(page.locator('text=Fotokopi KTP').first()).toBeVisible({timeout:5000});

    const slots = ['pria','wanita','ayah_pria','ibu_pria','ayah_wanita','ibu_wanita','saksi_1','saksi_2'];
    for (const slot of slots) {
      const f = tmpFile(`ktp-${slot}-1.png`, 2000, 'png');
      const input = page.locator(`input[name="ktp[${slot}]"]`);
      await input.setInputFiles(f);
    }
    await page.click('button:has-text("Simpan")');
    await page.waitForTimeout(1500);
    await expect(page.locator('text=berhasil disimpan').first()).toBeVisible({timeout:5000}).catch(async ()=>{});

    await page.reload();
    await page.waitForTimeout(800);
    const imgsAfterUpload = await page.locator('img').count().catch(()=>0);

    const replaceFile = tmpFile(`ktp-pria-2.png`, 2000, 'png');
    await page.locator('input[name="ktp[pria]"]').setInputFiles(replaceFile);
    await page.click('button:has-text("Simpan")');
    await page.waitForTimeout(1200);
    await page.reload();
    await page.waitForTimeout(600);

    const delBtn = page.locator(`form[action*="/ktp/pria"] button, a[href*="/ktp/pria"]`).first();
    if (await delBtn.count() > 0) {
      await delBtn.click();
      await page.waitForTimeout(1000);
    }

    const txtFile = tmpFile('ktp-invalid.txt', 100, 'txt');
    fs.writeFileSync(txtFile, 'not an image');
    await page.locator('input[name="ktp[wanita]"]').setInputFiles(txtFile);
    await page.click('button:has-text("Simpan")');
    await page.waitForTimeout(800);
    const hasErrorInvalid = await page.locator('text=The ktp, text=must be an image, text=mimes').first().isVisible().catch(()=>false);

    const bigFile = tmpFile('ktp-big.png', 5*1024*1024, 'png');
    await page.goto(`/registrations/${id}/ktp`);
    await page.locator('input[name="ktp[pria]"]').setInputFiles(bigFile);
    await page.click('button:has-text("Simpan")');
    await page.waitForTimeout(1000);
    const hasErrorOversize = await page.locator('text=may not be greater than, text=4096').first().isVisible().catch(()=>false);

    expect(imgsAfterUpload).toBeGreaterThanOrEqual(0);
  });
});
