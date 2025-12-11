<template>
    <AppLayout>
        <section class="page slip-page">
            <!-- HEADER -->
            <div class="page-header">
                <div>
                    <h2 class="page-title">Slip Gaji</h2>
                    <p class="page-subtitle">
                        Ringkasan komponen gaji dan slip PDF periode terpilih
                    </p>
                </div>

                <Button
                    variant="primary"
                    size="md"
                    @click="downloadSlip"
                    :disabled="!slip || !selectedPayrollPeriodId"
                    class="download-btn"
                >
                    Download PDF
                </Button>
            </div>

            <!-- PILIH PERIODE -->
            <div class="period-selector">
                <label class="form-label">Periode Payroll</label>
                <select
                    v-model="selectedPayrollPeriodId"
                    @change="loadSlip"
                    class="form-select"
                    :disabled="loadingImportPayslip"
                >
                    <!-- Placeholder -->
                    <option disabled value="">
                        -- Pilih Periode Payroll --
                    </option>

                    <!-- Data -->
                    <option
                        v-for="period in payrollPeriod"
                        :key="period.id"
                        :value="period.id"
                    >
                        {{ period.period_year }} /
                        {{ period.period_month.toString().padStart(2, '0') }}
                        ({{ period.start_date }} – {{ period.end_date }})
                    </option>
                </select>
            </div>

            <!-- STATE: BELUM PILIH PERIODE -->
            <div v-if="!selectedPayrollPeriodId" class="empty-state">
                <h3 class="empty-title">Pilih Periode Payroll</h3>
                <p class="empty-text">
                    Silakan pilih periode payroll di atas untuk melihat detail
                    slip gaji
                </p>
            </div>

            <!-- STATE: SUDAH PILIH, MASIH LOADING -->
            <div v-else-if="loadingImportPayslip" class="loading-item">
                <div class="loading-item-card">
                    <div class="spinner-item"></div>
                    <div class="loading-item-text">Memuat data Payroll</div>
                    <div class="loading-item-subtext">
                        Mohon tunggu sebentar
                    </div>
                </div>
            </div>

            <!-- STATE: SUDAH PILIH, TAPI SLIP KOSONG -->
            <div v-else-if="!slip" class="empty-state">
                <h3 class="empty-title">Slip Gaji Belum Tersedia</h3>
                <p class="empty-text">
                    Tidak ditemukan slip gaji untuk periode ini. Hubungi admin
                    payroll untuk informasi lebih lanjut.
                </p>
            </div>

            <!-- STATE: SLIP ADA -> TAMPILKAN DETAIL -->
            <div v-else class="slip-container">
                <!-- HEADER SLIP -->
                <div class="slip-card header-card">
                    <div class="company-info">
                        <div class="company-name">{{ slip.company_name }}</div>
                        <div class="company-subtitle">
                            Slip Gaji Periode {{ slip.period_name }}
                        </div>
                    </div>
                    <div class="period-info">
                        <div class="period-label">Periode</div>
                        <div class="period-value">{{ slip.period_range }}</div>
                    </div>
                </div>

                <!-- INFO GRID -->
                <div class="info-grid">
                    <div class="slip-card">
                        <h3 class="card-title">Data Karyawan</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="label">Nama</span>
                                <span class="value">{{
                                    slip.employee.nama
                                }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">NIK</span>
                                <span class="value">{{
                                    slip.employee.nik
                                }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Jabatan</span>
                                <span class="value">{{
                                    slip.employee.jabatan
                                }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Divisi</span>
                                <span class="value">{{
                                    slip.employee.divisi
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="slip-card">
                        <h3 class="card-title">Informasi Pembayaran</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="label">Tanggal Bayar</span>
                                <span class="value">
                                    {{ slip.payment.tanggal_bayar || '-' }}
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label">Metode</span>
                                <span class="value">{{
                                    slip.payment.metode
                                }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Bank</span>
                                <span class="value">{{
                                    slip.payment.bank
                                }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Status</span>
                                <span class="value">
                                    <span
                                        class="badge"
                                        :class="
                                            slip.payment.status === 'paid'
                                                ? 'badge-paid'
                                                : 'badge-unpaid'
                                        "
                                    >
                                        {{
                                            slip.payment.status === 'paid'
                                                ? 'Paid'
                                                : 'Unpaid'
                                        }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PENDAPATAN & POTONGAN -->
                <div class="amounts-grid">
                    <!-- Pendapatan -->
                    <div class="slip-card">
                        <h3 class="card-title earnings">Pendapatan</h3>
                        <table class="amount-table">
                            <tbody v-if="slip.earnings?.length">
                                <tr v-for="(item, i) in slip.earnings" :key="i">
                                    <td>{{ item.label }}</td>
                                    <td class="amount">
                                        {{ formatCurrency(item.amount) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="2" class="empty">
                                        Tidak ada pendapatan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="total earnings-total">
                            <span>Total Pendapatan</span>
                            <strong>{{
                                formatCurrency(slip.total_earnings || 0)
                            }}</strong>
                        </div>
                    </div>

                    <!-- Potongan -->
                    <div class="slip-card">
                        <h3 class="card-title deductions">Potongan</h3>
                        <table class="amount-table">
                            <tbody v-if="slip.deductions?.length">
                                <tr
                                    v-for="(item, i) in slip.deductions"
                                    :key="i"
                                >
                                    <td>{{ item.label }}</td>
                                    <td class="amount">
                                        {{ formatCurrency(item.amount) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="2" class="empty">
                                        Tidak ada potongan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="total deductions-total">
                            <span>Total Potongan</span>
                            <strong>{{
                                formatCurrency(slip.total_deductions || 0)
                            }}</strong>
                        </div>
                    </div>
                </div>

                <!-- TAKE HOME PAY -->
                <div class="slip-card netpay-card">
                    <div>
                        <h3 class="netpay-title">Take Home Pay</h3>
                        <p class="netpay-subtitle">
                            Gaji bersih yang Anda terima
                        </p>
                    </div>
                    <div class="netpay-amount">
                        {{ formatCurrency(slip.take_home_pay || 0) }}
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script>
import Button from '@/components/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import axios from 'axios';

export default {
    props: {
        payrollPeriodId: Number,
    },

    components: {
        AppLayout,
        Button,
    },
    data() {
        return {
            slip: null,
            loading: true,

            payrollPeriod: [],
            selectedPayrollPeriodId: '',
            loadingImportPayslip: false,
        };
    },

    mounted() {
        this.getPaymentPeriods();
        // this.loadSlip();
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
        downloadSlip() {
            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
            }).format(value ?? 0);
        },
        async loadSlip() {
            this.slip = null;
            this.loadingImportPayslip = true;
            try {
                const res = await axios.get(
                    `/payslip/show/` + this.selectedPayrollPeriodId,
                );
                console.log(res.data);
                this.slip = res.data.slip;
                this.loadingImportPayslip = false;
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.slip-page {
    /* max-width: 1100px;
    margin: 0 auto; */
    padding: 1.5rem;
}

/* HEADER */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    gap: 1rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.25rem 0;
}

.page-subtitle {
    color: #6b7280;
    margin: 0;
    font-size: 0.9rem;
}

.download-btn {
    padding: 0.625rem 1.25rem;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.download-btn:hover:not(:disabled) {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.download-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* PERIOD SELECTOR */
.period-selector {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.9rem;
    background: white;
    cursor: pointer;
    transition: border-color 0.2s;
}

.form-select:hover {
    border-color: #9ca3af;
}

.form-select:focus {
    outline: none;
    border-color: #2563eb;
}

/* EMPTY STATE */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 0.5rem 0;
}

.empty-text {
    color: #6b7280;
    margin: 0;
}

/* SPINNER */
.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e5e7eb;
    border-top-color: #2563eb;
    border-radius: 50%;
    margin: 0 auto 1rem;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* SLIP CONTAINER */
.slip-container {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.slip-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    transition: all 0.2s;
}

.slip-card:hover {
    border-color: #d1d5db;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* HEADER CARD */
.header-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border: 1px solid #e5e7eb;
}

.company-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.company-subtitle {
    color: #6b7280;
    font-size: 0.9rem;
}

.period-label {
    font-size: 0.8rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.period-value {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

/* INFO GRID */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 1rem 0;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-item .label {
    color: #6b7280;
    font-size: 0.9rem;
}

.info-item .value {
    color: #111827;
    font-weight: 500;
    text-align: right;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.badge-paid {
    background: #d1fae5;
    color: #065f46;
}

.badge-unpaid {
    background: #fee2e2;
    color: #991b1b;
}

/* AMOUNTS GRID */
.amounts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
}

.card-title.earnings {
    color: #059669;
}

.card-title.deductions {
    color: #dc2626;
}

.amount-table {
    width: 100%;
    border-collapse: collapse;
}

.amount-table td {
    padding: 0.625rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.amount-table tr:last-child td {
    border-bottom: none;
}

.amount-table td:first-child {
    color: #6b7280;
    font-size: 0.9rem;
}

.amount-table td.amount {
    text-align: right;
    color: #111827;
    font-weight: 500;
}

.amount-table .empty {
    text-align: center;
    color: #9ca3af;
    font-style: italic;
    padding: 1rem 0;
}

.total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.875rem;
    margin-top: 0.75rem;
    border-radius: 6px;
    font-size: 0.95rem;
}

.earnings-total {
    background: #ecfdf5;
    color: #065f46;
}

.deductions-total {
    background: #fef2f2;
    color: #991b1b;
}

.total strong {
    font-size: 1.1rem;
}

/* NETPAY CARD */
.netpay-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
}

.netpay-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e40af;
    margin: 0 0 0.25rem 0;
}

.netpay-subtitle {
    color: #3b82f6;
    margin: 0;
    font-size: 0.85rem;
}

.netpay-amount {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e40af;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .slip-page {
        padding: 1rem;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .netpay-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .netpay-amount {
        width: 100%;
        text-align: right;
    }

    .info-grid,
    .amounts-grid {
        grid-template-columns: 1fr;
    }
}
</style>
