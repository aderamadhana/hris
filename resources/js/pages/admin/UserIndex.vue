<template>
    <AppLayout>
        <section class="page employees-page">
            <!-- HEADER + ACTIONS -->
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Data Karyawan</h2>
                        <span class="page-chip">Master Data</span>
                    </div>
                    <p class="page-subtitle">
                        Kelola informasi karyawan, mulai dari penambahan data,
                        import massal, hingga penghapusan.
                    </p>
                </div>

                <div class="page-actions">
                    <Button variant="primary" @click="fiturBelumTersedia">
                        ➕ Tambah Karyawan
                    </Button>

                    <Button
                        variant="secondary"
                        @click="openImportKaryawanModal"
                    >
                        ⬆️ Import Karyawan
                    </Button>

                    <Button variant="success" @click="openImportPayslipModal">
                        🧾 Import Payslip
                    </Button>
                </div>
            </div>

            <!-- RINGKASAN DI ATAS TABEL -->
            <div class="overview-row">
                <div class="overview-card primary">
                    <div class="overview-label">Total Karyawan</div>
                    <div class="overview-value">{{ totalItems }}</div>
                    <div class="overview-meta">
                        Termasuk karyawan aktif dan nonaktif.
                    </div>
                </div>

                <div class="overview-card success">
                    <div class="overview-label">Aktif</div>
                    <div class="overview-value">{{ activeEmployees }}</div>
                    <div class="overview-meta">
                        Sedang bekerja di perusahaan.
                    </div>
                </div>

                <div class="overview-card neutral">
                    <div class="overview-label">Nonaktif</div>
                    <div class="overview-value">{{ inactiveEmployees }}</div>
                    <div class="overview-meta">
                        Resign, cuti panjang, atau kontrak selesai.
                    </div>
                </div>
            </div>

            <!-- KONTROL DATATABLE -->
            <div class="dt-toolbar">
                <div class="dt-length">
                    <label>
                        Tampil
                        <select v-model.number="perPage">
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                        </select>
                        data
                    </label>
                </div>

                <div class="dt-search">
                    <label>
                        Cari:
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Nama, NIK, atau jabatan..."
                        />
                    </label>
                </div>
            </div>

            <!-- TABEL DALAM CARD -->
            <div class="table-card">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="col-no">#</th>
                            <th class="col-name">Nama</th>
                            <th class="col-nik">NIK</th>
                            <th class="col-tanggal-lahir">Tanggal Lahir</th>
                            <th class="col-perusahaan">Perusahaan</th>
                            <th class="col-position">Jabatan</th>
                            <th class="col-status">Status</th>
                            <th class="col-action">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="paginatedUsers.length === 0">
                            <td colspan="8" class="empty-row">
                                Tidak ada data ...
                            </td>
                        </tr>

                        <tr v-for="(u, index) in paginatedUsers" :key="u.id">
                            <td>{{ startIndex + index + 1 }}</td>

                            <td>
                                <div class="cell-user">
                                    <div class="cell-avatar">
                                        {{ u.name.charAt(0) }}
                                    </div>
                                    <div class="cell-user-text">
                                        <div class="cell-name">
                                            {{ u.name }}
                                        </div>
                                        <div class="cell-dept">
                                            {{ u.department }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="cell-nik">{{ u.nik }}</span>
                            </td>

                            <td>
                                <span class="cell-tanggal-lahir">{{
                                    u.tanggal_lahir
                                }}</span>
                            </td>

                            <td>
                                <span class="cell-perusahaan">{{
                                    u.perusahaan
                                }}</span>
                            </td>

                            <td>{{ u.position }}</td>

                            <td>
                                <span
                                    class="status-pill"
                                    :class="{
                                        'status-active': u.status === 'Aktif',
                                        'status-inactive': u.status !== 'Aktif',
                                    }"
                                >
                                    {{ u.status }}
                                </span>
                            </td>

                            <td class="actions-cell">
                                <div class="action-group">
                                    <button
                                        type="button"
                                        class="action-icon view"
                                        title="Detail karyawan"
                                        @click="openDetail(u.id)"
                                    >
                                        <svg viewBox="0 0 24 24">
                                            <path
                                                d="M12 5C7 5 3.1 8 1.5 12c1.6 4 5.5 7 10.5 7s8.9-3 10.5-7C20.9 8 17 5 12 5Z"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="3"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.6"
                                            />
                                        </svg>
                                    </button>

                                    <!-- <button
                                        type="button"
                                        class="action-icon edit"
                                        title="Edit karyawan"
                                        @click="fiturBelumTersedia(u)"
                                    >
                                        <svg viewBox="0 0 24 24">
                                            <path
                                                d="M4 20h4l9.5-9.5a1.5 1.5 0 0 0 0-2.1L15.6 6.5a1.5 1.5 0 0 0-2.1 0L4 16v4Z"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        class="action-icon delete"
                                        title="Hapus karyawan"
                                        @click="fiturBelumTersedia(u)"
                                    >
                                        <svg viewBox="0 0 24 24">
                                            <path
                                                d="M5 7h14M10 10v7M14 10v7M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2M6 7l1 11a1.5 1.5 0 0 0 1.5 1.4h7a1.5 1.5 0 0 0 1.5-1.4L18 7"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </button> -->
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- FOOTER DATATABLE: INFO + PAGINATION -->
                <div class="dt-footer">
                    <div class="dt-info">
                        Menampilkan
                        <strong v-if="totalItems">{{ startIndex + 1 }}</strong>
                        <strong v-else>0</strong>
                        &nbsp;–&nbsp;
                        <strong>{{ endIndex }}</strong>
                        dari
                        <strong>{{ totalItems }}</strong>
                        karyawan
                    </div>

                    <div class="dt-pagination">
                        <button
                            class="dt-page-btn"
                            :disabled="currentPage === 1"
                            @click="goToPage(currentPage - 1)"
                        >
                            «
                        </button>
                        <button
                            v-for="page in pages"
                            :key="page"
                            class="dt-page-btn"
                            :class="{ active: page === currentPage }"
                            @click="goToPage(page)"
                        >
                            {{ page }}
                        </button>
                        <button
                            class="dt-page-btn"
                            :disabled="
                                currentPage === totalPages || totalPages === 0
                            "
                            @click="goToPage(currentPage + 1)"
                        >
                            »
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL IMPORT -->
            <Modal v-if="showImportKaryawanModal">
                <div class="modal-header">
                    <h3>Import Data Karyawan</h3>
                    <button
                        class="modal-close"
                        :disabled="loadingImportKaryawan"
                        @click="closeImportModal"
                    >
                        ✕
                    </button>
                </div>

                <p class="modal-text">
                    Pilih file Excel/CSV yang berisi data karyawan sesuai
                    template.
                </p>

                <div class="modal-body">
                    <input
                        type="file"
                        accept=".xlsx, .xls, .csv"
                        @change="handleFileChange"
                    />
                    <p class="hint">
                        Format disarankan: .xlsx atau .csv — pastikan kolom
                        sesuai template.
                    </p>
                </div>

                <div class="modal-footer">
                    <Button variant="secondary" @click="closeImportModal">
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

            <!-- MODAL IMPORT -->
            <Modal v-if="showImportPayslipModal">
                <div class="modal-header">
                    <h3>Import Data Payslip Karyawan</h3>
                    <button
                        class="modal-close"
                        :disabled="loadingImportPayslip"
                        @click="closeImportPayslipModal"
                    >
                        ✕
                    </button>
                </div>

                <p class="modal-text">
                    Pilih file Excel/CSV yang berisi data payslip karyawan dan
                    tentukan periode payroll.
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
                            :disabled="loadingImportPayslip"
                        >
                            <option value="" disabled>
                                -- Pilih Periode Payroll --
                            </option>
                            <option
                                v-for="period in payrollPeriod"
                                :key="period.id"
                                :value="period.id"
                            >
                                {{ period.period_year }} /
                                {{
                                    period.period_month
                                        .toString()
                                        .padStart(2, '0')
                                }}
                                ({{ period.start_date }} –
                                {{ period.end_date }})
                            </option>
                        </select>
                    </div>

                    <!-- FILE -->
                    <input
                        type="file"
                        accept=".xlsx, .xls, .csv"
                        @change="handleFilePayslipChange"
                    />
                    <p class="hint">
                        Format disarankan: .xlsx atau .csv — pastikan kolom
                        sesuai template.
                    </p>
                </div>

                <div class="modal-footer">
                    <Button
                        variant="secondary"
                        @click="closeImportPayslipModal"
                        :disabled="importProcessingPayslip"
                    >
                        Batal
                    </Button>
                    <Button
                        variant="primary"
                        :disabled="importProcessingPayslip"
                        @click="submitImportPayslip"
                        class="d-flex align-items-center justify-content-center gap-2"
                    >
                        <template v-if="importProcessingPayslip">
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
        </section>
    </AppLayout>
