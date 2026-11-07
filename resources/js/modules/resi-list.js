export default function resiList(data) {
    const currentQuery = new URL(window.location.href).searchParams;

    return {
        search: currentQuery.get('q') || currentQuery.get('search') || '',
        showModal: false,
        isEditing: false,
        saving: false,
        errors: [],
        formData: data || {},

        init() {
            this.$watch('search', () => {
                clearTimeout(this._searchTimer);
                this._searchTimer = setTimeout(() => {
                    this.doSearch();
                }, 400);
            });
        },

        doSearch() {
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            if (this.search) {
                url.searchParams.set('q', this.search);
            } else {
                url.searchParams.delete('q');
            }
            window.location.href = url.toString();
        },

        openModal() {
            this.showModal = true;
            this.isEditing = false;
            this.errors = [];
            this.formData = {};
        },

        closeModal() {
            this.showModal = false;
            this.isEditing = false;
            this.errors = [];
            this.formData = {};
        },

        async submit() {
            if (this.saving) {
                return;
            }

            this.errors = [];
            this.saving = true;

            const method = this.isEditing ? 'PUT' : 'POST';
            const url = this.isEditing
                ? `/residents/${this.formData.id}`
                : '/residents';

            const form = new FormData();
            Object.keys(this.formData).forEach((k) => {
                form.append(k, this.formData[k] ?? '');
            });

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                    },
                    body: form,
                });

                const json = await res.json().catch(() => ({}));

                if (res.status === 422) {
                    this.errors = Object.values(json.errors ?? {}).flat();
                    return;
                }

                if (!res.ok || !json.success) {
                    this.errors = [json.message || 'Data warga gagal disimpan. Silakan coba lagi.'];
                    return;
                }

                alert(json.message);
                this.closeModal();
                window.location.reload();
            } catch (error) {
                this.errors = ['Tidak dapat terhubung ke server. Periksa koneksi lalu coba lagi.'];
            } finally {
                this.saving = false;
            }
        },
    };
}
