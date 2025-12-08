<template>
    <AppLayout>
        <section class="page detail-page">
            <!-- HEADER -->
            <div class="page-header">
                <div>
                    <h2 class="page-title">Detail Karyawan</h2>
                    <p class="page-subtitle">
                        Ringkasan profil karyawan, riwayat pendidikan,
                        pengalaman kerja, dan informasi keluarga dalam satu
                        halaman.
                    </p>
                </div>
            </div>

            <!-- CARD TAB DETAIL -->
            <div class="card detail-card">
                <Tabs :tabs="tabs">
                    <!-- TAB: DATA KARYAWAN -->
                    <template #karyawan>
                        <div class="tab-section">
                            <h3 class="tab-title">Data Karyawan</h3>

                            <div class="tag-row">
                                <span class="tag tag-status">Aktif</span>
                                <span class="tag tag-tenure"
                                    >Masa kerja: 3 tahun 4 bulan</span
                                >
                            </div>

                            <div class="detail-grid two-col">
                                <div class="field">
                                    <div class="field-label">Nama Lengkap</div>
                                    <div class="field-value">Budi Santoso</div>
                                </div>
                                <div class="field">
                                    <div class="field-label">NIK Karyawan</div>
                                    <div class="field-value">HR-2021-0012</div>
                                </div>
                                <div class="field">
                                    <div class="field-label">Jabatan</div>
                                    <div class="field-value">Staff Finance</div>
                                </div>
                                <div class="field">
                                    <div class="field-label">Divisi</div>
                                    <div class="field-value">
                                        Keuangan &amp; Akuntansi
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="field-label">Tipe Karyawan</div>
                                    <div class="field-value">
                                        Karyawan Tetap
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="field-label">Tanggal Masuk</div>
                                    <div class="field-value">
                                        12 Januari 2021
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="field-label">Lokasi Kerja</div>
                                    <div class="field-value">
                                        Head Office - Jakarta
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="field-label">
                                        Atasan Langsung
                                    </div>
                                    <div class="field-value">
                                        Siti Rahmawati (Finance Manager)
                                    </div>
                                </div>
                            </div>

                            <div class="detail-grid two-col secondary">
                                <div class="field">
                                    <div class="field-label">No. KTP</div>
                                    <div class="field-value">
                                        3174xxxxxxxx0001
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="field-label">No. HP</div>
                                    <div class="field-value">
                                        0812-3456-7890
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="field-label">Email Kantor</div>
                                    <div class="field-value">
                                        budi.santoso@contoh.co.id
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="field-label">
                                        Alamat Domisili
                                    </div>
                                    <div class="field-value">
                                        Jl. Melati No. 15, Rawamangun, Jakarta
                                        Timur
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- TAB: PENDIDIKAN -->
                    <template #pendidikan>
                        <div class="tab-section">
                            <h3 class="tab-title">Riwayat Pendidikan</h3>

                            <div class="education-list">
                                <div class="edu-item">
                                    <div class="edu-header">
                                        <div>
                                            <div class="edu-degree">
                                                S1 Akuntansi
                                            </div>
                                            <div class="edu-school">
                                                Universitas Negeri Jakarta
                                            </div>
                                        </div>
                                        <div class="edu-year">2013 – 2017</div>
                                    </div>
                                    <div class="edu-extra">
                                        IPK 3.45 · Konsentrasi: Akuntansi
                                        Keuangan
                                    </div>
                                </div>

                                <div class="edu-item">
                                    <div class="edu-header">
                                        <div>
                                            <div class="edu-degree">
                                                SMA IPA
                                            </div>
                                            <div class="edu-school">
                                                SMA Negeri 5 Jakarta
                                            </div>
                                        </div>
                                        <div class="edu-year">2010 – 2013</div>
                                    </div>
                                    <div class="edu-extra">
                                        Aktif di organisasi OSIS dan Kegiatan
                                        Rohani
                                    </div>
                                </div>
                            </div>

                            <div class="education-note">
                                Data di atas hanya contoh dummy untuk tampilan
                                antarmuka. Integrasi ke data sebenarnya dapat
                                diambil dari master pendidikan karyawan.
                            </div>
                        </div>
                    </template>

                    <!-- TAB: RIWAYAT KERJA -->
                    <template #pekerjaan>
                        <div class="tab-section">
                            <h3 class="tab-title">Riwayat Kerja</h3>

                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-body">
                                        <div class="timeline-header">
                                            <div>
                                                <div class="job-title">
                                                    Staff Finance
                                                </div>
                                                <div class="job-company">
                                                    PT Contoh Sejahtera Abadi
                                                </div>
                                            </div>
                                            <div class="job-period">
                                                Jan 2021 – Sekarang
                                            </div>
                                        </div>
                                        <ul class="job-desc">
                                            <li>
                                                Mengelola voucher pembayaran dan
                                                pencatatan kas kecil.
                                            </li>
                                            <li>
                                                Mendukung proses closing bulanan
                                                dan rekonsiliasi bank.
                                            </li>
                                            <li>
                                                Berkoordinasi dengan tim HR
                                                untuk input komponen gaji.
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-body">
                                        <div class="timeline-header">
                                            <div>
                                                <div class="job-title">
                                                    Finance Admin
                                                </div>
                                                <div class="job-company">
                                                    PT Nusantara Retailindo
                                                </div>
                                            </div>
                                            <div class="job-period">
                                                Jul 2018 – Des 2020
                                            </div>
                                        </div>
                                        <ul class="job-desc">
                                            <li>
                                                Input transaksi penjualan harian
                                                ke sistem keuangan.
                                            </li>
                                            <li>
                                                Membantu penyusunan laporan arus
                                                kas cabang.
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-body">
                                        <div class="timeline-header">
                                            <div>
                                                <div class="job-title">
                                                    Internship Finance
                                                </div>
                                                <div class="job-company">
                                                    KAP &amp; Partner
                                                </div>
                                            </div>
                                            <div class="job-period">
                                                Feb 2017 – Jun 2017
                                            </div>
                                        </div>
                                        <ul class="job-desc">
                                            <li>
                                                Membantu tim audit dalam
                                                pengumpulan dokumen pendukung.
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- TAB: DATA KELUARGA -->
                    <template #keluarga>
                        <div class="tab-section">
                            <h3 class="tab-title">Data Keluarga</h3>

                            <div class="family-list">
                                <div class="family-item">
                                    <div class="family-main">
                                        <div>
                                            <div class="family-name">
                                                Sinta Maharani
                                            </div>
                                            <div class="family-relation">
                                                Istri
                                            </div>
                                        </div>
                                        <div class="family-meta">
                                            Kontak Darurat
                                        </div>
                                    </div>
                                    <div class="family-detail">
                                        <span>TTL: Jakarta, 12 Mei 1991</span>
                                        <span>No. HP: 0812-1111-2222</span>
                                    </div>
                                </div>

                                <div class="family-item">
                                    <div class="family-main">
                                        <div>
                                            <div class="family-name">
                                                Rafi Budiansyah
                                            </div>
                                            <div class="family-relation">
                                                Anak
                                            </div>
                                        </div>
                                        <div class="family-meta">
                                            Tanggungan
                                        </div>
                                    </div>
                                    <div class="family-detail">
                                        <span
                                            >TTL: Jakarta, 04 Agustus 2018</span
                                        >
                                        <span>Status sekolah: TK</span>
                                    </div>
                                </div>

                                <div class="family-item">
                                    <div class="family-main">
                                        <div>
                                            <div class="family-name">
                                                Sutrisno
                                            </div>
                                            <div class="family-relation">
                                                Ayah
                                            </div>
                                        </div>
                                        <div class="family-meta">Orang tua</div>
                                    </div>
                                    <div class="family-detail">
                                        <span>Alamat: Semarang</span>
                                        <span>No. HP: 0813-2222-3333</span>
                                    </div>
                                </div>
                            </div>

                            <div class="family-note">
                                Data keluarga digunakan untuk kebutuhan
                                administrasi HR, tunjangan, dan kontak darurat.
                                Contoh di atas hanya dummy.
                            </div>
                        </div>
                    </template>
                </Tabs>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import Tabs from '@/components/Tabs.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const tabs = [
    { key: 'karyawan', label: 'Data Karyawan' },
    { key: 'pendidikan', label: 'Pendidikan' },
    { key: 'pekerjaan', label: 'Riwayat Kerja' },
    { key: 'keluarga', label: 'Keluarga' },
];
</script>

