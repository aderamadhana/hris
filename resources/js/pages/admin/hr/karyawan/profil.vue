<template>
    <AppLayout>
        <section class="page detail-page">
            <!-- HEADER -->
            <div class="page-header">
                <div>
                    <h2 class="page-title">Detail Karyawan</h2>
                    <p class="page-subtitle">
                        Ringkasan profil, riwayat pendidikan, pekerjaan,
                        keluarga, dan kesehatan karyawan.
                    </p>
                </div>
                <div class="page-actions">
                    <Button
                        variant="primary"
                        size="md"
                        :loading="isDownloading"
                        @click="downloadProfil(employee.nama)"
                        class="download-btn"
                    >
                        <font-awesome-icon icon="download" class="icon" />
                        Download PDF
                    </Button>
                    <Button
                        variant="warning"
                        title="Edit Karyawan"
                        @click="openEdit()"
                    >
                        <font-awesome-icon icon="pen-to-square" />
                        Edit Karyawan
                    </Button>
                    <Button
                        variant="success"
                        title="Slip Gaji Karyawan"
                        @click="openPayslip()"
                    >
                        <font-awesome-icon icon="file-lines" />
                        Slip Gaji Karyawan
                    </Button>
                </div>
            </div>

            <!-- CONTENT -->
            <div v-if="!loading" class="card detail-card">
                <Tabs :tabs="tabs">
                    <!-- ================= DATA KARYAWAN ================= -->
                    <template #karyawan>
                        <div class="tab-section">
                            <h3 class="tab-title">Profil Karyawan</h3>

                            <div class="tag-row">
                                <span
                                    class="tag tag-status"
                                    :class="
                                        employee.status_active == 1
                                            ? 'is-active'
                                            : 'is-inactive'
                                    "
                                >
                                    {{
                                        employee.status_active == 1
                                            ? 'Aktif'
                                            : 'Tidak Aktif'
                                    }}
                                </span>
                                <span class="tag tag-id">
                                    NRP: {{ employee.nrp }}
                                </span>
                            </div>

                            <!-- DATA UTAMA -->
                            <h4 class="section-subtitle">Data Utama</h4>
                            <div class="detail-grid two-col">
                                <div class="field highlight">
                                    <div class="field-label">Nama Lengkap</div>
                                    <div class="field-value">
                                        {{ employee.nama || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">NRP</div>
                                    <div class="field-value">
                                        {{ employee.nrp || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Jenis Kelamin</div>
                                    <div class="field-value">
                                        {{ employee.jenis_kelamin || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Tempat Lahir</div>
                                    <div class="field-value">
                                        {{ employee.tempat_lahir || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Tanggal Lahir</div>
                                    <div class="field-value">
                                        {{ employee.tanggal_lahir || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Status Perkawinan
                                    </div>
                                    <div class="field-value">
                                        {{ employee.status_perkawinan || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Agama</div>
                                    <div class="field-value">
                                        {{ employee.agama || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Kewarganegaraan
                                    </div>
                                    <div class="field-value">
                                        {{ employee.kewarganegaraan || '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- KONTAK & IDENTITAS -->
                            <h4 class="section-subtitle">Kontak & Identitas</h4>
                            <div class="detail-grid two-col secondary">
                                <div class="field">
                                    <div class="field-label">No. KTP</div>
                                    <div class="field-value">
                                        {{ employee.no_ktp || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        No. Kartu Keluarga
                                    </div>
                                    <div class="field-value">
                                        {{ employee.no_kk || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">No. WhatsApp</div>
                                    <div class="field-value">
                                        {{ employee.no_wa || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Email</div>
                                    <div class="field-value">
                                        {{ employee.email || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Alamat Domisili
                                    </div>
                                    <div class="field-value">
                                        {{
                                            employee.alamat_lengkap_domisili ||
                                            '-'
                                        }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Kota Domisili</div>
                                    <div class="field-value">
                                        {{ employee.kota_domisili || '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- BPJS & LISENSI -->
                            <h4 class="section-subtitle">BPJS & Lisensi</h4>
                            <div class="detail-grid two-col secondary">
                                <div class="field">
                                    <div class="field-label">
                                        BPJS Ketenagakerjaan
                                    </div>
                                    <div class="field-value">
                                        {{ employee.bpjs_tk || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        BPJS Kesehatan
                                    </div>
                                    <div class="field-value">
                                        {{ employee.bpjs_kes || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Jenis Kepesertaan BPJS TK
                                    </div>
                                    <div class="field-value">
                                        {{ employee.jenis_bpjs_tk || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Lokasi Kepesertaan BPJS KS
                                    </div>
                                    <div class="field-value">
                                        {{ employee.nama_faskes || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Status Kepesertaan BPJS KS
                                    </div>
                                    <div class="field-value">
                                        {{ employee.status_bpjs_ks || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">No. SKCK</div>
                                    <div class="field-value">
                                        {{ employee.no_skck || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Masa Berlaku SKCK
                                    </div>
                                    <div class="field-value">
                                        {{ employee.masa_berlaku_skck || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Jenis Lisensi</div>
                                    <div class="field-value">
                                        {{ employee.jenis_lisensi || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">No. Lisensi</div>
                                    <div class="field-value">
                                        {{ employee.no_lisensi || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Masa Berlaku Lisensi
                                    </div>
                                    <div class="field-value">
                                        {{
                                            employee.masa_berlaku_lisensi || '-'
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- ================= PENDIDIKAN ================= -->
                    <template #pendidikan>
                        <div class="tab-section">
                            <h3 class="tab-title">Riwayat Pendidikan</h3>

                            <div
                                v-if="pendidikan.length"
                                class="education-list"
                            >
                                <div
                                    v-for="(edu, i) in pendidikan"
                                    :key="i"
                                    class="edu-item"
                                >
                                    <div class="edu-header">
                                        <div>
                                            <div class="edu-degree">
                                                {{ edu.jenjang }}
                                                {{ edu.jurusan }}
                                            </div>
                                            <div class="edu-school">
                                                {{ edu.institusi || '-' }}
                                            </div>
                                        </div>
                                        <div class="edu-year">
                                            Lulus {{ edu.tahun_lulus }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="empty">
                                Data pendidikan belum tersedia
                            </div>
                        </div>
                    </template>

                    <!-- ================= PEKERJAAN ================= -->
                    <template #pekerjaan>
                        <div class="tab-section">
                            <h3 class="tab-title">Riwayat Pekerjaan</h3>

                            <div v-if="pekerjaan.length" class="timeline">
                                <div
                                    v-for="(job, i) in pekerjaan"
                                    :key="i"
                                    class="timeline-item"
                                >
                                    <div class="timeline-dot"></div>

                                    <div class="timeline-body">
                                        <div class="timeline-header">
                                            <div>
                                                <div class="job-title">
                                                    {{ job.jabatan || '-' }}
                                                </div>
                                                <div class="job-company">
                                                    {{ job.perusahaan || '-' }}
                                                    – {{ job.bagian || '-' }}
                                                </div>
                                                <div class="job-meta">
                                                    No. Kontrak:
                                                    {{ job.no_kontrak || '-' }}
                                                </div>
                                            </div>
                                            <div class="job-period">
                                                {{ job.mulai }} –
                                                {{ job.selesai || 'Sekarang' }}
                                            </div>
                                        </div>

                                        <div class="job-type">
                                            {{ job.jenis_kontrak || '-' }}
                                        </div>

                                        <div class="job-extra">
                                            <div>
                                                Jenis Kerja:
                                                {{ job.jenis_kerja || '-' }}
                                            </div>
                                            <div>
                                                Pola Kerja:
                                                {{ job.pola_kerja || '-' }}
                                            </div>
                                            <div>
                                                Hari Kerja:
                                                {{ job.hari_kerja || '-' }}
                                            </div>
                                            <div>
                                                Status:
                                                {{ job.status_kontrak || '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="empty">
                                Riwayat pekerjaan belum tersedia
                            </div>
                        </div>
                    </template>

                    <!-- ================= KELUARGA ================= -->
                    <template #keluarga>
                        <div class="tab-section">
                            <h3 class="tab-title">Data Keluarga</h3>

                            <div v-if="keluarga.length" class="family-list">
                                <div
                                    v-for="(f, i) in keluarga"
                                    :key="i"
                                    class="family-item"
                                >
                                    <div class="family-main">
                                        <div>
                                            <div class="family-name">
                                                {{ f.nama }}
                                            </div>
                                            <div class="family-relation">
                                                {{ f.hubungan }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="family-detail">
                                        <span>
                                            TTL:
                                            {{
                                                f.ttl
                                                    ?.replace(',', '')
                                                    .trim() || '-'
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="empty">
                                Data keluarga belum tersedia
                            </div>
                        </div>
                    </template>

                    <!-- ================= KESEHATAN ================= -->
                    <template #kesehatan>
                        <div class="tab-section">
                            <h3 class="tab-title">Informasi Kesehatan</h3>

                            <!-- DATA FISIK -->
                            <h4 class="section-subtitle">Data Fisik</h4>
                            <div class="detail-grid two-col">
                                <div class="field">
                                    <div class="field-label">Tinggi Badan</div>
                                    <div class="field-value">
                                        {{ kesehatan.tinggi_badan || '-' }} cm
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Berat Badan</div>
                                    <div class="field-value">
                                        {{ kesehatan.berat_badan || '-' }} kg
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Golongan Darah
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.gol_darah || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Buta Warna</div>
                                    <div class="field-value">
                                        {{
                                            kesehatan.buta_warna
                                                ? 'Ya'
                                                : 'Tidak'
                                        }}
                                    </div>
                                </div>
                            </div>

                            <!-- RIWAYAT & SCREENING -->
                            <h4 class="section-subtitle">
                                Riwayat & Screening
                            </h4>
                            <div class="detail-grid two-col secondary">
                                <div class="field">
                                    <div class="field-label">
                                        Tanggal Medical Check Up
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.tanggal_mcu || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Kesimpulan Hasil MCU
                                    </div>
                                    <div class="field-value">
                                        {{
                                            kesehatan.kesimpulan_hasil_mcu ||
                                            '-'
                                        }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Tanggal Drug Test
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.tanggal_drug_test || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Hasil Drug Test
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.hasil_drug_test || '-' }}
                                    </div>
                                </div>

                                <div class="field" style="grid-column: 1 / -1">
                                    <div class="field-label">
                                        Riwayat Penyakit
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.riwayat_penyakit || '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- HASIL LABORATORIUM & MCU -->
                            <h4 class="section-subtitle">
                                Hasil Laboratorium & MCU
                            </h4>
                            <div class="detail-grid two-col secondary">
                                <div class="field">
                                    <div class="field-label">
                                        Pemeriksaan Darah
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.darah || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Pemeriksaan Urine
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.urine || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Fungsi Hati</div>
                                    <div class="field-value">
                                        {{ kesehatan.f_hati || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Gula Darah</div>
                                    <div class="field-value">
                                        {{ kesehatan.gula_darah || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Fungsi Ginjal</div>
                                    <div class="field-value">
                                        {{ kesehatan.ginjal || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Thorax</div>
                                    <div class="field-value">
                                        {{ kesehatan.thorax || '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- TANDA VITAL & PEMERIKSAAN MATA -->
                            <h4 class="section-subtitle">
                                Tanda Vital & Pemeriksaan Mata
                            </h4>
                            <div class="detail-grid two-col secondary">
                                <div class="field">
                                    <div class="field-label">
                                        Tekanan Darah (Tensi)
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.tensi || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Nadi</div>
                                    <div class="field-value">
                                        {{ kesehatan.nadi || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Mata Kanan (OD)
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.od || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Mata Kiri (OS)
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.os || '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- dokumen -->
                    <template #dokumen>
                        <div class="tab-section">
                            <h3 class="tab-title">Dokumen</h3>

                            <div class="detail-grid two-col secondary">
                                <div
                                    class="field"
                                    style="
                                        grid-column: 1 / -1;
                                        font-weight: 600;
                                        border-bottom: 1px solid #e5e7eb;
                                        padding-bottom: 6px;
                                    "
                                >
                                    Kelengkapan Dokumen
                                </div>

                                <div
                                    v-for="doc in dokumenList"
                                    :key="doc.key"
                                    class="field"
                                >
                                    <div class="field-label">
                                        {{ doc.label }}
                                    </div>
                                    <div class="field-value">
                                        <Button
                                            v-if="doc.value"
                                            variant="primary"
                                            @click="lihatDokumen(doc.value)"
                                        >
                                            Ada
                                        </Button>

                                        <span v-else class="status-empty">
                                            Tidak Ada
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </Tabs>
            </div>

            <div v-else class="loading-item">
                <div class="loading-item-card">
                    <div class="spinner-item"></div>
                    <div class="loading-item-text">Memuat data karyawan…</div>
                    <div class="loading-item-subtext">
                        Mohon tunggu sebentar
                    </div>
                </div>
            </div>
        </section>
        <Modal v-if="showModalDocument" size="lg">
            <div class="modal-header">
                <h3>Dokumen</h3>
                <button class="modal-close" @click="closeLihatDocumen">
                    ✕
                </button>
            </div>
            <div class="modal-body">
                <iframe
                    v-if="url"
                    :src="url"
                    class="doc-iframe"
                    frameborder="0"
                ></iframe>

                <div v-else class="empty-doc">Dokumen tidak tersedia</div>
            </div>

            <div class="modal-footer">
                <Button variant="secondary" @click="closeLihatDocumen">
                    Tutup
                </Button>
            </div>
        </Modal>
    </AppLayout>
</template>

<script>
import Button from '@/components/Button.vue';
import Modal from '@/components/Modal.vue';
import Tabs from '@/components/Tabs.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    components: { AppLayout, Tabs, Link, Modal, Button },

    data() {
        const page = usePage();
        return {
            user: page.props.auth.user,
            loading: true,
            isDownloading: false,

            employee: {},
            alamat: {},
            pendidikan: [],
            pekerjaan: [],
            keluarga: [],
            kesehatan: {},
            document: {},

            tabs: [
                { key: 'karyawan', label: 'Data Karyawan' },
                { key: 'pendidikan', label: 'Pendidikan' },
                { key: 'pekerjaan', label: 'Pekerjaan' },
                { key: 'keluarga', label: 'Keluarga' },
                { key: 'kesehatan', label: 'Kesehatan' },
                { key: 'dokumen', label: 'Kelengkapan Dokumen' },
            ],

            url: null,
            showModalDocument: false,
        };
    },

    computed: {
        dokumenList() {
            return [
                {
                    key: 'pas_foto',
                    label: 'Pas Foto',
                    value: this.document?.pas_foto,
                },
                { key: 'ktp', label: 'KTP', value: this.document?.ktp },
                {
                    key: 'kk',
                    label: 'Kartu Keluarga',
                    value: this.document?.kk,
                },
                {
                    key: 'ijazah_terakhir',
                    label: 'Ijazah Terakhir',
                    value: this.document?.ijazah_terakhir,
                },
                {
                    key: 'skck',
                    label: 'SKCK',
                    value: this.document?.skck,
                },
                {
                    key: 'lisensi',
                    label: 'Lisensi',
                    value: this.document?.lisensi,
                },
                {
                    key: 'form_bpjs_tk',
                    label: 'Formulir BPJS TK',
                    value: this.document?.form_bpjs_tk,
                },
                {
                    key: 'form_bpjs_kes',
                    label: 'Formulir BPJS Kesehatan',
                    value: this.document?.form_bpjs_kes,
                },
                {
                    key: 'paklaring',
                    label: 'Surat Pengalaman Kerja / Paklaring',
                    value: this.document?.paklaring,
                },
            ];
        },
    },

    mounted() {
        this.fetchEmployee();
        console.log(this.user);
    },

    methods: {
        lihatDokumen(url) {
            this.url = url;
            this.showModalDocument = true;
        },
        closeLihatDocumen() {
            this.url = null;
            this.showModalDocument = false;
        },
        fetchEmployee() {
            var id = null;
            if (this.user.role_id == 2) {
                id = this.user.employee.id;
            } else {
                id = window.location.pathname.split('/').pop();
            }
            axios
                .get(`/employee/get-data/${id}`)
                .then((res) => {
                    this.employee = res.data.employee || {};
                    this.document = res.data.document || {};
                    this.pendidikan = res.data.pendidikan || [];
                    this.pekerjaan = res.data.pekerjaan || [];
                    this.keluarga = res.data.keluarga || [];
                    this.kesehatan = res.data.kesehatan || {};
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        async downloadProfil(nama) {
            this.isDownloading = true;

            let id = null;
            if (this.user.role_id == 2) {
                id = this.user.employee.id;
            } else {
                id = window.location.pathname.split('/').pop();
            }

            const pad2 = (n) => String(n).padStart(2, '0');

            try {
                const response = await axios({
                    url: `/export/profil/${id}`,
                    method: 'GET',
                    responseType: 'blob',
                });

                const d = new Date();
                const tgl = pad2(d.getDate());
                const bln = pad2(d.getMonth() + 1);
                const thn = d.getFullYear();
                const jam = pad2(d.getHours());
                const menit = pad2(d.getMinutes());
                const detik = pad2(d.getSeconds());

                const safeNama = (nama || 'karyawan')
                    .toString()
                    .trim()
                    .replace(/\s+/g, '_')
                    .replace(/[^a-zA-Z0-9_-]/g, '');

                const filename = `Profil_${safeNama}_${tgl}${bln}${thn}${jam}${menit}${detik}.pdf`;

                const url = window.URL.createObjectURL(
                    new Blob([response.data]),
                );
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', filename);
                document.body.appendChild(link);

                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
            } finally {
                this.isDownloading = false;
            }
        },

        openEdit() {
            var id = null;
            if (this.user.role_id == 2) {
                id = this.user.employee.id;
            } else {
                id = window.location.pathname.split('/').pop();
            }
            router.visit(`/hr/karyawan/edit-karyawan/${id}`);
        },
        openPayslip() {
            var id = null;
            if (this.user.role_id == 2) {
                id = this.user.employee.id;
            } else {
                id = window.location.pathname.split('/').pop();
            }
            router.visit(`/hr/karyawan/daftar-gaji/${id}`);
        },
    },
};
</script>
<style scoped>
.tag-status.is-active {
    background-color: #e6f7ec;
    color: #1e7e34;
}

.tag-status.is-inactive {
    background-color: #fdecea;
    color: #c0392b;
}

.iframe-wrapper {
    width: 100%;
    height: 70vh; /* kontrol tinggi modal */
    overflow: hidden;
}

.doc-iframe {
    width: 100%;
    height: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: #fff;
}

.empty-doc {
    text-align: center;
    color: #9ca3af;
    padding: 2rem;
}
</style>
