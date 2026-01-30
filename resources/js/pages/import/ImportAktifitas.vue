<template>
    <Modal size="sm">
        <div class="modal-header">
            <h3>Import Data Aktifitas</h3>
            <button
                class="modal-close"
                :disabled="importProcessingAktifitas"
                @click="closeImportModal"
            >
                ✕
            </button>
        </div>

        <p class="modal-text">
            Pilih file Excel/CSV yang berisi data aktifitas sesuai template.
        </p>

        <div class="modal-body">
            <input
                type="file"
                accept=".xlsx, .xls, .csv"
                @change="handleFileChange"
                ref="fileInput"
            />

            <p class="hint">
                Format disarankan: .xlsx atau .csv — pastikan kolom sesuai
                template.
            </p>

            <button class="download-template-btn" @click="downloadTemplate">
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Download Template
            </button>
        </div>

        <div class="modal-footer">
            <Button
                variant="secondary"
                :disabled="importProcessingAktifitas"
                @click="closeImportModal"
            >
                Batal
            </Button>
            <Button
                variant="primary"
                :disabled="importProcessingAktifitas"
                @click="submitImport"
                class="d-flex align-items-center justify-content-center gap-2"
            >
                <template v-if="importProcessingAktifitas">
                    <span
                        class="spinner-border spinner-border-sm spinner"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    <span>Memproses...</span>
                </template>

                <template v-else> Upload &amp; Proses </template>
            </Button>
        </div>
    </Modal>
</template>

<script>
import Button from '@/components/Button.vue';
import Modal from '@/components/Modal.vue';
import { triggerAlert } from '@/utils/alert';
import axios from 'axios';

export default {
    components: {
        Modal,
        Button,
    },

    data() {
        return {
            selectedFileAktifitas: null,
            importIdAktifitas: null,
            importAktifitasResult: null,
            pollingTimer: null,
            importProcessingAktifitas: false,
        };
    },

    beforeUnmount() {
        // Bersihkan timer saat component di-destroy
        this.clearPolling();
    },

    methods: {
        handleFileChange(e) {
            this.selectedFileAktifitas = e.target.files?.[0] || null;
        },

        async submitImport() {
            if (!this.selectedFileAktifitas) {
                triggerAlert('warning', 'Silakan pilih file terlebih dahulu.');
                return;
            }

            this.importProcessingAktifitas = true;
            const formData = new FormData();
            formData.append('file', this.selectedFileAktifitas);

            try {
                const res = await axios.post(
                    '/logs/aktifitas/import',
                    formData,
                    {
                        headers: { 'Content-Type': 'multipart/form-data' },
                    },
                );

                // Ambil import_id dari backend
                this.importIdAktifitas = res.data.import_id;

                triggerAlert(
                    'info',
                    'Import sedang diproses. Mohon tunggu sampai selesai.',
                );

                // Mulai polling hasil
                this.startPollingImportAktifitasResult();
            } catch (err) {
                console.error('Import error:', err);
                this.importProcessingAktifitas = false;

                const errorMsg =
                    err.response?.data?.error || 'Gagal memulai proses import.';
                triggerAlert('error', errorMsg);
            }
        },

        startPollingImportAktifitasResult() {
            // Bersihkan polling sebelumnya jika ada
            this.clearPolling();

            let pollCount = 0;
            const maxPolls = 300; // Max 10 menit (300 * 2 detik)

            this.pollingTimer = setInterval(async () => {
                pollCount++;

                // Safety: stop polling setelah waktu tertentu
                if (pollCount >= maxPolls) {
                    this.clearPolling();
                    this.importProcessingAktifitas = false;
                    triggerAlert(
                        'error',
                        'Timeout: Import memakan waktu terlalu lama.',
                    );
                    this.$emit('closeModal');
                    return;
                }

                try {
                    const res = await axios.get(
                        `/logs/aktifitas/import-log/${this.importIdAktifitas}`,
                    );

                    const log = res.data;

                    console.log('Polling result:', log); // Debug log

                    this.importAktifitasResult = log;

                    // Cek status completed
                    if (log.status === 'completed') {
                        this.clearPolling();
                        this.importProcessingAktifitas = false;

                        // Cek apakah ada error
                        if (log.errors && log.errors.length > 0) {
                            this.showErrorAlert(log);
                        } else {
                            triggerAlert(
                                'success',
                                `Import selesai! Sukses: ${log.success}, Gagal: ${log.failed}`,
                            );
                        }

                        this.$emit('refreshData');
                        this.$emit('closeModal');
                        return;
                    }

                    // Cek status failed
                    if (log.status === 'failed') {
                        this.clearPolling();
                        this.importProcessingAktifitas = false;
                        this.showErrorAlert(log);
                        this.$emit('refreshData');
                        this.$emit('closeModal');
                        return;
                    }

                    // Cek status timeout
                    if (log.status === 'timeout') {
                        this.clearPolling();
                        this.importProcessingAktifitas = false;
                        this.showErrorAlert(log);
                        this.$emit('refreshData');
                        this.$emit('closeModal');
                        return;
                    }
                } catch (err) {
                    console.error('Polling error:', err);
                    this.clearPolling();
                    this.importProcessingAktifitas = false;
                    triggerAlert('error', 'Gagal mengecek status import.');
                    this.$emit('closeModal');
                }
            }, 2000); // Poll setiap 2 detik
        },

        showErrorAlert(log) {
            const errors = log.errors || [];

            let errorList = '';
            if (errors.length > 0) {
                errorList = errors
                    .map((e) => {
                        // Error dengan NIK/Reg
                        if (e.nik || e.reg) {
                            return `<li>
                            <b>Baris ${e.row || '-'}:</b> ${e.nik || e.reg || '-'}<br>
                            <b>Error:</b> ${e.error || 'Unknown error'}
                        </li>`;
                        }

                        // Error timeout
                        if (e.type === 'timeout') {
                            return `<li><b>System:</b> ${e.message}</li>`;
                        }

                        // Error job failed
                        if (e.type === 'job_failed') {
                            return `<li><b>Job Failed:</b> ${e.error}</li>`;
                        }

                        // Error lainnya
                        return `<li>${JSON.stringify(e)}</li>`;
                    })
                    .join('');
            }

            const html = `
                <div>
                    <strong>Import selesai dengan ${log.status === 'failed' ? 'kegagalan' : 'error'}</strong>

                    <div style="margin-top:6px">
                        Sukses: <b style="color:green">${log.success || 0}</b><br>
                        Gagal: <b style="color:red">${log.failed || 0}</b>
                    </div>

                    ${
                        errorList
                            ? `
                        <hr>
                        <strong>Detail Error:</strong>
                        <ul style="max-height:200px; overflow-y:auto; text-align:left;">
                            ${errorList}
                        </ul>
                    `
                            : ''
                    }
                </div>
            `;

            triggerAlert('warning', html, 15000, true);
        },

        clearPolling() {
            if (this.pollingTimer) {
                clearInterval(this.pollingTimer);
                this.pollingTimer = null;
            }
        },

        closeImportModal() {
            this.clearPolling();
            this.importProcessingAktifitas = false;
            this.$emit('closeModal');
        },

        downloadTemplate() {
            const link = document.createElement('a');
            link.href = '/template/FORMAT_UPLOAD_AKTIFITAS.xlsx';
            link.download = 'template_import_aktifitas.xlsx';
            document.body.appendChild(link);
            link.click();
            link.remove();
        },
    },
};
</script>
