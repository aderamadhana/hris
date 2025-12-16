<template>
    <Modal size="sm">
        <div class="modal-header">
            <h3>Import Data Slip Gaji Karyawan</h3>
            <button
                class="modal-close"
                :disabled="loadingImportGaji"
                @click="$emit('closeModal')"
            >
                ✕
            </button>
        </div>

        <p class="modal-text">
            Pilih file Excel/CSV yang berisi data slip gaji karyawan dan
            tentukan periode slip.
        </p>

        <div class="modal-body">
            <!-- PILIH PERIODE PAYROLL -->

            <div class="form-group mb-3">
                <label class="form-label">
                    Periode Payroll <span class="text-danger">*</span>
                </label>
                <select
                    v-model="selectedPayrollPeriodId"
                    class="form-control"
                    :disabled="loadingImportGaji"
                >
                    <option value="" disabled>
                        -- Pilih Periode Payroll --
                    </option>
                    <option
                        v-for="period in payrollPeriod"
                        :key="period.id"
                        :value="period.id"
                    >
                        {{ period.judul }} ({{ period.start_date }} –
                        {{ period.end_date }})
                    </option>
                </select>
            </div>

            <!-- FILE -->
            <input
                type="file"
                accept=".xlsx, .xls, .csv"
                @change="handleFileGajiChange"
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
                @click="closeModalImportGaji"
                :disabled="importProcessingGaji"
            >
                Batal
            </Button>
            <Button
                variant="primary"
                :disabled="importProcessingGaji"
                @click="submitImportGaji"
                class="d-flex align-items-center justify-content-center gap-2"
            >
                <template v-if="importProcessingGaji">
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
            showImportGajiModal: false,
            selectedFileGaji: null,

            importIdGaji: null,
            importGajiResult: null,
            loadingImportGaji: false,
            importProcessingGaji: false,

            payrollPeriod: [],
            selectedPayrollPeriodId: '',
        };
    },

    mounted() {
        this.getPaymentPeriods();
    },

    methods: {
        async getPaymentPeriods() {
            try {
                const res = await axios.get('/referensi/get-payroll_periods');
                this.payrollPeriod = res.data.payroll_periods;
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memuat data payments.');
            } finally {
                this.loadingUsers = false;
            }
        },

        handleFileGajiChange(e) {
            this.selectedFileGaji = e.target.files?.[0] || null;
        },

        async submitImportGaji() {
            if (!this.selectedPayrollPeriodId) {
                triggerAlert(
                    'warning',
                    'Silakan pilih payroll periods terlebih dahulu.',
                );
                return;
            }
            if (!this.selectedFileGaji) {
                triggerAlert('warning', 'Silakan pilih file terlebih dahulu.');
                return;
            }

            this.loadingImportGaji = true;
            this.importProcessingGaji = true;
            const formData = new FormData();
            formData.append('file', this.selectedFileGaji);
            formData.append('payroll_period_id', this.selectedPayrollPeriodId);

            try {
                triggerAlert(
                    'info',
                    'Import sedang diproses. Mohon tunggu sampai selesai.',
                );
                const res = await axios.post('/payroll/import', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                // ✅ ambil import_id dari backend
                const result = res.data;
                this.pollImportGajiProgress(result.check_progress_url);
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memulai proses import.');
                this.$emit('closeModal');
                this.$emit('refreshData');
            }
        },

        async pollImportGajiProgress(url) {
            let done = false;

            while (!done) {
                await new Promise((resolve) => setTimeout(resolve, 1500)); // polling setiap 1.5 detik

                const progress = await axios.get(url);

                const status = progress.data.status;

                if (status === 'processing') {
                    // masih proses → lanjut polling
                    continue;
                }

                if (status === 'completed') {
                    const d = progress.data.data;
                    triggerAlert(
                        'success',
                        `Import selesai. Total: ${d.total}, Sukses: ${d.success}, Gagal: ${d.failed}`,
                    );
                    done = true;
                    break;
                }

                if (status === 'completed_with_errors') {
                    const d = progress.data.data;
                    const html = `
                        <div>
                        <strong>Import selesai dengan error</strong>

                        <div style="margin-top:6px">
                            Total: <b>${d.total}</b><br>
                            Sukses: <b style="color:green">${d.success}</b><br>
                            Gagal: <b style="color:red">${d.failed}</b>
                        </div>

                        <hr>

                        <strong>Detail Error:</strong>
                        <ul>
                            ${d.errors.map((e) => `<li>${e}</li>`).join('')}
                        </ul>
                        </div>
                        `;

                    triggerAlert('warning', html, 15000, true);

                    done = true;
                    break;
                }

                if (status === 'failed') {
                    triggerAlert('danger', 'Import gagal diproses.');
                    done = true;
                    break;
                }
            }

            this.loadingImportGaji = false;
            this.importProcessingGaji = false;
            this.$emit('closeModal');
            this.$emit('refreshData');
        },

        downloadTemplate() {
            const link = document.createElement('a');
            link.href = '/template/template_import_gaji.xlsx'; // harus sama persis
            link.download = 'template_import_gaji.xlsx';
            document.body.appendChild(link);
            link.click();
            link.remove();
        },
    },
};
</script>
