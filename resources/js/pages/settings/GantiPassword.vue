<template>
    <AppLayout>
        <section class="page detail-page">
            <!-- HEADER -->
            <div class="page-header">
                <div>
                    <h2 class="page-title">Ganti Password</h2>
                </div>
            </div>
            <div class="card detail-card">
                <form @submit.prevent="handleSubmitPassword">
                    <div class="form-section">
                        <h4 class="form-section-title">Ganti Password</h4>

                        <div class="detail-grid two-col">
                            <div class="form-group">
                                <label class="field-label">
                                    Password Baru
                                    <span class="required">*</span>
                                </label>
                                <input
                                    type="password"
                                    v-model="formPassword.password"
                                    class="form-input"
                                    required
                                />
                            </div>

                            <div class="form-group">
                                <label class="field-label">
                                    Konfirmasi Password
                                    <span class="required">*</span>
                                </label>
                                <input
                                    type="password"
                                    v-model="formPassword.password_confirmation"
                                    class="form-input"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <Button
                            variant="primary"
                            type="submit"
                            :disabled="loading"
                            class="d-flex align-items-center justify-content-center gap-2"
                        >
                            <template v-if="loading">
                                <span
                                    class="spinner-border spinner-border-sm spinner"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                <span>Menyimpan...</span>
                            </template>

                            <template v-else> Simpan Password </template>
                        </Button>
                    </div>
                </form>
            </div>
        </section>
    </AppLayout>
</template>
<script>
import Button from '@/components/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import axios from 'axios';
export default {
    components: { AppLayout, Button },
    data() {
        return {
            formPassword: {
                password: '',
                password_confirmation: '',
            },
            loading: false,
        };
    },
    methods: {
        async handleSubmitPassword() {
            // frontend guard tetap boleh (UX)
            if (
                this.formPassword.password !==
                this.formPassword.password_confirmation
            ) {
                triggerAlert('error', 'Konfirmasi password tidak cocok');
                return;
            }

            this.loading = true;

            try {
                const res = await axios.post(
                    '/employee/proses-change-password',
                    this.formPassword,
                );

                // sukses

                triggerAlert(
                    'success',
                    res.data.message || 'Password berhasil diubah',
                );

                this.formPassword.password = '';
                this.formPassword.password_confirmation = '';
            } catch (e) {
                // VALIDATION ERROR (Laravel 422)
                if (e.response && e.response.status === 422) {
                    const errors = e.response.data.errors;

                    // ambil error pertama yang relevan
                    const message =
                        errors?.password?.[0] ||
                        errors?.password_confirmation?.[0] ||
                        'Data tidak valid';

                    triggerAlert('error', message);
                    return;
                }

                // UNAUTHORIZED
                if (e.response && e.response.status === 401) {
                    triggerAlert(
                        'error',
                        'Sesi Anda sudah habis, silakan login ulang',
                    );
                    return;
                }

                console.log(e);

                triggerAlert('error', 'Terjadi kesalahan, silakan coba lagi');
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
