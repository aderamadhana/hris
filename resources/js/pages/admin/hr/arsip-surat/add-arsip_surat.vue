<template>
    <Modal size="lg">
        <div class="modal-header">
            <h3>Tambah Surat</h3>
            <button class="modal-close" @click="$emit('closeModal')">✕</button>
        </div>

        <p class="modal-text">Tambah arsip surat sesuai data yang benar</p>

        <div class="modal-body">
            <div class="detail-grid two-col">
                <!-- Nomor Surat -->
                <div class="form-group">
                    <label class="field-label">
                        Nomor Surat
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.nomor_surat"
                        class="form-input"
                        placeholder="Contoh: 001/ABC/I/2026"
                        required
                    />
                    <span v-if="errors.nomor_surat" class="error-text">
                        {{ errors.nomor_surat }}
                    </span>
                </div>

                <!-- Tanggal Surat -->
                <div class="form-group">
                    <label class="field-label">
                        Tanggal Surat
                        <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        v-model="form.tanggal_surat"
                        class="form-input"
                        required
                    />
                    <span v-if="errors.tanggal_surat" class="error-text">
                        {{ errors.tanggal_surat }}
                    </span>
                </div>

                <!-- Perihal -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">
                        Perihal
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.perihal"
                        class="form-input"
                        placeholder="Contoh: Pemberitahuan..."
                        required
                    />
                    <span v-if="errors.perihal" class="error-text">
                        {{ errors.perihal }}
                    </span>
                </div>

                <!-- Upload File -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">
                        Upload File Surat
                        <span class="required">*</span>
                    </label>

                    <input
                        type="file"
                        class="form-input"
                        @change="handleFile"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        required
                    />

                    <small v-if="fileName" class="text-muted">
                        File dipilih: {{ fileName }}
                    </small>

                    <span v-if="errors.file" class="error-text">
                        {{ errors.file }}
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
                @click="submitSurat"
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
    components: { Modal, Button },

    data() {
        return {
            processing: false,
            fileName: '',
            form: {
                nomor_surat: '',
                tanggal_surat: '',
                perihal: '',
                file: null,
            },
            errors: {},
        };
    },

    methods: {
        handleFile(e) {
            const file = e.target.files?.[0] || null;
            this.form.file = file;
            this.fileName = file ? file.name : '';
        },

        submitSurat() {
            this.processing = true;
            this.errors = {};

            // Inertia perlu multipart untuk file upload
            router.post('/hr/arsip-surat/store', this.form, {
                forceFormData: true,

                onSuccess: () => {
                    triggerAlert('success', 'Surat berhasil disimpan');
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
