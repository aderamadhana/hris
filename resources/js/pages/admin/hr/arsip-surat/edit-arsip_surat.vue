<template>
    <Modal size="lg">
        <div class="modal-header">
            <h3>Edit Surat</h3>
            <button class="modal-close" @click="$emit('closeModal')">✕</button>
        </div>

        <p class="modal-text">
            Edit atau hapus arsip surat sesuai data yang benar
        </p>

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

                <!-- File saat ini -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">File Saat Ini</label>

                    <div
                        v-if="surat?.file_url"
                        class="d-flex align-items-center gap-2"
                    >
                        <a
                            :href="surat.file_url"
                            target="_blank"
                            rel="noopener"
                        >
                            Lihat File
                        </a>
                        <span class="text-muted"
                            >({{ surat.file_path || 'file' }})</span
                        >
                    </div>

                    <div v-else class="text-muted">Belum ada file</div>
                </div>

                <!-- Upload File Baru (opsional) -->
                <div class="form-group" style="grid-column: 1 / -1">
                    <label class="field-label">Ganti File (Opsional)</label>

                    <input
                        type="file"
                        class="form-input"
                        @change="handleFile"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                    />

                    <small v-if="fileName" class="text-muted">
                        File baru dipilih: {{ fileName }}
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
                variant="danger"
                @click="deleteSurat"
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
                @click="submitSurat"
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
import { triggerAlert } from '@/utils/alert';
import { router } from '@inertiajs/vue3';

export default {
    components: { Modal, Button },

    props: {
        surat: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            processingUpdate: false,
            processingDelete: false,
            fileName: '',
            form: {
                nomor_surat: this.surat?.nomor_surat ?? '',
                tanggal_surat: this.surat?.tanggal_surat ?? '',
                perihal: this.surat?.perihal ?? '',
                file: null, // file baru (opsional)
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
            this.processingUpdate = true;
            this.errors = {};

            router.post(`/hr/arsip-surat/update/${this.surat.id}`, this.form, {
                forceFormData: true,
                _method: 'post', // supaya tetap bisa upload file + PUT

                onSuccess: () => {
                    triggerAlert('success', 'Surat berhasil diupdate');
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

        deleteSurat() {
            if (!confirm('Apakah Anda yakin ingin menghapus surat ini?'))
                return;

            this.processingDelete = true;

            router.delete(`/hr/arsip-surat/delete/${this.surat.id}`, {
                onSuccess: () => {
                    triggerAlert('success', 'Surat berhasil dihapus');
                    this.$emit('closeModal');
                    this.$emit('refreshData');
                },

                onError: () => {
                    triggerAlert('error', 'Gagal menghapus surat');
                },

                onFinish: () => {
                    this.processingDelete = false;
                },
            });
        },
    },
};
</script>
