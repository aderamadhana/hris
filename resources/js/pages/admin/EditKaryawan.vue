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
                                                placeholder="Contoh: Budi Santoso"
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
                                                placeholder="Contoh: 123456"
                                                inputmode="numeric"
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
                                                <option value="" disabled>
                                                    Pilih jenis kelamin
                                                </option>
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
                                                placeholder="Contoh: Jakarta"
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
                                                placeholder="Pilih tanggal lahir"
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
                                                <option value="" disabled>
                                                    Pilih status perkawinan
                                                </option>
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
                                                <option value="" disabled>
                                                    Pilih agama
                                                </option>
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
                                                placeholder="Contoh: Indonesia"
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
                                            <label class="field-label">
                                                No. KTP
                                                <span class="required">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                v-model="formAlamat.ktp"
                                                class="form-input"
                                                placeholder="Contoh: 3174xxxxxxxxxxxx (16 digit)"
                                                inputmode="numeric"
                                                maxlength="16"
                                                autocomplete="off"
                                                required
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label class="field-label">
                                                No. KK
                                            </label>
                                            <input
                                                type="text"
                                                v-model="formEmployee.kk"
                                                class="form-input"
                                                placeholder="Contoh: 3174xxxxxxxxxxxx (16 digit)"
                                                inputmode="numeric"
                                                maxlength="16"
                                                autocomplete="off"
                                                required
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
                                                placeholder="Contoh: 08xxxxxxxxxx / +62xxxxxxxxxx"
                                                inputmode="tel"
                                                autocomplete="tel"
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
                                                placeholder="Contoh: nama@domain.com"
                                                autocomplete="email"
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
                                                placeholder="Contoh: Jl. Sudirman No. 10, RT/RW, Kelurahan"
                                                autocomplete="street-address"
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
                                                placeholder="Contoh: Jakarta Selatan"
                                                autocomplete="address-level2"
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
                                                placeholder="Nomor BPJS TK (jika ada)"
                                                inputmode="numeric"
                                                autocomplete="off"
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
                                                placeholder="Nomor BPJS Kesehatan (jika ada)"
                                                inputmode="numeric"
                                                autocomplete="off"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Jenis Kepesertaan BPJS
                                                TK</label
                                            >
                                            <select
                                                v-model="
                                                    formEmployee.jenis_bpjs_tk
                                                "
                                                class="form-input"
                                            >
                                                <option value="" disabled>
                                                    Pilih jenis kepesertaan
                                                </option>
                                                <option value="PU">
                                                    Penerima Upah (PU)
                                                </option>
                                                <option value="BPU">
                                                    Bukan Penerima Upah (BPU)
                                                </option>
                                                <option value="JAKON">
                                                    Jasa Konstruksi
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Lokasi Kepesertaan BPJS
                                                KS</label
                                            >
                                            <input
                                                type="text"
                                                v-model="
                                                    formEmployee.nama_faskes
                                                "
                                                class="form-input"
                                                placeholder="Contoh: Jakarta Selatan"
                                                autocomplete="off"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >Status Kepesertaan BPJS
                                                KS</label
                                            >
                                            <select
                                                v-model="
                                                    formEmployee.status_bpjs_ks
                                                "
                                                class="form-input"
                                            >
                                                <option value="" disabled>
                                                    Pilih status kepesertaan
                                                </option>
                                                <option value="aktif">
                                                    Aktif
                                                </option>
                                                <option value="nonaktif">
                                                    Nonaktif
                                                </option>
                                                <option value="belum_terdaftar">
                                                    Belum Terdaftar
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label"
                                                >No SKCK</label
                                            >
                                            <input
                                                type="text"
                                                v-model="formEmployee.no_skck"
                                                class="form-input"
                                                placeholder="Contoh: SKCK/123/ABC/2025"
                                                autocomplete="off"
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
                                                    Pilih jenis lisensi
                                                </option>
                                                <option value="SIM A / B1 / B2">
                                                    SIM A / B1 / B2
                                                </option>
                                                <option value="Forklift I / II">
                                                    Forklift I / II
                                                </option>
                                                <option value="AK3U">
                                                    AK3U
                                                </option>
                                                <option value="Loader">
                                                    Loader
                                                </option>
                                                <option value="Balakar">
                                                    Balakar
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
                                                placeholder="Nomor lisensi / sertifikat"
                                                autocomplete="off"
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
                                                Pilih jenjang pendidikan
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
                                            placeholder="Contoh: Teknik Informatika"
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
                                            placeholder="Contoh: SMK Negeri 1 Jakarta"
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
                                            placeholder="Contoh: 2020"
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
                                            placeholder="Contoh: Operational"
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
                                            placeholder="Contoh: PT Maju Jaya Sejahtera"
                                        />
                                    </div>

                                    <div class="form-group">
                                        <label class="field-label"
                                            >Divisi / Departemen</label
                                        >
                                        <input
                                            type="text"
                                            v-model="formPekerjaan.bagian"
                                            class="form-input"
                                            placeholder="Contoh: Operasional / HR / Finance"
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
                                            placeholder="Contoh: PKWT/001/2025"
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
                                                Pilih jenis kontrak
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
                                            placeholder="Contoh: Full-time / Part-time"
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
                                            placeholder="Contoh: WFO / WFH / Hybrid"
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
                                            placeholder="Contoh: Senin–Jumat"
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
                                                Pilih status kontrak
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
                                                    – {{ job.bagian || '-' }}
                                                </div>
                                                <div class="job-meta">
                                                    No. Kontrak:
                                                    {{ job.no_kontrak || '-' }}
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
                                            placeholder="Contoh: Siti Aminah"
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
                                                Pilih hubungan keluarga
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
          <label class="field-label">No. HP</label>
          <input type="tel" v-model="formKeluarga.no_hp" class="form-input" placeholder="Contoh: 08xxxxxxxxxx" />
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
                                                placeholder="Contoh: 170"
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
                                                placeholder="Contoh: 65"
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
                                                placeholder="Contoh: A / B / AB / O"
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
                                        <div class="form-group">
                                            <label class="field-label"
                                                >Tanggal Pemeriksaan Medical
                                                Check Up
                                            </label>
                                            <input
                                                type="date"
                                                v-model="
                                                    formKesehatan.tanggal_mcu
                                                "
                                                class="form-input"
                                            />
                                        </div>
                                        <div
                                            class="form-group"
                                            style="grid-column: 1 / -1"
                                        >
                                            <label class="field-label"
                                                >Kesimpulan Hasil Medical Check
                                                Up</label
                                            >
                                            <textarea
                                                v-model="
                                                    formKesehatan.kesimpulan_hasil_mcu
                                                "
                                                class="form-input"
                                                placeholder="Contoh: Asma sejak kecil, alergi obat tertentu, dll."
                                            ></textarea>
                                        </div>
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
                                                placeholder="Contoh: Asma sejak kecil, alergi obat tertentu, dll."
                                            ></textarea>
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
                                                placeholder="Contoh: Normal"
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
                                                placeholder="Contoh: Normal"
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
                                                placeholder="Contoh: Normal"
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
                                                placeholder="Contoh: Normal"
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
                                                placeholder="Contoh: Normal"
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
                                                placeholder="Contoh: Normal"
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
                                                placeholder="Contoh: 120/80"
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
                                                placeholder="Contoh: 80 bpm"
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
                                                placeholder="Contoh: 6/6"
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
                                                placeholder="Contoh: 6/6"
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
                jenis_bpjs_tk: '',
                nama_faskes: '',
                status_bpjs_ks: '',
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
                tanggal_mcu: '',
                kesimpulan_hasil_mcu: '',
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
                lisensi: false,
                form_bpjs_tk: false,
                form_bpjs_kes: false,
                paklaring: false,
                ijazah_terakhir: false,
                skck: false,
            },

            dokumenMeta: [
                { key: 'pas_foto', label: 'Pas Foto' },
                { key: 'ktp', label: 'KTP' },
                { key: 'kk', label: 'Kartu Keluarga' },
                { key: 'lisensi', label: 'Lisensi' },
                { key: 'form_bpjs_tk', label: 'Formulir BPJS TK' },
                { key: 'form_bpjs_kes', label: 'Formulir BPJS Kesehatan' },
                {
                    key: 'paklaring',
                    label: 'Surat Pengalaman Kerja / Paklaring',
                },
                {
                    key: 'ijazah_terakhir',
                    label: 'Ijazah Terakhir',
                },
                {
                    key: 'skck',
                    label: 'SKCK',
                },
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
            await axios
                .get(`/employee/${this.employee_id}`)
                .then(({ data }) => {
                    /* ===============================
             | FORM EMPLOYEE
             ===============================*/
                    Object.assign(this.formEmployee, {
                        nama: data.nama ?? '',
                        nrp: data.nrp ?? '',
                        jk: data.jenis_kelamin ?? '',
                        kk: data.no_kk ?? '',
                        tempat_lahir: data.tempat_lahir ?? '',
                        tanggal_lahir: data.tanggal_lahir ?? '',
                        perkawinan: data.status_perkawinan ?? '',
                        agama: data.agama ?? '',
                        kewarganegaraan: data.kewarganegaraan ?? '',
                        status: data.status_active ?? '1',

                        // Data dari employee_personals table
                        no_wa: data.no_wa ?? '',
                        bpjs_tk: data.bpjs_tk ?? '',
                        bpjs_kes: data.bpjs_kes ?? '',
                        jenis_bpjs_tk: data.jenis_bpjs_tk ?? '',
                        nama_faskes: data.nama_faskes ?? '',
                        status_bpjs_ks: data.status_bpjs_ks ?? '',
                        email: data.email ?? '',
                        no_skck: data.no_skck ?? '',
                        masa_berlaku_skck: data.masa_berlaku_skck ?? '',
                        jenis_lisensi: data.jenis_lisensi ?? '',
                        no_lisensi: data.no_lisensi ?? '',
                        masa_berlaku_lisensi: data.masa_berlaku_lisensi ?? '',
                    });

                    /* ===============================
             | FORM ALAMAT
             ===============================*/
                    Object.assign(this.formAlamat, {
                        ktp: data.no_ktp ?? '',
                        phone: data.no_wa ?? '',
                        domisili: data.alamat_lengkap_domisili ?? '',
                        kota: data.kota_domisili ?? '',
                    });

                    /* ===============================
             | PENDIDIKAN
             ===============================*/
                    this.listPendidikan = (data.educations ?? []).map((p) => ({
                        jenjang: p.jenjang ?? '',
                        jurusan: p.jurusan ?? '',
                        sekolah: p.institusi ?? p.sekolah_asal ?? '',
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
                        status_kontrak: j.status ?? j.keterangan_status ?? '',
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
                            tanggal_mcu: data.health.tanggal_mcu ?? '',
                            kesimpulan_hasil_mcu:
                                data.health.kesimpulan_hasil_mcu ?? '',
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
                            lisensi: !!data.documents.dokumen_lisensi,
                            form_bpjs_tk:
                                !!data.documents.dokumen_bpjs_ketenagakerjaan,
                            form_bpjs_kes:
                                !!data.documents
                                    .dokumen_formulir_bpjs_kesehatan,
                            paklaring:
                                !!data.documents.dokumen_surat_pengalaman_kerja,
                            ijazah_terakhir:
                                !!data.documents.dokumen_ijazah_terakhir,
                            skck: !!data.documents.dokumen_skck,
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
                formData.append('kk', this.formEmployee.kk);
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
                    'jenis_bpjs_tk',
                    this.formEmployee.jenis_bpjs_tk || '',
                );
                formData.append(
                    'nama_faskes',
                    this.formEmployee.nama_faskes || '',
                );
                formData.append(
                    'status_bpjs_ks',
                    this.formEmployee.status_bpjs_ks || '',
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
                    'alamat_lengkap_domisili',
                    this.formAlamat.domisili || '',
                );
                formData.append('kota_domisili', this.formAlamat.kota || '');
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
                    'tanggal_mcu',
                    this.formKesehatan.tanggal_mcu || '',
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

                formData.append(
                    'tanggal_mcu',
                    this.formKesehatan.tanggal_mcu || '',
                );
                formData.append(
                    'kesimpulan_hasil_mcu',
                    this.formKesehatan.kesimpulan_hasil_mcu || '',
                );

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
                kk: '',
                tempat_lahir: '',
                tanggal_lahir: '',
                perkawinan: '',
                agama: '',
                kewarganegaraan: '',
                status: '1',
                no_wa: '',
                bpjs_tk: '',
                bpjs_kes: '',
                jenis_bpjs_tk: '',
                nama_faskes: '',
                status_bpjs_ks: '',
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
                tanggal_mcu: '',
                kesimpulan_hasil_mcu: '',
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
