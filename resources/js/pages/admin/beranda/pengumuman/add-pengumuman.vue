<template>
    <Modal size="lg">
        <div class="modal-header">
            <h3>Tambah Pengumuman</h3>
            <button class="modal-close" @click="$emit('closeModal')">✕</button>
        </div>

        <p class="modal-text">Tambah pengumuman sesuai data yang benar</p>

        <div class="modal-body">
            <div class="detail-grid two-col">
                <!-- Kategori -->
                <div class="form-group">
                    <label class="field-label">
                        Kategori
                        <span class="required">*</span>
                    </label>

                    <select v-model="form.kategori" class="form-input" required>
                        <option value="" disabled>Pilih kategori</option>
                        <option value="Recruitment">Recruitment</option>
                        <option value="Info">Info</option>
                        <option value="Operasional">Operasional</option>
                    </select>

                    <span v-if="errors.kategori" class="error-text">
                        {{ errors.kategori }}
                    </span>
                </div>

                <!-- Urutan -->
                <div class="form-group">
                    <label class="field-label">Urutan Tampil</label>
                    <input
                        type="number"
                        min="0"
                        v-model.number="form.urutan"
                        class="form-input"
                        placeholder="Contoh: 0"
                    />
                    <small class="hint">
                        Angka lebih kecil akan tampil lebih dulu (opsional).
                    </small>
                    <span v-if="errors.urutan" class="error-text">
                        {{ errors.urutan }}
                    </span>
                </div>

                <!-- Judul -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">
                        Judul Pengumuman
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.judul"
                        class="form-input"
                        placeholder="Contoh: Pembukaan Rekrutmen Security"
                        required
                    />
                    <span v-if="errors.judul" class="error-text">
                        {{ errors.judul }}
                    </span>
                </div>

                <!-- Slug -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">
                        Slug
                        <span class="required">*</span>
                    </label>

                    <div class="d-flex gap-2">
                        <input
                            type="text"
                            v-model="form.slug"
                            class="form-input"
                            placeholder="Contoh: pembukaan-rekrutmen-security"
                            required
                            readonly
                            @input="slugManual = true"
                        />
                        <!-- <button
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="resetAutoSlug"
                            title="Reset slug otomatis"
                        >
                            Reset
                        </button> -->
                    </div>

                    <small class="hint">
                        Gunakan huruf kecil dan tanda - (tanpa spasi).
                    </small>

                    <span v-if="errors.slug" class="error-text">
                        {{ errors.slug }}
                    </span>
                </div>

                <!-- Ringkasan -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">Ringkasan</label>
                    <input
                        type="text"
                        v-model="form.ringkasan"
                        class="form-input"
                        placeholder="Contoh: Rekrutmen dibuka sampai 30 Jan 2026..."
                    />
                    <span v-if="errors.ringkasan" class="error-text">
                        {{ errors.ringkasan }}
                    </span>
                </div>

                <!-- Isi -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">Isi Pengumuman</label>
                    <textarea
                        v-model="form.isi"
                        class="form-input"
                        placeholder="Tulis detail pengumuman..."
                        rows="6"
                    ></textarea>
                    <span v-if="errors.isi" class="error-text">
                        {{ errors.isi }}
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

                <!-- Diutamakan -->
                <div class="form-group">
                    <label class="field-label">Diutamakan</label>
                    <div class="toggle-row">
                        <input
                            type="checkbox"
                            id="diutamakan"
                            v-model="form.diutamakan"
                        />
                        <label for="diutamakan" class="toggle-label">
                            {{ form.diutamakan ? 'Ya' : 'Tidak' }}
                        </label>
                    </div>
                    <span v-if="errors.diutamakan" class="error-text">
                        {{ errors.diutamakan }}
                    </span>
                </div>

                <!-- Status -->
                <div class="form-group">
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
                variant="primary"
                @click="submitPengumuman"
                class="d-flex align-items-center justify-content-center gap-2"
            >
                <template v-if="processing">
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
import { triggerAlert } from '@/utils/alert';
import { router } from '@inertiajs/vue3';

export default {
    components: {
        Modal,
        Button,
    },

    data() {
        return {
            processing: false,
            slugSeparator: '-',
            slugManual: false,

            form: {
                kategori: '',
                judul: '',
                slug: '',
                ringkasan: '',
                isi: '',

                diutamakan: false,
                aktif: true,

                tanggal_publish: '',
                tanggal_berakhir: '',

                urutan: 0,
            },

            errors: {},
        };
    },

    watch: {
        'form.judul'(val) {
            if (this.slugManual) return;
            this.form.slug = this.makeSlug(val, this.slugSeparator);
        },
    },

    methods: {
        makeSlug(text, sep = '-') {
            return (text || '')
                .toString()
                .trim()
                .toLowerCase()
                .replace(/[^a-z0-9\s-_]/g, '')
                .replace(/\s+/g, sep)
                .replace(new RegExp(`[${sep}]+`, 'g'), sep)
                .replace(new RegExp(`^${sep}+|${sep}+$`, 'g'), '');
        },

        resetAutoSlug() {
            this.slugManual = false;
            this.form.slug = this.makeSlug(this.form.judul, this.slugSeparator);
        },

        submitPengumuman() {
            this.processing = true;

            router.post('/beranda/pengumuman/store', this.form, {
                onSuccess: () => {
                    triggerAlert('success', 'Pengumuman berhasil disimpan');
                    this.$emit('closeModal');
                    this.$emit('refreshData');
                },

                onError: (errors) => {
                    this.errors = errors;
                    triggerAlert('error', 'Periksa kembali data yang diinput');
                },

                onFinish: () => {
                    this.processing = false;
                },
            });
        },
    },
};
</script>
