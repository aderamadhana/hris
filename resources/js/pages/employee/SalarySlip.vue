<template>
    <AppLayout>
        <section class="page slip-page">
            <!-- HEADER -->
            <div class="page-header">
                <div>
                    <h2 class="page-title">Slip Gaji</h2>
                    <p class="page-subtitle">
                        Ringkasan komponen gaji dan slip PDF periode terpilih.
                    </p>
                </div>

                <div class="page-actions">
                    <Button variant="primary" size="md" @click="downloadSlip">
                        ⬇️ Download PDF
                    </Button>
                </div>
            </div>

            <div class="card slip-card" v-if="slip">
                <!-- HEADER SLIP -->
                <div class="slip-header">
                    <div class="slip-company">
                        <div class="slip-company-name">
                            {{ slip.company_name }}
                        </div>
                        <div class="slip-company-sub">
                            Slip gaji karyawan · Periode
                            <strong>{{ slip.period_name }}</strong>
                        </div>
                    </div>

                    <div class="slip-period-badge">
                        <span>Periode</span>
                        <strong>{{ slip.period_range }}</strong>
                    </div>
                </div>

                <!-- INFO -->
                <div class="slip-info-grid">
                    <div class="slip-info-block">
                        <div class="slip-info-title">Data Karyawan</div>
                        <dl>
                            <div class="row">
                                <dt>Nama</dt>
                                <dd>{{ slip.employee.nama }}</dd>
                            </div>
                            <div class="row">
                                <dt>NIK</dt>
                                <dd>{{ slip.employee.nik }}</dd>
                            </div>
                            <div class="row">
                                <dt>Jabatan</dt>
                                <dd>{{ slip.employee.jabatan }}</dd>
                            </div>
                            <div class="row">
                                <dt>Divisi</dt>
                                <dd>{{ slip.employee.divisi }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="slip-info-block">
                        <div class="slip-info-title">Informasi Pembayaran</div>
                        <dl>
                            <div class="row">
                                <dt>Tanggal Bayar</dt>
                                <dd>
                                    {{ slip.payment.tanggal_bayar || '-' }}
                                </dd>
                            </div>
                            <div class="row">
                                <dt>Metode</dt>
                                <dd>{{ slip.payment.metode }}</dd>
                            </div>
                            <div class="row">
                                <dt>Bank</dt>
                                <dd>{{ slip.payment.bank }}</dd>
                            </div>
                            <div class="row">
                                <dt>Status</dt>
                                <dd>
                                    <span
                                        class="pill"
                                        :class="
                                            slip.payment.status === 'paid'
                                                ? 'pill-success'
                                                : 'pill-warning'
                                        "
                                    >
                                        {{
                                            slip.payment.status === 'paid'
                                                ? 'Paid'
                                                : 'Unpaid'
                                        }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- PENDAPATAN & POTONGAN -->
                <div class="slip-amounts">
                    <!-- Pendapatan -->
                    <div class="slip-table-block">
                        <div class="slip-info-title">Pendapatan</div>
                        <table class="slip-table">
                            <tbody v-if="slip.earnings.length">
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
                            <tfoot>
                                <tr>
                                    <th>Total Pendapatan</th>
                                    <th class="amount">
                                        {{
                                            formatCurrency(
                                                slip.total_earnings || 0,
                                            )
                                        }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Potongan -->
                    <div class="slip-table-block">
                        <div class="slip-info-title">Potongan</div>
                        <table class="slip-table">
                            <tbody v-if="slip.deductions.length">
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
                            <tfoot>
                                <tr>
                                    <th>Total Potongan</th>
                                    <th class="amount">
                                        {{
                                            formatCurrency(
                                                slip.total_deductions || 0,
                                            )
                                        }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TAKE HOME PAY -->
                <div class="slip-netpay">
                    <div>
                        <div class="slip-info-title">Take Home Pay</div>
                        <p class="slip-netpay-caption">
                            Gaji bersih diterima karyawan.
                        </p>
                    </div>
                    <div class="slip-netpay-amount">
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
        };
    },

    mounted() {
        this.loadSlip();
    },

    methods: {
        downloadSlip() {
            alert('Download PDF belum tersedia');
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
            }).format(value ?? 0);
        },
        async loadSlip() {
            try {
                const res = await axios.get(`/payslip/show/1`);
                console.log(res.data);
                this.slip = res.data.slip;
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.slip-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* kartu slip gaji */
.slip-card {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* header atas */
.slip-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.slip-company-name {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
}

.slip-company-sub {
    margin-top: 2px;
    font-size: 14px;
    color: #6b7280;
}

.slip-period-badge {
    align-self: flex-start;
    padding: 6px 12px;
    border-radius: 10px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    font-size: 12px;
    color: #4b5563;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.slip-period-badge span {
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-size: 11px;
    color: #9ca3af;
}
.slip-period-badge strong {
    font-size: 13px;
    color: #111827;
}

/* info karyawan & pembayaran */
.slip-info-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.5fr);
    gap: 18px;
}

.slip-info-block {
    background: #f9fafb;
    border-radius: 12px;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
}

.slip-info-title {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #6b7280;
    margin-bottom: 6px;
}

/* definisi list */
dl {
    margin: 0;
}
.row {
    display: grid;
    grid-template-columns: 110px minmax(0, 1fr);
    gap: 4px;
    padding-block: 2px;
}
dt {
    font-size: 13px;
    color: #6b7280;
}
dd {
    margin: 0;
    font-size: 14px;
    color: #111827;
}

/* badge status */
.pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 11px;
    border: 1px solid transparent;
}
.pill-success {
    background: #ecfdf3;
    border-color: #bbf7d0;
    color: #15803d;
}

/* pendapatan & potongan */
.slip-amounts {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.5fr);
    gap: 18px;
}

.slip-table-block {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 10px 12px 8px;
}

/* tabel komponen */
.slip-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 4px;
    font-size: 14px;
}

.slip-table td,
.slip-table th {
    padding: 4px 0;
}

.slip-table tbody tr + tr td {
    border-top: 1px dashed #e5e7eb;
}

.slip-table tfoot tr th {
    padding-top: 6px;
    border-top: 1px solid #d1d5db;
    font-weight: 600;
}

.slip-table .amount {
    text-align: right;
    white-space: nowrap;
}

/* take home pay */
.slip-netpay {
    margin-top: 4px;
    border-radius: 14px;
    border: 1px solid #bbf7d0;
    background: linear-gradient(90deg, #ecfdf3 0%, #f9fafb 55%);
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    box-shadow: 0 10px 24px rgba(16, 185, 129, 0.18);
}

.slip-netpay-caption {
    margin: 3px 0 0;
    font-size: 13px;
    color: #6b7280;
}

.slip-netpay-amount {
    display: inline-flex;
    align-items: baseline;
    gap: 4px;
    font-size: 18px;
    font-weight: 600;
    color: #14532d;
}
.slip-netpay-amount span {
    font-size: 14px;
    color: #166534;
}

/* responsif dasar */
@media (max-width: 900px) {
    .slip-info-grid,
    .slip-amounts {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
