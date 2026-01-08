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

            <div class="attendance-grid">
                <!-- KIRI -->
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
                                <div class="att-location-icon">
                                    <font-awesome-icon
                                        :icon="['fas', 'building']"
                                    />
                                </div>

                                <div class="att-location-texts">
                                    <div class="att-location-name">
                                        {{
                                            perusahaan?.nama_perusahaan ||
                                            'Perusahaan -'
                                        }}
                                        -
                                        {{ divisi?.nama_divisi || 'Divisi -' }}
                                    </div>

                                    <div class="att-actions">
                                        <button
                                            type="button"
                                            @click="refreshLocation"
                                            :disabled="locationLoading"
                                            class="btn btn-primary"
                                        >
                                            <font-awesome-icon
                                                v-if="!locationLoading"
                                                :icon="[
                                                    'fas',
                                                    currentCoords
                                                        ? 'rotate'
                                                        : 'location-crosshairs',
                                                ]"
                                                class="btn-ic"
                                            />
                                            <font-awesome-icon
                                                v-else
                                                :icon="['fas', 'spinner']"
                                                spin
                                                class="btn-ic"
                                            />
                                            {{
                                                locationLoading
                                                    ? 'Mengambil...'
                                                    : currentCoords
                                                      ? 'Ambil Ulang Lokasi'
                                                      : 'Ambil Lokasi'
                                            }}
                                        </button>

                                        <!-- <button
                                            v-if="
                                                divisi?.latitude &&
                                                divisi?.longitude
                                            "
                                            type="button"
                                            class="btn btn-secondary"
                                            @click="openTargetMap"
                                        >
                                            <font-awesome-icon
                                                :icon="[
                                                    'fas',
                                                    'map-location-dot',
                                                ]"
                                                class="btn-ic"
                                            />
                                            Buka Titik Presensi
                                        </button> -->
                                    </div>

                                    <div class="att-meta">
                                        <span
                                            ><b>Radius:</b>
                                            {{ radiusText }}</span
                                        >
                                        <span
                                            ><b>Akurasi:</b>
                                            {{ accuracyText }}</span
                                        >
                                        <span v-if="lastLocationAt"
                                            ><b>Update:</b>
                                            {{ lastLocationAt }}</span
                                        >
                                    </div>

                                    <div class="att-location-address">
                                        <span v-if="locationStatus === 'idle'">
                                            {{ locationStatusHint }}
                                        </span>

                                        <span
                                            v-else-if="
                                                locationStatus === 'loading'
                                            "
                                        >
                                            Mengambil lokasi dari perangkat...
                                        </span>

                                        <span v-else>
                                            {{ branchAddress }}
                                        </span>

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
                                    'att-distance-ok': locationStatus === 'ok',
                                    'att-distance-bad':
                                        locationStatus === 'out' ||
                                        locationStatus === 'error',
                                    'att-distance-warn':
                                        locationStatus === 'poor_gps',
                                    'att-distance-idle':
                                        locationStatus === 'idle',
                                }"
                            >
                                <span class="att-distance-symbol">
                                    <font-awesome-icon
                                        :icon="statusIcon"
                                        class="att-fa"
                                    />
                                </span>

                                <span class="att-distance-text">
                                    <b>{{ locationStatusLabel }}</b>
                                    <template v-if="currentCoords">
                                        — Jarak: {{ distanceText }}
                                    </template>
                                    <template
                                        v-if="
                                            locationStatusHint &&
                                            locationStatus !== 'ok'
                                        "
                                    >
                                        · {{ locationStatusHint }}
                                    </template>
                                </span>
                            </div>
                        </div>
                    </div>

                    <CameraCapture v-bind="cameraProps" />
                </div>

                <!-- KANAN -->
                <div class="card right-column">
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
                                <span class="side-icon">
                                    <font-awesome-icon
                                        :icon="['fas', 'clock']"
                                    />
                                </span>
                                <h3 class="side-title">Riwayat Hari Ini</h3>
                            </div>
                            <span class="side-date">{{ todayLabel }}</span>
                        </div>

                        <div class="side-section">
                            <div class="side-label">Clock In</div>

                            <div v-if="firstIn" class="side-block">
                                <div class="side-time">{{ firstIn.time }}</div>
                                <div class="side-location">
                                    <span class="side-location-icon">
                                        <font-awesome-icon
                                            :icon="['fas', 'location-dot']"
                                        />
                                    </span>
                                    <span class="side-location-text">
                                        {{ firstIn.location }}
                                    </span>
                                </div>
                                <div class="side-note">{{ firstIn.note }}</div>
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
                                    <span class="side-location-icon">
                                        <font-awesome-icon
                                            :icon="['fas', 'location-dot']"
                                        />
                                    </span>
                                    <span class="side-location-text">
                                        {{ lastOut.location }}
                                    </span>
                                </div>
                                <div class="side-note">{{ lastOut.note }}</div>
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
import { triggerAlert } from '@/utils/alert';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    name: 'AttendancePage',
    components: {
        CameraCapture,
        AppLayout,
    },

    data() {
        const page = usePage();

        return {
            user: page.props.auth.user,

            // jam berjalan
            now: new Date(),
            timer: null,

            // lokasi (manual refresh)
            branchName: 'Lokasi Anda',
            branchAddress: 'Klik "Ambil Lokasi" untuk mendeteksi lokasi.',
            locationLoading: false,
            locationError: '',
            currentCoords: null,
            lastLocationAt: null,

            // target lokasi (divisi terakhir dari backend)
            divisi: null,
            perusahaan: null,
            history: [],

            // hasil hitung jarak
            distanceMetersValue: null,
            distanceText: '-',
            inRange: false,

            // hard lock presensi + kualitas GPS
            gpsAccuracyMax: 100, // meter

            // dummy riwayat absen
            todayAttendance: [
                {
                    time: '07.43.54',
                    type: 'Masuk',
                    note: 'Shift pagi',
                    location:
                        'RW 12, Kel. Bunulrejo, Kota Malang, Jawa Timur, 65123',
                },
            ],

            // dummy clock state
            isCheckedIn: true,

            loading: true,
            withinRadius: false,
            gpsOk: false,
        };
    },

    computed: {
        timeText() {
            return this.now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            });
        },

        todayLabel() {
            return this.now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });
        },

        firstIn() {
            return this.todayAttendance.find((x) => x.type === 'Masuk') || null;
        },

        lastOut() {
            const outs = this.todayAttendance.filter(
                (x) => x.type === 'Pulang',
            );
            return outs.length ? outs[outs.length - 1] : null;
        },

        // ===== UX status =====
        locationStatus() {
            if (this.locationLoading) return 'loading';
            if (this.locationError) return 'error';
            if (!this.currentCoords) return 'idle';

            // gunakan flag hasil recalcDistance
            if (!this.gpsOk) return 'poor_gps';
            if (this.withinRadius) return 'ok';
            return 'out';
        },

        locationStatusLabel() {
            switch (this.locationStatus) {
                case 'loading':
                    return 'Mengambil lokasi...';
                case 'error':
                    return 'Gagal ambil lokasi';
                case 'idle':
                    return 'Belum ambil lokasi';
                case 'poor_gps':
                    return 'GPS kurang akurat';
                case 'ok':
                    return 'Dalam jangkauan';
                case 'out':
                    return 'Di luar jangkauan';
                default:
                    return '';
            }
        },

        locationStatusHint() {
            switch (this.locationStatus) {
                case 'idle':
                    return 'Klik tombol untuk ambil lokasi. Pastikan izin lokasi aktif.';
                case 'poor_gps':
                    return 'Akurasi GPS terlalu besar. Pindah ke area terbuka / dekat jendela, lalu coba lagi.';
                case 'out':
                    return `Dekati titik presensi divisi (radius ${this.divisi?.radius_presensi ?? '-'} m) lalu ambil ulang lokasi.`;
                case 'error':
                    return 'Cek izin lokasi browser / HTTPS, lalu coba lagi.';
                default:
                    return '';
            }
        },

        accuracyText() {
            const acc = this.currentCoords?.accuracy;
            return acc ? `±${Math.round(acc)} m` : '-';
        },

        radiusText() {
            const r = this.divisi?.radius_presensi;
            return r ? `${r} m` : '-';
        },

        statusIcon() {
            switch (this.locationStatus) {
                case 'ok':
                    return ['fas', 'circle-check'];
                case 'out':
                    return ['fas', 'triangle-exclamation'];
                case 'poor_gps':
                    return ['fas', 'satellite-dish'];
                case 'error':
                    return ['fas', 'circle-xmark'];
                default:
                    return ['fas', 'circle-info'];
            }
        },

        cameraProps() {
            return {
                // identitas untuk backend
                employeeId: this.user?.employee_id ?? null,
                userId: this.user?.id ?? null,

                perusahaanId: this.perusahaan?.id ?? null,
                divisiId: this.divisi?.id ?? null,

                // info target presensi (titik & radius)
                targetLat:
                    this.divisi?.latitude != null
                        ? Number(this.divisi.latitude)
                        : null,
                targetLng:
                    this.divisi?.longitude != null
                        ? Number(this.divisi.longitude)
                        : null,
                radiusPresensi: this.divisi?.radius_presensi ?? null,

                // lokasi user saat ini
                currentCoords: this.currentCoords, // { latitude, longitude, accuracy }
                currentLat: this.currentCoords?.latitude ?? null,
                currentLng: this.currentCoords?.longitude ?? null,
                accuracy: this.currentCoords?.accuracy ?? null,

                // hasil kalkulasi
                distanceMeters: this.distanceMetersValue,
                distanceText: this.distanceText,
                inRange: this.inRange,

                // status UI lokasi
                locationStatus: this.locationStatus,
                locationStatusLabel: this.locationStatusLabel,
                locationStatusHint: this.locationStatusHint,
                locationLoading: this.locationLoading,
                locationError: this.locationError,

                // alamat hasil reverse geocode + waktu refresh
                branchAddress: this.branchAddress,
                lastLocationAt: this.lastLocationAt,

                // konteks tampilan (opsional, kalau CameraCapture butuh)
                todayLabel: this.todayLabel,
                timeText: this.timeText,

                // state clock (opsional)
                isCheckedIn: this.isCheckedIn,
            };
        },
    },

    mounted() {
        this.timer = setInterval(() => {
            this.now = new Date();
        }, 1000);

        this.fetchEmployee();
    },

    beforeUnmount() {
        if (this.timer) clearInterval(this.timer);
    },

    methods: {
        // ===================== DISTANCE =====================
        distanceMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const toRad = (x) => (x * Math.PI) / 180;

            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);

            const a =
                Math.sin(dLat / 2) ** 2 +
                Math.cos(toRad(lat1)) *
                    Math.cos(toRad(lat2)) *
                    Math.sin(dLon / 2) ** 2;

            return 2 * R * Math.asin(Math.sqrt(a));
        },

        formatDistance(m) {
            if (m == null) return '-';
            if (m < 1000) return `${Math.round(m)} meter`;
            return `${(m / 1000).toFixed(2)} km`;
        },

        recalcDistance() {
            // reset biar ga nyangkut nilai lama
            this.distanceMetersValue = null;
            this.distanceText = '-';
            this.withinRadius = false;
            this.gpsOk = false;
            this.inRange = false;

            if (!this.currentCoords) return;

            const targetLat = Number(this.divisi?.latitude);
            const targetLng = Number(this.divisi?.longitude);
            if (!Number.isFinite(targetLat) || !Number.isFinite(targetLng))
                return;

            const userLat = Number(this.currentCoords.latitude);
            const userLng = Number(this.currentCoords.longitude);

            const radius = Number(this.divisi?.radius_presensi ?? 0);
            const acc = Number(this.currentCoords?.accuracy ?? 0);

            const m = this.distanceMeters(
                userLat,
                userLng,
                targetLat,
                targetLng,
            );
            this.distanceMetersValue = m;
            this.distanceText = this.formatDistance(m);

            // 1) cek radius
            this.withinRadius = radius > 0 ? m <= radius : false;

            // 2) cek kualitas GPS
            this.gpsOk =
                Number.isFinite(acc) &&
                acc > 0 &&
                acc <= Number(this.gpsAccuracyMax);

            // 3) INI yang dipakai untuk presensi (tombol capture)
            this.inRange = this.withinRadius && this.gpsOk;
        },

        // ===================== REVERSE GEOCODE =====================
        async reverseGeocode(lat, lon) {
            try {
                const url =
                    `https://nominatim.openstreetmap.org/reverse?format=jsonv2` +
                    `&lat=${lat}&lon=${lon}&accept-language=id`;

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
        },

        // ===================== MANUAL REFRESH LOCATION =====================
        refreshLocation() {
            if (!('geolocation' in navigator)) {
                this.locationError = 'Browser tidak mendukung geolokasi.';
                this.branchAddress = 'Lokasi tidak tersedia';
                this.currentCoords = null;
                this.recalcDistance();
                return;
            }

            if (this.locationLoading) return;

            this.locationLoading = true;
            this.locationError = '';
            this.branchAddress = 'Mengambil lokasi...';

            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const { latitude, longitude, accuracy } = pos.coords;

                    this.currentCoords = { latitude, longitude, accuracy };

                    this.lastLocationAt = new Date().toLocaleTimeString(
                        'id-ID',
                        {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                        },
                    );

                    const addr = await this.reverseGeocode(latitude, longitude);
                    this.branchAddress = addr;

                    this.recalcDistance();

                    this.locationLoading = false;
                },
                (err) => {
                    this.currentCoords = null;
                    this.inRange = false;
                    this.distanceText = '-';
                    this.lastLocationAt = null;

                    this.locationError =
                        err.code === 1
                            ? 'Izin lokasi ditolak / diblokir.'
                            : 'Lokasi tidak dapat diambil.';

                    this.branchAddress = 'Lokasi tidak tersedia';
                    this.locationLoading = false;

                    console.error('Geolocation error:', err);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                },
            );
        },

        openTargetMap() {
            const lat = Number(this.divisi?.latitude);
            const lng = Number(this.divisi?.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const url = `https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}#map=18/${lat}/${lng}`;
            window.open(url, '_blank');
        },

        // ===================== FETCH DIVISI TERAKHIR =====================
        fetchEmployee() {
            const employee_id = this.user.employee_id;

            axios
                .get(`/referensi/perusahaan-terakhir/${employee_id}`)
                .then((res) => {
                    this.divisi = res.data.divisi;
                    this.history = res.data.history;
                    this.perusahaan = res.data.perusahaan;

                    this.recalcDistance();
                })
                .catch((err) => {
                    console.error('fetchEmployee error', err);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        // ===================== CLOCK (DUMMY) =====================
        handleClock() {
            const label = this.isCheckedIn ? 'Clock Out' : 'Clock In';
            triggerAlert('warning', `${label} belum dihubungkan ke backend`);
            this.isCheckedIn = !this.isCheckedIn;
        },
    },
};
</script>

<style scoped>
.attendance-page {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.attendance-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1.4fr);
    gap: 24px;
}

.attendance-grid .card {
    grid-column: auto;
}

/* KIRI */
.camera-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 18px;
    padding: 28px 22px 32px;
}

.camera-header {
    align-self: stretch;
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

/* TOP */
.right-top {
    width: 100%;
    align-self: stretch;
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
    font-size: 14px;
}

.att-location-texts {
    display: flex;
    flex-direction: column;
    gap: 2px;
    width: 100%;
    text-align: left;
}

.att-location-name {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.att-actions {
    display: flex;
    gap: 8px;
    margin: 6px 0 6px;
    flex-wrap: wrap;
}

.att-meta {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 4px;
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
    margin-top: 6px;
    padding-top: 8px;
    border-top: 1px dashed #e5e7eb;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 13px;
    line-height: 1.35;
}

.att-distance-symbol {
    font-size: 14px;
    margin-top: 1px;
}

.att-distance-text {
    font-size: 13px;
}

.att-fa {
    font-size: 14px;
}

.btn-ic {
    margin-right: 6px;
}

/* status color */
.att-distance-ok {
    color: #15803d;
}

.att-distance-bad {
    color: #b91c1c;
}

.att-distance-warn {
    color: #b45309;
}

.att-distance-idle {
    color: #374151;
}

/* KANAN */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 22px 20px 18px;
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
