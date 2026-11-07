export default function registrationModal() {
    return {
        openModal: false,
        
        // Form Data Awal Akad & Mahar
        formData: {
            lokasiAkad: 'kua', // 'kua' (Kantor) atau 'bedolan' (Luar)
            tanggalAkad: '',
            jamAkad: '',
            alamatBedolan: '',
            jenisMahar: 'uang', // 'uang', 'emas', atau 'lainnya'
            nominalUang: '',
            rincianMahar: ''
        },

        init() {
            // Logika inisialisasi jika diperlukan
        },

        toggleModal(status) {
            this.openModal = status;
        },

        saveAndNext() {
            // Mengirim data awal akad/mahar lalu mengarahkan ke Dashboard 4 Card
            console.log('Data Akad Disimpan:', this.formData);
            this.openModal = false;
            // Redirect atau muat card pendaftaran
        }
    }
}