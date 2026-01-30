<template>
    <Modal size="lg">
        <div class="modal-header">
            <h3>Tambah Surat Peringatan</h3>
            <button class="modal-close" @click="$emit('closeModal')">✕</button>
        </div>

        <p class="modal-text">Tambah surat peringatan sesuai data yang benar</p>

        <div class="modal-body">
            <div class="detail-grid two-col">
                <!-- Nomor SP -->
                <div class="form-group">
                    <label class="field-label">
                        Nomor SP <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.nomor_sp"
                        class="form-input"
                        placeholder="Contoh: SP-001/HR/2026"
                        required
                    />
                    <span v-if="errors.nomor_sp" class="error-text">
                        {{ errors.nomor_sp }}
                    </span>
                </div>

                <!-- Tanggal SP -->
                <div class="form-group">
                    <label class="field-label">
                        Tanggal SP <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        v-model="form.tanggal_sp"
                        class="form-input"
                        required
                    />
                    <span v-if="errors.tanggal_sp" class="error-text">
                        {{ errors.tanggal_sp }}
                    </span>
                </div>

                <!-- Tingkat -->
                <div class="form-group">
                    <label class="field-label">
                        Tingkat <span class="required">*</span>
                    </label>
                    <select v-model="form.tingkat" class="form-input" required>
                        <option value="SP1">SP1</option>
                        <option value="SP2">SP2</option>
                        <option value="SP3">SP3</option>
                    </select>
                    <span v-if="errors.tingkat" class="error-text">
                        {{ errors.tingkat }}
                    </span>
                </div>

                <!-- Tanggal Kejadian (opsional) -->
                <div class="form-group">
                    <label class="field-label">Tanggal Kejadian</label>
                    <input
                        type="date"
                        v-model="form.tanggal_kejadian"
                        class="form-input"
                    />
                    <span v-if="errors.tanggal_kejadian" class="error-text">
                        {{ errors.tanggal_kejadian }}
                    </span>
                </div>

                <!-- Periode Bulan -->
                <div class="form-group">
                    <label class="field-label">
                        Periode (Bulan) <span class="required">*</span>
                    </label>
                    <select
                        v-model.number="form.periode_bulan"
                        class="form-input"
                        required
                    >
                        <option :value="''">-- Pilih Periode --</option>
                        <option v-for="m in periodeOptions" :key="m" :value="m">
                            {{ m }} bulan
                        </option>
                    </select>
                    <span v-if="errors.periode_bulan" class="error-text">
                        {{ errors.periode_bulan }}
                    </span>
                </div>

                <!-- Tanggal Berakhir (readonly/disabled tampil) -->
                <div class="form-group">
                    <label class="field-label">Tanggal Berakhir</label>

                    <!-- tampil: disabled supaya tidak bisa diubah -->
                    <input
                        type="date"
                        class="form-input"
                        :value="form.tanggal_berakhir"
                        disabled
                        :placeholder="
                            !form.periode_bulan
                                ? 'Pilih periode bulan dulu'
                                : ''
                        "
                    />

                    <!-- ini yang ikut terkirim -->
                    <input type="hidden" v-model="form.tanggal_berakhir" />

                    <small
                        v-if="!form.tanggal_sp && form.periode_bulan"
                        class="text-muted"
                    >
                        Isi tanggal SP untuk menghitung tanggal berakhir.
                    </small>

                    <span v-if="errors.tanggal_berakhir" class="error-text">
                        {{ errors.tanggal_berakhir }}
                    </span>
                </div>

                <!-- Karyawan (employee_id) -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">
                        Karyawan <span class="required">*</span>
                    </label>

                    <Select2
                        id="karyawan"
                        v-model="form.employee_id"
                        class="form-input"
                        required
                    >
                        <option value="">-- Pilih Karyawan --</option>
                        <option
                            v-for="karyawan in data_karyawan"
                            :key="karyawan.id"
                            :value="karyawan.id"
                        >
                            {{ karyawan.nama
                            }}{{ karyawan.nip ? ' - ' + karyawan.nip : '' }}
                        </option>
                    </Select2>

                    <span v-if="errors.employee_id" class="error-text">
                        {{ errors.employee_id }}
                    </span>
                </div>

                <!-- Pelanggaran -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">
                        Pelanggaran / Alasan <span class="required">*</span>
                    </label>
                    <textarea
                        v-model="form.pelanggaran"
                        class="form-input"
                        placeholder="Tuliskan pelanggaran/alasan SP"
                        rows="4"
                        required
                    ></textarea>
                    <span v-if="errors.pelanggaran" class="error-text">
                        {{ errors.pelanggaran }}
                    </span>
                </div>

                <!-- Upload File -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">
                        Upload File Surat Peringatan
                        <span class="required">*</span>
                    </label>

                    <input
                        type="file"
                        class="form-input"
                        @change="handleFile"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        required
                    />

                    <small v-if="fileName" class="text-muted">
                        File dipilih: {{ fileName }}
                    </small>

                    <span v-if="errors.file" class="error-text">
                        {{ errors.file }}
                    </span>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <Button variant="secondary" @click="$emit('closeModal')">
                Batal
            </Button>

            <Button
                variant="primary"
                @click="submitSP"
                class="d-flex align-items-center justify-content-center gap-2"
            >
                <template v-if="processing">
                    <span
                        class="spinner-border spinner-border-sm spinner"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    <span>Memproses...</span>
                </template>

                <template v-else> Simpan </template>
            </Button>
        </div>
    </Modal>
