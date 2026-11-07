export default function serviceDashboard(provinces = [], regencies = [], districts = [], villages = []) {
    return {
        provinces: provinces || [],
        regencies: regencies || [],
        districts: districts || [],
        villages: villages || [],

        openModal: false,
        selectedService: null,

        serviceType: 'bedol',
        tanggalAkad: '',
        jamAkad: '',
        alamatBedolan: '',
        jenisMahar: 'uang',
        nominalUang: '',
        rincianMahar: '',

        provinceId: '',
        regencyId: '',
        districtId: '',
        villageId: '',

        filteredRegencies: [],
        filteredDistricts: [],
        filteredVillages: [],
        isSubmitting: false,

        async init() {
            if (this.provinces && Array.isArray(this.provinces) && this.provinces.length > 0) {
                // Pre-loaded provinces from server
            }
        },

        openServiceModal(service) {
            if (service === 'andon') {
                window.location.href = '/registration';
                return;
            }
            this.selectedService = service;
            this.openModal = true;
            this.serviceType = service;
            this.resetRegion();
        },

        closeModal() {
            this.openModal = false;
            this.selectedService = null;
            this.resetForm();
        },

        resetForm() {
            this.tanggalAkad = '';
            this.jamAkad = '';
            this.alamatBedolan = '';
            this.jenisMahar = 'uang';
            this.nominalUang = '';
            this.rincianMahar = '';
            this.resetRegion();
        },

        resetRegion() {
            this.provinceId = '';
            this.regencyId = '';
            this.districtId = '';
            this.villageId = '';
            this.filteredRegencies = [];
            this.filteredDistricts = [];
            this.filteredVillages = [];
        },

        async updateRegencies() {
            this.regencyId = '';
            this.districtId = '';
            this.villageId = '';
            this.filteredDistricts = [];
            this.filteredVillages = [];
            if (!this.provinceId) return;
            this.filteredRegencies = await this.fetchRegion('regencies', this.provinceId);
        },

        async updateDistricts() {
            this.districtId = '';
            this.villageId = '';
            this.filteredVillages = [];
            if (!this.regencyId) return;
            this.filteredDistricts = await this.fetchRegion('districts', this.regencyId);
        },

        async updateVillages() {
            this.villageId = '';
            if (!this.districtId) return;
            this.filteredVillages = await this.fetchRegion('villages', this.districtId);
        },

        async fetchRegion(table, parentId = null) {
            const url = parentId
                ? `/registration/api/region/${table}/${parentId}`
                : `/registration/api/region/${table}`;
            try {
                const r = await fetch(url);
                if (!r.ok) return [];
                return await r.json();
            } catch (e) {
                console.error('Region fetch error:', e);
                return [];
            }
        },

        get kuaDisplay() {
            const kec = this.filteredDistricts.find(d => d.id === this.districtId);
            return kec ? `KUA Kecamatan ${kec.name}` : '---';
        },

        get dowryDisplay() {
            if (this.jenisMahar === 'uang') {
                return this.nominalUang ? `Rp ${Number(this.nominalUang).toLocaleString('id-ID')}` : '-';
            }
            return this.rincianMahar || '-';
        },

        saveAndNext() {
            if (!this.tanggalAkad || !this.jamAkad) {
                alert('Harap lengkapi tanggal dan jam akad.');
                return;
            }

            const formData = new FormData();
            formData.append('service_type', this.serviceType);
            formData.append('tanggal_akad', this.tanggalAkad);
            formData.append('jam_akad', this.jamAkad);
            formData.append('marriage_dowry_display', this.dowryDisplay);
            formData.append('province_id', this.provinceId);
            formData.append('regency_id', this.regencyId);
            formData.append('district_id', this.districtId);
            formData.append('village_id', this.villageId);

            if (this.serviceType === 'bedol') {
                formData.append('marriage_location_address', this.alamatBedolan);
            }

            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            this.isSubmitting = true;

            fetch('/registration/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(async (response) => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                         window.location.href = `/registration/dashboard/${data.registration.id}`;
                    } else {
                        alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch((error) => {
                    alert('Terjadi kesalahan jaringan: ' + error.message);
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        },

        backToDashboard() {
            window.location.href = '/';
        },
    };
}
