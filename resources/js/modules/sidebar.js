export default function sidebar() {
    const storageKey = 'silapandu.sidebarOpen';

    return {
        /**
         * Default: terbuka di desktop (mengikuti preferensi terakhir), tertutup di mobile.
         */
        sidebarOpen: window.innerWidth >= 768 && window.localStorage.getItem(storageKey) !== 'false',

        toggle() {
            this.sidebarOpen = !this.sidebarOpen;

            if (window.innerWidth >= 768) {
                window.localStorage.setItem(storageKey, this.sidebarOpen ? 'true' : 'false');
            }
        },

        /**
         * Tutup sidebar di mobile setelah salah satu menu diklik.
         */
        closeOnMobile(event) {
            if (window.innerWidth < 768 && event.target.closest('a')) {
                this.sidebarOpen = false;
            }
        },
    };
}
