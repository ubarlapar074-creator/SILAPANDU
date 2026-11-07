export default function settingsHandler() {
    return {
        activeTab: 'identitas',
        showRestoreModal: false,
        restoreFile: null,
        restoreConfirmation: '',

        init() {
            // Initialization
        },

        downloadBackup() {
            window.location.href = '/settings/backup/download';
        },

        async confirmRestore() {
            if (this.restoreConfirmation !== 'RESTORE') return;

            const form = new FormData();
            form.append('file', this.restoreFile);

            const res = await fetch('/settings/backup/restore', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: form,
            });

            const json = await res.json();
            alert(json.message);
            this.showRestoreModal = false;
            this.restoreFile = null;
            this.restoreConfirmation = '';
        },
    };
}