</template>

<script>
import Button from '@/components/Button.vue';
import Modal from '@/components/Modal.vue';
import Select2 from '@/components/Select2.vue';
import { triggerAlert } from '@/utils/alert';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    components: { Modal, Button, Select2 },

    data() {
        return {
            processing: false,
            fileName: '',
            periodeOptions: [1, 2, 3, 6, 12], // ubah sesuai kebutuhan

            form: {
                nomor_sp: '',
                tanggal_sp: '',
                tingkat: 'SP1',
                tanggal_kejadian: '',
                employee_id: '',
                pelanggaran: '',
                periode_bulan: '',
                tanggal_berakhir: '',
                file: null,
            },

            errors: {},
            data_karyawan: [],
        };
    },

    watch: {
        'form.tanggal_sp': 'syncTanggalBerakhir',
        'form.periode_bulan': 'syncTanggalBerakhir',
    },

    mounted() {
        this.fetchKaryawan();
    },

    methods: {
        handleFile(e) {
            const file = e.target.files?.[0] || null;
            this.form.file = file;
            this.fileName = file ? file.name : '';
        },

        // hitung tanggal_berakhir = tanggal_sp + periode_bulan (bulan)
        syncTanggalBerakhir() {
            const sp = this.form.tanggal_sp;
            const months = this.form.periode_bulan;

            if (!sp || !months) {
                this.form.tanggal_berakhir = '';
                return;
            }

            const end = this.addMonthsYYYYMMDD(sp, Number(months));
            this.form.tanggal_berakhir = end;
        },

        // input: "YYYY-MM-DD" + N months -> output "YYYY-MM-DD"
        addMonthsYYYYMMDD(ymd, monthsToAdd) {
            const [Y, M, D] = (ymd || '').split('-').map(Number);
            if (!Y || !M || !D) return '';

            // pakai Date(y, m-1, d) supaya tidak offset timezone dari Date("YYYY-MM-DD")
            const origDay = D;

            // tentukan target month/year
            const baseMonthIndex = M - 1 + monthsToAdd;
            const targetYear = Y + Math.floor(baseMonthIndex / 12);
            const targetMonth = ((baseMonthIndex % 12) + 12) % 12; // 0..11 aman

            const lastDay = this.daysInMonth(targetYear, targetMonth);
            const clampedDay = Math.min(origDay, lastDay);

            const dt = new Date(targetYear, targetMonth, clampedDay);
            return this.formatYYYYMMDD(dt);
        },

        daysInMonth(year, monthIndex0) {
            // monthIndex0: 0..11, day=0 => hari terakhir bulan sebelumnya
            return new Date(year, monthIndex0 + 1, 0).getDate();
        },

        formatYYYYMMDD(dt) {
            const yyyy = dt.getFullYear();
            const mm = String(dt.getMonth() + 1).padStart(2, '0');
            const dd = String(dt.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        },

        submitSP() {
            this.processing = true;
            this.errors = {};

            // safety: pastikan tanggal_berakhir terset sebelum submit
            this.syncTanggalBerakhir();

            router.post('/hr/surat-peringatan/store', this.form, {
                forceFormData: true,

                onSuccess: () => {
                    triggerAlert(
                        'success',
                        'Surat peringatan berhasil disimpan',
                    );
                    this.$emit('closeModal');
                    this.$emit('refreshData');
                },

                onError: (errors) => {
                    this.errors = errors;
                    triggerAlert('error', 'Periksa kembali data yang diinput');
                },

                onFinish: () => {
                    this.processing = false;
                },
            });
        },

        async fetchKaryawan() {
            try {
                const res = await axios.get('/referensi/karyawan');
                this.data_karyawan = res.data.data || [];
            } catch (err) {
                triggerAlert('error', 'Gagal memuat karyawan.');
            }
        },
    },
};
</script>
