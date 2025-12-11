<template>
    <AppLayout>
        <section class="page attendance-page">
            <!-- HEADER -->
            <div class="page-header">
                <div>
                    <h2 class="page-title">Absensi Kehadiran</h2>
                    <p class="page-subtitle">
                        Catat kehadiran dengan foto realtime, jam otomatis, dan
                        lokasi akses saat ini.
                    </p>
                </div>
            </div>

            <!-- GRID: KIRI FOTO | KANAN PANDUAN + RIWAYAT -->
            <div class="attendance-grid">
                <!-- KIRI: FOTO / CAMERA -->

                <div class="card camera-column">
                    <div class="camera-header">
                        <h3 class="camera-title">Ambil Foto Kehadiran</h3>
                        <p class="camera-subtitle">
                            Pastikan wajah jelas, hanya satu orang di frame, dan
                            pencahayaan cukup.
                        </p>
                    </div>
                    <div class="right-top">
                        <div class="att-date">{{ todayLabel }}</div>
                        <div class="att-clock">{{ timeText }}</div>

                        <div class="att-location-card">
                            <div class="att-location-header">
                                <div class="att-location-icon">🏢</div>
                                <div class="att-location-texts">
                                    <div class="att-location-name">
                                        {{ branchName }}
                                    </div>
                                    <div class="att-location-address">
                                        <span v-if="locationLoading">
                                            Mendeteksi lokasi Anda...
                                        </span>
                                        <span v-else>{{ branchAddress }}</span>
                                        <span
                                            v-if="locationError"
                                            class="att-location-error"
                                        >
                                            {{ locationError }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="att-distance-row"
                                :class="{
                                    'att-distance-ok': inRange,
                                    'att-distance-bad': !inRange,
                                }"
                            >
                                <span class="att-distance-symbol">
                                    {{ inRange ? '✅' : '⚠️' }}
                                </span>
                                <span class="att-distance-text">
                                    Jarak: {{ distanceText }}.
                                    {{
                                        inRange
                                            ? 'Dalam jangkauan absen.'
                                            : 'Di luar jangkauan.'
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <CameraCapture />
                </div>

                <!-- KANAN: JAM + LOKASI + PANDUAN + RIWAYAT -->
                <div class="card right-column">
                    <!-- PANDUAN -->
                    <div class="guide-block">
                        <h3 class="info-title">Panduan Singkat</h3>
                        <ul class="info-list">
                            <li>Gunakan posisi tegak, hadap ke kamera.</li>
                            <li>Pastikan hanya satu orang dalam frame.</li>
                            <li>Jarak ideal sekitar 0.5–1 meter.</li>
                            <li>
                                Jika kamera tidak muncul, cek izin akses browser
                                / aplikasi.
                            </li>
                        </ul>

                        <div class="info-note">
                            Data foto digunakan sebagai bukti kehadiran sesuai
                            kebijakan HR perusahaan.
                        </div>
                        <div class="side-divider"></div>
                        <div class="side-header">
                            <div class="side-title-wrap">
                                <span class="side-icon">🕒</span>
                                <h3 class="side-title">Riwayat Hari Ini</h3>
                            </div>
                            <span class="side-date">{{ todayLabel }}</span>
                        </div>

                        <!-- RIWAYAT HARI INI -->

                        <div class="side-section">
                            <div class="side-label">Clock In</div>

                            <div v-if="firstIn" class="side-block">
                                <div class="side-time">{{ firstIn.time }}</div>
                                <div class="side-location">
                                    <span class="side-location-icon">📍</span>
                                    <span class="side-location-text">
                                        {{ firstIn.location }}
                                    </span>
                                </div>
                                <div class="side-note">
                                    {{ firstIn.note }}
                                </div>
                            </div>
                            <div v-else class="side-empty">
                                Belum ada data clock in.
                            </div>
                        </div>

                        <div class="side-section">
                            <div class="side-label">Clock Out</div>

                            <div v-if="lastOut" class="side-block">
                                <div class="side-time">{{ lastOut.time }}</div>
                                <div class="side-location">
                                    <span class="side-location-icon">📍</span>
                                    <span class="side-location-text">
                                        {{ lastOut.location }}
                                    </span>
                                </div>
                                <div class="side-note">
                                    {{ lastOut.note }}
                                </div>
                            </div>
                            <div v-else class="side-empty">
                                – Menunggu clock out –
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script>
import CameraCapture from '@/components/CameraCapture.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/Utils/alert';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

export default {
    name: 'AttendancePage',
    components: {
        CameraCapture,
        AppLayout,
    },
    setup() {
        /** JAM BERJALAN **/
        const now = ref(new Date());
        let timer = null;

        const timeText = computed(() =>
            now.value.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            }),
        );

        const todayLabel = computed(() =>
            now.value.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            }),
        );

        /** LOKASI SAAT INI + REVERSE GEOCODE **/
        const branchName = 'Kantor Malang';
        const branchAddress = ref('Mendeteksi lokasi Anda...');
        const locationLoading = ref(true);
        const locationError = ref('');
        const currentCoords = ref(null);

        const reverseGeocode = async (lat, lon) => {
            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}&accept-language=id`;
                const res = await fetch(url);
                if (!res.ok) throw new Error('HTTP error');
                const data = await res.json();

                if (data.display_name) return data.display_name;

                if (data.address) {
                    const a = data.address;
                    return [
                        a.road,
                        a.suburb || a.village,
                        a.city || a.town || a.county,
                        a.state,
                        a.postcode,
                        a.country,
                    ]
                        .filter(Boolean)
                        .join(', ');
                }

                return `Lat: ${lat.toFixed(5)}, Lng: ${lon.toFixed(5)}`;
            } catch (e) {
                console.error('Reverse geocode error', e);
                return `Lat: ${lat.toFixed(5)}, Lng: ${lon.toFixed(5)}`;
            }
        };

        const initGeolocation = () => {
            if (!('geolocation' in navigator)) {
                locationError.value = 'Browser tidak mendukung geolokasi.';
                branchAddress.value = 'Lokasi tidak tersedia';
                locationLoading.value = false;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const { latitude, longitude } = pos.coords;
                    currentCoords.value = { latitude, longitude };

                    const addr = await reverseGeocode(latitude, longitude);
                    branchAddress.value = addr;
                    locationLoading.value = false;
                },
                (err) => {
                    locationError.value =
                        err.code === 1
                            ? 'Izin lokasi ditolak.'
                            : 'Lokasi tidak dapat diambil.';
                    branchAddress.value = 'Lokasi tidak tersedia';
                    locationLoading.value = false;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                },
            );
        };

        onMounted(() => {
            timer = setInterval(() => {
                now.value = new Date();
            }, 1000);

            initGeolocation();
        });

        onBeforeUnmount(() => {
            if (timer) clearInterval(timer);
        });

        /** JARAK / STATUS JANGKAUAN – MASIH DUMMY */
        const distanceText = '1042 meter';
        const inRange = false;

        /** RIWAYAT ABSEN DUMMY */
        const todayAttendance = ref([
            {
                time: '07.43.54',
                type: 'Masuk',
                note: 'Shift pagi',
                location:
                    'RW 12, Kel. Bunulrejo, Kota Malang, Jawa Timur, 65123',
            },
            // contoh clock out:
            // {
            //   time: '17.02.10',
            //   type: 'Pulang',
            //   note: 'Selesai shift',
            //   location: 'Kantor Malang',
            // },
        ]);

        const firstIn = computed(
            () => todayAttendance.value.find((x) => x.type === 'Masuk') || null,
        );

        const lastOut = computed(() => {
            const outs = todayAttendance.value.filter(
                (x) => x.type === 'Pulang',
            );
            if (!outs.length) return null;
            return outs[outs.length - 1];
        });

        /** CLOCK (DUMMY) */
        const isCheckedIn = ref(true);

        const handleClock = () => {
            const label = isCheckedIn.value ? 'Clock Out' : 'Clock In';
            triggerAlert('warning', `${label} belum dihubungkan ke backend`);
            isCheckedIn.value = !isCheckedIn.value;
        };

        return {
            // time
            now,
            timeText,
            todayLabel,

            // lokasi
            branchName,
            branchAddress,
            locationLoading,
            locationError,
            currentCoords,

            // jarak / status
            distanceText,
            inRange,

            // riwayat absen
            todayAttendance,
            firstIn,
            lastOut,

            // clock
            isCheckedIn,
            handleClock,
        };
    },
};
</script>

<style scoped>
.attendance-page {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* GRID: kiri foto, kanan panduan+riwayat */
.attendance-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1.4fr);
    gap: 24px;
}

/* Override aturan global .card */
.attendance-grid .card {
    grid-column: auto;
}

/* KIRI: CAMERA */
.camera-column {
    display: flex;
    flex-direction: column;
    align-items: center; /* kamera tetap center secara visual */
    gap: 18px;
    padding: 28px 22px 32px;
}

/* header tetap full-width dan rata kiri */
.camera-header {
    align-self: stretch; /* <-- biar header melebar full */
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.camera-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.camera-subtitle {
    margin: 0;
    font-size: 14px;
    color: #6b7280;
}

/* KANAN: TOP (JAM+LOKASI) + PANDUAN + RIWAYAT */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 22px 20px 18px;
}

.right-top {
    width: 100%;
    align-self: stretch; /* ikut lebar card */
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 12px;
    margin-top: 4px;
}

.att-date {
    font-size: 14px;
    color: #6b7280;
}

.att-clock {
    font-size: 40px;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
    letter-spacing: 0.14em;
    color: #111827;
}

/* KARTU LOKASI */
.att-location-card {
    width: 100%;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
    padding: 14px 18px 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.att-location-header {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.att-location-icon {
    width: 26px;
    height: 26px;
    border-radius: 999px;
    background: #fee2e2;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.att-location-texts {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.att-location-name {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.att-location-address {
    font-size: 13px;
    color: #4b5563;
}

.att-location-error {
    display: block;
    font-size: 12px;
    color: #b91c1c;
    margin-top: 2px;
}

/* jarak */
.att-distance-row {
    margin-top: 4px;
    padding-top: 6px;
    border-top: 1px dashed #e5e7eb;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
}

.att-distance-symbol {
    font-size: 14px;
}

.att-distance-text {
    font-size: 13px;
}

.att-distance-ok {
    color: #15803d;
}

.att-distance-bad {
    color: #b91c1c;
}

/* PANDUAN */
.guide-block {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.info-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
}

.info-list {
    margin: 4px 0 0;
    padding-left: 18px;
    font-size: 14px;
    color: #4b5563;
}

.info-list li + li {
    margin-top: 4px;
}

.info-note {
    margin-top: 6px;
    font-size: 13px;
    color: #6b7280;
    padding: 8px 10px;
    border-radius: 10px;
    background: #f9fafb;
    border: 1px dashed #e5e7eb;
}

.side-divider {
    height: 1px;
    width: 100%;
    background: #e5e7eb;
}

/* RIWAYAT */
.side-header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 10px;
}

.side-title-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
}

.side-icon {
    font-size: 18px;
}

.side-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
}

.side-date {
    font-size: 12px;
    color: #6b7280;
}

.side-section {
    padding-top: 8px;
    border-top: 1px solid #e5e7eb;
}

.side-label {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 4px;
}

.side-block {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.side-time {
    font-size: 18px;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
    color: #111827;
}

.side-location {
    display: flex;
    align-items: flex-start;
    gap: 4px;
    margin-top: 2px;
}

.side-location-icon {
    font-size: 13px;
    margin-top: 2px;
}

.side-location-text {
    font-size: 13px;
    color: #4b5563;
}

.side-note {
    font-size: 13px;
    color: #6b7280;
}

.side-empty {
    font-size: 13px;
    color: #9ca3af;
    font-style: italic;
    padding: 4px 0 2px;
}

.camera-column .camera-capture {
    width: 100%;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .attendance-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .camera-column {
        padding: 20px 16px 24px;
    }

    .att-clock {
        font-size: 34px;
        letter-spacing: 0.12em;
    }

    .right-column {
        padding: 20px 18px;
    }
}
</style>
