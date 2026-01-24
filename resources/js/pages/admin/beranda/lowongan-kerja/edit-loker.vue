<template>
    <Modal size="lg">
        <div class="modal-header">
            <h3>Edit Loker</h3>
            <button class="modal-close" @click="$emit('closeModal')">✕</button>
        </div>

        <p class="modal-text">Edit atau hapus loker sesuai data yang benar</p>

        <div class="modal-body">
            <div class="detail-grid two-col">
                <!-- Judul Loker -->
                <div class="form-group">
                    <label class="field-label">
                        Judul Loker
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.judul"
                        class="form-input"
                        placeholder="Contoh: Security Guard"
                        required
                    />
                    <span v-if="errors.judul" class="error-text">
                        {{ errors.judul }}
                    </span>
                </div>

                <!-- Slug / Kode -->
                <div class="form-group">
                    <label class="field-label">
                        Slug / Kode Loker
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.slug"
                        class="form-input"
                        readonly="readonly"
                        placeholder="Contoh: security-guard"
                        required
                    />
                    <small class="hint">
                        Gunakan huruf kecil dan tanda - (tanpa spasi).
                    </small>
                    <span v-if="errors.slug" class="error-text">
                        {{ errors.slug }}
                    </span>
                </div>

                <!-- Tipe Pekerjaan -->
                <div class="form-group">
                    <label class="field-label">
                        Tipe Pekerjaan
                        <span class="required">*</span>
                    </label>
                    <select
                        v-model="form.tipe_pekerjaan"
                        class="form-input"
                        required
                    >
                        <option value="" disabled>Pilih tipe</option>
                        <option value="Full-time">Full-time</option>
                        <option value="Contract">Contract</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Freelance">Freelance</option>
                    </select>
                    <span v-if="errors.tipe_pekerjaan" class="error-text">
                        {{ errors.tipe_pekerjaan }}
                    </span>
                </div>

                <!-- Jam Kerja -->
                <div class="form-group">
                    <label class="field-label">Jam Kerja</label>
                    <select v-model="form.jam_kerja" class="form-input">
                        <option value="" disabled>Pilih jam kerja</option>
                        <option value="Shift">Shift</option>
                        <option value="Office">Office</option>
                        <option value="Day">Day</option>
                        <option value="Night">Night</option>
                    </select>
                    <span v-if="errors.jam_kerja" class="error-text">
                        {{ errors.jam_kerja }}
                    </span>
                </div>

                <!-- Perusahaan Nama -->
                <div class="form-group">
                    <label class="field-label">Perusahaan</label>
                    <Select2
                        v-model="form.perusahaan_id"
                        :settings="{ width: '100%' }"
                    >
                        <option value="">Semua Perusahaan</option>
                        <option
                            v-for="value in data_perusahaan"
                            :key="value"
                            :value="value.id"
                        >
                            {{ value.nama_perusahaan }}
                        </option>
                    </Select2>
                    <span v-if="errors.perusahaan_id" class="error-text">
                        {{ errors.perusahaan_id }}
                    </span>
                </div>

                <!-- Penempatan -->
                <div class="form-group">
                    <label class="field-label">Penempatan</label>
                    <Select2
                        v-model="form.penempatan_id"
                        :settings="{ width: '100%' }"
                        :disabled="!form.perusahaan_id"
                    >
                        <option value="">Semua Divisi / Dept</option>
                        <option
                            v-for="value in data_jabatan"
                            :key="value"
                            :value="value.id"
                        >
                            {{ value.nama_divisi }}
                        </option>
                    </Select2>
                    <span v-if="errors.penempatan_id" class="error-text">
                        {{ errors.penempatan_id }}
                    </span>
                </div>

                <!-- Gaji Min -->
                <div class="form-group">
                    <label class="field-label">Gaji Minimum (opsional)</label>
                    <input
                        type="number"
                        min="0"
                        v-model="form.gaji_min"
                        class="form-input"
                        placeholder="Contoh: 4500000"
                    />
                    <span v-if="errors.gaji_min" class="error-text">
                        {{ errors.gaji_min }}
                    </span>
                </div>

                <!-- Gaji Max -->
                <div class="form-group">
                    <label class="field-label">Gaji Maksimum (opsional)</label>
                    <input
                        type="number"
                        min="0"
                        v-model="form.gaji_max"
                        class="form-input"
                        placeholder="Contoh: 5500000"
                    />
                    <span v-if="errors.gaji_max" class="error-text">
                        {{ errors.gaji_max }}
                    </span>
                </div>

                <!-- Ringkasan -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">Ringkasan</label>
                    <input
                        type="text"
                        v-model="form.ringkasan"
                        class="form-input"
                        placeholder="Contoh: Penempatan Jabodetabek, sistem loker..."
                    />
                    <span v-if="errors.ringkasan" class="error-text">
                        {{ errors.ringkasan }}
                    </span>
                </div>

                <!-- Deskripsi -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">Deskripsi Pekerjaan</label>
                    <textarea
                        v-model="form.deskripsi"
                        class="form-input"
                        placeholder="Jelaskan jobdesk dan tanggung jawab..."
                        rows="4"
                    ></textarea>
                    <span v-if="errors.deskripsi" class="error-text">
                        {{ errors.deskripsi }}
                    </span>
                </div>

                <!-- Persyaratan -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">Persyaratan</label>
                    <textarea
                        v-model="form.persyaratan"
                        class="form-input"
                        placeholder="Pisahkan per baris. Contoh:&#10;- Minimal SMA/SMK&#10;- Tinggi badan min 165 cm&#10;- Bersedia loker"
                        rows="4"
                    ></textarea>
                    <span v-if="errors.persyaratan" class="error-text">
                        {{ errors.persyaratan }}
                    </span>
                </div>

                <!-- Tanggal Publish -->
                <div class="form-group">
                    <label class="field-label">Tanggal Publish</label>
                    <input
                        type="datetime-local"
                        v-model="form.tanggal_publish"
                        class="form-input"
                    />
                    <span v-if="errors.tanggal_publish" class="error-text">
                        {{ errors.tanggal_publish }}
                    </span>
                </div>

                <!-- Tanggal Berakhir -->
                <div class="form-group">
                    <label class="field-label">Tanggal Berakhir</label>
                    <input
                        type="datetime-local"
                        v-model="form.tanggal_berakhir"
                        class="form-input"
                    />
                    <span v-if="errors.tanggal_berakhir" class="error-text">
                        {{ errors.tanggal_berakhir }}
                    </span>
                </div>

                <!-- Link Lamar -->
                <div class="form-group">
                    <label class="field-label">Link Lamar (opsional)</label>
                    <input
                        type="url"
                        v-model="form.link_lamar"
                        class="form-input"
                        placeholder="https://..."
                    />
                    <span v-if="errors.link_lamar" class="error-text">
                        {{ errors.link_lamar }}
                    </span>
                </div>

                <!-- WhatsApp Kontak -->
                <div class="form-group">
                    <label class="field-label"
                        >WhatsApp Kontak (opsional)</label
                    >
                    <input
                        type="text"
                        v-model="form.whatsapp_kontak"
                        class="form-input"
                        placeholder="Contoh: 62812xxxxxxx"
                    />
                    <span v-if="errors.whatsapp_kontak" class="error-text">
                        {{ errors.whatsapp_kontak }}
                    </span>
                </div>

                <!-- Status -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">Status</label>
                    <div class="toggle-row">
                        <input
                            type="checkbox"
                            id="aktif"
                            v-model="form.aktif"
                        />
                        <label for="aktif" class="toggle-label">
                            {{ form.aktif ? 'Aktif' : 'Nonaktif' }}
                        </label>
                    </div>
                    <span v-if="errors.aktif" class="error-text">
                        {{ errors.aktif }}
                    </span>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <Button variant="secondary" @click="$emit('closeModal')">
                Batal
            </Button>

            <Button
                variant="danger"
                @click="deleteLoker"
                class="d-flex align-items-center justify-content-center gap-2"
            >
                <template v-if="processingDelete">
                    <span
                        class="spinner-border spinner-border-sm spinner"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    <span>Memproses...</span>
                </template>

                <template v-else> Hapus </template>
            </Button>
            <Button
                variant="primary"
                @click="submitLoker"
                class="d-flex align-items-center justify-content-center gap-2"
            >
                <template v-if="processingUpdate">
                    <span
                        class="spinner-border spinner-border-sm spinner"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    <span>Memproses...</span>
                </template>

                <template v-else> Simpan </template>
            </Button>
        </div>
    </Modal>
