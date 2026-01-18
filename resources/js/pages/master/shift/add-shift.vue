<template>
    <Modal size="lg">
        <div class="modal-header">
            <h3>Tambah Shift</h3>
            <button class="modal-close" @click="$emit('closeModal')">✕</button>
        </div>

        <p class="modal-text">Tambah shift sesuai data yang benar</p>

        <div class="modal-body">
            <div class="detail-grid two-col">
                <!-- Nama Shift -->
                <div class="form-group">
                    <label class="field-label">
                        Nama Shift
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.nama_shift"
                        class="form-input"
                        placeholder="Contoh: Shift Pagi"
                        required
                    />
                    <span v-if="errors.nama_shift" class="error-text">
                        {{ errors.nama_shift }}
                    </span>
                </div>

                <!-- Kode Shift -->
                <div class="form-group">
                    <label class="field-label">
                        Kode Shift
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.kode_shift"
                        class="form-input"
                        placeholder="Contoh: SPG"
                        required
                    />
                    <span v-if="errors.kode_shift" class="error-text">
                        {{ errors.kode_shift }}
                    </span>
                </div>

                <!-- Jam Masuk -->
                <div class="form-group">
                    <label class="field-label">
                        Jam Masuk
                        <span class="required">*</span>
                    </label>
                    <input
                        type="time"
                        v-model="form.jam_masuk"
                        class="form-input"
                        step="900"
                        required
                    />
                    <span v-if="errors.jam_masuk" class="error-text">
                        {{ errors.jam_masuk }}
                    </span>
                </div>

                <!-- Jam Pulang -->
                <div class="form-group">
                    <label class="field-label">
                        Jam Pulang
                        <span class="required">*</span>
                    </label>
                    <input
                        type="time"
                        v-model="form.jam_pulang"
                        step="900"
                        class="form-input"
                        required
                    />
                    <span v-if="errors.jam_pulang" class="error-text">
                        {{ errors.jam_pulang }}
                    </span>
                </div>

                <!-- Toleransi Keterlambatan -->
                <div class="form-group">
                    <label class="field-label">
                        Toleransi Keterlambatan (menit)
                    </label>
                    <input
                        type="number"
                        min="0"
                        v-model="form.toleransi_keterlambatan"
                        class="form-input"
                    />
                    <span
                        v-if="errors.toleransi_keterlambatan"
                        class="error-text"
                    >
                        {{ errors.toleransi_keterlambatan }}
                    </span>
                </div>

                <!-- Durasi Kerja -->
                <div class="form-group">
                    <label class="field-label">
                        Durasi Kerja (menit)
                        <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        min="1"
                        v-model="form.durasi_kerja"
                        class="form-input"
                        required
                    />
                    <span v-if="errors.durasi_kerja" class="error-text">
                        {{ errors.durasi_kerja }}
                    </span>
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label class="field-label">Keterangan</label>
                    <textarea
                        v-model="form.keterangan"
                        class="form-input"
                        placeholder="Opsional"
                        rows="3"
                    ></textarea>
                    <span v-if="errors.keterangan" class="error-text">
                        {{ errors.keterangan }}
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
                @click="submitShift"
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
    props: {
        period: {
            type: Object, // atau String/Number sesuai data kamu
            required: true,
        },
    },
    data() {
        return {
            loading: false,
            processing: false,
            form: {
                nama_shift: '',
                kode_shift: '',
                jam_masuk: '',
                jam_pulang: '',
                toleransi_keterlambatan: 15,
                durasi_kerja: '',
                is_active: true,
                keterangan: '',
            },
            errors: {},
        };
    },
    methods: {
        async submitShift() {
            this.processing = true;

            router.post('/hr/shift/store', this.form, {
                onSuccess: () => {
                    triggerAlert('success', 'Shift berhasil disimpan');
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
