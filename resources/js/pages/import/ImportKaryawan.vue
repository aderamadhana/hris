<template>
    <Modal size="sm">
        <div class="modal-header">
            <h3>Import Data Karyawan</h3>
            <button
                class="modal-close"
                :disabled="loadingImportKaryawan"
                @click="$emit('closeModal')"
            >
                ✕
            </button>
        </div>

        <p class="modal-text">
            Pilih file Excel/CSV yang berisi data karyawan sesuai template.
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
            <Button variant="secondary" @click="$emit('closeModal')">
                Batal
            </Button>
            <Button
                variant="primary"
                :disabled="importProcessingKaryawan"
                @click="submitImport"
                class="d-flex align-items-center justify-content-center gap-2"
            >
                <template v-if="importProcessingKaryawan">
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
            showImportKaryawanModal: false,
            selectedFileKaryawan: null,

            importIdKaryawan: null,
            importKaryawanResult: null,
            pollingTimer: null,
            loadingImportKaryawan: false,
            importProcessingKaryawan: false,
        };
    },
    methods: {
        handleFileChange(e) {
            this.selectedFileKaryawan = e.target.files?.[0] || null;
        },
        async submitImport() {
            if (!this.selectedFileKaryawan) {
                triggerAlert('warning', 'Silakan pilih file terlebih dahulu.');
                return;
            }

            this.loadingImportKaryawan = true;
            this.importProcessingKaryawan = true;
            const formData = new FormData();
            formData.append('file', this.selectedFileKaryawan);

            try {
                const res = await axios.post(
                    '/employee/import-karyawan',
                    formData,
                    {
                        headers: { 'Content-Type': 'multipart/form-data' },
                    },
                );

                // ✅ ambil import_id dari backend
                this.importIdKaryawan = res.data.import_id;
                triggerAlert(
                    'info',
                    'Import sedang diproses. Mohon tunggu sampai selesai.',
                );
                // ✅ mulai polling hasil
                this.startPollingImportKaryawanResult();
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memulai proses import.');
                this.$emit('refreshData');
                this.$emit('closeModal');
            }
        },

        startPollingImportKaryawanResult() {
            this.pollingTimer = setInterval(async () => {
                const res = await axios.get(
                    `/employee/import-log/${this.importIdKaryawan}`,
                );
                const log = res.data;

                this.importKaryawanResult = log;

                if (log.status === 'completed') {
                    clearInterval(this.pollingTimer);
                    this.pollingTimer = null;

                    this.importProcessingKaryawan = false;

                    if (log.errors) {
                        const html = `
                        <div>
                            <strong>Import selesai dengan error</strong>

                            <div style="margin-top:6px">
                                Sukses: <b style="color:green">${log.success}</b><br>
                                Gagal: <b style="color:red">${log.failed}</b>
                            </div>

                            <hr>

                            <strong>Detail Error:</strong>
                            <ul>
                                ${log.errors
                                    .map((e) => {
                                        if (e.nrp) {
                                            return `<li>
                                            <b>NRP:</b> ${e.nrp}<br>
                                            <b>Error:</b> ${e.error}
                                        </li>`;
                                        }

                                        if (e.type === 'timeout') {
                                            return `<li><b>System:</b> ${e.message}</li>`;
                                        }

                                        return `<li>${JSON.stringify(e)}</li>`;
                                    })
                                    .join('')}
                            </ul>
                        </div>`;

                        triggerAlert('warning', html, 15000, true);
                    } else {
                        triggerAlert(
                            'success',
                            `Import selesai. Sukses: ${log.success}, Gagal: ${log.failed}`,
                        );
                    }

                    this.$emit('refreshData');
                    this.$emit('closeModal');
                }

                if (log.status === 'timeout') {
                    clearInterval(this.pollingTimer);
                    this.pollingTimer = null;

                    this.importProcessingKaryawan = false;

                    const html = `
                        <div>
                            <strong>Import selesai dengan error</strong>

                            <div style="margin-top:6px">
                                Sukses: <b style="color:green">${log.success}</b><br>
                                Gagal: <b style="color:red">${log.failed}</b>
                            </div>

                            <hr>

                            <strong>Detail Error:</strong>
                            <ul>
                                ${log.errors
                                    .map((e) => {
                                        if (e.nrp) {
                                            return `<li>
                                            <b>NRP:</b> ${e.nrp}<br>
                                            <b>Error:</b> ${e.error}
                                        </li>`;
                                        }

                                        if (e.type === 'timeout') {
                                            return `<li><b>System:</b> ${e.message}</li>`;
                                        }

                                        return `<li>${JSON.stringify(e)}</li>`;
                                    })
                                    .join('')}
                            </ul>
                        </div>`;

                    triggerAlert('warning', html, 15000, true);

                    this.$emit('refreshData');
                    this.$emit('closeModal');
                }
            }, 2000);
        },
        downloadTemplate() {
            const link = document.createElement('a');
            link.href = '/template/template_import_employee.xlsx'; // harus sama persis
            link.download = 'template_import_employee.xlsx';
            document.body.appendChild(link);
            link.click();
            link.remove();
        },
    },
};
</script>
