<template>
    <AppLayout>
        <section class="page detail-page">
            <!-- HEADER -->
            <div class="page-header">
                <div>
                    <h2 class="page-title">Edit Data Karyawan</h2>
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
                                                >No. KTP
                                                <span class="required"
                                                    >*</span
                                                ></label
                                            >
                                            <input
                                                type="text"
                                                v-model="formAlamat.ktp"
                                                class="form-input"
                                                required
                                            />
                                        </div>

                                        <!-- <div class="form-group">
                                            <label class="field-label"
                                                >No. HP</label
                                            >
                                            <input
                                                type="tel"
                                                v-model="formAlamat.phone"
                                                class="form-input"
                                            />
                                        </div> -->

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

                                            <select
                                                v-model="
                                                    formEmployee.jenis_lisensi
                                                "
                                                class="form-input"
                                            >
                                                <option disabled value="">
                                                    -- Pilih Jenis Lisensi --
                                                </option>
                                                <option value="A">A</option>
                                                <option value="B1">B1</option>
                                                <option value="B2">B2</option>
                                                <option value="C">C</option>
                                                <option value="SIM A">
                                                    SIM A
                                                </option>
                                                <option value="SIM B1">
                                                    SIM B1
                                                </option>
                                                <option value="SIM B2">
                                                    SIM B2
                                                </option>
                                            </select>
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

                                        <select
                                            v-model="formPendidikan.jenjang"
                                            class="form-input"
                                        >
                                            <option disabled value="">
                                                -- Pilih Jenjang Pendidikan --
                                            </option>
                                            <option value="TK">TK</option>
                                            <option value="SD">SD</option>
                                            <option value="SMP">SMP</option>
                                            <option value="SMA/SMK">
                                                SMA/SMK
                                            </option>
                                            <option value="D1">D1</option>
                                            <option value="D2">D2</option>
                                            <option value="D3">D3</option>
                                            <option value="D4">D4</option>
                                            <option value="S1">S1</option>
                                            <option value="S2">S2</option>
                                            <option value="S3">S3</option>
                                            <option value="Non-Formal">
                                                Non-Formal
                                            </option>
                                        </select>
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

                                        <select
                                            v-model="
                                                formPekerjaan.jenis_kontrak
                                            "
                                            class="form-input"
                                        >
                                            <option disabled value="">
                                                -- Pilih Jenis Kontrak --
                                            </option>
                                            <option value="PKWT">
                                                PKWT (Kontrak)
                                            </option>
                                            <option value="PKWTT">
                                                PKWTT (Tetap)
                                            </option>
                                            <option value="Magang">
                                                Magang
                                            </option>
                                            <option value="Harian">
                                                Harian
                                            </option>
                                            <option value="Freelance">
                                                Freelance
                                            </option>
                                            <option value="Outsource">
                                                Outsource
                                            </option>
                                        </select>
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

                                        <select
                                            v-model="
                                                formPekerjaan.status_kontrak
                                            "
                                            class="form-input"
                                        >
                                            <option disabled value="">
                                                -- Pilih Status Kontrak --
                                            </option>
                                            <option value="Aktif">Aktif</option>
                                            <option value="Tidak Aktif">
                                                Tidak Aktif
                                            </option>
                                            <option value="Resign">
                                                Resign
                                            </option>
                                        </select>
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

                                        <select
                                            v-model="formKeluarga.hubungan"
                                            class="form-input"
                                        >
                                            <option disabled value="">
                                                -- Pilih Hubungan Keluarga --
                                            </option>
                                            <option value="Ayah">Ayah</option>
                                            <option value="Ibu">Ibu</option>
                                            <option value="Suami">Suami</option>
                                            <option value="Istri">Istri</option>
                                            <option value="Anak">Anak</option>
                                            <option value="Saudara Kandung">
                                                Saudara Kandung
                                            </option>
                                            <option value="Kakak">Kakak</option>
                                            <option value="Adik">Adik</option>
                                            <option value="Kakek">Kakek</option>
                                            <option value="Nenek">Nenek</option>
                                            <option value="Paman">Paman</option>
                                            <option value="Bibi">Bibi</option>
                                            <option value="Sepupu">
                                                Sepupu
                                            </option>
                                            <option value="Wali">Wali</option>
                                            <option value="Lainnya">
                                                Lainnya
                                            </option>
                                        </select>
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
                                    <!-- <div class="form-group">
                                        <label class="field-label"
                                            >No. HP</label
                                        >
                                        <input
                                            type="tel"
                                            v-model="formKeluarga.no_hp"
                                            class="form-input"
                                        />
                                    </div> -->
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
import { triggerAlert } from '@/utils/alert';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    props: {
        employee_id: String,
    },
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
                // Data utama employees table
                nama: '',
                nrp: '',
                jk: '',
                tempat_lahir: '',
                tanggal_lahir: '',
                perkawinan: '',
                agama: '',
                kewarganegaraan: '',
                status: '1',

                // Data dari employee_personals table
                no_wa: '',
                bpjs_tk: '',
                bpjs_kes: '',
                nama_faskes: '',
                email: '',
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
            dokumenToDelete: [],
        };
    },

    mounted() {
        // TODO: ganti ini dengan fetch data kamu (props inertia / axios)
        // simulasi: setelah data ada, panggil hydrateFormsFromDetail()
        this.getDataKaryawan();
        console.log(this.employee_id);
        this.loading = false;
    },

    methods: {
        // ====== HYDRATE (DETAIL -> FORM) ======
        async getDataKaryawan() {
            axios
                .get(`/employee/${this.employee_id}`)
                .then(({ data }) => {
                    /* ===============================
             | FORM EMPLOYEE
             ===============================*/
                    Object.assign(this.formEmployee, {
                        nama: data.nama ?? '',
                        nrp: data.nrp ?? '',
                        jk: data.jenis_kelamin ?? '',
                        tempat_lahir: data.tempat_lahir ?? '',
                        tanggal_lahir: data.tanggal_lahir ?? '',
                        perkawinan: data.status_perkawinan ?? '',
                        agama: data.agama ?? '',
                        kewarganegaraan: data.kewarganegaraan ?? '',
                        status: data.status_active ?? '1',

                        no_wa: data.personal?.no_wa ?? '',
                        bpjs_tk: data.personal?.bpjs_tk ?? '',
                        bpjs_kes: data.personal?.bpjs_kes ?? '',
                        nama_faskes: data.personal?.nama_faskes ?? '',
                        email: data.personal?.email ?? '',
                        no_skck: data.personal?.no_skck ?? '',
                        masa_berlaku_skck:
                            data.personal?.masa_berlaku_skck ?? '',
                        jenis_lisensi: data.personal?.jenis_lisensi ?? '',
                        no_lisensi: data.personal?.no_lisensi ?? '',
                        masa_berlaku_lisensi:
                            data.personal?.masa_berlaku_lisensi ?? '',
                    });

                    /* ===============================
             | FORM ALAMAT
             ===============================*/
                    Object.assign(this.formAlamat, {
                        ktp: data.personal?.no_ktp ?? '',
                        phone: data.personal?.no_hp ?? '',
                        domisili: data.address?.alamat_lengkap ?? '',
                        kota: data.address?.kota ?? '',
                    });

                    /* ===============================
             | PENDIDIKAN
             ===============================*/
                    this.listPendidikan = (data.educations ?? []).map((p) => ({
                        jenjang: p.jenjang ?? '',
                        jurusan: p.jurusan ?? '',
                        sekolah: p.sekolah_asal ?? '',
                        tahun_lulus: p.tahun_lulus ?? '',
                    }));

                    /* ===============================
             | PEKERJAAN
             ===============================*/
                    this.listPekerjaan = (data.employmentss ?? []).map((j) => ({
                        jabatan: j.jabatan ?? j.job_roll ?? '',
                        perusahaan: j.perusahaan ?? '',
                        bagian: j.penempatan ?? '',
                        no_kontrak: j.no_kontrak ?? '',
                        cost_center: j.cost_center ?? '',
                        jenis_kontrak: j.jenis_kontrak ?? '',
                        mulai: j.tgl_awal_kerja ?? '',
                        selesai: j.tgl_akhir_kerja ?? '',
                        jenis_kerja: j.jenis_kerja ?? '',
                        pola_kerja: j.pola_kerja ?? '',
                        hari_kerja: j.hari_kerja ?? '',
                        status_kontrak: j.keterangan_status ?? j.status ?? '',
                    }));

                    /* ===============================
             | KELUARGA
             ===============================*/
                    this.listKeluarga = (data.families ?? []).map((f) => ({
                        nama: f.nama ?? '',
                        hubungan: f.hubungan ?? '',
                        ttl:
                            f.tempat_lahir && f.tanggal_lahir
                                ? `${f.tempat_lahir}, ${f.tanggal_lahir}`
                                : '',
                        no_hp: f.no_hp ?? '',
                    }));

                    /* ===============================
             | KESEHATAN
             ===============================*/
                    if (data.health) {
                        Object.assign(this.formKesehatan, {
                            tinggi_badan: data.health.tinggi_badan ?? '',
                            berat_badan: data.health.berat_badan ?? '',
                            gol_darah: data.health.gol_darah ?? '',
                            buta_warna: !!data.health.buta_warna,
                            riwayat_penyakit:
                                data.health.riwayat_penyakit ?? '',
                            hasil_drug_test: data.health.hasil_drug_test ?? '',
                            tanggal_drug_test:
                                data.health.tanggal_drug_test ?? '',
                            darah: data.health.darah ?? '',
                            urine: data.health.urine ?? '',
                            f_hati: data.health.f_hati ?? '',
                            gula_darah: data.health.gula_darah ?? '',
                            ginjal: data.health.ginjal ?? '',
                            thorax: data.health.thorax ?? '',
                            tensi: data.health.tensi ?? '',
                            nadi: data.health.nadi ?? '',
                            od: data.health.od ?? '',
                            os: data.health.os ?? '',
                        });
                    }

                    /* ===============================
             | DOKUMEN (CHECKLIST)
             ===============================*/
                    if (data.documents) {
                        Object.assign(this.formDokumen, {
                            pas_foto: !!data.documents.pas_foto,
                            ktp: !!data.documents.dokumen_ktp,
                            kk: !!data.documents.dokumen_kk,
                            bpjs_tk:
                                !!data.documents.dokumen_bpjs_ketenagakerjaan,
                            vaksin: !!data.documents.dokumen_sertifikat_vaksin,
                            sio_forklift: !!data.documents.dokumen_sio_forklift,
                            form_bpjs_tk:
                                !!data.documents.dokumen_formulir_bpjs_tk,
                            form_bpjs_kes:
                                !!data.documents
                                    .dokumen_formulir_bpjs_kesehatan,
                            paklaring:
                                !!data.documents.dokumen_surat_pengalaman_kerja,
                            sim_b1: !!data.documents.dokumen_sim_b1,
                            kartu_garda:
                                !!data.documents.dokumen_kartu_garda_pratama,
                        });
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        // ====== SUBMIT PER TAB (optional) ======
        async handleSubmitKaryawan() {
            try {
                // Validasi data utama
                if (
                    !this.formEmployee.nama ||
                    !this.formEmployee.nrp ||
                    !this.formEmployee.jk
                ) {
                    triggerAlert(
                        'warning',
                        'Nama, NRP, dan Jenis Kelamin wajib diisi!',
                    );
                    return;
                }

                triggerAlert(
                    'success',
                    'Data karyawan berhasil disimpan sementara. Klik "Simpan Semua Perubahan" untuk menyimpan ke database.',
                );
            } catch (error) {
                console.error('Error submit karyawan:', error);
                triggerAlert('error', 'Gagal menyimpan data karyawan');
            }
        },

        async handleSubmitPendidikan() {
            if (!this.formPendidikan.jenjang && !this.formPendidikan.sekolah) {
                triggerAlert('warning', 'Minimal isi jenjang atau sekolah');
                return;
            }

            this.listPendidikan.push({ ...this.formPendidikan });
            this.resetFormPendidikan();
            triggerAlert('success', 'Pendidikan ditambahkan');
        },

        async handleSubmitPekerjaan() {
            if (!this.formPekerjaan.perusahaan && !this.formPekerjaan.jabatan) {
                triggerAlert('warning', 'Minimal isi perusahaan atau jabatan');
                return;
            }

            this.listPekerjaan.push({ ...this.formPekerjaan });
            this.resetFormPekerjaan();
            triggerAlert('success', 'Pekerjaan ditambahkan');
        },

        async handleSubmitKeluarga() {
            if (!this.formKeluarga.nama) {
                triggerAlert('warning', 'Nama wajib diisi');
                return;
            }

            this.listKeluarga.push({ ...this.formKeluarga });
            this.resetFormKeluarga();
            triggerAlert('success', 'Data keluarga ditambahkan');
        },

        async handleSubmitKesehatan() {
            triggerAlert(
                'success',
                'Data kesehatan berhasil disimpan sementara. Klik "Simpan Semua Perubahan" untuk menyimpan ke database.',
            );
        },

        async handleSubmitDokumen() {
            triggerAlert(
                'success',
                'Dokumen berhasil disimpan sementara. Klik "Simpan Semua Perubahan" untuk menyimpan ke database.',
            );
        },

        // ================= HANDLE SUBMIT ALL =================

        async handleSubmitAll() {
            if (
                !this.formEmployee.nama ||
                !this.formEmployee.nrp ||
                !this.formEmployee.jk
            ) {
                triggerAlert(
                    'warning',
                    'Data utama karyawan (Nama, NRP, No. KTP, Jenis Kelamin) wajib diisi!',
                );
                return;
            }

            if (!confirm('Apakah Anda yakin ingin menyimpan semua data?')) {
                return;
            }

            this.loading = true;

            try {
                // Prepare FormData untuk upload file
                const formData = new FormData();

                // 1. Data Employees (table employees)
                formData.append('nama', this.formEmployee.nama);
                formData.append('nrp', this.formEmployee.nrp);
                formData.append('jenis_kelamin', this.formEmployee.jk);
                formData.append(
                    'tempat_lahir',
                    this.formEmployee.tempat_lahir || '',
                );
                formData.append(
                    'tanggal_lahir',
                    this.formEmployee.tanggal_lahir || '',
                );
                formData.append('agama', this.formEmployee.agama || '');
                formData.append(
                    'status_perkawinan',
                    this.formEmployee.perkawinan || '',
                );
                formData.append(
                    'kewarganegaraan',
                    this.formEmployee.kewarganegaraan || '',
                );
                formData.append('status_active', this.formEmployee.status);

                // 2. Data Employee Personals (table employee_personals)
                formData.append('no_ktp', this.formAlamat.ktp || '');
                formData.append('no_wa', this.formEmployee.no_wa || '');
                formData.append('bpjs_tk', this.formEmployee.bpjs_tk || '');
                formData.append('bpjs_kes', this.formEmployee.bpjs_kes || '');
                formData.append(
                    'nama_faskes',
                    this.formEmployee.nama_faskes || '',
                );
                formData.append('email', this.formEmployee.email || '');
                formData.append('no_skck', this.formEmployee.no_skck || '');
                formData.append(
                    'masa_berlaku_skck',
                    this.formEmployee.masa_berlaku_skck || '',
                );
                formData.append(
                    'jenis_lisensi',
                    this.formEmployee.jenis_lisensi || '',
                );
                formData.append(
                    'no_lisensi',
                    this.formEmployee.no_lisensi || '',
                );
                formData.append(
                    'masa_berlaku_lisensi',
                    this.formEmployee.masa_berlaku_lisensi || '',
                );

                // 3. Data Address (table employee_addresses)
                formData.append(
                    'alamat_lengkap',
                    this.formAlamat.domisili || '',
                );
                formData.append('kota', this.formAlamat.kota || '');
                formData.append('tipe', 'Domisili');

                // 4. Data Pendidikan (table employee_educations) - JSON Array
                formData.append(
                    'pendidikan',
                    JSON.stringify(
                        this.listPendidikan.map((edu) => ({
                            jenjang: edu.jenjang,
                            jurusan: edu.jurusan,
                            institusi: edu.sekolah, // sekolah -> institusi
                            tahun_lulus: edu.tahun_lulus,
                        })),
                    ),
                );

                // 5. Data Pekerjaan (table employee_employment_histories) - JSON Array
                // Cek apakah ada no_kontrak yang diisi untuk create user
                const hasNoKontrak = this.listPekerjaan.some(
                    (job) => job.no_kontrak,
                );

                formData.append(
                    'pekerjaan',
                    JSON.stringify(
                        this.listPekerjaan.map((job) => ({
                            perusahaan: job.perusahaan,
                            jabatan: job.jabatan,
                            penempatan: job.bagian, // bagian -> penempatan
                            no_kontrak: job.no_kontrak,
                            cost_center: job.cost_center,
                            jenis_kontrak: job.jenis_kontrak,
                            tgl_awal_kerja: job.mulai,
                            tgl_akhir_kerja: job.selesai,
                            jenis_kerja: job.jenis_kerja,
                            pola_kerja: job.pola_kerja,
                            hari_kerja: job.hari_kerja,
                            status: job.status_kontrak,
                        })),
                    ),
                );

                // Flag untuk create user jika ada no_kontrak
                formData.append('create_user', hasNoKontrak ? '1' : '0');

                // 6. Data Keluarga (table employee_family_members) - JSON Array
                formData.append(
                    'keluarga',
                    JSON.stringify(
                        this.listKeluarga.map((fam) => {
                            // Parse TTL format "Jakarta, 01-01-2000"
                            const ttlParts = fam.ttl
                                ? fam.ttl.split(',')
                                : ['', ''];
                            const tempatLahir = ttlParts[0]?.trim() || '';
                            const tanggalLahir = ttlParts[1]?.trim() || '';

                            return {
                                nama: fam.nama,
                                hubungan: fam.hubungan,
                                tempat_lahir: tempatLahir,
                                tanggal_lahir: tanggalLahir,
                                // no_hp bisa disimpan di field lain jika perlu
                            };
                        }),
                    ),
                );

                // 7. Data Kesehatan (table employee_health_records)
                formData.append(
                    'tinggi_badan',
                    this.formKesehatan.tinggi_badan || '',
                );
                formData.append(
                    'berat_badan',
                    this.formKesehatan.berat_badan || '',
                );
                formData.append(
                    'gol_darah',
                    this.formKesehatan.gol_darah || '',
                );
                formData.append(
                    'buta_warna',
                    this.formKesehatan.buta_warna ? '1' : '0',
                );
                formData.append(
                    'riwayat_penyakit',
                    this.formKesehatan.riwayat_penyakit || '',
                );
                formData.append(
                    'hasil_drug_test',
                    this.formKesehatan.hasil_drug_test || '',
                );
                formData.append(
                    'tanggal_drug_test',
                    this.formKesehatan.tanggal_drug_test || '',
                );
                formData.append('darah', this.formKesehatan.darah || '');
                formData.append('urine', this.formKesehatan.urine || '');
                formData.append('f_hati', this.formKesehatan.f_hati || '');
                formData.append(
                    'gula_darah',
                    this.formKesehatan.gula_darah || '',
                );
                formData.append('ginjal', this.formKesehatan.ginjal || '');
                formData.append('thorax', this.formKesehatan.thorax || '');
                formData.append('tensi', this.formKesehatan.tensi || '');
                formData.append('nadi', this.formKesehatan.nadi || '');
                formData.append('od', this.formKesehatan.od || '');
                formData.append('os', this.formKesehatan.os || '');

                // 8. Upload Files (Dokumen)
                for (const doc of this.dokumenMeta) {
                    if (this.formDokumen[doc.key]) {
                        formData.append(
                            `dokumen_${doc.key}`,
                            this.formDokumen[doc.key],
                        );
                    }
                }

                // 9. Dokumen yang akan dihapus
                if (this.dokumenToDelete.length > 0) {
                    formData.append(
                        'dokumen_to_delete',
                        JSON.stringify(this.dokumenToDelete),
                    );
                }

                // Kirim ke backend
                try {
                    const response = await axios.post(
                        '/employee/store-edit/' + this.employee_id,
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            },
                        },
                    );

                    triggerAlert('success', 'Semua data berhasil disimpan!');

                    router.visit(`/admin/karyawan`);
                } catch (error) {
                    if (axios.isAxiosError(error) && error.response) {
                        // VALIDATION ERROR (Laravel)
                        if (error.response.status === 422) {
                            const data = error.response.data || {};
                            const fieldErrors = data.errors || {};

                            // flatten jadi array string: "nrp: The nrp has already been taken."
                            const errorsArr = Object.entries(
                                fieldErrors,
                            ).flatMap(([field, msgs]) =>
                                (msgs || []).map((m) => `${field}: ${m}`),
                            );

                            const d = {
                                total: errorsArr.length, // total error message
                                success: 0, // karena submit ini gagal
                                failed: errorsArr.length,
                                errors: errorsArr,
                            };

                            const html = `
                                <div>
                                    <strong>Submit gagal karena validasi</strong>

                                    <strong>Detail Error:</strong>
                                    <ul>
                                        ${d.errors.map((e) => `<li>${e}</li>`).join('')}
                                    </ul>
                                 </div>
                            `;

                            triggerAlert('warning', html, 15000, true);
                            return;
                        }

                        // selain 422
                        triggerAlert(
                            'error',
                            error.response.data?.message ||
                                `Gagal menyimpan data (HTTP ${error.response.status})`,
                        );
                        return;
                    }

                    console.error(error);
                    triggerAlert('error', 'Gagal menyimpan data');
                }
            } catch (error) {
                console.error('Error submit all:', error);
                triggerAlert('error', 'Gagal menyimpan data: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        // ================= RESET FORMS =================

        resetFormKaryawan() {
            this.formEmployee = {
                nama: '',
                nrp: '',
                jk: '',
                tempat_lahir: '',
                tanggal_lahir: '',
                perkawinan: '',
                agama: '',
                kewarganegaraan: '',
                status: '1',
                no_wa: '',
                bpjs_tk: '',
                bpjs_kes: '',
                nama_faskes: '',
                email: '',
                no_skck: '',
                masa_berlaku_skck: '',
                jenis_lisensi: '',
                no_lisensi: '',
                masa_berlaku_lisensi: '',
            };
            this.formAlamat = {
                ktp: '',
                phone: '',
                domisili: '',
                kota: '',
            };
        },

        resetFormPendidikan() {
            this.formPendidikan = {
                jenjang: '',
                jurusan: '',
                sekolah: '',
                tahun_lulus: null,
            };
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

        resetFormKeluarga() {
            this.formKeluarga = {
                nama: '',
                hubungan: '',
                ttl: '',
                no_hp: '',
            };
        },

        resetFormKesehatan() {
            this.formKesehatan = {
                tinggi_badan: null,
                berat_badan: null,
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

        resetFormDokumen() {
            this.formDokumen = {};
            this.dokumenToDelete = [];
        },

        // ================= HANDLE DOKUMEN =================

        onFileChange(event, key) {
            const file = event.target.files[0];
            if (file) {
                // Validasi ukuran file (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    triggerAlert('warning', 'Ukuran file maksimal 5MB');
                    event.target.value = '';
                    return;
                }

                // Validasi tipe file
                const allowedTypes = [
                    'application/pdf',
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                ];
                if (!allowedTypes.includes(file.type)) {
                    triggerAlert(
                        'warning',
                        'Format file harus PDF, JPG, atau PNG',
                    );
                    event.target.value = '';
                    return;
                }

                this.formDokumen[key] = file;
            }
        },

        removeSelectedFile(key) {
            delete this.formDokumen[key];
            // Reset input file
            const input = document.querySelector(
                `input[type="file"][data-key="${key}"]`,
            );
            if (input) input.value = '';
        },

        getExistingDocUrl(key) {
            // Implementasi untuk mendapatkan URL dokumen yang sudah ada
            // Misalnya dari data yang di-load saat edit
            return null; // atau return URL jika ada
        },

        markDeleteExisting(key) {
            if (confirm('Hapus dokumen yang sudah tersimpan?')) {
                this.dokumenToDelete.push(key);
            }
        },

        // ================= HANDLE DELETE LIST =================

        hapusPendidikan(index) {
            if (confirm('Hapus data pendidikan ini?')) {
                this.listPendidikan.splice(index, 1);
            }
        },

        hapusPekerjaan(index) {
            if (confirm('Hapus data pekerjaan ini?')) {
                this.listPekerjaan.splice(index, 1);
            }
        },

        hapusKeluarga(index) {
            if (confirm('Hapus data keluarga ini?')) {
                this.listKeluarga.splice(index, 1);
            }
        },

        // ================= HANDLE BATAL =================

        handleBatal() {
            if (confirm('Batalkan semua perubahan dan kembali?')) {
                window.history.back();
                // atau
                // this.$router.push('/employees');
            }
        },
    },
};
</script>