</template>

<script>
import Button from '@/components/Button.vue';
import Modal from '@/components/Modal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/Utils/alert';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    components: { Button, Modal, AppLayout },
    data() {
        return {
            users: [], // ← DATA DARI /employee
            currentPage: 1,
            perPage: 10,
            loadingUsers: false,

            search: '',
            sortKey: 'name',
            sortDir: 'asc',

            showImportKaryawanModal: false,
            selectedFileKaryawan: null,

            importIdKaryawan: null,
            importKaryawanResult: null,
            pollingTimer: null,
            loadingImportKaryawan: false,
            importProcessingKaryawan: false,

            showImportPayslipModal: false,
            selectedFilePayslip: null,

            importIdPayslip: null,
            importPayslipResult: null,
            loadingImportPayslip: false,
            importProcessingPayslip: false,

            payrollPeriod: [],
            selectedPayrollPeriodId: null,
        };
    },

    computed: {
        activeEmployees() {
            return this.filteredUsers.filter((u) => u.status === 'Aktif')
                .length;
        },

        inactiveEmployees() {
            return this.filteredUsers.filter((u) => u.status !== 'Aktif')
                .length;
        },

        filteredUsers() {
            if (!this.search) {
                return this.users;
            }

            const keyword = this.search.toLowerCase();

            return this.users.filter((u) => {
                return (
                    u.name.toLowerCase().includes(keyword) ||
                    u.nik.toLowerCase().includes(keyword) ||
                    u.position.toLowerCase().includes(keyword)
                );
            });
        },

        totalItems() {
            return this.filteredUsers.length;
        },

        totalPages() {
            return Math.ceil(this.totalItems / this.perPage);
        },

        startIndex() {
            return (this.currentPage - 1) * this.perPage;
        },

        endIndex() {
            return Math.min(this.startIndex + this.perPage, this.totalItems);
        },

        paginatedUsers() {
            return this.filteredUsers.slice(this.startIndex, this.endIndex);
        },

        pages() {
            return Array.from({ length: this.totalPages }, (_, i) => i + 1);
        },
    },

    watch: {
        search() {
            this.currentPage = 1;
        },
        perPage() {
            this.currentPage = 1;
        },
    },
    mounted() {
        this.getPaymentPeriods();
        this.fetchEmployees();
    },
    methods: {
        showSuccess() {
            this.triggerAlertHtml('success', '<b>Berhasil</b>', 3000);
        },
        fiturBelumTersedia() {
            triggerAlert('warning', 'Fitur masih dalam tahap pengembangan.');
        },
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
        async fetchEmployees() {
            this.loadingUsers = true;

            try {
                const res = await axios.get('/employee');
                this.users = res.data;
                this.currentPage = 1; // reset pagination
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memuat data karyawan.');
            } finally {
                this.loadingUsers = false;
            }
        },
        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
        },

        openImportKaryawanModal() {
            this.showImportKaryawanModal = true;
        },

        openImportPayslipModal() {
            this.showImportPayslipModal = true;
        },

        closeImportModal() {
            this.showImportKaryawanModal = false;
            this.selectedFileKaryawan = null;
        },

        closeImportPayslipModal() {
            this.showImportPayslipModal = false;
            this.selectedFilePayslip = null;
        },
        handleFileChange(e) {
            this.selectedFileKaryawan = e.target.files?.[0] || null;
        },
        handleFilePayslipChange(e) {
            this.selectedFilePayslip = e.target.files?.[0] || null;
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
                this.loadingImportKaryawan = false;
                this.fetchEmployees();
            }
        },

        startPollingImportKaryawanResult() {
            this.pollingTimer = setInterval(async () => {
                const res = await axios.get(
                    `/employee/import-log/${this.importIdKaryawan}`,
                );
                const log = res.data;

                this.importKaryawanResult = log;

                // ⛔ JANGAN triggerAlert di sini selama processing

                if (log.status === 'completed') {
                    clearInterval(this.pollingTimer);
                    this.pollingTimer = null;

                    this.importProcessingKaryawan = false;
                    this.loadingImportKaryawan = false;

                    triggerAlert(
                        'success',
                        `Import selesai. Sukses: ${log.success}, Gagal: ${log.failed}`,
                    );

                    this.closeImportModal();
                    this.fetchEmployees();
                }
            }, 2000);
        },

        async submitImportPayslip() {
            console.log(this.selectedPayrollPeriodId);
            if (!this.selectedPayrollPeriodId) {
                triggerAlert(
                    'warning',
                    'Silakan pilih payroll periods terlebih dahulu.',
                );
                return;
            }
            if (!this.selectedFilePayslip) {
                triggerAlert('warning', 'Silakan pilih file terlebih dahulu.');
                return;
            }

            this.loadingImportPayslip = true;
            this.importProcessingPayslip = true;
            const formData = new FormData();
            formData.append('file', this.selectedFilePayslip);
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
                this.pollImportPayslipProgress(result.check_progress_url);
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memulai proses import.');
                this.loadingImportKaryawan = false;
                this.fetchEmployees();
            }
        },

        async pollImportPayslipProgress(url) {
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

            this.loadingImportPayslip = false;
            this.importProcessingPayslip = false;
            this.closeImportPayslipModal();
            this.fetchEmployees();
        },

        openDetail(u) {
            router.visit(`/employee/profil/${u}`);
        },
        editUser(u) {
            alert(`Edit: ${u.name}`);
        },
        deleteUser(u) {
            if (confirm(`Hapus ${u.name}?`)) {
                alert('Deleted');
            }
        },
        createUser() {
            alert('Tambah karyawan');
        },
    },
};
</script>

