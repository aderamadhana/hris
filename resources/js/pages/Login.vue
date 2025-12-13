<template>
    <div class="auth-page">
        <div class="background-blur-1"></div>
        <div class="background-blur-2"></div>

        <div class="auth-container">
            <!-- LEFT: brand identity -->
            <div class="auth-left">
                <div class="brand-center">
                    <img
                        src="/assets/images/logo.png"
                        alt="PT Mitra Wira Mas"
                        class="brand-logo-login"
                    />
                </div>
            </div>

            <!-- RIGHT: login -->
            <div class="auth-right">
                <div class="login-card">
                    <h2>Masuk ke akun Anda</h2>
                    <p class="subtitle">
                        Gunakan NIK dan kata sandi yang sudah didaftarkan.
                    </p>

                    <form @submit.prevent="handleLogin">
                        <div class="form-group">
                            <label>NIK</label>
                            <input
                                v-model="nik"
                                type="text"
                                placeholder="Masukkan NIK"
                                :class="{ 'has-error': errors.nik }"
                                required
                            />
                            <small v-if="errors.nik" class="error">
                                {{ errors.nik[0] }}
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Kata Sandi</label>
                            <div class="password">
                                <input
                                    v-model="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="Masukkan kata sandi"
                                    minlength="5"
                                    :class="{ 'has-error': errors.password }"
                                    required
                                />
                                <button
                                    type="button"
                                    class="toggle-password"
                                    @click="showPassword = !showPassword"
                                >
                                    {{ showPassword ? '🙈' : '👁️' }}
                                </button>
                            </div>

                            <small v-if="errors.password" class="error">
                                {{ errors.password[0] }}
                            </small>

                            <div class="helper-row">
                                <small>* minimal 5 karakter</small>
                                <a class="forgot" href="#">Lupa Kata Sandi?</a>
                            </div>
                        </div>

                        <button
                            class="btn-login"
                            type="submit"
                            :disabled="processing"
                        >
                            <span v-if="!processing">Masuk</span>
                            <span v-else class="btn-loading">
                                <span class="spinner"></span>
                                Memproses...
                            </span>
                        </button>
                    </form>

                    <p class="footer-note">
                        Butuh bantuan? <a href="#">Hubungi admin HR</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            nik: '',
            password: '',
            processing: false,
            errors: {},
            showPassword: false,
        };
    },

    methods: {
        async handleLogin() {
            this.processing = true;
            this.errors = {};

            try {
                const response = await axios.post('/login', {
                    nik: this.nik,
                    password: this.password,
                });

                this.$inertia.visit('/dashboard');
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    console.error('Error login:', error);
                }
            } finally {
                this.processing = false;
                this.password = '';
            }
        },
    },
};
</script>

<style scoped>
/* ===== LAYOUT DASAR (LIGHT MODE) ===== */
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 16px;
    /* kombinasi radial + linear gradient */
    background:
        radial-gradient(circle at top left, #e0f2fe 0%, transparent 55%),
        radial-gradient(circle at bottom right, #ddd6fe 0%, transparent 55%),
        linear-gradient(135deg, #f9fafb 0%, #eef2ff 40%, #fefce8 100%);
    font-family:
        'Poppins',
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        'Segoe UI',
        sans-serif;
    color: #0f172a;
    position: relative;
    overflow: hidden;
}

/* blob blur background */
.background-blur-1,
.background-blur-2 {
    position: absolute;
    border-radius: 999px;
    filter: blur(70px);
    opacity: 0.7;
    pointer-events: none;
    z-index: 1; /* di atas pola grid, di bawah card */
}
.background-blur-1 {
    width: 320px;
    height: 320px;
    background: #93c5fd; /* biru lembut */
    top: -40px;
    right: -80px;
}
.background-blur-2 {
    width: 260px;
    height: 260px;
    background: #a7f3d0; /* hijau lembut */
    bottom: -60px;
    left: -60px;
}
/* error text */
.form-group .error {
    margin-top: 6px;
    color: #d93025; /* merah standar error */
    font-size: 0.85rem;
    line-height: 1.4;
}

/* input dengan error */
.form-group input.has-error {
    border-color: #d93025;
    background-color: #fff5f5;
}

/* focus state tetap jelas */
.form-group input.has-error:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(217, 48, 37, 0.2);
}

/* spacing agar tidak saling nabrak */
.form-group {
    display: flex;
    flex-direction: column;
}

.auth-container {
    position: relative;
    width: 100%;
    max-width: 1120px;
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
    gap: 32px;
    align-items: stretch;
    z-index: 2; /* di atas grid + blur */
}

/* ===== LEFT PANEL ===== */
.auth-left {
    padding: 32px;
    border-radius: 24px;
    background: linear-gradient(135deg, #ffffff, #f8fafc);
    border: 1px solid #e5e7eb;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);

    display: flex;
    flex-direction: column;
}

.brand-center {
    flex: 1; /* ambil sisa tinggi card */
    display: flex;
    flex-direction: column;
    align-items: center; /* horizontal center */
    justify-content: center; /* vertical center */
    transform: translateY(-6%); /* optical center */
}

/* supaya konten tidak “ketiban” efek pseudo-element */
.auth-left > * {
    position: relative;
    z-index: 1;
}

/* LOGO */
.brand-logo-login {
    width: 100%;
    filter: none; /* HAPUS glow */
}

/* TAGLINE */
.tagline {
    font-size: 12px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: #64748b;
}

/* chip */
.brand-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 999px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    font-size: 11px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #1e293b;
    width: fit-content;
}

.brand-chip .dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #22c55e;
}

