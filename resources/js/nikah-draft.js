export const NikahDraft = {
    schedule(ctx){ if(ctx.isEditMode) return; clearTimeout(ctx.draftTimer); ctx.draftTimer=setTimeout(()=>this.persist(ctx),800); },
    persist(ctx){
        if(ctx.isEditMode) return;
        try{
            const form=ctx.$refs.editForm; if(!form) return;
            const fd=new FormData(form); const values={};
            for(const [k,v] of fd.entries()){
                if(k==='_token'||k==='_method') continue;
                if(v instanceof File) continue;
                if(/nik|no_hp|phone|email/i.test(k)) continue;
                if(values[k]===undefined) values[k]=v;
                else if(Array.isArray(values[k])) values[k].push(v);
                else values[k]=[values[k],v];
            }
            values._step=String(ctx.step);
            localStorage.setItem(ctx.draftKey, JSON.stringify({ts:Date.now(), step:ctx.step, values}));
        }catch(e){}
    },
    check(ctx, hasOld){
        if(ctx.isEditMode) return;
        try{
            const raw=localStorage.getItem(ctx.draftKey); if(!raw) return;
            const d=JSON.parse(raw);
            if(!d||!d.values||Date.now()-(d.ts||0)>86400000){ localStorage.removeItem(ctx.draftKey); return; }
            if(hasOld) return;
            ctx._pendingDraft=d; ctx.showDraftBanner=true;
        }catch(e){}
    },
    apply(ctx){
        const d=ctx._pendingDraft; if(!d) return;
        const rawStep = parseInt(d.step, 10) || 1;
        const isAndon = (d.values?.registration_path ?? ctx.registrationPath) === 'PEMBUATAN_ANDON_NIKAH';
        const max = isAndon ? 5 : 7;
        ctx.step = Math.min(Math.max(rawStep, 1), max);
        if (isAndon && ctx.step > 5) ctx.step = 5;
        ctx.$nextTick(async ()=>{
            const form=ctx.$refs.editForm; if(!form) return;
            const regionGroups={};
            for(const [k,rawV] of Object.entries(d.values)){
                if(k==='_step') continue;
                const vs=Array.isArray(rawV)?rawV:[rawV];
                if(k.endsWith('_province_id')||k.endsWith('_regency_id')||k.endsWith('_district_id')||k.endsWith('_village_id')){
                    const prefix=k.replace(/_(province|regency|district|village)_id$/,'');
                    if(!regionGroups[prefix]) regionGroups[prefix]={province:'',regency:'',district:'',village:''};
                    if(k.endsWith('_province_id')) regionGroups[prefix].province=vs[0]||'';
                    else if(k.endsWith('_regency_id')) regionGroups[prefix].regency=vs[0]||'';
                    else if(k.endsWith('_district_id')) regionGroups[prefix].district=vs[0]||'';
                    else if(k.endsWith('_village_id')) regionGroups[prefix].village=vs[0]||'';
                    continue;
                }
                const sel='[name=\''+k.replace(/'/g,'\\\'')+'\']';
                let els=form.querySelectorAll(sel);
                if(!els.length) continue;
                els.forEach((el,idx)=>{
                    const v=vs[idx]!==undefined?vs[idx]:vs[0];
                    if(el.type==='checkbox'||el.type==='radio'){
                        el.checked = vs.includes(el.value);
                    } else { el.value=v??''; }
                    el.dispatchEvent(new Event('input',{bubbles:true}));
                    el.dispatchEvent(new Event('change',{bubbles:true}));
                });
            }
            for(const [prefix, ids] of Object.entries(regionGroups)){
                if(!ids.province) continue;
                try{
                    const prov=form.querySelector('[name=\''+prefix+'_province_id\']');
                    const container=prov ? prov.closest('[x-data]') : null;
                    if(container && window.Alpine){
                        const data=window.Alpine.$data(container);
                        if(data && typeof data.getRegencies==='function'){
                            data.province=ids.province;
                            await data.getRegencies(true);
                            if(ids.regency){ data.regency=ids.regency; await data.getDistricts(true); }
                            if(ids.district){ data.district=ids.district; await data.getVillages(true); }
                            if(ids.village) data.village=ids.village;
                            continue;
                        }
                    }
                }catch(e){}
            }
            ctx._pendingDraft=null; ctx.showDraftBanner=false; ctx.handleStepWarning();
        });
    },
    discard(ctx){ try{localStorage.removeItem(ctx.draftKey);}catch(e){} ctx.showDraftBanner=false; ctx._pendingDraft=null; },
    clear(ctx){ try{localStorage.removeItem(ctx.draftKey);}catch(e){} },
    saveStep(ctx){
        if(!ctx.isEditMode) return;
        try{ const m=ctx.autosaveUrl.match(/registrations\/(\d+)\/autosave/); if(m) sessionStorage.setItem('nikah:step:'+m[1], String(ctx.step)); }catch(e){}
    },
    restoreStep(ctx){
        if(!ctx.isEditMode) return false;
        try{
            const m=ctx.autosaveUrl.match(/registrations\/(\d+)\/autosave/);
            if(!m) return false;
            const s=sessionStorage.getItem('nikah:step:'+m[1]);
            if(s && !isNaN(parseInt(s,10))){
                let n=parseInt(s,10);
                const max = ctx.totalSteps;
                if(n<1) n=1; if(n>max) n=max;
                if(ctx.isAndon && n>5) n=5;
                if(!ctx.isAndon && ctx.isAndon!==undefined) {}
                ctx.step=n; return true;
            }
        }catch(e){} return false;
    },
    init(ctx, hasOld){
        if(ctx.isEditMode){ this.restoreStep(ctx); ctx.handleStepWarning(); }
        else { this.check(ctx, hasOld); ctx.handleStepWarning(); }
    }
};

