<script>
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    props: {
        period: {
            type: Object,
            required: true,
        },
    },
    components: {
        AppLayout,
        Link,
    },

    data() {
        return {
            loading: false,
            processing: false,
            form: {
                judul_periode: this.period.judul_periode || '',
                period_year: this.period.period_year,
                period_month: this.period.period_month,
                start_date: this.period.start_date,
                end_date: this.period.end_date,
                status: this.period.status,
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

    mounted() {
        this.getDataPeriod();
    },

    methods: {
        async getDataPeriod() {
            try {
                this.loading = true;

                const res = await axios.get(
                    `/master/payroll-period/get-data/${this.period.id}`,
                );

                const period = res.data.data;

                // isi form / state
                this.form = {
                    judul_periode: period.judul_periode ?? '',
                    period_year: period.period_year,
                    period_month: period.period_month,
                    start_date: period.start_date,
                    end_date: period.end_date,
                    status: period.status,
                };
            } catch (error) {
                console.error(error);
                triggerAlert('error', 'Gagal mengambil data payroll period');
            } finally {
                this.loading = false;
            }
        },
        submitForm() {
            this.processing = true;

            router.put(
                `/master/payroll-period/update/${this.period.id}`,
                this.form,
                {
                    onSuccess: () => {
                        triggerAlert(
                            'success',
                            'Payroll period berhasil diupdate',
                        );
                    },

                    onError: (errors) => {
                        this.errors = errors;
                        triggerAlert(
                            'error',
                            'Periksa kembali data yang diinput',
                        );
                    },

                    onFinish: () => {
                        this.processing = false;
                    },
                },
            );
        },
    },
};
</script>

<template>
    <AppLayout>
        <div class="page-container">
            <div class="page-header">
                <h1 class="page-title">Edit Periode Gaji</h1>
            </div>

            <div class="card">
                <form @submit.prevent="submitForm">
                    <div class="detail-grid two-col">
                        <div class="form-group">
                            <label class="field-label"> Judul Periode </label>
                            <input
                                type="text"
                                v-model="form.judul_periode"
                                class="form-input"
                                placeholder="Contoh: Payroll Januari 2024"
                            />
                            <span
                                v-if="errors.judul_periode"
                                class="error-text"
                            >
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
                                <option
                                    v-for="year in years"
                                    :key="year"
                                    :value="year"
                                >
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
                            <select
                                v-model="form.status"
                                class="form-input"
                                required
                            >
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
                                class="form-input"
                                required
                            />
                            <span v-if="errors.end_date" class="error-text">
                                {{ errors.end_date }}
                            </span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <Link
                            href="/master/payroll-period/all-data"
                            class="btn btn-secondary"
                        >
                            Batal
                        </Link>
                        <button type="submit" class="btn btn-primary">
                            <template v-if="processing">
                                <span
                                    class="spinner-border spinner-border-sm spinner"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                <span>Memproses...</span>
                            </template>

                            <template v-else> Simpan </template>
                        </button>
                    </div>
                </form>
            </div>
        </div></AppLayout
    >
</template>
