<template>
    <AppLayout>
        <section class="page companies-page">
            <div class="page-header">
                <div>
                    <div class="page-heading-row">
                        <h2 class="page-title">Log Aktifitas</h2>
                    </div>

                    <p class="page-subtitle">
                        Monitoring log aktifitas semua karyawan
                    </p>
                </div>
                <div class="page-actions">
                    <Button
                        v-if="user.role_id != 2"
                        variant="success"
                        size="md"
                        @click="openImportKaryawanModal"
                        class="download-btn"
                    >
                        <font-awesome-icon icon="upload" class="icon" />
                        Upload Log Aktifitas
                    </Button>
                    <Button
                        variant="warning"
                        size="md"
                        :loading="isDownloading"
                        @click="downloadAktifitas()"
                        class="download-btn"
                    >
                        <font-awesome-icon icon="download" class="icon" />
                        Download Log Aktifitas
                    </Button>
                    <Button
                        variant="primary"
                        size="md"
                        @click="openActivityModal"
                        class="download-btn"
                    >
                        <font-awesome-icon icon="plus" class="icon" />
                        Tambah Log Aktifitas
                    </Button>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="dt-toolbar-mobile">
                <!-- Row 1: Length & Search -->
                <div class="dt-row-main">
                    <label class="dt-length-compact">
                        Tampil
                        <select
                            v-model.number="perPage"
                            @change="fetchLogActivities"
                        >
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                        data
                    </label>

                    <div class="dt-search-compact">
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Cari nama karyawan"
                        />
                    </div>
                </div>

                <!-- Row 2: Filters (collapsible) -->
                <div class="dt-filters-wrapper">
                    <button
                        class="filter-toggle-btn"
                        @click="showFilters = !showFilters"
                    >
                        <font-awesome-icon icon="filter" />
                        <span>Filter</span>
                        <font-awesome-icon
                            :icon="showFilters ? 'chevron-up' : 'chevron-down'"
                            class="toggle-icon"
                        />
                    </button>

                    <div class="dt-filters" :class="{ show: showFilters }">
                        <div class="form-group" v-if="user.role_id != 2">
                            <label for="">Perusahaan</label>
                            <Select2
                                v-model="filtered_perusahaan"
                                :settings="{ width: '100%' }"
                            >
                                <option value="">Semua Perusahaan</option>
                                <option
                                    v-for="value in data_filtered_perusahaan"
                                    :key="value"
                                    :value="value.id"
                                >
                                    {{ value.nama_perusahaan }}
                                </option>
                            </Select2>
                        </div>
                        <div class="form-group" v-if="user.role_id != 2">
                            <label for="">Divisi / Dept</label>
                            <Select2
                                v-model="filtered_jabatan"
                                :settings="{ width: '100%' }"
                                :disabled="!filtered_perusahaan"
                            >
                                <option value="">Semua Divisi / Dept</option>
                                <option
                                    v-for="value in data_filtered_jabatan"
                                    :key="value"
                                    :value="value.id"
                                >
                                    {{ value.nama_divisi }}
                                </option>
                            </Select2>
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Dari</label>
                            <input
                                type="date"
                                v-model="filtered_tanggal_dari"
                                class="form-control"
                            />
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Sampai</label>
                            <input
                                type="date"
                                :min="filtered_tanggal_dari"
                                v-model="filtered_tanggal_sampai"
                                class="form-control"
                            />
                        </div>

                        <!-- <div class="form-group filter-actions">
                            <label class="filter-label">&nbsp;</label>
                            <Button
                                variant="secondary"
                                class="filter-apply-btn"
                                @click="filteredData"
                            >
                                <font-awesome-icon icon="filter" />
                                Terapkan Filter
                            </Button>
                        </div> -->
                    </div>
                </div>

                <!-- TABLE CARD -->
                <div class="table-card card">
                    <div class="table-responsive-custom">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Karyawan</th>
                                    <th>Perusahaan / Penempatan</th>
                                    <th>Aktivitas & Jam Kerja</th>
                                    <th>Hasil & Biaya</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="6" class="loading-row">
                                        <div class="table-spinner">
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <tr v-else-if="items.length === 0">
                                    <td colspan="6" class="empty-row">
                                        Tidak ada data
                                    </td>
                                </tr>

                                <tr
                                    v-else
                                    v-for="(item, index) in items"
                                    :key="item.id"
                                >
                                    <td>{{ startIndex + index + 1 }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="cell-konfig"
                                            title="Klik untuk edit pengumuman"
                                            @click="editActivity(item)"
                                        >
                                            <span class="cell-konfig__title">
                                                {{ item.tanggal || '-' }}
                                            </span>
                                            <span class="cell-konfig__hint">
                                                Klik untuk edit log aktifitas
                                            </span>
                                        </button>
                                    </td>
                                    <td>{{ item.nama_karyawan }}</td>
                                    <td>
                                        {{ item.nama_perusahaan }}
                                        <div class="subtext">
                                            {{ item.nama_divisi }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="aktifitas-cell">
                                            <div class="aktifitas-main">
                                                <div class="aktifitas-kode">
                                                    {{
                                                        item.data_aktifitas
                                                            ?.kode_kerja || '-'
                                                    }}
                                                </div>

                                                <div class="aktifitas-shift">
                                                    Shift:
                                                    {{
                                                        item.data_aktifitas
                                                            ?.nama_shift || '-'
                                                    }}
                                                </div>

                                                <div class="aktifitas-jam">
                                                    Jam:
                                                    <span
                                                        v-if="
                                                            item.data_aktifitas
                                                                ?.jam_masuk &&
                                                            item.data_aktifitas
                                                                ?.jam_pulang
                                                        "
                                                    >
                                                        {{
                                                            toHHMM(
                                                                item
                                                                    .data_aktifitas
                                                                    .jam_masuk,
                                                            )
                                                        }}
                                                        –
                                                        {{
                                                            toHHMM(
                                                                item
                                                                    .data_aktifitas
                                                                    .jam_pulang,
                                                            )
                                                        }}
                                                    </span>
                                                    <span v-else>-</span>
                                                </div>

                                                <div class="aktifitas-durasi">
                                                    Durasi:
                                                    {{
                                                        item.data_aktifitas
                                                            ?.total_jam_kerja_hhmm ||
                                                        '-'
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="hasil-cell">
                                            <div>
                                                Hasil:
                                                {{
                                                    item.data_aktifitas
                                                        ?.hasil_kerja ?? 0
                                                }}
                                            </div>

                                            <div
                                                v-if="
                                                    item.data_aktifitas
                                                        ?.hasil_lembur
                                                "
                                            >
                                                Lembur:
                                                {{
                                                    item.data_aktifitas
                                                        .hasil_lembur
                                                }}
                                            </div>

                                            <div class="hasil-total">
                                                ACT: Rp
                                                {{
                                                    formatCurrency(
                                                        item.data_aktifitas
                                                            ?.total_act ?? 0,
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER -->
                    <div class="dt-footer" v-if="!loading">
                        <div class="dt-info">
                            Menampilkan
                            <strong v-if="totalItems">{{
                                startIndex + 1
                            }}</strong>
                            <strong v-else>0</strong>
                            &nbsp;–&nbsp;
                            <strong>{{ endIndex }}</strong>
                            dari <strong>{{ totalItems }}</strong> aktifitas
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
                                    currentPage === totalPages ||
                                    totalPages === 0
                                "
                                @click="goToPage(currentPage + 1)"
                            >
                                »
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <Modal v-if="showActivityModal" @click.self="closeActivityModal">
            <div class="activity-modal">
                <div class="activity-modal__header">
                    <div class="activity-modal__title">
                        <font-awesome-icon :icon="['fas', 'clipboard-list']" />
                        Log Aktivitas
                    </div>
                    <button
                        class="activity-modal__close"
                        type="button"
                        @click="closeActivityModal"
                    >
                        ×
                    </button>
                </div>

                <div class="activity-modal__body">
                    <form @submit.prevent="submitActivity">
                        <div class="form-group">
                            <label for="karyawan" class="form-label">
                                Karyawan
                                <span class="required">*</span>
                            </label>
                            <Select2
                                id="karyawan"
                                v-model="activityForm.employee_id"
                                :disabled="user.role_id == 2"
                                readonly
                                class="form-input"
                                required
                            >
                                <option value="">-- Pilih Karyawan --</option>
                                <option
                                    v-for="karyawan in data_karyawan"
                                    :key="karyawan.id"
                                    :value="karyawan.id"
                                >
                                    {{ karyawan.nama }}
                                </option>
                            </Select2>
                        </div>
                        <!-- Tanggal -->
                        <div class="form-group">
                            <label for="activity-date" class="form-label">
                                Tanggal
                                <span class="required">*</span>
                            </label>
                            <input
                                id="activity-date"
                                v-model="activityForm.tgl"
                                type="date"
                                class="form-input"
                                required
                            />
                        </div>

                        <!-- Shift -->
                        <div class="form-group">
                            <label for="activity-shift" class="form-label">
                                Shift
                                <span class="required">*</span>
                            </label>
                            <select
                                id="activity-shift"
                                v-model="activityForm.shift"
                                class="form-input"
                                style="pointer-events: none"
                                required
                            >
                                <option value="">-- Pilih Shift --</option>
                                <option
                                    v-for="shift in shiftOptions"
                                    :key="shift.id"
                                    :value="shift.id"
                                >
                                    {{ shift.nama_shift }} (
                                    {{ shift.jam_masuk }} -
                                    {{ shift.jam_pulang }} )
                                </option>
                            </select>
                        </div>

                        <!-- Aktivitas -->
                        <div class="form-group">
                            <label for="activity-type" class="form-label">
                                Jenis Aktivitas
                                <span class="required">*</span>
                            </label>
                            <Select2
                                id="activity-type"
                                v-model="activityForm.aktifitas_id"
                                class="form-input"
                                required
                                @change="onActivityChange"
                            >
                                <option value="">-- Pilih Aktivitas --</option>
                                <option
                                    v-for="activity in availableActivities"
                                    :key="activity.id"
                                    :value="activity.id"
                                >
                                    {{ activity.kode }} -
                                    {{ activity.nama_aktifitas }}
                                </option>
                            </Select2>
                        </div>

                        <!-- Jam Kerja -->
                        <div class="form-row">
                            <div class="form-group form-group-half">
                                <label for="jam-masuk" class="form-label">
                                    Waktu Mulai
                                </label>
                                <input
                                    id="jam-masuk"
                                    v-model="activityForm.jam_masuk"
                                    type="time"
                                    class="form-input"
                                />
                            </div>

                            <div class="form-group form-group-half">
                                <label for="jam-pulang" class="form-label">
                                    Waktu Selesai
                                </label>
                                <input
                                    id="jam-pulang"
                                    v-model="activityForm.jam_pulang"
                                    type="time"
                                    class="form-input"
                                />
                            </div>
                        </div>

                        <!-- Hasil Kerja -->
                        <div class="form-section">
                            <h4 class="form-section-title">Hasil Kerja</h4>

                            <div class="form-row">
                                <div class="form-group form-group-half">
                                    <label for="hasil-kerja" class="form-label">
                                        Hasil Kerja
                                    </label>
                                    <input
                                        id="hasil-kerja"
                                        v-model.number="
                                            activityForm.hasil_kerja
                                        "
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>

                                <div class="form-group form-group-half">
                                    <label
                                        for="hasil-lembur"
                                        class="form-label"
                                    >
                                        Hasil Lembur
                                    </label>
                                    <input
                                        id="hasil-lembur"
                                        v-model.number="
                                            activityForm.hasil_lembur
                                        "
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group form-group-half">
                                    <label for="return-qty" class="form-label">
                                        Return Qty
                                    </label>
                                    <input
                                        id="return-qty"
                                        v-model.number="activityForm.return_qty"
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>

                                <div class="form-group form-group-half">
                                    <label for="tolak-qc" class="form-label">
                                        Tolak QC
                                    </label>
                                    <input
                                        id="tolak-qc"
                                        v-model.number="activityForm.tolak_qc"
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- SCF -->
                        <div class="form-section">
                            <h4 class="form-section-title">
                                SCF (Standard Cost)
                            </h4>

                            <div class="form-row">
                                <div class="form-group form-group-half">
                                    <label for="upah-scf" class="form-label">
                                        Upah SCF (Rp)
                                    </label>
                                    <input
                                        id="upah-scf"
                                        v-model.number="activityForm.upah_scf"
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>

                                <div class="form-group form-group-half">
                                    <label for="bantu-scf" class="form-label">
                                        Bantu SCF (Rp)
                                    </label>
                                    <input
                                        id="bantu-scf"
                                        v-model.number="activityForm.bantu_scf"
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="denda-scf" class="form-label">
                                    Denda SCF (Rp)
                                </label>
                                <input
                                    id="denda-scf"
                                    v-model.number="activityForm.denda_scf"
                                    type="number"
                                    class="form-input"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>

                            <div class="form-group">
                                <label class="form-label">Total SCF</label>
                                <div class="form-input-display">
                                    Rp {{ formatCurrency(totalSCF) }}
                                </div>
                            </div>
                        </div>

                        <!-- ACT -->
                        <div class="form-section">
                            <h4 class="form-section-title">
                                ACT (Actual Cost)
                            </h4>

                            <div class="form-row">
                                <div class="form-group form-group-half">
                                    <label for="upah-act" class="form-label">
                                        Upah ACT (Rp)
                                    </label>
                                    <input
                                        id="upah-act"
                                        v-model.number="activityForm.upah_act"
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>

                                <div class="form-group form-group-half">
                                    <label
                                        for="upah-bantu-act"
                                        class="form-label"
                                    >
                                        Upah Bantu ACT (Rp)
                                    </label>
                                    <input
                                        id="upah-bantu-act"
                                        v-model.number="
                                            activityForm.upah_bantu_act
                                        "
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group form-group-half">
                                    <label for="return-act" class="form-label">
                                        Return ACT (Rp)
                                    </label>
                                    <input
                                        id="return-act"
                                        v-model.number="activityForm.return_act"
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>

                                <div class="form-group form-group-half">
                                    <label for="denda-act" class="form-label">
                                        Denda ACT (Rp)
                                    </label>
                                    <input
                                        id="denda-act"
                                        v-model.number="activityForm.denda_act"
                                        type="number"
                                        class="form-input"
                                        min="0"
                                        placeholder="0"
                                    />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Total ACT</label>
                                <div class="form-input-display">
                                    Rp {{ formatCurrency(totalACT) }}
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="form-group">
                            <label for="keterangan" class="form-label">
                                Keterangan
                            </label>
                            <textarea
                                id="keterangan"
                                v-model="activityForm.ket"
                                class="form-textarea"
                                rows="3"
                                placeholder="Keterangan tambahan..."
                                maxlength="500"
                            ></textarea>
                            <div class="char-counter">
                                {{ activityForm.ket?.length || 0 }}/500
                            </div>
                        </div>

                        <div class="form-actions">
                            <button
                                type="button"
                                @click="closeActivityModal"
                                class="btn-cancel"
                                :disabled="activitySubmitting"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="btn-submit"
                                :disabled="activitySubmitting"
                            >
                                <font-awesome-icon
                                    v-if="activitySubmitting"
                                    :icon="['fas', 'spinner']"
                                    class="fa-spin"
                                />
                                <span v-else>Simpan Aktivitas</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>

        <ImportAktifitas
            v-if="showImportAktifitasModal"
            @closeModal="closeModalImportAktifitas"
            @refreshData="fetchActivities"
        />
    </AppLayout>
</template>

<script>
import Button from '@/components/Button.vue';
import Modal from '@/components/Modal.vue';
import Select2 from '@/components/Select2.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import ImportAktifitas from '../import/ImportAktifitas.vue';

export default {
    components: { AppLayout, Button, Link, Select2, Modal, ImportAktifitas },

    data() {
        const page = usePage();
        return {
            user: page.props.auth.user,
            search: '',
            status: '',
            items: [],
            loading: false,
            isDownloading: false,

            currentPage: 1,
            perPage: 50,
            totalItems: 0,
            totalPages: 0,

            data_filtered_perusahaan: [],
            data_filtered_jabatan: [],

            filtered_jabatan: '',
            filtered_perusahaan: '',
            filtered_tanggal_dari: '',
            filtered_tanggal_sampai: '',
            showFilters: false,
            loadingStatus: {},

            showActivityModal: false,
            activitySubmitting: false,
            availableActivities: [], // List aktivitas dari tabel 'aktifitas'
            activityForm: {
                id: null,
                employee_id: null,
                tgl: '',
                shift: '',
                aktifitas_id: '',
                jam_masuk: '',
                jam_pulang: '',
                hasil_kerja: 0,
                hasil_lembur: 0,
                return_qty: 0,
                tolak_qc: 0,
                upah_scf: 0,
                bantu_scf: 0,
                denda_scf: 0,
                upah_act: 0,
                upah_bantu_act: 0,
                return_act: 0,
                denda_act: 0,
                ket: '',
            },
            data_karyawan: [],
            shiftOptions: [],

            showImportAktifitasModal: false,
        };
    },

    watch: {
        search() {
            this.fetchLogActivities();
        },
        status() {
            this.fetchLogActivities();
        },
        filtered_perusahaan(newVal, oldVal) {
            console.log('Perusahaan changed:', newVal);
            this.onPerusahaanChange();
            this.filteredData();
        },
        filtered_jabatan(newVal, oldVal) {
            console.log('Jabatan changed:', newVal);
            this.filteredData();
        },
        filtered_tanggal_dari() {
            this.filteredData();
        },
        filtered_tanggal_sampai() {
            this.filteredData();
        },
        'activityForm.employee_id'(newVal) {
            const employeeId = Number(this.activityForm.employee_id);

            const selectedKaryawan = this.data_karyawan.find(
                (a) => a.id === employeeId,
            );

            if (selectedKaryawan) {
                this.activityForm.shift = selectedKaryawan.shift_id;
            }
        },
    },

    computed: {
        startIndex() {
            return (this.currentPage - 1) * this.perPage;
        },
        endIndex() {
            return Math.min(
                this.startIndex + this.items.length,
                this.totalItems,
            );
        },
        pages() {
            if (this.totalPages <= 1) return [];
            const range = 2;
            const pages = [];
            const start = Math.max(1, this.currentPage - range);
            const end = Math.min(this.totalPages, this.currentPage + range);
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },

        totalSCF() {
            const upah = Number(this.activityForm.upah_scf) || 0;
            const bantu = Number(this.activityForm.bantu_scf) || 0;
            const denda = Number(this.activityForm.denda_scf) || 0;
            return upah + bantu - denda;
        },

        totalACT() {
            const upah = Number(this.activityForm.upah_act) || 0;
            const bantu = Number(this.activityForm.upah_bantu_act) || 0;
            const returnVal = Number(this.activityForm.return_act) || 0;
            const denda = Number(this.activityForm.denda_act) || 0;
            return upah + bantu - returnVal - denda;
        },
    },
    //created() {
    //     const now = new Date();
    //     const yyyy = now.getFullYear();
    //     const mm = String(now.getMonth() + 1).padStart(2, '0');
    //     const dd = String(now.getDate()).padStart(2, '0');

    //     this.filtered_tanggal_absen = `${yyyy}-${mm}-${dd}`;
    // },

    methods: {
        openImportKaryawanModal() {
            this.showImportAktifitasModal = true;
        },
        closeModalImportAktifitas() {
            this.showImportAktifitasModal = false;
            this.selectedFileKaryawan = null;
        },
        async fetchShiftOptions() {
            try {
                // Pastikan endpoint return data dari table `shift`
                // format ideal: [{id, nama_shift, keterangan}]
                const { data } = await axios.get(
                    '/referensi/get-shift-options',
                );

                const arr = Array.isArray(data) ? data : data.data || [];

                this.shiftOptions = Array.isArray(data?.data) ? data.data : [];
            } catch (e) {
                console.warn('Gagal fetch shift options:', e);
                this.shiftOptions = [];
            }
        },
        formatCurrency(value) {
            if (!value) return '0';
            return new Intl.NumberFormat('id-ID').format(value);
        },
        toHHMM(v) {
            if (!v) return null;
            const s = String(v).trim();

            // ISO: ambil setelah "T" -> "HH:MM"
            let m = s.match(/T(\d{2}:\d{2})/);
            if (m) return m[1];

            // TIME biasa: "HH:MM" atau "HH:MM:SS"
            m = s.match(/(\d{2}:\d{2})/);
            return m ? m[1] : null;
        },
        async fetchKaryawan() {
            try {
                const res = await axios.get('/referensi/karyawan');
                this.data_karyawan = res.data.data;
            } catch (err) {
                console.error(err);
                triggerAlert('error', 'Gagal memuat karyawan.');
            }
        },
        async getFilteredPerusahaanDanJabatan() {
            try {
                const res = await axios.get('/referensi/perusahaan-divisi');
                this.data_filtered_perusahaan = res.data.data || [];
                this.all_data = res.data.data;
            } catch (err) {
                console.error(err);
                triggerAlert(
                    'error',
                    'Gagal memuat filter perusahaan/jabatan.',
                );
            }
        },
        onPerusahaanChange() {
            // Reset divisi
            this.filtered_jabatan = '';
            this.data_filtered_jabatan = [];

            // Jika tidak ada perusahaan dipilih, stop
            if (!this.filtered_perusahaan) {
                return;
            }

            // Filter perusahaan yang dipilih dari data_perusahaan
            const perusahaanSelected = this.data_filtered_perusahaan.find(
                (p) => p.id == this.filtered_perusahaan, // Gunakan == untuk compare
            );

            // Ambil divisi dari perusahaan yang dipilih
            if (perusahaanSelected) {
                if (
                    perusahaanSelected.divisi &&
                    Array.isArray(perusahaanSelected.divisi) &&
                    perusahaanSelected.divisi.length > 0
                ) {
                    this.data_filtered_jabatan =
                        perusahaanSelected.divisi.filter(
                            (d) => d.status === 'aktif',
                        );
                } else {
                    console.log('Perusahaan tidak memiliki divisi');
                }
            } else {
                console.log('Perusahaan tidak ditemukan dalam data');
            }
        },
        async fetchLogActivities(page = 1) {
            this.loading = true;
            try {
                const res = await axios.get('/logs/aktifitas/all', {
                    params: {
                        search: this.search,
                        status: this.status,
                        page,
                        per_page: this.perPage,
                        filtered_jabatan: this.filtered_jabatan,
                        filtered_perusahaan: this.filtered_perusahaan,
                        filtered_tanggal_dari: this.filtered_tanggal_dari,
                        filtered_tanggal_sampai: this.filtered_tanggal_sampai,
                        employee_id:
                            this.user.role_id == 2 ? this.user.employee_id : '',
                    },
                });

                this.items = res.data.data;
                this.currentPage = res.data.meta.current_page;
                this.totalItems = res.data.meta.total;
                this.totalPages = res.data.meta.last_page;
            } catch (e) {
                console.log(e);
                triggerAlert('error', 'Gagal memuat data perusahaan');
            } finally {
                this.loading = false;
            }
        },

        goToPage(page) {
            this.fetchLogActivities(page);
        },

        tambahPerusahaan() {
            this.$inertia.visit('/master/client/create');
        },

        async hapus(id) {
            if (!confirm('Hapus perusahaan ini?')) return;

            try {
                await axios.delete(`/master/client/${id}`);
                triggerAlert('success', 'Perusahaan berhasil dihapus');
                this.fetchLogActivities(this.currentPage);
            } catch {
                triggerAlert('error', 'Gagal menghapus perusahaan');
            }
        },

        async downloadAktifitas() {
            try {
                this.isDownloading = true;
                const response = await axios.get('/export/aktifitas', {
                    responseType: 'blob',
                    params: {
                        search: this.search,
                        filtered_jabatan: this.filtered_jabatan,
                        filtered_perusahaan: this.filtered_perusahaan,
                        filtered_tanggal_dari: this.filtered_tanggal_dari,
                        filtered_tanggal_sampai: this.filtered_tanggal_sampai,
                        employee_id: this.user.employee_id,
                    },
                });

                const blob = new Blob([response.data], {
                    type: response.headers['content-type'],
                });

                const pad2 = (n) => String(n).padStart(2, '0');
                const d = new Date();
                const tgl = pad2(d.getDate());
                const bln = pad2(d.getMonth() + 1);
                const thn = d.getFullYear();
                const hari = pad2(d.getHours());
                const jam = pad2(d.getMinutes());
                const detik = pad2(d.getSeconds());

                const filename = `aktifitas_${tgl}${bln}${thn}${hari}${jam}${detik}.xlsx`;

                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', filename);
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Download gagal', error);
            } finally {
                this.isDownloading = false;
            }
        },

        filteredData() {
            this.fetchLogActivities();
        },

        async toggleStatus(item) {
            const newStatus =
                item.status_kehadiran === 'valid' ? 'Tidak Valid' : 'Valid';

            if (!confirm(`Ubah status kehadiran menjadi ${newStatus}?`)) {
                return;
            }

            // Set loading - Vue 3 way atau vanilla JS
            this.loadingStatus[item.id] = true;
            this.loadingStatus = { ...this.loadingStatus }; // Trigger reactivity

            try {
                const statusValue =
                    item.status_kehadiran === 'valid' ? 'tidak_valid' : 'valid';

                const response = await axios.post(
                    `/logs/aktifitas/${item.id}/update-status`,
                    {
                        status_kehadiran: statusValue,
                    },
                );

                // Update local data
                item.status_kehadiran = statusValue;

                triggerAlert('success', 'Status kehadiran berhasil diubah');
            } catch (error) {
                console.error('Error updating status:', error);
                triggerAlert(
                    'warning',
                    error.response?.data?.message ||
                        'Terjadi kesalahan saat mengubah status',
                );
            } finally {
                // Remove loading
                delete this.loadingStatus[item.id];
                this.loadingStatus = { ...this.loadingStatus }; // Trigger reactivity
            }
        },
        openActivityModal() {
            this.showActivityModal = true;
            // this.activityForm.shift = this.shiftInfo.id_shift;
            // this.activityForm.jam_masuk = this.shiftInfo.jam_masuk;
            // this.activityForm.jam_pulang = this.shiftInfo.jam_pulang;
            this.setDefaultDate();
        },
        setDefaultDate() {
            const today = new Date();
            this.activityForm.tgl = today.toISOString().split('T')[0];
        },
        closeActivityModal() {
            this.showActivityModal = false;
            this.resetActivityForm();
        },
        resetActivityForm() {
            this.activityForm = {
                id: null,
                employee_id: null,
                tgl: '',
                shift: '',
                aktifitas_id: '',
                jam_masuk: '',
                jam_pulang: '',
                hasil_kerja: 0,
                hasil_lembur: 0,
                return_qty: 0,
                tolak_qc: 0,
                upah_scf: 0,
                bantu_scf: 0,
                denda_scf: 0,
                upah_act: 0,
                upah_bantu_act: 0,
                return_act: 0,
                denda_act: 0,
                ket: '',
            };
        },
        editActivity(activity) {
            this.showActivityModal = true;
            const activity_detail = activity.rincian;
            // Simpan ID untuk mode edit
            this.activityForm.id = activity_detail.id;

            this.activityForm = {
                id: activity_detail.id,
                employee_id: activity.id_karyawan,
                tgl: activity.tanggal_aktifitas ?? activity_detail.tgl, // tergantung response
                shift: activity_detail.shift,
                aktifitas_id: activity_detail.aktifitas_id,
                jam_masuk: activity_detail.jam_masuk ?? '',
                jam_pulang: activity_detail.jam_pulang ?? '',
                hasil_kerja: activity_detail.hasil_kerja ?? 0,
                hasil_lembur: activity_detail.hasil_lembur ?? 0,
                return_qty: activity_detail.return_qty ?? 0,
                tolak_qc: activity_detail.tolak_qc ?? 0,
                upah_scf: activity_detail.upah_scf ?? 0,
                bantu_scf: activity_detail.bantu_scf ?? 0,
                denda_scf: activity_detail.denda_scf ?? 0,
                upah_act: activity_detail.upah_act ?? 0,
                upah_bantu_act: activity_detail.upah_bantu_act ?? 0,
                return_act: activity_detail.return_act ?? 0,
                denda_act: activity_detail.denda_act ?? 0,
                ket: activity_detail.ket ?? '',
            };
            console.log(this.activityForm);
        },
        onActivityChange() {
            // Auto-fill data berdasarkan aktivitas yang dipilih
            const selectedActivity = this.availableActivities.find(
                (a) => a.id === this.activityForm.aktifitas_id,
            );

            if (selectedActivity) {
                // Bisa auto-fill upah SCF dari master data
                if (selectedActivity.upah_standar) {
                    this.activityForm.upah_scf = selectedActivity.upah_standar;
                }
            }
        },

        async fetchActivities() {
            try {
                const response = await axios.get('/referensi/get-aktifitas');
                if (response.data.success) {
                    this.availableActivities = response.data.data;
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            }
        },

        async submitActivity() {
            if (!this.activityForm?.employee_id) {
                triggerAlert('error', 'Employee tidak ditemukan');
                return;
            }

            this.activitySubmitting = true;

            try {
                // =========================
                // Hitung jam kerja (menit)
                // =========================
                let jam_kerja_menit = null;
                const { jam_masuk, jam_pulang } = this.activityForm;

                if (jam_masuk && jam_pulang) {
                    const [jm, mm] = jam_masuk.split(':').map(Number);
                    const [jp, mp] = jam_pulang.split(':').map(Number);

                    if (
                        Number.isFinite(jm) &&
                        Number.isFinite(mm) &&
                        Number.isFinite(jp) &&
                        Number.isFinite(mp)
                    ) {
                        const masuk = jm * 60 + mm;
                        const pulang = jp * 60 + mp;

                        jam_kerja_menit = pulang - masuk;
                        if (jam_kerja_menit < 0) jam_kerja_menit += 1440;
                    }
                }

                // =========================
                // Payload
                // =========================
                const payload = {
                    ...this.activityForm,
                    jam_kerja_menit,
                    total_scf: this.totalSCF,
                    total_act: this.totalACT,
                    employee_id: this.activityForm?.employee_id,
                };

                // =========================
                // MODE: CREATE vs EDIT
                // =========================
                const isEdit = Boolean(this.activityForm.id);

                const endpoint = isEdit
                    ? `/logs/aktifitas/update/${this.activityForm.id}`
                    : '/logs/aktifitas/store';

                const method = isEdit ? 'put' : 'post';

                const { data } = await axios({
                    method,
                    url: endpoint,
                    data: payload,
                });

                if (data?.success) {
                    triggerAlert(
                        'success',
                        isEdit
                            ? 'Log aktivitas berhasil diperbarui'
                            : 'Log aktivitas berhasil disimpan',
                    );

                    this.fetchLogActivities();
                    this.closeActivityModal();
                } else {
                    triggerAlert(
                        'error',
                        data?.message || 'Gagal menyimpan log aktivitas',
                    );
                }
            } catch (error) {
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
                    const firstError = Object.values(errors)?.[0]?.[0];
                    if (firstError) triggerAlert('error', firstError);
                } else if (error.response?.data?.message) {
                    triggerAlert('error', error.response.data.message);
                } else {
                    triggerAlert('error', 'Gagal menyimpan log aktivitas');
                }
            } finally {
                this.activitySubmitting = false;
            }
        },
    },

    async mounted() {
        await this.fetchLogActivities();
        await this.fetchShiftOptions();
        await this.fetchKaryawan();
        await this.fetchActivities();
        await this.getFilteredPerusahaanDanJabatan();

        this.activityForm.employee_id = this.user.employee_id;
        const selectedKaryawan = this.data_karyawan.find(
            (a) => a.id === this.activityForm.employee_id,
        );

        if (selectedKaryawan) {
            this.activityForm.shift = selectedKaryawan.shift_id;
        }
    },
};
</script>
<style>
/* ===== AKTIVITAS CELL ===== */
.aktifitas-cell {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.aktifitas-main {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.aktifitas-kode {
    font-weight: 600;
    color: #111827;
}

.aktifitas-shift,
.aktifitas-jam,
.aktifitas-durasi {
    font-size: 13px;
    color: #374151;
}

/* ===== HASIL CELL ===== */
.hasil-cell {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 13px;
}

.hasil-cell div {
    color: #374151;
}

/* ACT highlight */
.hasil-total {
    margin-top: 4px;
    font-weight: 600;
    color: #065f46;
    background: #ecfdf5;
    padding: 4px 8px;
    border-radius: 6px;
    width: fit-content;
}

/* ===== ROW SPACING ===== */
tbody tr td {
    vertical-align: top;
    padding: 12px 10px;
}

/* ===== RESPONSIVE TOUCH ===== */
@media (max-width: 768px) {
    .aktifitas-shift,
    .aktifitas-jam,
    .aktifitas-durasi,
    .hasil-cell {
        font-size: 12px;
    }

    .hasil-total {
        font-size: 12px;
    }
}

/* ==================== ACTIVITY MODAL ==================== */
.activity-modal {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.activity-modal__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.activity-modal__title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.activity-modal__close {
    background: none;
    border: none;
    font-size: 2rem;
    color: #9ca3af;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
}

.activity-modal__close:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.activity-modal__body {
    padding: 1.5rem;
    overflow-y: auto;
}

/* ==================== FORM STYLES ==================== */
.form-group {
    margin-bottom: 0.5rem !important;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.required {
    color: #ef4444;
}

.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    font-family: inherit;
    resize: vertical;
    transition: border-color 0.2s;
}

.form-textarea:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.char-counter {
    text-align: right;
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 0.25rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-hint {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.form-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn-cancel {
    padding: 0.75rem 1.5rem;
    background: white;
    color: #6b7280;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover:not(:disabled) {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-submit {
    padding: 0.75rem 1.5rem;
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 140px;
}

.btn-submit:hover:not(:disabled) {
    background: #4338ca;
    transform: translateY(-1px);
}

.btn-submit:disabled,
.btn-cancel:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}
</style>
