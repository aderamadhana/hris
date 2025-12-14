<template>
    <AppLayout>
        <section class="page detail-page">
            <!-- HEADER -->
            <div class="page-header">
                <div>
                    <h2 class="page-title">Tambah Data Karyawan</h2>
                    <p class="page-subtitle">
                        Ubah profil, pendidikan, pekerjaan, keluarga, kesehatan,
                        dan kelengkapan dokumen.
                    </p>
                </div>
            </div>

            <!-- CONTENT -->
            <div v-if="!loading" class="card detail-card">
                <Tabs :tabs="tabs">
                    <!-- ================= FORM KARYAWAN ================= -->
                    <template #karyawan>
                        <div class="tab-section">
                            <h3 class="tab-title">Profil Karyawan</h3>

                            <form @submit.prevent="handleSubmitKaryawan">
                                <!-- DATA UTAMA -->
                                <div class="form-section">
                                    <h4 class="form-section-title">
                                        Data Utama
                                    </h4>

                                    <div class="detail-grid two-col">
                                        <div class="form-group">
                                            <label class="field-label">
                                                Nama Lengkap
                                                <span class="required">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                v-model="formEmployee.nama"
                                                class="form-input"
                                                required
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label">
                                                NRP
                                                <span class="required">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                v-model="formEmployee.nrp"
                                                class="form-input"
                                                required
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label">
                                                Jenis Kelamin
                                                <span class="required">*</span>
                                            </label>
                                            <select
                                                v-model="formEmployee.jk"
                                                class="form-input"
                                                required
                                            >
                                                <option value="">Pilih</option>
                                                <option value="Laki-laki">
                                                    Laki-laki
                                                </option>
                                                <option value="Perempuan">
                                                    Perempuan
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Tempat Lahir</label
                                            >
                                            <input
                                                type="text"
                                                v-model="
                                                    formEmployee.tempat_lahir
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Tanggal Lahir</label
                                            >
                                            <input
                                                type="date"
                                                v-model="
                                                    formEmployee.tanggal_lahir
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Status Perkawinan</label
                                            >
                                            <select
                                                v-model="
                                                    formEmployee.perkawinan
                                                "
                                                class="form-input"
                                            >
                                                <option value="">Pilih</option>
                                                <option value="Belum Kawin">
                                                    Belum Kawin
                                                </option>
                                                <option value="Kawin">
                                                    Kawin
                                                </option>
                                                <option value="Cerai">
                                                    Cerai
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Agama</label
                                            >
                                            <select
                                                v-model="formEmployee.agama"
                                                class="form-input"
                                            >
                                                <option value="">Pilih</option>
                                                <option value="Islam">
                                                    Islam
                                                </option>
                                                <option value="Kristen">
                                                    Kristen
                                                </option>
                                                <option value="Katolik">
                                                    Katolik
                                                </option>
                                                <option value="Hindu">
                                                    Hindu
                                                </option>
                                                <option value="Buddha">
                                                    Buddha
                                                </option>
                                                <option value="Konghucu">
                                                    Konghucu
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Kewarganegaraan</label
                                            >
                                            <input
                                                type="text"
                                                v-model="
                                                    formEmployee.kewarganegaraan
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Status</label
                                            >
                                            <select
                                                v-model="formEmployee.status"
                                                class="form-input"
                                            >
                                                <option value="1">Aktif</option>
                                                <option value="0">
                                                    Tidak Aktif
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- KONTAK & IDENTITAS -->
                                <div class="form-section">
                                    <h4 class="form-section-title">
                                        Kontak & Identitas
                                    </h4>

                                    <div class="detail-grid two-col">
                                        <div class="form-group">
                                            <label class="field-label"
                                                >No. KTP</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formAlamat.ktp"
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >No. HP</label
                                            >
                                            <input
                                                type="tel"
                                                v-model="formAlamat.phone"
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >No. WhatsApp</label
                                            >
                                            <input
                                                type="tel"
                                                v-model="formEmployee.no_wa"
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Email</label
                                            >
                                            <input
                                                type="email"
                                                v-model="formEmployee.email"
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Domisili</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formAlamat.domisili"
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Kota</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formAlamat.kota"
                                                class="form-input"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- BPJS & LISENSI -->
                                <div class="form-section">
                                    <h4 class="form-section-title">
                                        BPJS & Lisensi
                                    </h4>

                                    <div class="detail-grid two-col">
                                        <div class="form-group">
                                            <label class="field-label"
                                                >BPJS Ketenagakerjaan</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formEmployee.bpjs_tk"
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >BPJS Kesehatan</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formEmployee.bpjs_kes"
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Faskes</label
                                            >
                                            <input
                                                type="text"
                                                v-model="
                                                    formEmployee.nama_faskes
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >No SKCK</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formEmployee.no_skck"
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Masa Berlaku SKCK</label
                                            >
                                            <input
                                                type="date"
                                                v-model="
                                                    formEmployee.masa_berlaku_skck
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Jenis Lisensi</label
                                            >
                                            <input
                                                type="text"
                                                v-model="
                                                    formEmployee.jenis_lisensi
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >No Lisensi</label
                                            >
                                            <input
                                                type="text"
                                                v-model="
                                                    formEmployee.no_lisensi
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Masa Berlaku Lisensi</label
                                            >
                                            <input
                                                type="date"
                                                v-model="
                                                    formEmployee.masa_berlaku_lisensi
                                                "
                                                class="form-input"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        @click="resetFormKaryawan"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Simpan Tab Karyawan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </template>

                    <!-- ================= FORM PENDIDIKAN ================= -->
                    <template #pendidikan>
                        <div class="tab-section">
                            <h3 class="tab-title">Riwayat Pendidikan</h3>

                            <form @submit.prevent="handleSubmitPendidikan">
                                <div class="detail-grid two-col">
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Jenjang</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPendidikan.jenjang"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Jurusan</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPendidikan.jurusan"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Sekolah</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPendidikan.sekolah"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Tahun Lulus</label
                                        >
                                        <input
                                            type="number"
                                            v-model="formPendidikan.tahun_lulus"
                                            class="form-input"
                                            min="1900"
                                            max="2100"
                                        />
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        @click="resetFormPendidikan"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Tambah Pendidikan
                                    </button>
                                </div>
                            </form>

                            <div
                                class="mt-4 education-list"
                                v-if="listPendidikan.length"
                            >
                                <div
                                    v-for="(edu, i) in listPendidikan"
                                    :key="i"
                                    class="edu-item"
                                >
                                    <div class="edu-header">
                                        <div>
                                            <div class="edu-degree">
                                                {{ edu.jenjang || '-' }}
                                                {{ edu.jurusan || '' }}
                                            </div>
                                            <div class="edu-school">
                                                {{ edu.sekolah || '-' }}
                                            </div>
                                        </div>
                                        <div class="edu-year">
                                            Lulus {{ edu.tahun_lulus || '-' }}
                                            <button
                                                class="btn-delete"
                                                type="button"
                                                @click="hapusPendidikan(i)"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="empty mt-4">
                                Data pendidikan belum tersedia
                            </div>
                        </div>
                    </template>

                    <!-- ================= FORM PEKERJAAN ================= -->
                    <template #pekerjaan>
                        <div class="tab-section">
                            <h3 class="tab-title">Riwayat Pekerjaan</h3>

                            <form @submit.prevent="handleSubmitPekerjaan">
                                <div class="detail-grid two-col">
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Jabatan</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPekerjaan.jabatan"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Perusahaan</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPekerjaan.perusahaan"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Bagian</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPekerjaan.bagian"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >No. Kontrak</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPekerjaan.no_kontrak"
                                            class="form-input"
                                        />
                                    </div>

                                    <div class="form-group">
                                        <label class="field-label"
                                            >Cost Center</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPekerjaan.cost_center"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Jenis Kontrak</label
                                        >
                                        <input
                                            type="text"
                                            v-model="
                                                formPekerjaan.jenis_kontrak
                                            "
                                            class="form-input"
                                        />
                                    </div>

                                    <div class="form-group">
                                        <label class="field-label">Mulai</label>
                                        <input
                                            type="date"
                                            v-model="formPekerjaan.mulai"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Selesai</label
                                        >
                                        <input
                                            type="date"
                                            v-model="formPekerjaan.selesai"
                                            class="form-input"
                                        />
                                    </div>

                                    <div class="form-group">
                                        <label class="field-label"
                                            >Jenis Kerja</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPekerjaan.jenis_kerja"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Pola Kerja</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPekerjaan.pola_kerja"
                                            class="form-input"
                                        />
                                    </div>

                                    <div class="form-group">
                                        <label class="field-label"
                                            >Hari Kerja</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPekerjaan.hari_kerja"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Status Kontrak</label
                                        >
                                        <input
                                            type="text"
                                            v-model="
                                                formPekerjaan.status_kontrak
                                            "
                                            class="form-input"
                                        />
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        @click="resetFormPekerjaan"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Tambah Pekerjaan
                                    </button>
                                </div>
                            </form>

                            <div
                                v-if="listPekerjaan.length"
                                class="mt-4 timeline"
                            >
                                <div
                                    v-for="(job, i) in listPekerjaan"
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
                                                    –
                                                    {{ job.bagian || '-' }}
                                                </div>
                                                <div class="job-meta">
                                                    No. Kontrak:
                                                    {{ job.no_kontrak || '-' }}
                                                    | Cost Center:
                                                    {{ job.cost_center || '-' }}
                                                </div>
                                            </div>
                                            <div class="job-period">
                                                {{ job.mulai || '-' }} –
                                                {{ job.selesai || 'Sekarang' }}
                                                <div>
                                                    <button
                                                        class="btn-delete"
                                                        type="button"
                                                        @click="
                                                            hapusPekerjaan(i)
                                                        "
                                                    >
                                                        Hapus
                                                    </button>
                                                </div>
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

                            <div v-else class="empty mt-4">
                                Riwayat pekerjaan belum tersedia
                            </div>
                        </div>
                    </template>

                    <!-- ================= FORM KELUARGA ================= -->
                    <template #keluarga>
                        <div class="tab-section">
                            <h3 class="tab-title">Data Keluarga</h3>

                            <form @submit.prevent="handleSubmitKeluarga">
                                <div class="detail-grid two-col">
                                    <div class="form-group">
                                        <label class="field-label">Nama</label>
                                        <input
                                            type="text"
                                            v-model="formKeluarga.nama"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Hubungan</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formKeluarga.hubungan"
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label">TTL</label>
                                        <input
                                            type="text"
                                            v-model="formKeluarga.ttl"
                                            class="form-input"
                                            placeholder="Contoh: Jakarta, 01-01-2000"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label class="field-label"
                                            >No. HP</label
                                        >
                                        <input
                                            type="tel"
                                            v-model="formKeluarga.no_hp"
                                            class="form-input"
                                        />
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        @click="resetFormKeluarga"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Tambah Keluarga
                                    </button>
                                </div>
                            </form>

                            <div
                                v-if="listKeluarga.length"
                                class="mt-4 family-list"
                            >
                                <div
                                    v-for="(f, i) in listKeluarga"
                                    :key="i"
                                    class="family-item"
                                >
                                    <div class="family-main">
                                        <div>
                                            <div class="family-name">
                                                {{ f.nama || '-' }}
                                            </div>
                                            <div class="family-relation">
                                                {{ f.hubungan || '-' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="family-detail">
                                        <span>TTL: {{ f.ttl || '-' }}</span>
                                        <span
                                            >No. HP: {{ f.no_hp || '-' }}</span
                                        >
                                    </div>

                                    <button
                                        class="btn-delete"
                                        type="button"
                                        @click="hapusKeluarga(i)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>

                            <div v-else class="empty mt-4">
                                Data keluarga belum tersedia
                            </div>
                        </div>
                    </template>

                    <!-- ================= FORM KESEHATAN ================= -->
                    <template #kesehatan>
                        <div class="tab-section">
                            <h3 class="tab-title">Informasi Kesehatan</h3>

                            <form @submit.prevent="handleSubmitKesehatan">
                                <div class="form-section">
                                    <h4 class="form-section-title">
                                        Data Fisik
                                    </h4>
                                    <div class="detail-grid two-col">
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Tinggi Badan (cm)</label
                                            >
                                            <input
                                                type="number"
                                                v-model="
                                                    formKesehatan.tinggi_badan
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Berat Badan (kg)</label
                                            >
                                            <input
                                                type="number"
                                                v-model="
                                                    formKesehatan.berat_badan
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Golongan Darah</label
                                            >
                                            <input
                                                type="text"
                                                v-model="
                                                    formKesehatan.gol_darah
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Buta Warna</label
                                            >
                                            <select
                                                v-model="
                                                    formKesehatan.buta_warna
                                                "
                                                class="form-input"
                                            >
                                                <option :value="false">
                                                    Tidak
                                                </option>
                                                <option :value="true">
                                                    Ya
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h4 class="form-section-title">
                                        Riwayat & Screening
                                    </h4>
                                    <div class="detail-grid two-col">
                                        <div
                                            class="form-group"
                                            style="grid-column: 1 / -1"
                                        >
                                            <label class="field-label"
                                                >Riwayat Penyakit</label
                                            >
                                            <textarea
                                                v-model="
                                                    formKesehatan.riwayat_penyakit
                                                "
                                                class="form-input"
                                            ></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Hasil Drug Test</label
                                            >
                                            <input
                                                type="text"
                                                v-model="
                                                    formKesehatan.hasil_drug_test
                                                "
                                                class="form-input"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Tanggal Drug Test</label
                                            >
                                            <input
                                                type="date"
                                                v-model="
                                                    formKesehatan.tanggal_drug_test
                                                "
                                                class="form-input"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h4 class="form-section-title">
                                        Hasil Lab & MCU
                                    </h4>
                                    <div class="detail-grid two-col">
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Darah</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formKesehatan.darah"
                                                class="form-input"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Urine</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formKesehatan.urine"
                                                class="form-input"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Fungsi Hati</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formKesehatan.f_hati"
                                                class="form-input"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Gula Darah</label
                                            >
                                            <input
                                                type="text"
                                                v-model="
                                                    formKesehatan.gula_darah
                                                "
                                                class="form-input"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Ginjal</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formKesehatan.ginjal"
                                                class="form-input"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Thorax</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formKesehatan.thorax"
                                                class="form-input"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h4 class="form-section-title">
                                        Tanda Vital & Mata
                                    </h4>
                                    <div class="detail-grid two-col">
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Tensi</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formKesehatan.tensi"
                                                class="form-input"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Nadi</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formKesehatan.nadi"
                                                class="form-input"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Mata OD</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formKesehatan.od"
                                                class="form-input"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Mata OS</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formKesehatan.os"
                                                class="form-input"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        @click="resetFormKesehatan"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Simpan Tab Kesehatan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </template>

                    <!-- ================= FORM DOKUMEN ================= -->
                    <template #dokumen>
                        <div class="tab-section">
                            <h3 class="tab-title">Kelengkapan Dokumen</h3>

                            <div class="form-section">
                                <h4 class="form-section-title">
                                    Upload Dokumen
                                </h4>

                                <div class="detail-grid two-col">
                                    <div
                                        v-for="doc in dokumenMeta"
                                        :key="doc.key"
                                        class="doc-upload"
                                    >
                                        <div class="field-label">
                                            {{ doc.label }}
                                        </div>

                                        <!-- status existing dari server -->
                                        <div
                                            v-if="getExistingDocUrl(doc.key)"
                                            class="doc-existing"
                                        >
                                            <a
                                                :href="
                                                    getExistingDocUrl(doc.key)
                                                "
                                                target="_blank"
                                                rel="noopener"
                                            >
                                                Lihat file tersimpan
                                            </a>
                                            <button
                                                type="button"
                                                class="btn-delete"
                                                @click="
                                                    markDeleteExisting(doc.key)
                                                "
                                            >
                                                Hapus file tersimpan
                                            </button>
                                        </div>

                                        <!-- status file baru -->
                                        <div
                                            v-if="formDokumen[doc.key]"
                                            class="doc-new"
                                        >
                                            <div class="doc-filename">
                                                File baru:
                                                {{ formDokumen[doc.key].name }}
                                            </div>
                                            <button
                                                type="button"
                                                class="btn-delete"
                                                @click="
                                                    removeSelectedFile(doc.key)
                                                "
                                            >
                                                Batalkan
                                            </button>
                                        </div>

                                        <input
                                            type="file"
                                            class="form-input"
                                            @change="
                                                onFileChange($event, doc.key)
                                            "
                                            accept=".pdf,.jpg,.jpeg,.png"
                                        />

                                        <small class="doc-hint">
                                            Format: PDF/JPG/PNG
                                        </small>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        @click="resetFormDokumen"
                                    >
                                        Reset Pilihan File
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-primary"
                                        @click="handleSubmitDokumen"
                                    >
                                        Simpan Tab Dokumen
                                    </button>
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

            <!-- TOMBOL SUBMIT AKHIR -->
            <div class="final-actions" v-if="!loading">
                <button
                    type="button"
                    class="btn btn-secondary-large"
                    @click="handleBatal"
                >
                    Batal
                </button>
                <button
                    type="button"
                    class="btn btn-primary-large"
                    @click="handleSubmitAll"
                >
                    Simpan Semua Perubahan
                </button>
            </div>
        </section>
    </AppLayout>
</template>

<script>
import Tabs from '@/components/Tabs.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

export default {
    components: { AppLayout, Tabs },

    data() {
        const page = usePage();
        return {
            user: page.props.auth.user,
            loading: true,

            // sumber data detail (optional dipakai kalau kamu masih fetch dari API)
            employee: {},
            alamat: {},
            pendidikan: [],
            pekerjaan: [],
            keluarga: [],
            kesehatan: {},
            dokumen: {},

            // tabs: SAMAKAN SLOT KEY -> pakai id
            tabs: [
                { key: 'karyawan', id: 'karyawan', label: 'Data Karyawan' },
                { key: 'pendidikan', id: 'pendidikan', label: 'Pendidikan' },
                { key: 'pekerjaan', id: 'pekerjaan', label: 'Pekerjaan' },
                { key: 'keluarga', id: 'keluarga', label: 'Keluarga' },
                { key: 'kesehatan', id: 'kesehatan', label: 'Kesehatan' },
                { key: 'dokumen', id: 'dokumen', label: 'Kelengkapan Dokumen' },
            ],

            // ===== FORM STATE (EDIT) =====
            formEmployee: {
                nama: '',
                nrp: '',
                jk: '',
                tempat_lahir: '',
                tanggal_lahir: '',
                perkawinan: '',
                agama: '',
                kewarganegaraan: 'Indonesia',
                status: '1',
                no_wa: '',
                email: '',
                bpjs_tk: '',
                bpjs_kes: '',
                nama_faskes: '',
                no_skck: '',
                masa_berlaku_skck: '',
                jenis_lisensi: '',
                no_lisensi: '',
                masa_berlaku_lisensi: '',
            },

            formAlamat: {
                ktp: '',
                phone: '',
                domisili: '',
                kota: '',
            },

            formPendidikan: {
                jenjang: '',
                jurusan: '',
                sekolah: '',
                tahun_lulus: '',
            },
            listPendidikan: [],

            formPekerjaan: {
                jabatan: '',
                perusahaan: '',
                bagian: '',
                no_kontrak: '',
                cost_center: '',
                jenis_kontrak: '',
                mulai: '',
                selesai: '',
                jenis_kerja: '',
                pola_kerja: '',
                hari_kerja: '',
                status_kontrak: '',
            },
            listPekerjaan: [],

            formKeluarga: {
                nama: '',
                hubungan: '',
                ttl: '',
                no_hp: '',
            },
            listKeluarga: [],

            formKesehatan: {
                tinggi_badan: '',
                berat_badan: '',
                gol_darah: '',
                buta_warna: false,
                riwayat_penyakit: '',
                hasil_drug_test: '',
                tanggal_drug_test: '',
                darah: '',
                urine: '',
                f_hati: '',
                gula_darah: '',
                ginjal: '',
                thorax: '',
                tensi: '',
                nadi: '',
                od: '',
                os: '',
            },

            formDokumen: {
                pas_foto: false,
                ktp: false,
                kk: false,
                bpjs_tk: false,
                vaksin: false,
                sio_forklift: false,
                form_bpjs_tk: false,
                form_bpjs_kes: false,
                paklaring: false,
                sim_b1: false,
                kartu_garda: false,
            },

            dokumenMeta: [
                { key: 'pas_foto', label: 'Pas Foto' },
                { key: 'ktp', label: 'KTP' },
                { key: 'kk', label: 'Kartu Keluarga' },
                { key: 'bpjs_tk', label: 'BPJS Ketenagakerjaan' },
                { key: 'vaksin', label: 'Sertifikat Vaksin' },
                { key: 'sio_forklift', label: 'SIO Forklift' },
                { key: 'form_bpjs_tk', label: 'Formulir BPJS TK' },
                { key: 'form_bpjs_kes', label: 'Formulir BPJS Kesehatan' },
                {
                    key: 'paklaring',
                    label: 'Surat Pengalaman Kerja / Paklaring',
                },
                { key: 'sim_b1', label: 'SIM B1' },
                { key: 'kartu_garda', label: 'Kartu Garda Pratama' },
            ],
            deleteExistingDokumen: {},
        };
    },

    mounted() {
        // TODO: ganti ini dengan fetch data kamu (props inertia / axios)
        // simulasi: setelah data ada, panggil hydrateFormsFromDetail()
        this.hydrateFormsFromDetail();
        this.loading = false;
    },

    methods: {
        // ====== HYDRATE (DETAIL -> FORM) ======
        hydrateFormsFromDetail() {
            // kalau data berasal dari this.employee/dll, map ke form:
            // ini aman meskipun objek kosong
            this.formEmployee = {
                ...this.formEmployee,
                ...this.employee,
                status: String(
                    this.employee?.status ?? this.formEmployee.status,
                ),
            };

            this.formAlamat = {
                ...this.formAlamat,
                ...this.alamat,
            };

            this.listPendidikan = Array.isArray(this.pendidikan)
                ? [...this.pendidikan]
                : [];

            this.listPekerjaan = Array.isArray(this.pekerjaan)
                ? [...this.pekerjaan]
                : [];

            this.listKeluarga = Array.isArray(this.keluarga)
                ? [...this.keluarga]
                : [];

            this.formKesehatan = {
                ...this.formKesehatan,
                ...this.kesehatan,
                buta_warna: Boolean(this.kesehatan?.buta_warna),
            };

            // dokumen (boolean)
            if (this.dokumen && typeof this.dokumen === 'object') {
                Object.keys(this.formDokumen).forEach((k) => {
                    this.formDokumen[k] = Boolean(this.dokumen?.[k]);
                });
            }
        },

        // ====== SUBMIT PER TAB (optional) ======
        handleSubmitKaryawan() {
            console.log('submit tab karyawan', {
                employee: this.formEmployee,
                alamat: this.formAlamat,
            });
            alert('Tab Karyawan tersimpan (dummy).');
        },

        handleSubmitPendidikan() {
            this.listPendidikan.push({ ...this.formPendidikan });
            this.resetFormPendidikan();
            alert('Pendidikan ditambahkan.');
        },
        resetFormPendidikan() {
            this.formPendidikan = {
                jenjang: '',
                jurusan: '',
                sekolah: '',
                tahun_lulus: '',
            };
        },
        hapusPendidikan(index) {
            if (confirm('Hapus data pendidikan ini?')) {
                this.listPendidikan.splice(index, 1);
            }
        },

        handleSubmitPekerjaan() {
            this.listPekerjaan.push({ ...this.formPekerjaan });
            this.resetFormPekerjaan();
            alert('Pekerjaan ditambahkan.');
        },
        resetFormPekerjaan() {
            this.formPekerjaan = {
                jabatan: '',
                perusahaan: '',
                bagian: '',
                no_kontrak: '',
                cost_center: '',
                jenis_kontrak: '',
                mulai: '',
                selesai: '',
                jenis_kerja: '',
                pola_kerja: '',
                hari_kerja: '',
                status_kontrak: '',
            };
        },
        hapusPekerjaan(index) {
            if (confirm('Hapus riwayat pekerjaan ini?')) {
                this.listPekerjaan.splice(index, 1);
            }
        },

        handleSubmitKeluarga() {
            this.listKeluarga.push({ ...this.formKeluarga });
            this.resetFormKeluarga();
            alert('Keluarga ditambahkan.');
        },
        resetFormKeluarga() {
            this.formKeluarga = { nama: '', hubungan: '', ttl: '', no_hp: '' };
        },
        hapusKeluarga(index) {
            if (confirm('Hapus data keluarga ini?')) {
                this.listKeluarga.splice(index, 1);
            }
        },

        handleSubmitKesehatan() {
            console.log('submit tab kesehatan', this.formKesehatan);
            alert('Tab Kesehatan tersimpan (dummy).');
        },
        resetFormKesehatan() {
            this.formKesehatan = {
                tinggi_badan: '',
                berat_badan: '',
                gol_darah: '',
                buta_warna: false,
                riwayat_penyakit: '',
                hasil_drug_test: '',
                tanggal_drug_test: '',
                darah: '',
                urine: '',
                f_hati: '',
                gula_darah: '',
                ginjal: '',
                thorax: '',
                tensi: '',
                nadi: '',
                od: '',
                os: '',
            };
        },

        handleSubmitDokumen() {
            console.log('submit tab dokumen', this.formDokumen);
            alert('Tab Dokumen tersimpan (dummy).');
        },
        resetFormDokumen() {
            Object.keys(this.formDokumen).forEach(
                (k) => (this.formDokumen[k] = false),
            );
        },
        checkAllDokumen() {
            Object.keys(this.formDokumen).forEach(
                (k) => (this.formDokumen[k] = true),
            );
        },

        resetFormKaryawan() {
            // pilih: reset ke default atau reset ke data awal (kalau mau data awal, simpan snapshot)
            Object.assign(this.formEmployee, {
                nama: '',
                nrp: '',
                jk: '',
                tempat_lahir: '',
                tanggal_lahir: '',
                perkawinan: '',
                agama: '',
                kewarganegaraan: 'Indonesia',
                status: '1',
                no_wa: '',
                email: '',
                bpjs_tk: '',
                bpjs_kes: '',
                nama_faskes: '',
                no_skck: '',
                masa_berlaku_skck: '',
                jenis_lisensi: '',
                no_lisensi: '',
                masa_berlaku_lisensi: '',
            });
            Object.assign(this.formAlamat, {
                ktp: '',
                phone: '',
                domisili: '',
                kota: '',
            });
        },

        // ====== SUBMIT SEMUA ======
        handleSubmitAll() {
            const fd = new FormData();

            // data biasa (objek) -> stringify
            fd.append('employee', JSON.stringify(this.formEmployee));
            fd.append('alamat', JSON.stringify(this.formAlamat));
            fd.append('pendidikan', JSON.stringify(this.listPendidikan));
            fd.append('pekerjaan', JSON.stringify(this.listPekerjaan));
            fd.append('keluarga', JSON.stringify(this.listKeluarga));
            fd.append('kesehatan', JSON.stringify(this.formKesehatan));

            // files
            Object.keys(this.formDokumen).forEach((k) => {
                if (this.formDokumen[k])
                    fd.append(`dokumen[${k}]`, this.formDokumen[k]);
            });

            // delete existing
            Object.keys(this.deleteExistingDokumen).forEach((k) => {
                if (this.deleteExistingDokumen[k])
                    fd.append(`dokumen_delete[${k}]`, '1');
            });

            // TODO: ganti ke inertia/axios
            // router.post(route('karyawan.update', this.employee.id), fd, { forceFormData: true })
            console.log('SUBMIT ALL FormData ready');
        },

        handleBatal() {
            if (
                confirm(
                    'Batalkan perubahan? Data yang belum disimpan akan hilang.',
                )
            ) {
                // TODO: redirect
                // this.$router.push('/karyawan')
            }
        },

        onFileChange(e, key) {
            const file = e.target.files?.[0] || null;
            if (!file) return;

            // contoh validasi ukuran (opsional)
            const maxMB = 5;
            if (file.size > maxMB * 1024 * 1024) {
                alert(`Ukuran file maksimal ${maxMB}MB`);
                e.target.value = '';
                return;
            }

            this.formDokumen[key] = file;
            // kalau upload file baru, otomatis batalin delete existing
            if (this.deleteExistingDokumen[key])
                delete this.deleteExistingDokumen[key];
        },

        removeSelectedFile(key) {
            this.formDokumen[key] = null;
        },

        resetFormDokumen() {
            Object.keys(this.formDokumen).forEach(
                (k) => (this.formDokumen[k] = null),
            );
        },

        // mapping existing URL dari server (sesuaikan nama field di backend)
        getExistingDocUrl(key) {
            // contoh pola: this.dokumen.ktp_url
            return this.dokumen?.[`${key}_url`] || null;
        },

        markDeleteExisting(key) {
            if (confirm('Hapus file dokumen yang tersimpan?')) {
                this.deleteExistingDokumen[key] = true;
                // optional: juga kosongkan file baru kalau ada
                this.formDokumen[key] = null;
            }
        },

        handleSubmitDokumen() {
            // biasanya digabung di handleSubmitAll(),
            // tapi ini kalau mau simpan khusus tab dokumen
            const fd = new FormData();

            // file baru
            Object.keys(this.formDokumen).forEach((k) => {
                if (this.formDokumen[k]) {
                    fd.append(`dokumen[${k}]`, this.formDokumen[k]);
                }
            });

            // request hapus file existing
            Object.keys(this.deleteExistingDokumen).forEach((k) => {
                if (this.deleteExistingDokumen[k] === true) {
                    fd.append(`dokumen_delete[${k}]`, '1');
                }
            });

            console.log('FormData dokumen ready');
            alert('Siap submit dokumen (ganti ke inertia/axios).');
        },
    },
};
</script>
