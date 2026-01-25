<template>
    <div class="apply-root">
        <!-- HEADER (tetap style landing) -->
        <header class="header-landing">
            <div class="nav container">
                <div class="brand">
                    <img
                        :src="companyLogo"
                        alt="PT Mitra Wira Mas"
                        class="brand__logo"
                    />
                    <div class="brand__text">
                        <strong class="brand-title">Apply Form</strong>
                        <small class="brand-subtitle">
                            {{ loker?.judul || 'Form Lamaran' }}
                        </small>
                    </div>
                </div>

                <button
                    class="btn btn--outline btn--sm"
                    type="button"
                    @click="goBack"
                >
                    ← Kembali
                </button>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="page">
            <div class="container">
                <div class="apply-shell">
                    <div class="apply-card">
                        <div class="alerts-wrap" v-if="alert.show">
                            <div
                                class="alert"
                                :class="
                                    alert.type === 'success'
                                        ? 'alert-success'
                                        : 'alert-error'
                                "
                            >
                                <div class="alert-left">
                                    <span class="alert-icon">
                                        {{
                                            alert.type === 'success' ? '✓' : '!'
                                        }}
                                    </span>

                                    <div class="alert-text">
                                        <div class="alert-title">
                                            {{
                                                alert.type === 'success'
                                                    ? 'Berhasil'
                                                    : 'Gagal'
                                            }}
                                        </div>
                                        <div class="alert-msg">
                                            {{ alert.message }}
                                        </div>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="alert-close"
                                    @click="closeAlert"
                                >
                                    ×
                                </button>
                            </div>
                        </div>
                        <div class="apply-head">
                            <div>
                                <h1 class="apply-title">Form Lamaran</h1>
                                <p class="apply-sub">
                                    Posisi:
                                    <strong>{{ loker?.judul || '-' }}</strong>
                                </p>
                            </div>

                            <div
                                class="apply-badge"
                                v-if="loker?.tipe_pekerjaan"
                            >
                                {{ loker?.tipe_pekerjaan }}
                            </div>
                        </div>

                        <div v-if="!loker?.id" class="warn-top">
                            <div class="warn-title">
                                Data loker tidak ditemukan
                            </div>
                            <div class="warn-desc">
                                Silakan kembali dan pilih loker lagi.
                            </div>
                        </div>

                        <!-- FORM (tanpa tabs) -->
                        <form @submit.prevent="submitAll">
                            <!-- PROFIL PELAMAR (tanpa NRP) -->
                            <details class="acc" open>
                                <summary class="acc__sum">
                                    <div class="acc__left">
                                        <span class="acc__dot"></span>
                                        <span class="acc__title"
                                            >Profil Pelamar</span
                                        >
                                    </div>
                                    <span class="acc__chev">⌄</span>
                                </summary>

                                <div class="acc__body">
                                    <div class="two grid">
                                        <div class="field">
                                            <label class="label">
                                                Nama Lengkap
                                                <span class="req">*</span>
                                            </label>
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="formEmployee.nama"
                                                placeholder="Contoh: Budi Santoso"
                                                required
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label">
                                                Jenis Kelamin
                                                <span class="req">*</span>
                                            </label>
                                            <select
                                                class="input"
                                                v-model="formEmployee.jk"
                                                required
                                            >
                                                <option value="" disabled>
                                                    Pilih jenis kelamin
                                                </option>
                                                <option value="L">
                                                    Laki-laki
                                                </option>
                                                <option value="P">
                                                    Perempuan
                                                </option>
                                            </select>
                                        </div>

                                        <div class="field">
                                            <label class="label"
                                                >Tempat Lahir</label
                                            >
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="
                                                    formEmployee.tempat_lahir
                                                "
                                                placeholder="Contoh: Jakarta"
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label"
                                                >Tanggal Lahir</label
                                            >
                                            <input
                                                class="input"
                                                type="date"
                                                v-model="
                                                    formEmployee.tanggal_lahir
                                                "
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label"
                                                >Status Perkawinan</label
                                            >
                                            <select
                                                class="input"
                                                v-model="
                                                    formEmployee.perkawinan
                                                "
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

                                        <div class="field">
                                            <label class="label">Agama</label>
                                            <select
                                                class="input"
                                                v-model="formEmployee.agama"
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

                                        <div class="field">
                                            <label class="label"
                                                >Kewarganegaraan</label
                                            >
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="
                                                    formEmployee.kewarganegaraan
                                                "
                                                placeholder="Contoh: Indonesia"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </details>

                            <!-- KONTAK & IDENTITAS -->
                            <details class="acc" open>
                                <summary class="acc__sum">
                                    <div class="acc__left">
                                        <span class="acc__dot"></span>
                                        <span class="acc__title"
                                            >Kontak & Identitas</span
                                        >
                                    </div>
                                    <span class="acc__chev">⌄</span>
                                </summary>

                                <div class="acc__body">
                                    <div class="two grid">
                                        <div class="field">
                                            <label class="label">
                                                No. KTP
                                                <span class="req">*</span>
                                            </label>
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="formAlamat.ktp"
                                                placeholder="16 digit"
                                                inputmode="numeric"
                                                maxlength="16"
                                                required
                                                @input="onlyNumberKtp"
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label">No. KK</label>
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="formEmployee.kk"
                                                placeholder="16 digit"
                                                inputmode="numeric"
                                                maxlength="16"
                                                @input="onlyNumberKk"
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label"
                                                >No. WhatsApp</label
                                            >
                                            <input
                                                class="input"
                                                type="tel"
                                                v-model="formEmployee.no_wa"
                                                placeholder="08xxxx / +62xxxx"
                                                inputmode="tel"
                                                autocomplete="tel"
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label">Email</label>
                                            <input
                                                class="input"
                                                type="email"
                                                v-model="formEmployee.email"
                                                placeholder="nama@domain.com"
                                                autocomplete="email"
                                            />
                                        </div>

                                        <div
                                            class="field"
                                            style="grid-column: 1 / -1"
                                        >
                                            <label class="label"
                                                >Domisili</label
                                            >
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="formAlamat.domisili"
                                                placeholder="Alamat lengkap"
                                                autocomplete="street-address"
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label">Kota</label>
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="formAlamat.kota"
                                                placeholder="Contoh: Jakarta Selatan"
                                                autocomplete="address-level2"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </details>

                            <!-- RIWAYAT PENDIDIKAN -->
                            <details class="acc">
                                <summary class="acc__sum">
                                    <div class="acc__left">
                                        <span class="acc__dot"></span>
                                        <span class="acc__title"
                                            >Riwayat Pendidikan</span
                                        >
                                    </div>
                                    <span class="acc__chev">⌄</span>
                                </summary>

                                <div class="acc__body">
                                    <div class="two grid">
                                        <div class="field">
                                            <label class="label">Jenjang</label>
                                            <select
                                                class="input"
                                                v-model="formPendidikan.jenjang"
                                            >
                                                <option disabled value="">
                                                    Pilih jenjang
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

                                        <div class="field">
                                            <label class="label">Jurusan</label>
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="formPendidikan.jurusan"
                                                placeholder="Contoh: Teknik Informatika"
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label">Sekolah</label>
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="formPendidikan.sekolah"
                                                placeholder="Contoh: SMK Negeri 1 Jakarta"
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label"
                                                >Tahun Lulus</label
                                            >
                                            <input
                                                class="input"
                                                type="number"
                                                v-model="
                                                    formPendidikan.tahun_lulus
                                                "
                                                placeholder="Contoh: 2020"
                                                min="1900"
                                                max="2100"
                                            />
                                        </div>
                                    </div>

                                    <div class="row-actions">
                                        <button
                                            class="btn btn--dark btn--sm"
                                            type="button"
                                            @click="resetFormPendidikan"
                                            v-if="editPendidikanIndex === null"
                                        >
                                            Reset
                                        </button>

                                        <button
                                            class="btn btn--dark btn--sm"
                                            type="button"
                                            @click="batalEditPendidikan"
                                            v-else
                                        >
                                            Batal Edit
                                        </button>

                                        <button
                                            class="btn btn--gold btn--sm"
                                            type="button"
                                            @click="submitPendidikan"
                                        >
                                            {{
                                                editPendidikanIndex !== null
                                                    ? 'Update Pendidikan'
                                                    : 'Tambah Pendidikan'
                                            }}
                                        </button>
                                    </div>

                                    <div
                                        class="list"
                                        v-if="listPendidikan.length"
                                    >
                                        <div
                                            class="item"
                                            v-for="(edu, i) in listPendidikan"
                                            :key="`edu-${i}`"
                                        >
                                            <div class="item-main">
                                                <div class="item-title">
                                                    {{ edu.jenjang || '-' }}
                                                    <span class="muted">{{
                                                        edu.jurusan || ''
                                                    }}</span>
                                                </div>
                                                <div class="item-sub">
                                                    {{ edu.sekolah || '-' }} •
                                                    Lulus
                                                    {{ edu.tahun_lulus || '-' }}
                                                </div>
                                            </div>

                                            <div class="item-actions">
                                                <button
                                                    class="btn-ghost"
                                                    type="button"
                                                    @click="editPendidikan(i)"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    class="btn-danger-ghost"
                                                    type="button"
                                                    @click="hapusPendidikan(i)"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else class="empty">
                                        Data pendidikan belum tersedia
                                    </div>
                                </div>
                            </details>

                            <!-- DATA KELUARGA -->
                            <details class="acc">
                                <summary class="acc__sum">
                                    <div class="acc__left">
                                        <span class="acc__dot"></span>
                                        <span class="acc__title"
                                            >Data Keluarga</span
                                        >
                                    </div>
                                    <span class="acc__chev">⌄</span>
                                </summary>

                                <div class="acc__body">
                                    <div class="two grid">
                                        <div class="field">
                                            <label class="label">Nama</label>
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="formKeluarga.nama"
                                                placeholder="Contoh: Siti Aminah"
                                            />
                                        </div>

                                        <div class="field">
                                            <label class="label"
                                                >Hubungan</label
                                            >
                                            <select
                                                class="input"
                                                v-model="formKeluarga.hubungan"
                                            >
                                                <option disabled value="">
                                                    Pilih hubungan
                                                </option>
                                                <option value="Ayah">
                                                    Ayah
                                                </option>
                                                <option value="Ibu">Ibu</option>
                                                <option value="Suami">
                                                    Suami
                                                </option>
                                                <option value="Istri">
                                                    Istri
                                                </option>
                                                <option value="Anak">
                                                    Anak
                                                </option>
                                                <option value="Saudara Kandung">
                                                    Saudara Kandung
                                                </option>
                                                <option value="Kakak">
                                                    Kakak
                                                </option>
                                                <option value="Adik">
                                                    Adik
                                                </option>
                                                <option value="Wali">
                                                    Wali
                                                </option>
                                                <option value="Lainnya">
                                                    Lainnya
                                                </option>
                                            </select>
                                        </div>

                                        <div
                                            class="field"
                                            style="grid-column: 1 / -1"
                                        >
                                            <label class="label">TTL</label>
                                            <input
                                                class="input"
                                                type="text"
                                                v-model="formKeluarga.ttl"
                                                placeholder="Contoh: Jakarta, 01-01-2000"
                                            />
                                        </div>
                                    </div>

                                    <div class="row-actions">
                                        <button
                                            class="btn btn--dark btn--sm"
                                            type="button"
                                            @click="resetFormKeluarga"
                                            v-if="editKeluargaIndex === null"
                                        >
                                            Reset
                                        </button>

                                        <button
                                            class="btn btn--dark btn--sm"
                                            type="button"
                                            @click="batalEditKeluarga"
                                            v-else
                                        >
                                            Batal Edit
                                        </button>

                                        <button
                                            class="btn btn--gold btn--sm"
                                            type="button"
                                            @click="submitKeluarga"
                                        >
                                            {{
                                                editKeluargaIndex !== null
                                                    ? 'Update Keluarga'
                                                    : 'Tambah Keluarga'
                                            }}
                                        </button>
                                    </div>

                                    <div
                                        class="list"
                                        v-if="listKeluarga.length"
                                    >
                                        <div
                                            class="item"
                                            v-for="(f, i) in listKeluarga"
                                            :key="`fam-${i}`"
                                        >
                                            <div class="item-main">
                                                <div class="item-title">
                                                    {{ f.nama || '-' }}
                                                </div>
                                                <div class="item-sub">
                                                    {{ f.hubungan || '-' }} •
                                                    TTL: {{ f.ttl || '-' }}
                                                </div>
                                            </div>

                                            <div class="item-actions">
                                                <button
                                                    class="btn-ghost"
                                                    type="button"
                                                    @click="editKeluarga(i)"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    class="btn-danger-ghost"
                                                    type="button"
                                                    @click="hapusKeluarga(i)"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else class="empty">
                                        Data keluarga belum tersedia
                                    </div>
                                </div>
                            </details>

                            <!-- UPLOAD DOKUMEN (sesuai data sebelumnya: KTP, IJAZAH, SERTIFIKAT) -->
                            <details class="acc" open>
                                <summary class="acc__sum">
                                    <div class="acc__left">
                                        <span class="acc__dot"></span>
                                        <span class="acc__title"
                                            >Upload Dokumen</span
                                        >
                                    </div>
                                    <span class="acc__chev">⌄</span>
                                </summary>

                                <div class="acc__body">
                                    <div class="two grid">
                                        <div
                                            v-for="doc in dokumenMeta"
                                            :key="doc.key"
                                            class="field doc-field"
                                        >
                                            <label class="label">
                                                {{ doc.label }}
                                                <span
                                                    v-if="doc.required"
                                                    class="req"
                                                    >*</span
                                                >
                                            </label>

                                            <input
                                                class="input file"
                                                type="file"
                                                :accept="doc.accept"
                                                @change="
                                                    onFileChange(
                                                        $event,
                                                        doc.key,
                                                    )
                                                "
                                                :required="doc.required"
                                            />

                                            <div class="hint-row">
                                                <span class="hint">
                                                    {{ doc.hint }}
                                                </span>

                                                <span
                                                    v-if="formDokumen[doc.key]"
                                                    class="file-chip"
                                                    :title="
                                                        formDokumen[doc.key]
                                                            ?.name
                                                    "
                                                >
                                                    {{
                                                        shortFileName(
                                                            formDokumen[doc.key]
                                                                ?.name,
                                                        )
                                                    }}
                                                    <button
                                                        class="chip-x"
                                                        type="button"
                                                        @click="
                                                            removeSelectedFile(
                                                                doc.key,
                                                            )
                                                        "
                                                    >
                                                        ×
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </details>

                            <!-- SUBMIT -->
                            <div class="final-actions">
                                <button
                                    class="btn btn--gold full"
                                    type="submit"
                                    :disabled="loading || !loker?.id"
                                >
                                    {{
                                        loading
                                            ? 'Mengirim...'
                                            : 'Kirim Lamaran'
                                    }}
                                </button>

                                <div v-if="!loker?.id" class="warn">
                                    Data loker tidak ditemukan. Silakan kembali
                                    dan pilih loker lagi.
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- side note (optional) -->
                    <div class="apply-note">
                        <div class="note-card">
                            <div class="note-title">Catatan</div>
                            <ul class="note-list">
                                <li>Pastikan data sesuai KTP.</li>
                                <li>Dokumen disarankan format <b>PDF</b>.</li>
                                <li>Sertifikat bersifat opsional.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    name: 'ApplyFormPage',
    props: {
        loker: Object,
    },

    data() {
        return {
            companyLogo: '/assets/images/logo_baru.png',
            loading: false,

            // ================================
            // FORM DATA
            // ================================
            formEmployee: {
                nama: '',
                jk: '',
                tempat_lahir: '',
                tanggal_lahir: '',
                perkawinan: '',
                agama: '',
                kewarganegaraan: '',

                kk: '',
                no_wa: '',
                email: '',
            },

            formAlamat: {
                ktp: '',
                domisili: '',
                kota: '',
            },

            // Pendidikan (list)
            formPendidikan: {
                jenjang: '',
                jurusan: '',
                sekolah: '',
                tahun_lulus: '',
            },
            listPendidikan: [],
            editPendidikanIndex: null,

            // Keluarga (list)
            formKeluarga: {
                nama: '',
                hubungan: '',
                ttl: '',
            },
            listKeluarga: [],
            editKeluargaIndex: null,

            // Dokumen (sesuai sebelumnya)
            dokumenMeta: [
                {
                    key: 'pas_foto',
                    label: 'Pas Foto (JPG/PNG)',
                    hint: 'Format: JPG/PNG',
                    accept: '.jpg,.jpeg,.png',
                    required: false,
                },
                {
                    key: 'dokumen_kk',
                    label: 'Kartu Keluarga (PDF/JPG/PNG)',
                    hint: 'Format: PDF/JPG/PNG',
                    accept: '.pdf,.jpg,.jpeg,.png',
                    required: false,
                },
                {
                    key: 'dokumen_surat_pengalaman_kerja',
                    label: 'Surat Pengalaman Kerja (PDF/JPG/PNG)',
                    hint: 'Format: PDF/JPG/PNG',
                    accept: '.pdf,.jpg,.jpeg,.png',
                    required: false,
                },
                {
                    key: 'dokumen_ktp',
                    label: 'KTP (PDF/JPG/PNG)',
                    hint: 'Format: PDF/JPG/PNG',
                    accept: '.pdf,.jpg,.jpeg,.png',
                    required: true, // kalau mau wajib
                },
                {
                    key: 'dokumen_bpjs_ketenagakerjaan',
                    label: 'BPJS Ketenagakerjaan (PDF/JPG/PNG)',
                    hint: 'Format: PDF/JPG/PNG',
                    accept: '.pdf,.jpg,.jpeg,.png',
                    required: false,
                },
                {
                    key: 'dokumen_sio_forklift',
                    label: 'SIO Forklift (PDF/JPG/PNG)',
                    hint: 'Format: PDF/JPG/PNG',
                    accept: '.pdf,.jpg,.jpeg,.png',
                    required: false,
                },
                {
                    key: 'dokumen_formulir_bpjs_kesehatan',
                    label: 'Formulir BPJS Kesehatan (PDF/JPG/PNG)',
                    hint: 'Format: PDF/JPG/PNG',
                    accept: '.pdf,.jpg,.jpeg,.png',
                    required: false,
                },
                {
                    key: 'dokumen_ijazah_terakhir',
                    label: 'Ijazah Terakhir (PDF/JPG/PNG)',
                    hint: 'Format: PDF/JPG/PNG',
                    accept: '.pdf,.jpg,.jpeg,.png',
                    required: true, // kalau mau wajib
                },
                {
                    key: 'dokumen_skck',
                    label: 'SKCK (PDF/JPG/PNG)',
                    hint: 'Format: PDF/JPG/PNG',
                    accept: '.pdf,.jpg,.jpeg,.png',
                    required: false,
                },
                {
                    key: 'dokumen_lisensi',
                    label: 'Lisensi / Sertifikat (PDF/JPG/PNG)',
                    hint: 'Opsional',
                    accept: '.pdf,.jpg,.jpeg,.png',
                    required: false,
                },
            ],

            formDokumen: {
                pas_foto: null,
                dokumen_kk: null,
                dokumen_surat_pengalaman_kerja: null,
                dokumen_ktp: null,
                dokumen_bpjs_ketenagakerjaan: null,
                dokumen_sio_forklift: null,
                dokumen_formulir_bpjs_kesehatan: null,
                dokumen_ijazah_terakhir: null,
                dokumen_skck: null,
                dokumen_lisensi: null,
            },

            alert: {
                show: false,
                type: 'success', // success | error
                message: '',
            },
        };
    },

    methods: {
        showAlert(type, message) {
            this.alert.show = true;
            this.alert.type = type;
            this.alert.message = message;

            // auto scroll ke atas biar kelihatan
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        closeAlert() {
            this.alert.show = false;
            this.alert.message = '';
        },
        goBack() {
            if (window.history.length > 1) {
                window.history.back();
                return;
            }
            router.visit('/landing');
        },

        // ======== digit-only helpers
        onlyNumberKtp() {
            this.formAlamat.ktp = String(this.formAlamat.ktp || '')
                .replace(/\D/g, '')
                .slice(0, 16);
        },
        onlyNumberKk() {
            this.formEmployee.kk = String(this.formEmployee.kk || '')
                .replace(/\D/g, '')
                .slice(0, 16);
        },

        // ======== pendidikan CRUD
        resetFormPendidikan() {
            this.formPendidikan = {
                jenjang: '',
                jurusan: '',
                sekolah: '',
                tahun_lulus: '',
            };
            this.editPendidikanIndex = null;
        },
        submitPendidikan() {
            const payload = { ...this.formPendidikan };

            if (this.editPendidikanIndex !== null) {
                this.listPendidikan.splice(
                    this.editPendidikanIndex,
                    1,
                    payload,
                );
                this.editPendidikanIndex = null;
            } else {
                this.listPendidikan.push(payload);
            }

            this.resetFormPendidikan();
        },
        editPendidikan(i) {
            this.formPendidikan = { ...this.listPendidikan[i] };
            this.editPendidikanIndex = i;
        },
        hapusPendidikan(i) {
            this.listPendidikan.splice(i, 1);
            if (this.editPendidikanIndex === i) this.resetFormPendidikan();
        },
        batalEditPendidikan() {
            this.resetFormPendidikan();
        },

        // ======== keluarga CRUD
        resetFormKeluarga() {
            this.formKeluarga = { nama: '', hubungan: '', ttl: '' };
            this.editKeluargaIndex = null;
        },
        submitKeluarga() {
            const payload = { ...this.formKeluarga };

            if (this.editKeluargaIndex !== null) {
                this.listKeluarga.splice(this.editKeluargaIndex, 1, payload);
                this.editKeluargaIndex = null;
            } else {
                this.listKeluarga.push(payload);
            }

            this.resetFormKeluarga();
        },
        editKeluarga(i) {
            this.formKeluarga = { ...this.listKeluarga[i] };
            this.editKeluargaIndex = i;
        },
        hapusKeluarga(i) {
            this.listKeluarga.splice(i, 1);
            if (this.editKeluargaIndex === i) this.resetFormKeluarga();
        },
        batalEditKeluarga() {
            this.resetFormKeluarga();
        },

        // ======== dokumen
        onFileChange(e, key) {
            const f = e?.target?.files?.[0] || null;
            this.formDokumen[key] = f;
        },
        removeSelectedFile(key) {
            this.formDokumen[key] = null;
        },
        shortFileName(name) {
            if (!name) return '';
            if (name.length <= 22) return name;
            const ext = name.includes('.') ? '.' + name.split('.').pop() : '';
            return name.slice(0, 14) + '…' + ext;
        },

        // ======== SUBMIT ALL
        async submitAll() {
            if (!this.loker?.id) return;
            this.closeAlert();
            this.loading = true;

            const data = new FormData();

            // Data employee (gabungkan dari formEmployee)
            const employeePayload = {
                nama: this.formEmployee.nama,
                jk: this.formEmployee.jk,
                tempat_lahir: this.formEmployee.tempat_lahir,
                tanggal_lahir: this.formEmployee.tanggal_lahir,
                perkawinan: this.formEmployee.perkawinan,
                agama: this.formEmployee.agama,
                kewarganegaraan: this.formEmployee.kewarganegaraan,
                kk: this.formEmployee.kk,
                no_wa: this.formEmployee.no_wa,
                email: this.formEmployee.email,
            };

            // Data alamat
            const alamatPayload = {
                ktp: this.formAlamat.ktp,
                alamat_ktp: this.formAlamat.ktp,
                domisili: this.formAlamat.domisili,
                kota: this.formAlamat.kota,
            };

            // Append sebagai JSON string
            data.append('employee', JSON.stringify(employeePayload));
            data.append('alamat', JSON.stringify(alamatPayload));
            data.append('pendidikan', JSON.stringify(this.listPendidikan));
            data.append('keluarga', JSON.stringify(this.listKeluarga));
            data.append('pekerjaan', JSON.stringify([]));

            // Data kesehatan
            data.append(
                'kesehatan',
                JSON.stringify({
                    tinggi_badan: '',
                    berat_badan: '',
                    gol_darah: '',
                    buta_warna: false,
                    tanggal_mcu: '',
                    kesimpulan_hasil_mcu: '',
                    riwayat_penyakit: '',
                }),
            );

            // Append semua file dokumen
            Object.entries(this.formDokumen).forEach(([key, file]) => {
                if (file) {
                    data.append(key, file);
                }
            });

            try {
                const res = await axios.post(`/apply/${this.loker.id}`, data, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                if (res.data?.success) {
                    this.showAlert(
                        'success',
                        res.data.message || 'Lamaran berhasil dikirim!',
                    );
                    // optional: reset form
                    // this.resetAllForm();
                } else {
                    this.showAlert(
                        'error',
                        res.data?.message || 'Terjadi kesalahan.',
                    );
                }
            } catch (err) {
                if (err.response?.status === 422) {
                    const errors = err.response.data?.errors || {};
                    const firstKey = Object.keys(errors)[0];
                    const firstMsg = firstKey
                        ? errors[firstKey][0]
                        : 'Validasi gagal.';
                    this.showAlert('error', firstMsg);
                } else {
                    // error 500 / network / dll
                    const msg =
                        err.response?.data?.message ||
                        err.message ||
                        'Gagal mengirim lamaran.';
                    this.showAlert('error', msg);
                }
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style>
/* =========================
   BASE (biar refresh tetap rapih)
========================= */
:root {
    --navy: #071a33;
    --navy2: #051225;
    --gold: #d6a33a;
    --gold2: #b98a2e;

    --text: #0f172a;
    --muted: #5b6472;
    --bg: #f5f7fb;
    --card: #ffffff;

    --shadow: 0 14px 38px rgba(10, 20, 40, 0.12);
    --radius: 18px;
}

* {
    box-sizing: border-box;
}
html,
body {
    margin: 0;
    padding: 0;
}
body {
    font-family:
        'Inter',
        system-ui,
        -apple-system,
        Segoe UI,
        Roboto,
        Arial,
        sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.5;
}

.container {
    width: 100%;
    padding-left: 10%;
    padding-right: 10%;
}

/* NAVBAR */
.header-landing {
    position: sticky;
    top: 0;
    z-index: 99;
    background: linear-gradient(
        180deg,
        rgba(7, 26, 51, 0.95),
        rgba(5, 18, 37, 0.92)
    );
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
}
.nav {
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.brand__logo {
    width: 46px;
    height: 46px;
    object-fit: contain;
    padding: 6px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.brand__text {
    display: flex;
    flex-direction: column;
    line-height: 1.05;
    color: #fff;
    min-width: 0;
}
.brand-title {
    font-weight: 900;
}
.brand-subtitle {
    opacity: 0.75;
    font-weight: 800;
    letter-spacing: 0.18em;
    font-size: 0.68rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 46vw;
}

/* BUTTON */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    padding: 10px 14px;
    border-radius: 999px;
    font-weight: 800;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition:
        transform 0.15s ease,
        opacity 0.15s ease,
        background 0.15s ease;
}
.btn:hover {
    transform: translateY(-1px);
}
.btn:active {
    transform: translateY(0);
    opacity: 0.92;
}
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn--gold {
    background: var(--gold);
    color: #111;
    border-color: rgba(0, 0, 0, 0.08);
}
.btn--gold:hover {
    background: var(--gold2);
}
.btn--outline {
    background: transparent;
    border-color: rgba(255, 255, 255, 0.25);
    color: rgba(255, 255, 255, 0.9);
}
.btn--outline:hover {
    border-color: rgba(255, 255, 255, 0.4);
    color: #fff;
}
.btn--dark {
    background: var(--navy);
    color: #fff;
}
.btn--dark:hover {
    background: var(--navy2);
}
.btn--sm {
    padding: 9px 12px;
    font-size: 0.9rem;
}

/* =========================
   APPLY PAGE
========================= */
.page {
    padding: 0;
}

.apply-shell {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 16px;
    padding: 24px 0 32px;
    align-items: start;
}

.apply-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 18px 46px rgba(10, 20, 40, 0.12);
    border: 1px solid rgba(10, 20, 40, 0.06);
    overflow: hidden;
    padding: 20px;
}

.apply-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
}

.apply-title {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 900;
    letter-spacing: 0.01em;
}

.apply-sub {
    margin: 6px 0 0;
    color: rgba(15, 23, 42, 0.65);
    font-weight: 650;
    font-size: 0.92rem;
}

.apply-badge {
    padding: 8px 10px;
    border-radius: 999px;
    background: rgba(7, 26, 51, 0.08);
    border: 1px solid rgba(7, 26, 51, 0.12);
    font-weight: 900;
    font-size: 0.78rem;
    color: rgba(7, 26, 51, 0.88);
    white-space: nowrap;
}

.warn-top {
    margin-top: 12px;
    padding: 12px 14px;
    border-radius: 14px;
    background: rgba(185, 28, 28, 0.08);
    border: 1px solid rgba(185, 28, 28, 0.18);
}
.warn-title {
    font-weight: 900;
    color: #b91c1c;
    margin-bottom: 3px;
}
.warn-desc {
    color: rgba(185, 28, 28, 0.86);
    font-weight: 650;
    font-size: 0.92rem;
}

/* accordion */
.acc {
    margin-top: 12px;
    border: 1px solid rgba(10, 20, 40, 0.08);
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
}
.acc__sum {
    list-style: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px;
    cursor: pointer;
    background: rgba(7, 26, 51, 0.03);
}
.acc__sum::-webkit-details-marker {
    display: none;
}
.acc__left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.acc__dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: rgba(214, 163, 58, 0.95);
    box-shadow: 0 0 0 4px rgba(214, 163, 58, 0.18);
    flex: 0 0 auto;
}
.acc__title {
    font-weight: 900;
    letter-spacing: 0.01em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.acc__chev {
    font-weight: 900;
    opacity: 0.7;
    transition: transform 0.2s ease;
}
details[open] .acc__chev {
    transform: rotate(180deg);
}
.acc__body {
    padding: 14px;
}

.grid {
    display: grid;
    gap: 12px;
}
.grid.two {
    grid-template-columns: 1fr 1fr;
}

.field {
    display: grid;
    gap: 6px;
    min-width: 0;
}
.label {
    font-weight: 850;
    font-size: 0.92rem;
}
.req {
    color: #b91c1c;
}

.input {
    width: 100%;
    padding: 11px 12px;
    border-radius: 12px;
    border: 1px solid rgba(15, 23, 42, 0.18);
    outline: none;
    background: #fff;
    font-weight: 650;
}
.input:focus {
    border-color: rgba(214, 163, 58, 0.8);
    box-shadow: 0 0 0 4px rgba(214, 163, 58, 0.16);
}

/* file input */
.input.file {
    padding: 10px 12px;
}
.input.file::file-selector-button {
    border: 1px solid rgba(10, 20, 40, 0.14);
    background: rgba(7, 26, 51, 0.06);
    padding: 8px 10px;
    border-radius: 10px;
    font-weight: 850;
    cursor: pointer;
    margin-right: 10px;
}
.input.file::file-selector-button:hover {
    background: rgba(7, 26, 51, 0.1);
}

.hint-row {
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}
.hint {
    color: rgba(15, 23, 42, 0.55);
    font-weight: 650;
    font-size: 0.86rem;
}

.file-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(214, 163, 58, 0.14);
    border: 1px solid rgba(214, 163, 58, 0.22);
    font-weight: 850;
    font-size: 0.82rem;
    max-width: 100%;
}
.chip-x {
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
    font-weight: 900;
    opacity: 0.75;
}
.chip-x:hover {
    opacity: 1;
}

.row-actions {
    margin-top: 12px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.list {
    margin-top: 12px;
    display: grid;
    gap: 10px;
}
.item {
    padding: 12px 12px;
    border-radius: 14px;
    border: 1px solid rgba(10, 20, 40, 0.08);
    background: #fff;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}
.item-main {
    min-width: 0;
}
.item-title {
    font-weight: 900;
}
.item-sub {
    margin-top: 3px;
    color: rgba(15, 23, 42, 0.68);
    font-weight: 650;
    font-size: 0.9rem;
}
.muted {
    opacity: 0.65;
    font-weight: 750;
    margin-left: 6px;
}
.item-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}
.btn-ghost {
    border: 1px solid rgba(10, 20, 40, 0.14);
    background: transparent;
    padding: 8px 10px;
    border-radius: 12px;
    font-weight: 850;
    cursor: pointer;
}
.btn-ghost:hover {
    background: rgba(7, 26, 51, 0.06);
}

.btn-danger-ghost {
    border: 1px solid rgba(185, 28, 28, 0.25);
    background: transparent;
    padding: 8px 10px;
    border-radius: 12px;
    font-weight: 850;
    cursor: pointer;
    color: #b91c1c;
}
.btn-danger-ghost:hover {
    background: rgba(185, 28, 28, 0.08);
}

/* empty state */
.empty {
    margin-top: 12px;
    padding: 12px 14px;
    border-radius: 14px;
    background: rgba(15, 23, 42, 0.03);
    border: 1px dashed rgba(15, 23, 42, 0.18);
    color: rgba(15, 23, 42, 0.65);
    font-weight: 650;
}

/* submit */
.final-actions {
    margin-top: 16px;
    display: grid;
    gap: 10px;
}
.full {
    width: 100%;
    padding: 12px 16px;
    font-size: 1rem;
    border-radius: 16px;
}
.warn {
    padding: 12px 14px;
    border-radius: 14px;
    background: rgba(185, 28, 28, 0.08);
    border: 1px solid rgba(185, 28, 28, 0.18);
    color: rgba(185, 28, 28, 0.9);
    font-weight: 650;
    font-size: 0.92rem;
}

/* side note */
.apply-note {
    position: sticky;
    top: 92px;
}
.note-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 18px 46px rgba(10, 20, 40, 0.12);
    border: 1px solid rgba(10, 20, 40, 0.06);
    padding: 16px;
}
.note-title {
    font-weight: 950;
    letter-spacing: 0.01em;
    margin-bottom: 10px;
}
.note-list {
    margin: 0;
    padding-left: 18px;
    color: rgba(15, 23, 42, 0.75);
    font-weight: 650;
}
.note-list li {
    margin: 6px 0;
}

/* doc field tweaks */
.doc-field .input.file {
    height: 44px;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
    .container {
        padding-left: 6%;
        padding-right: 6%;
    }

    .apply-shell {
        grid-template-columns: 1fr;
    }

    .apply-note {
        position: static;
        order: 2;
    }
}

@media (max-width: 720px) {
    .nav {
        height: 68px;
    }

    .brand__logo {
        width: 42px;
        height: 42px;
    }

    .brand-subtitle {
        max-width: 56vw;
    }

    .apply-card {
        padding: 16px;
    }

    .grid.two {
        grid-template-columns: 1fr;
    }

    .apply-head {
        flex-direction: column;
        align-items: flex-start;
    }

    .apply-badge {
        align-self: flex-start;
    }

    .item {
        flex-direction: column;
        align-items: flex-start;
    }

    .item-actions {
        width: 100%;
        justify-content: flex-start;
    }
}
.alerts-wrap {
    width: 100%;
    margin-bottom: 14px;
}

/* Base Alert */
.alert {
    width: 100%;
    box-sizing: border-box;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    background: #fff;

    font-family: Arial, sans-serif;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.04);
}

.alert-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.alert-icon {
    width: 18px;
    height: 18px;
    border-radius: 999px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    font-size: 12px;
    font-weight: 700;
    line-height: 1;
    flex: 0 0 auto;
}

.alert-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.alert-title {
    font-size: 13px;
    font-weight: 700;
}

.alert-msg {
    font-size: 12px;
    opacity: 0.9;
    word-break: break-word;
    white-space: normal;
}

/* Close */
.alert-close {
    width: 28px;
    height: 28px;

    border: 1px solid #d1d5db;
    background: #fff;
    border-radius: 8px;

    cursor: pointer;
    font-size: 16px;
    line-height: 1;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    transition: 0.15s ease;
}

.alert-close:hover {
    background: #f3f4f6;
}

.alert-close:active {
    transform: scale(0.98);
}

/* SUCCESS */
.alert-success {
    border-color: #bbf7d0;
    background: #f0fdf4;
    color: #166534;
}

.alert-success .alert-icon {
    background: #22c55e;
    color: #fff;
}

/* ERROR */
.alert-error {
    border-color: #fecaca;
    background: #fef2f2;
    color: #7f1d1d;
}

.alert-error .alert-icon {
    background: #ef4444;
    color: #fff;
}
</style>
