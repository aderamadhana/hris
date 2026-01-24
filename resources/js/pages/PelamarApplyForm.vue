<template>
    <div class="pelamar-card">
        <!-- HEADER -->
        <div class="pelamar-head">
            <div>
                <h3 class="pelamar-title">Form Lamaran</h3>
                <p class="pelamar-sub">
                    Isi data singkat untuk melamar posisi ini.
                </p>
            </div>

            <div class="job-pill" v-if="loker?.judul">
                {{ loker.judul }}
            </div>
        </div>

        <!-- ALERT -->
        <div v-if="successMsg" class="state state--success">
            {{ successMsg }}
        </div>

        <div v-if="errors.general" class="state state--error">
            {{ errors.general }}
        </div>

        <!-- FORM -->
        <form @submit.prevent="submit" class="pelamar-form">
            <!-- DATA PELAMAR -->
            <div class="form-section">
                <h4 class="section-title">Data Pelamar</h4>

                <div class="two-col grid">
                    <div class="form-group">
                        <label class="field-label">
                            Nama Lengkap <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.nama"
                            class="form-input"
                            placeholder="Contoh: Budi Santoso"
                        />
                        <small v-if="errors.nama" class="err">{{
                            errors.nama
                        }}</small>
                    </div>

                    <div class="form-group">
                        <label class="field-label">
                            No. WhatsApp <span class="required">*</span>
                        </label>
                        <input
                            type="tel"
                            v-model="form.no_hp"
                            class="form-input"
                            placeholder="Contoh: 08xxxxxxxxxx"
                            inputmode="tel"
                        />
                        <small v-if="errors.no_hp" class="err">{{
                            errors.no_hp
                        }}</small>
                    </div>

                    <div class="form-group">
                        <label class="field-label">Email</label>
                        <input
                            type="email"
                            v-model="form.email"
                            class="form-input"
                            placeholder="Contoh: nama@email.com"
                        />
                        <small v-if="errors.email" class="err">{{
                            errors.email
                        }}</small>
                    </div>

                    <div class="form-group">
                        <label class="field-label">Domisili</label>
                        <input
                            type="text"
                            v-model="form.domisili"
                            class="form-input"
                            placeholder="Contoh: Bekasi"
                        />
                        <small v-if="errors.domisili" class="err">{{
                            errors.domisili
                        }}</small>
                    </div>

                    <div class="form-group full">
                        <label class="field-label"
                            >Pesan Singkat (Opsional)</label
                        >
                        <textarea
                            v-model="form.pesan"
                            class="form-input"
                            rows="3"
                            placeholder="Contoh: Saya memiliki pengalaman 2 tahun sebagai security..."
                        ></textarea>
                        <small v-if="errors.pesan" class="err">{{
                            errors.pesan
                        }}</small>
                    </div>
                </div>
            </div>

            <!-- DOKUMEN -->
            <div class="form-section">
                <h4 class="section-title">Dokumen</h4>

                <div class="two-col grid">
                    <div class="form-group full">
                        <label class="field-label">
                            Upload CV (PDF/DOC/DOCX, max 2MB)
                        </label>

                        <div class="file-row">
                            <input
                                type="file"
                                class="form-input"
                                accept=".pdf,.doc,.docx"
                                @change="onCvChange"
                            />
                            <span v-if="form.cv" class="file-name">
                                {{ form.cv.name }}
                            </span>
                        </div>

                        <small v-if="errors.cv" class="err">{{
                            errors.cv
                        }}</small>
                        <small class="hint">
                            Pastikan file dapat dibaca (tidak blur / tidak
                            terkunci password).
                        </small>
                    </div>
                </div>
            </div>

            <!-- ACTIONS -->
            <div class="actions">
                <button
                    type="button"
                    class="btn btn--secondary"
                    :disabled="loading"
                    @click="$emit('close')"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn--primary"
                    :disabled="loading"
                >
                    <span v-if="loading">Mengirim...</span>
                    <span v-else>Kirim Lamaran</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'PelamarApplyForm',

    props: {
        loker: {
            type: Object,
            default: () => ({}),
        },
    },

    emits: ['close', 'submitted'],

    data() {
        return {
            loading: false,
            successMsg: '',
            errors: {},

            form: {
                nama: '',
                no_hp: '',
                email: '',
                domisili: '',
                pesan: '',
                cv: null,
            },
        };
    },

    methods: {
        onCvChange(e) {
            const file = e.target.files?.[0] || null;
            if (!file) {
                this.form.cv = null;
                return;
            }

            // validasi ringan di frontend
            const max = 2 * 1024 * 1024; // 2MB
            if (file.size > max) {
                this.errors.cv = 'Ukuran file maksimal 2MB.';
                e.target.value = '';
                this.form.cv = null;
                return;
            }

            this.errors.cv = null;
            this.form.cv = file;
        },

        normalizeErrors(serverErrors) {
            // Laravel: { field: ["msg"] }
            const out = {};
            for (const k in serverErrors) {
                out[k] = serverErrors[k]?.[0] || 'Tidak valid';
            }
            return out;
        },

        async submit() {
            if (!this.loker?.slug) {
                this.errors.general = 'Data loker tidak valid.';
                return;
            }

            this.loading = true;
            this.successMsg = '';
            this.errors = {};

            try {
                const fd = new FormData();
                fd.append('nama', this.form.nama || '');
                fd.append('no_hp', this.form.no_hp || '');
                fd.append('email', this.form.email || '');
                fd.append('domisili', this.form.domisili || '');
                fd.append('pesan', this.form.pesan || '');
                if (this.form.cv) fd.append('cv', this.form.cv);

                const res = await axios.post(
                    `/landing-page/loker/${this.loker.slug}/apply`,
                    fd,
                    { headers: { 'Content-Type': 'multipart/form-data' } },
                );

                if (res.data?.success) {
                    this.successMsg =
                        res.data?.message ||
                        'Lamaran berhasil dikirim. Tim kami akan menghubungi Anda.';

                    // reset form
                    this.form = {
                        nama: '',
                        no_hp: '',
                        email: '',
                        domisili: '',
                        pesan: '',
                        cv: null,
                    };

                    this.$emit('submitted', res.data);
                }
            } catch (e) {
                if (e.response?.status === 422) {
                    this.errors = this.normalizeErrors(
                        e.response.data?.errors || {},
                    );
                } else {
                    this.errors.general = 'Gagal mengirim lamaran. Coba lagi.';
                }
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.pelamar-card {
    padding: 18px;
}

.pelamar-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
}

.pelamar-title {
    margin: 0;
    font-weight: 900;
    font-size: 1.05rem;
}

.pelamar-sub {
    margin: 6px 0 0;
    font-weight: 600;
    color: rgba(15, 23, 42, 0.65);
}

.job-pill {
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(214, 163, 58, 0.14);
    border: 1px solid rgba(214, 163, 58, 0.22);
    font-weight: 900;
    font-size: 0.85rem;
    white-space: nowrap;
}

.form-section {
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid rgba(15, 23, 42, 0.08);
}

.section-title {
    margin: 0 0 10px;
    font-weight: 900;
    font-size: 0.95rem;
}

.grid {
    display: grid;
    gap: 12px;
}

.two-col {
    grid-template-columns: 1fr 1fr;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group.full {
    grid-column: 1 / -1;
}

.field-label {
    font-weight: 800;
    font-size: 0.9rem;
}

.required {
    color: #b91c1c;
}

.form-input {
    border: 1px solid rgba(15, 23, 42, 0.14);
    border-radius: 12px;
    padding: 10px 12px;
    font-weight: 650;
    outline: none;
}

.form-input:focus {
    border-color: rgba(214, 163, 58, 0.85);
    box-shadow: 0 0 0 3px rgba(214, 163, 58, 0.18);
}

.file-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.file-name {
    font-weight: 750;
    color: rgba(15, 23, 42, 0.7);
}

.hint {
    color: rgba(15, 23, 42, 0.55);
    font-weight: 600;
}

.err {
    color: #b91c1c;
    font-weight: 700;
}

.actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    flex-wrap: wrap;
    margin-top: 16px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    padding: 10px 14px;
    border-radius: 999px;
    font-weight: 900;
    border: 1px solid transparent;
    cursor: pointer;
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.btn--secondary {
    background: transparent;
    border-color: rgba(15, 23, 42, 0.18);
    color: rgba(15, 23, 42, 0.85);
}

.btn--primary {
    background: #d6a33a;
    color: #111;
}

.state {
    padding: 10px 12px;
    border-radius: 14px;
    margin-bottom: 12px;
    font-weight: 800;
}

.state--success {
    background: rgba(34, 197, 94, 0.12);
    border: 1px solid rgba(34, 197, 94, 0.25);
}

.state--error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.22);
}

@media (max-width: 720px) {
    .two-col {
        grid-template-columns: 1fr;
    }
}
</style>
