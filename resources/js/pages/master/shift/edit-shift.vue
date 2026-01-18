<template>
    <Modal size="lg">
        <div class="modal-header">
            <h3>Edit Shift</h3>
            <button class="modal-close" @click="$emit('closeModal')">✕</button>
        </div>

        <p class="modal-text">Edit atau hapus shift sesuai data yang benar</p>

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
                        step="900"
                        class="form-input"
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
                variant="danger"
                @click="deleteShift"
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
                @click="submitShift"
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
    components: {
        Modal,
        Button,
    },
    props: {
        shift: {
            type: Object, // atau String/Number sesuai data kamu
            required: true,
        },
    },
    data() {
        return {
            loading: false,
            processingUpdate: false,
            processingDelete: false,
            form: {
                nama_shift: this.shift?.nama_shift ?? '',
                kode_shift: this.shift?.kode_shift ?? '',
                jam_masuk: this.shift?.jam_masuk ?? '',
                jam_pulang: this.shift?.jam_pulang ?? '',
                toleransi_keterlambatan:
                    this.shift?.toleransi_keterlambatan ?? 15,
                durasi_kerja: this.shift?.durasi_kerja ?? '',
                is_active: true,
                keterangan: this.shift?.keterangan ?? '',
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
            years: Array.from({ length: 21 }, (_, i) => 2020 + i),
        };
    },
    methods: {
        toDateInput(dmy) {
            // expect "DD/MM/YYYY"
            if (!dmy || typeof dmy !== 'string') return '';
            const parts = dmy.split('/');
            if (parts.length !== 3) return '';
            const [dd, mm, yyyy] = parts.map((p) => p.trim());
            if (!dd || !mm || !yyyy) return '';
            return `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
        },

        async submitShift() {
            this.processingUpdate = true;

            router.put(`/hr/shift/update/${this.shift.id}`, this.form, {
                onSuccess: () => {
                    triggerAlert('success', 'Shift berhasil diupdate');
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

        async deleteShift() {
            if (!confirm('Apakah Anda yakin ingin menghapus shift ini?'))
                return;

            this.processingDelete = true;

            router.delete(`/hr/shift/delete/${this.shift.id}`, {
                onSuccess: () => {
                    triggerAlert('success', 'Shift berhasil dihapus');
                    this.$emit('closeModal');
                    this.$emit('refreshData');
                },

                onError: () => {
                    triggerAlert('error', 'Gagal menghapus shift');
                },

                onFinish: () => {
                    this.processingDelete = false;
                },
            });
        },
    },
};
</script>