.auth-left h1 {
    font-size: 30px;
    line-height: 1.25;
    font-weight: 700;
    margin-bottom: 10px;
    color: #0f172a;
}
.auth-left p {
    font-size: 14px;
    line-height: 1.7;
    color: #4b5563;
    max-width: 480px;
}

/* meta di bawah sendiri */
.left-meta {
    display: flex;
    gap: 16px;
    margin-top: auto;
    margin-bottom: 0;
    flex-wrap: wrap;
}
.meta-item {
    padding: 10px 12px;
    border-radius: 14px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    min-width: 170px;
}
.meta-label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6b7280;
    margin-bottom: 4px;
}
.meta-value {
    font-size: 13px;
    color: #111827;
}

.hero-image {
    display: block;
    margin-top: 18px;
    max-width: 460px;
    width: 100%;
    filter: drop-shadow(0 20px 45px rgba(15, 23, 42, 0.16));
    -webkit-mask-image: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 1) 80%,
        rgba(0, 0, 0, 0)
    );
}

/* ===== RIGHT PANEL ===== */
.auth-right {
    display: flex;
    justify-content: center;
    align-items: stretch;
}

.login-card {
    width: 100%;
    max-width: none;
    height: 100%;
    padding: 26px 24px 22px;
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    position: relative;
    overflow: hidden;

    display: flex;
    flex-direction: column;
}

/* agar teks/form tetap jelas di atas pola */
.login-card > * {
    position: relative;
    z-index: 1;
}

.card-logo {
    height: 50px;
    margin-bottom: 16px;
}

.login-card h2 {
    font-size: 22px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 6px;
}

.subtitle {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 18px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e7eb; /* aksen tipis pemisah header–form */
}

/* ===== FORM ===== */
.form-group {
    margin-bottom: 18px;
}

label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #111827;
    margin-bottom: 6px;
}

input {
    width: 100%;
    padding: 10px 12px;
    font-size: 14px;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    background: #ffffff;
    color: #111827;
    transition:
        border-color 0.18s ease,
        box-shadow 0.18s ease,
        background-color 0.18s ease;
}

input::placeholder {
    color: #9ca3af;
}

input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.3);
    background: #ffffff;
}

/* password */
.password {
    position: relative;
}
.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    padding: 4px;
}

.helper-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 6px;
}
small {
    font-size: 11px;
    color: #6b7280;
}
.forgot {
    font-size: 11px;
    color: #6366f1;
    text-decoration: none;
}
.forgot:hover {
    text-decoration: underline;
}

/* button utama */
.btn-login {
    margin-top: 12px;
    width: 100%;
    padding: 11px 14px;
    border-radius: 999px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #f9fafb;
    box-shadow: 0 16px 30px rgba(79, 70, 229, 0.35);
    transition:
        transform 0.12s ease,
        box-shadow 0.12s ease,
        opacity 0.12s ease;
}
.btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 18px 40px rgba(79, 70, 229, 0.4);
}
.btn-login:active {
    transform: translateY(0);
    box-shadow: 0 10px 22px rgba(79, 70, 229, 0.32);
}

/* divider */
.divider {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 18px 0 12px;
}
.divider span {
    flex: 1;
    height: 1px;
    background: #e5e7eb;
}
.divider p {
    font-size: 11px;
    color: #9ca3af;
}

/* button outline */
.btn-outline {
    width: 100%;
    padding: 10px 14px;
    border-radius: 999px;
    border: 1px solid #d1d5db;
    background: #ffffff;
    font-size: 13px;
    font-weight: 500;
    color: #111827;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition:
        background-color 0.14s ease,
        border-color 0.14s ease,
        box-shadow 0.14s ease;
}
.btn-outline:hover {
    background: #f9fafb;
    border-color: #a5b4fc;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
}

/* footer kecil */
.footer-note {
    margin-top: 14px;
    font-size: 11px;
    color: #6b7280;
    text-align: center;
}
.footer-note a {
    color: #6366f1;
    text-decoration: none;
}
.footer-note a:hover {
    text-decoration: underline;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 960px) {
    .auth-container {
        grid-template-columns: minmax(0, 1fr);
        max-width: 540px;
    }

    .auth-left {
        padding: 24px 20px;
    }

    .auth-left h1 {
        font-size: 24px;
    }

    .hero-image {
        max-width: 360px;
        margin-inline: auto;
    }

    .login-card {
        margin-top: 10px;
        height: auto;
    }
}

@media (max-width: 640px) {
    .auth-page {
        padding: 20px 12px;
    }

    .auth-left {
        display: none; /* kalau mau tetap tampil di HP, hapus baris ini */
    }

    .login-card {
        max-width: 100%;
        padding: 22px 18px 18px;
    }
}

.btn-login:disabled {
    opacity: 0.85;
    cursor: not-allowed;
}

.btn-loading {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

/* spinner kecil, halus, tidak norak */
.spinner {
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