<style scoped>
.detail-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.detail-card {
    padding: 18px 18px 16px;
}

/* wrapper konten tiap tab */
.tab-section {
    margin-top: 14px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.tab-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
}

/* chip kecil di data karyawan */
.tag-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag {
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid transparent;
}
.tag-status {
    background: #ecfdf3;
    border-color: #bbf7d0;
    color: #15803d;
}
.tag-tenure {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
}

/* grid field */
.detail-grid {
    display: grid;
    gap: 10px 16px;
}

.detail-grid.two-col {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
}

.detail-grid.secondary {
    margin-top: 6px;
    padding-top: 8px;
    border-top: 1px dashed #e5e7eb;
}

/* field */
.field {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.field-label {
    font-size: 12px;
    color: #6b7280;
}

.field-value {
    font-size: 14px;
    color: #111827;
}

/* pendidikan */
.education-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.edu-item {
    border-radius: 12px;
    padding: 10px 12px;
    background: linear-gradient(135deg, #eff6ff 0%, #ffffff 60%);
    border: 1px solid #dbeafe;
}

.edu-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
}

.edu-degree {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.edu-school {
    font-size: 13px;
    color: #4b5563;
}

.edu-year {
    font-size: 13px;
    color: #6b7280;
    white-space: nowrap;
}

.edu-extra {
    margin-top: 4px;
    font-size: 13px;
    color: #4b5563;
}

.education-note {
    font-size: 12px;
    color: #6b7280;
    padding: 8px 10px;
    border-radius: 10px;
    background: #f9fafb;
    border: 1px dashed #e5e7eb;
}

/* timeline riwayat kerja */
.timeline {
    position: relative;
    margin-top: 4px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    padding-bottom: 12px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: 4px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 999px;
    background: #3b82f6;
    border: 2px solid #eff6ff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
}

.timeline-body {
    border-radius: 12px;
    padding: 8px 10px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
}

.timeline-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
}

.job-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.job-company {
    font-size: 13px;
    color: #4b5563;
}

.job-period {
    font-size: 12px;
    color: #6b7280;
    white-space: nowrap;
}

.job-desc {
    margin: 6px 0 0;
    padding-left: 18px;
    font-size: 13px;
    color: #4b5563;
}

.job-desc li + li {
    margin-top: 2px;
}

/* keluarga */
.family-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.family-item {
    border-radius: 12px;
    padding: 10px 12px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
}

.family-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.family-name {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.family-relation {
    font-size: 13px;
    color: #6b7280;
}

.family-meta {
    font-size: 12px;
    color: #2563eb;
    padding: 3px 8px;
    border-radius: 999px;
    background: #dbeafe;
}

.family-detail {
    margin-top: 4px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px 12px;
    font-size: 13px;
    color: #4b5563;
}

.family-note {
    margin-top: 6px;
    font-size: 12px;
    color: #6b7280;
    padding: 8px 10px;
    border-radius: 10px;
    background: #fefce8;
    border: 1px dashed #facc15;
}

/* responsif */
@media (max-width: 800px) {
    .detail-grid.two-col {
        grid-template-columns: minmax(0, 1fr);
    }

    .edu-header,
    .timeline-header,
    .family-main {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
