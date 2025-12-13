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
                                        employee.status == 1
                                            ? 'is-active'
                                            : 'is-inactive'
                                    "
                                >
                                    {{
                                        employee.status == 1
                                            ? 'Aktif'
                                            : 'Tidak Aktif'
                                    }}
                                </span>
                                <span class="tag tag-id">
                                    NRP: {{ employee.nrp }}
                                </span>
                            </div>

                            <!-- DATA UTAMA -->
                            <div class="detail-grid two-col">
                                <div class="field highlight">
                                    <div class="field-label">Nama Lengkap</div>
                                    <div class="field-value">
                                        {{ employee.nama }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Jenis Kelamin</div>
                                    <div class="field-value">
                                        {{ employee.jk }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Tempat, Tanggal Lahir
                                    </div>
                                    <div class="field-value">
                                        {{ employee.tempat_lahir }},
                                        {{ employee.tanggal_lahir || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Status Perkawinan
                                    </div>
                                    <div class="field-value">
                                        {{ employee.perkawinan }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Agama</div>
                                    <div class="field-value">
                                        {{ employee.agama }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Kewarganegaraan
                                    </div>
                                    <div class="field-value">
                                        {{ employee.kewarganegaraan }}
                                    </div>
                                </div>
                            </div>

                            <!-- KONTAK -->
                            <div class="detail-grid two-col secondary">
                                <div class="field">
                                    <div class="field-label">No. KTP</div>
                                    <div class="field-value">
                                        {{ alamat.ktp || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">No. HP</div>
                                    <div class="field-value">
                                        {{ alamat.phone || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Email</div>
                                    <div class="field-value">
                                        {{ alamat.email || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Domisili</div>
                                    <div class="field-value">
                                        {{ alamat.domisili }},
                                        {{ alamat.kota }}
                                    </div>
                                </div>
                            </div>
                            <div class="detail-grid two-col secondary">
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
                                    <div class="field-label">Faskes</div>
                                    <div class="field-value">
                                        {{ employee.nama_faskes || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">No SKCK</div>
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
                                    <div class="field-label">No Lisensi</div>
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
                                                {{ edu.sekolah || '-' }}
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
                                                    | Cost Center:
                                                    {{ job.cost_center || '-' }}
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
                                        <span>
                                            No. HP:
                                            {{ f.no_hp || '-' }}
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

                            <div class="detail-grid two-col secondary">
                                <!-- ANTR0P0METRI -->
                                <div
                                    class="field"
                                    style="
                                        grid-column: 1 / -1;
                                        font-weight: 600;
                                        border-bottom: 1px solid #e5e7eb;
                                        padding-bottom: 6px;
                                    "
                                >
                                    Data Fisik
                                </div>

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

                                <!-- RIWAYAT & DRUG TEST -->
                                <div
                                    class="field"
                                    style="
                                        grid-column: 1 / -1;
                                        margin-top: 8px;
                                        font-weight: 600;
                                        border-bottom: 1px solid #e5e7eb;
                                        padding-bottom: 6px;
                                    "
                                >
                                    Riwayat & Screening
                                </div>

                                <div class="field">
                                    <div class="field-label">
                                        Riwayat Penyakit
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.riwayat_penyakit || '-' }}
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

                                <div class="field">
                                    <div class="field-label">
                                        Tanggal Drug Test
                                    </div>
                                    <div class="field-value">
                                        {{ kesehatan.tanggal_drug_test || '-' }}
                                    </div>
                                </div>

                                <!-- LAB & MCU -->
                                <div
                                    class="field"
                                    style="
                                        grid-column: 1 / -1;
                                        margin-top: 8px;
                                        font-weight: 600;
                                        border-bottom: 1px solid #e5e7eb;
                                        padding-bottom: 6px;
                                    "
                                >
                                    Hasil Laboratorium & MCU
                                </div>

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
                                    <div class="field-label">Ginjal</div>
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

                                <!-- VITAL & MATA -->
                                <div
                                    class="field"
                                    style="
                                        grid-column: 1 / -1;
                                        margin-top: 8px;
                                        font-weight: 600;
                                        border-bottom: 1px solid #e5e7eb;
                                        padding-bottom: 6px;
                                    "
                                >
                                    Tanda Vital & Pemeriksaan Mata
                                </div>

                                <div class="field">
                                    <div class="field-label">Tensi</div>
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
                                    <div class="field-label">Mata OD</div>
                                    <div class="field-value">
                                        {{ kesehatan.od || '-' }}
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="field-label">Mata OS</div>
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
                                        <span
                                            :class="
                                                doc.value
                                                    ? 'status-ok'
                                                    : 'status-empty'
                                            "
                                        >
                                            {{
                                                doc.value ? 'Ada' : 'Tidak Ada'
                                            }}
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
    </AppLayout>
</template>

<script>
import Tabs from '@/components/Tabs.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    components: { AppLayout, Tabs },

    data() {
        const page = usePage();
        return {
            user: page.props.auth.user,
            loading: true,

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
        };
    },

    computed: {
        dokumenList() {
            return [
                {
                    key: 'pas_foto',
                    label: 'Pas Foto',
                    value: this.dokumen?.pas_foto,
                },
                { key: 'ktp', label: 'KTP', value: this.dokumen?.ktp },
                { key: 'kk', label: 'Kartu Keluarga', value: this.dokumen?.kk },
                {
                    key: 'bpjs_tk',
                    label: 'BPJS Ketenagakerjaan',
                    value: this.dokumen?.bpjs_tk,
                },
                {
                    key: 'vaksin',
                    label: 'Sertifikat Vaksin',
                    value: this.dokumen?.vaksin,
                },
                {
                    key: 'sio_forklift',
                    label: 'SIO Forklift',
                    value: this.dokumen?.sio_forklift,
                },
                {
                    key: 'form_bpjs_tk',
                    label: 'Formulir BPJS TK',
                    value: this.dokumen?.form_bpjs_tk,
                },
                {
                    key: 'form_bpjs_kes',
                    label: 'Formulir BPJS Kesehatan',
                    value: this.dokumen?.form_bpjs_kes,
                },
                {
                    key: 'paklaring',
                    label: 'Surat Pengalaman Kerja / Paklaring',
                    value: this.dokumen?.paklaring,
                },
                { key: 'sim_b1', label: 'SIM B1', value: this.dokumen?.sim_b1 },
                {
                    key: 'kartu_garda',
                    label: 'Kartu Garda Pratama',
                    value: this.dokumen?.kartu_garda,
                },
            ];
        },
    },

    mounted() {
        this.fetchEmployee();
    },

    methods: {
        fetchEmployee() {
            const id = window.location.pathname.split('/').pop();
            axios
                .get(`/employee/get-data/${id}`)
                .then((res) => {
                    this.employee = res.data.employee || {};
                    this.alamat = res.data.alamat || {};
                    this.pendidikan = res.data.pendidikan || [];
                    this.pekerjaan = res.data.pekerjaan || [];
                    this.keluarga = res.data.keluarga || [];
                    this.kesehatan = res.data.kesehatan || {};
                })
                .finally(() => {
                    this.loading = false;
                });
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
</style>