<style scoped>
.employees-page {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* judul + chip */
.page-heading-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.page-chip {
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 12px;
    background: #eff6ff;
    color: #1d4ed8;
}

/* ringkasan */
.overview-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.overview-card {
    padding: 12px 14px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.overview-card.primary {
    background: radial-gradient(
        circle at top left,
        #dbeafe 0%,
        #eff6ff 55%,
        #ffffff 100%
    );
    border-color: #bfdbfe;
}

.overview-card.success {
    background: radial-gradient(
        circle at top left,
        #dcfce7 0%,
        #ecfdf3 55%,
        #ffffff 100%
    );
    border-color: #bbf7d0;
}

.overview-card.neutral {
    background: radial-gradient(
        circle at top left,
        #fee2e2 0%,
        #fef2f2 55%,
        #ffffff 100%
    );
    border-color: #fecaca;
}

.overview-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.overview-value {
    font-size: 22px;
    font-weight: 600;
    color: #111827;
}

.overview-meta {
    font-size: 12px;
    color: #6b7280;
}

/* cell nama + avatar kecil */
.cell-user {
    display: flex;
    align-items: center;
    gap: 8px;
}

.cell-avatar {
    width: 30px;
    height: 30px;
    border-radius: 999px;
    background: #e5f2ff;
    color: #1d4ed8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
}

.cell-user-text {
    display: flex;
    flex-direction: column;
}

.cell-name {
    font-size: 13px;
    font-weight: 500;
    color: #111827;
}

.cell-dept {
    font-size: 12px;
    color: #6b7280;
}

.cell-nik {
    font-family:
        ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas,
        'Liberation Mono', 'Courier New', monospace;
    font-size: 12px;
}

/* status pill */
.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 76px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
}

.status-active {
    background: #ecfdf3;
    color: #15803d;
}

.status-inactive {
    background: #fef2f2;
    color: #b91c1c;
}

/* ikon aksi */
.action-group {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.action-icon {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition:
        transform 0.12s ease,
        box-shadow 0.12s ease,
        background-color 0.12s ease;
}

.action-icon svg {
    width: 16px;
    height: 16px;
}

.action-icon.view {
    background: #eff6ff;
    color: #1d4ed8;
}

.action-icon.edit {
    background: #fef3c7;
    color: #92400e;
}

.action-icon.delete {
    background: #fee2e2;
    color: #b91c1c;
}

.action-icon:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(15, 23, 42, 0.16);
}

/* responsive */
@media (max-width: 900px) {
    .overview-row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .overview-row {
        grid-template-columns: minmax(0, 1fr);
    }

    .actions-cell {
        justify-content: flex-start;
    }
}
.toast-container {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 9999;
}
.toast {
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    background: #333;
    color: white;
    border-radius: 4px;
}
.toast--success {
    background: #16a34a;
}
.toast--error {
    background: #dc2626;
}
.toast--info {
    background: #2563eb;
}
</style>