</template>
<script>
import Button from '@/components/Button.vue';
import Modal from '@/components/Modal.vue';
import Select2 from '@/components/Select2.vue';
import { triggerAlert } from '@/utils/alert';

import { router } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    components: {
        Modal,
        Button,
        Select2,
    },
    props: {
        loker: {
            type: Object, // atau String/Number sesuai data kamu
            required: true,
        },
    },
    watch: {
        'form.judul'(val) {
            // kalau slug sudah diedit manual, jangan timpa
            if (this.slugManual) return;

            // auto-generate slug mengikuti judul
            this.form.slug = this.makeSlug(val, this.slugSeparator);
        },

        'form.perusahaan_id'(newVal, oldVal) {
            console.log('Perusahaan changed:', newVal);
            this.onPerusahaanChange();
        },
    },
    data() {
        return {
            loading: false,
            processingUpdate: false,
            processingDelete: false,
            form: {
                // wajib
                judul: this.loker?.judul ?? '',
                slug: this.loker?.slug ?? '',
                tipe_pekerjaan: this.loker?.tipe_pekerjaan ?? '',

                // ambil dari master (ID saja)
                perusahaan_id: this.loker?.perusahaan_id ?? '',
                penempatan_id: this.loker?.penempatan_id ?? '',

                // optional
                jam_kerja: this.loker?.jam_kerja ?? '',
                ringkasan: this.loker?.ringkasan ?? '',
                deskripsi: this.loker?.deskripsi ?? '',
                persyaratan: this.loker?.persyaratan ?? '',

                // gaji
                gaji_min: this.loker?.gaji_min ?? null,
                gaji_max: this.loker?.gaji_max ?? null,
                mata_uang: this.loker?.mata_uang ?? 'IDR',

                // apply info
                link_lamar: this.loker?.link_lamar ?? '',
                whatsapp_kontak: this.loker?.whatsapp_kontak ?? '',

                // status
                aktif: this.loker?.aktif ?? true,

                // publish window
                tanggal_publish: this.loker?.tanggal_publish ?? '',
                tanggal_berakhir: this.loker?.tanggal_berakhir ?? '',
            },
            errors: {},
            months: [
                { value: 1, label: 'Januari' },
                { value: 2, label: 'Februari' },
                { value: 3, label: 'Maret' },
                { value: 4, label: 'April' },
                { value: 5, label: 'Mei' },
                { value: 6, label: 'Juni' },
                { value: 7, label: 'Juli' },
                { value: 8, label: 'Agustus' },
                { value: 9, label: 'September' },
                { value: 10, label: 'Oktober' },
                { value: 11, label: 'November' },
                { value: 12, label: 'Desember' },
            ],
            data_perusahaan: [],
            data_jabatan: [],
            years: Array.from({ length: 21 }, (_, i) => 2020 + i),
        };
    },
    methods: {
        makeSlug(text, sep = '-') {
            return (
                (text || '')
                    .toString()
                    .trim()
                    .toLowerCase()
                    // buang karakter aneh (boleh huruf/angka/spasi/-/_)
                    .replace(/[^a-z0-9\s-_]/g, '')
                    // spasi jadi separator
                    .replace(/\s+/g, sep)
                    // rapihin separator dobel
                    .replace(new RegExp(`[${sep}]+`, 'g'), sep)
                    // hapus separator di awal/akhir
                    .replace(new RegExp(`^${sep}+|${sep}+$`, 'g'), '')
            );
        },

        // opsional: tombol untuk "kembalikan ke auto slug"
        resetAutoSlug() {
            this.slugManual = false;
            this.form.slug = this.makeSlug(this.form.judul, this.slugSeparator);
        },

        toDateInput(dmy) {
            // expect "DD/MM/YYYY"
            if (!dmy || typeof dmy !== 'string') return '';
            const parts = dmy.split('/');
            if (parts.length !== 3) return '';
            const [dd, mm, yyyy] = parts.map((p) => p.trim());
            if (!dd || !mm || !yyyy) return '';
            return `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
        },

        async submitLoker() {
            this.processingUpdate = true;

            router.put(`/hr/loker/update/${this.loker.id}`, this.form, {
                onSuccess: () => {
                    triggerAlert('success', 'Loker berhasil diupdate');
                    this.$emit('closeModal');
                    this.$emit('refreshData');
                },

                onError: (errors) => {
                    this.errors = errors;
                    triggerAlert('error', 'Periksa kembali data yang diinput');
                },

                onFinish: () => {
                    this.processingUpdate = false;
                },
            });
        },

        async deleteLoker() {
            if (!confirm('Apakah Anda yakin ingin menghapus loker ini?'))
                return;

            this.processingDelete = true;

            router.delete(`/hr/loker/delete/${this.loker.id}`, {
                onSuccess: () => {
                    triggerAlert('success', 'Loker berhasil dihapus');
                    this.$emit('closeModal');
                    this.$emit('refreshData');
                },

                onError: () => {
                    triggerAlert('error', 'Gagal menghapus loker');
                },

                onFinish: () => {
                    this.processingDelete = false;
                },
            });
        },

        onPerusahaanChange() {
            // Reset divisi
            this.jabatan = '';
            this.data_jabatan = [];

            // Jika tidak ada perusahaan dipilih, stop
            if (!this.form.perusahaan_id) {
                console.log('Tidak ada perusahaan dipilih');
                return;
            }
            console.log(this.form.perusahaan_id);

            // Filter perusahaan yang dipilih dari data_perusahaan
            const perusahaanSelected = this.data_perusahaan.find(
                (p) => p.id == this.form.perusahaan_id, // Gunakan == untuk compare
            );

            // Ambil divisi dari perusahaan yang dipilih
            if (perusahaanSelected) {
                if (
                    perusahaanSelected.divisi &&
                    Array.isArray(perusahaanSelected.divisi) &&
                    perusahaanSelected.divisi.length > 0
                ) {
                    this.data_jabatan = perusahaanSelected.divisi.filter(
                        (d) => d.status === 'aktif',
                    );
                    console.log('Divisi ditemukan:', this.data_jabatan);
                } else {
                    console.log('Perusahaan tidak memiliki divisi');
                }
            } else {
                console.log('Perusahaan tidak ditemukan dalam data');
            }
        },

        async getFilteredPerusahaanDanJabatan() {
            try {
                const res = await axios.get('/referensi/perusahaan-divisi');
                this.data_perusahaan = res.data.data || [];
                this.all_data = res.data.data;
            } catch (err) {
                console.error(err);
                triggerAlert(
                    'error',
                    'Gagal memuat filter perusahaan/jabatan.',
                );
            }
        },
    },

    mounted() {
        this.getFilteredPerusahaanDanJabatan();
    },
};
</script>