export function initNikLookup() {
    const priaNik = document.getElementById('pria_nik');
    const wanitaNik = document.getElementById('wanita_nik');
    const priaStatus = document.getElementById('pria_nik_status');
    const wanitaStatus = document.getElementById('wanita_nik_status');
    if (!priaNik || !wanitaNik) return;
    function showStatus(el, msg, ok) {
        el.textContent = msg;
        el.className = 'mt-1 text-xs ' + (ok ? 'text-emerald-600' : 'text-amber-600');
        el.classList.remove('hidden');
    }
    function hideStatus(el) { el.classList.add('hidden'); el.textContent = ''; }
    async function setRegion(prefix, province_id, regency_id, district_id, village_id) {
        if (!province_id) return;
        const prov = document.querySelector('[name="' + prefix + '_province_id"]');
        if (!prov) return;
        const container = prov.closest('[x-data]');
        try {
            if (window.Alpine && container) {
                const data = window.Alpine.$data(container);
                if (data && typeof data.getRegencies === 'function') {
                    data.province = province_id;
                    await data.getRegencies(true);
                    if (regency_id) { data.regency = regency_id; await data.getDistricts(true); }
                    if (district_id) { data.district = district_id; await data.getVillages(true); }
                    if (village_id) data.village = village_id;
                    return;
                }
            }
        } catch (e) {}
        prov.value = province_id;
        prov.dispatchEvent(new Event('change', { bubbles: true }));
        await new Promise(r => setTimeout(r, 700));
        const reg = document.querySelector('[name="' + prefix + '_regency_id"]');
        if (reg && regency_id) { reg.value = regency_id; reg.dispatchEvent(new Event('change', { bubbles: true })); await new Promise(r => setTimeout(r, 700)); }
        const dist = document.querySelector('[name="' + prefix + '_district_id"]');
        if (dist && district_id) { dist.value = district_id; dist.dispatchEvent(new Event('change', { bubbles: true })); await new Promise(r => setTimeout(r, 700)); }
        const vil = document.querySelector('[name="' + prefix + '_village_id"]');
        if (vil && village_id) { vil.value = village_id; vil.dispatchEvent(new Event('change', { bubbles: true })); }
    }
    async function lookup(nik, prefix) {
        const statusEl = prefix === 'pria' ? priaStatus : wanitaStatus;
        if (!/^\d{16}$/.test(nik)) { hideStatus(statusEl); return; }
        showStatus(statusEl, 'Mencari data NIK...', false);
        try {
            const token = document.querySelector('meta[name=csrf-token]')?.content || '';
            const res = await fetch('/internal/catins/lookup', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ nik }) });
            const json = await res.json();
            if (!json.found || !json.data) { showStatus(statusEl, 'NIK belum terdaftar — data baru akan disimpan.', true); return; }
            const d = json.data;
            showStatus(statusEl, 'NIK ditemukan — data existing di-prefill (bisa menikah kembali).', true);
            const map = { name: prefix + '_name', bin_binti: prefix + '_bin_binti', birth_place: prefix + '_birth_place', birth_date: prefix + '_birth_date', nationality: prefix + '_nationality', religion: prefix + '_religion', marital_status: prefix + '_marital_status', no_hp: prefix + '_no_hp', email: prefix + '_email', occupation: prefix + '_occupation', education: prefix + '_education', address: prefix + '_address' };
            for (const [k, name] of Object.entries(map)) {
                if (d[k] == null) continue;
                const el = document.querySelector('[name="' + name + '"]');
                if (el) { el.value = d[k]; el.dispatchEvent(new Event('input', { bubbles: true })); el.dispatchEvent(new Event('change', { bubbles: true })); }
            }
            await setRegion(prefix, d.province_id, d.regency_id, d.district_id, d.village_id);
            for (const role of ['father', 'mother']) {
                const p = d[role];
                if (!p) continue;
                const prefix2 = prefix + '_' + role;
                const m2 = { status: prefix2 + '_status', name: prefix2 + '_name', nik: prefix2 + '_nik', birth_place: prefix2 + '_birth_place', birth_date: prefix2 + '_birth_date', address: prefix2 + '_address', education: prefix2 + '_education', occupation: prefix2 + '_occupation' };
                for (const [k, name] of Object.entries(m2)) {
                    if (p[k] == null) continue;
                    const el = document.querySelector('[name="' + name + '"]');
                    if (el) { el.value = p[k]; el.dispatchEvent(new Event('input', { bubbles: true })); el.dispatchEvent(new Event('change', { bubbles: true })); }
                }
                await setRegion(prefix2, p.province_id, p.regency_id, p.district_id, p.village_id);
            }
        } catch (e) { showStatus(statusEl, 'Gagal lookup NIK.', false); }
    }
    let t1, t2;
    priaNik.addEventListener('input', () => { clearTimeout(t1); t1 = setTimeout(() => lookup(priaNik.value.trim(), 'pria'), 500); });
    wanitaNik.addEventListener('input', () => { clearTimeout(t2); t2 = setTimeout(() => lookup(wanitaNik.value.trim(), 'wanita'), 500); });
    priaNik.addEventListener('blur', () => lookup(priaNik.value.trim(), 'pria'));
    wanitaNik.addEventListener('blur', () => lookup(wanitaNik.value.trim(), 'wanita'));
}
