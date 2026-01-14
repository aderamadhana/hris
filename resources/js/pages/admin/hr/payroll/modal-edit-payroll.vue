<template>
    <Modal size="lg">
        <div class="modal-header">
            <h3>Edit Periode Gaji</h3>
            <button
                class="modal-close"
                :disabled="loadingImportGaji"
                @click="$emit('closeModal')"
            >
                ✕
            </button>
        </div>

        <p class="modal-text">
            Edit atau hapus periode gaji sesuai data yang benar
        </p>

        <div class="modal-body">
            <div class="detail-grid two-col">
                <div class="form-group">
                    <label class="field-label"> Judul Periode </label>
                    <input
                        type="text"
                        v-model="form.judul_periode"
                        class="form-input"
                        placeholder="Contoh: Payroll Januari 2024"
                    />
                    <span v-if="errors.judul_periode" class="error-text">
                        {{ errors.judul_periode }}
                    </span>
                </div>

                <div class="form-group">
                    <label class="field-label">
                        Tahun
                        <span class="required">*</span>
                    </label>
                    <select
                        v-model="form.period_year"
                        class="form-input"
                        required
                    >
                        <option value="">Pilih Tahun</option>
                        <option v-for="year in years" :key="year" :value="year">
                            {{ year }}
                        </option>
                    </select>
                    <span v-if="errors.period_year" class="error-text">
                        {{ errors.period_year }}
                    </span>
                </div>

                <div class="form-group">
                    <label class="field-label">
                        Bulan
                        <span class="required">*</span>
                    </label>
                    <select
                        v-model="form.period_month"
                        class="form-input"
                        required
                    >
                        <option value="">Pilih Bulan</option>
                        <option
                            v-for="month in months"
                            :key="month.value"
                            :value="month.value"
                        >
                            {{ month.label }}
                        </option>
                    </select>
                    <span v-if="errors.period_month" class="error-text">
                        {{ errors.period_month }}
                    </span>
                </div>

                <div class="form-group">
                    <label class="field-label">
                        Status
                        <span class="required">*</span>
                    </label>
                    <select v-model="form.status" class="form-input" required>
                        <option value="">Pilih Status</option>
                        <option value="open">Terbuka</option>
                        <option value="closed">Ditutup</option>
                        <option value="processed">Diproses</option>
                    </select>
                    <span v-if="errors.status" class="error-text">
                        {{ errors.status }}
                    </span>
                </div>

                <div class="form-group">
                    <label class="field-label">
                        Tanggal Mulai
                        <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        v-model="form.start_date"
                        class="form-input"
                        required
                    />
                    <span v-if="errors.start_date" class="error-text">
                        {{ errors.start_date }}
                    </span>
                </div>

                <div class="form-group">
                    <label class="field-label">
                        Tanggal Selesai
                        <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        v-model="form.end_date"
                        :min="form.start_date"
                        class="form-input"
                        required
                    />
                    <span v-if="errors.end_date" class="error-text">
                        {{ errors.end_date }}
                    </span>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <Button
                variant="secondary"
                @click="$emit('closeModal')"
                :disabled="submitProcessingUpdate"
            >
                Batal
            </Button>

            <Button
                variant="danger"
                @click="deletePeriod"
                class="d-flex align-items-center justify-content-center gap-2"
            >
                <template v-if="processingDelete">
                    <span
                        class="spinner-border spinner-border-sm spinner"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    <span>Memproses...</span>
                </template>

                <template v-else> Hapus </template>
            </Button>
            <Button
                variant="primary"
                @click="submitPeriodeGaji"
                class="d-flex align-items-center justify-content-center gap-2"
            >
                <template v-if="processingUpdate">
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
import { triggerAlert } from '@/utils/alert';
import axios from 'axios';

import { router } from '@inertiajs/vue3';

export default {
    components: {
        Modal,
        Button,
    },
    props: {
        period: {
            type: Object, // atau String/Number sesuai data kamu
            required: true,
        },
    },
    data() {
        return {
            loading: false,
            processingUpdate: false,
            processingDelete: false,
            form: {
                judul_periode: this.period?.judul || '',
                period_year: this.period?.year ?? '',
                period_month: this.period?.month ?? '',
                start_date: this.toDateInput(this.period?.start_date),
                end_date: this.toDateInput(this.period?.end_date),
                status: this.period?.status ?? 'open',
            },
            errors: {},
            months: [
                { value: 1, label: 'Januari' },
                { value: 2, label: 'Februari' },
                { value: 3, label: 'Maret' },
                { value: 4, label: 'April' },
                { value: 5, label: 'Mei' },
                { value: 6, label: 'Juni' },
                { value: 7, label: 'Juli' },
                { value: 8, label: 'Agustus' },
                { value: 9, label: 'September' },
                { value: 10, label: 'Oktober' },
                { value: 11, label: 'November' },
                { value: 12, label: 'Desember' },
            ],
            years: Array.from({ length: 21 }, (_, i) => 2020 + i),
        };
    },
    methods: {
        toDateInput(dmy) {
            // expect "DD/MM/YYYY"
            if (!dmy || typeof dmy !== 'string') return '';
            const parts = dmy.split('/');
            if (parts.length !== 3) return '';
            const [dd, mm, yyyy] = parts.map((p) => p.trim());
            if (!dd || !mm || !yyyy) return '';
            return `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
        },

        async submitPeriodeGaji() {
            this.processingUpdate = true;

            router.put(`/hr/payroll/update/${this.period.id}`, this.form, {
                onSuccess: () => {
                    triggerAlert('success', 'Payroll period berhasil diupdate');
                    this.$emit('closeModal');
                    this.$emit('refreshData');
                },

                onError: (errors) => {
                    this.errors = errors;
                    triggerAlert('error', 'Periksa kembali data yang diinput');
                    this.$emit('closeModal');
                    this.$emit('refreshData');
                },

                onFinish: () => {
                    this.processingUpdate = false;

                    this.$emit('closeModal');
                    this.$emit('refreshData');
                },
            });
        },

        async deletePeriod() {
            if (!confirm('Apakah Anda yakin ingin menghapus periode ini?'))
                return;

            try {
                this.processingDelete = true;
                await axios.delete(`/hr/payroll/delete/${this.period.id}`);

                triggerAlert('success', 'Periode payroll berhasil dihapus');

                this.processingDelete = false;
                this.$emit('closeModal');
                this.$emit('refreshData');
            } catch (error) {
                this.processingDelete = false;
                console.error(error);
                this.$emit('closeModal');
                this.$emit('refreshData');
            }
        },
    },
};
</script>
