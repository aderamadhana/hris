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
                                    <font-awesome-icon icon="building" />
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
                                                :icon="
                                                    currentCoords
                                                        ? faRotate
                                                        : faLocationCrosshairs
                                                "
                                                class="btn-ic"
                                            />
                                            <font-awesome-icon
                                                v-else
                                                :icon="faSpinner"
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

                                        <!--
                    <button
                      v-if="divisi?.latitude && divisi?.longitude"
                      type="button"
                      class="btn btn-secondary"
                      @click="openTargetMap"
                    >
                      <font-awesome-icon :icon="faMapLocationDot" class="btn-ic" />
                      Buka Titik Presensi
                    </button>
                    -->
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
                                        — Jarak: {{ distanceText }}</template
                                    >
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
                                    <font-awesome-icon :icon="faClock" />
                                </span>
                                <h3 class="side-title">Riwayat Hari Ini</h3>
                            </div>
                            <span class="side-date">{{ todayLabel }}</span>
                        </div>

                        <div class="side-section">
                            <div class="side-label">
                                <font-awesome-icon :icon="faRightToBracket" />
                                Clock In
                            </div>

                            <div v-if="firstIn" class="side-block">
                                <div class="side-time">{{ firstIn.time }}</div>

                                <div class="side-location">
                                    <span class="side-location-icon">
                                        <font-awesome-icon
                                            :icon="faLocationDot"
                                        />
                                    </span>
                                    <span class="side-location-text">
                                        {{ firstIn.location }}
                                    </span>
                                </div>

                                <div
                                    class="side-note"
                                    :class="getStatusClass(firstIn.status)"
                                >
                                    <font-awesome-icon
                                        :icon="
                                            firstIn.status === 'hadir'
                                                ? faCircleCheck
                                                : faCircleExclamation
                                        "
                                    />
                                    {{ firstIn.note }}
                                </div>

                                <!-- Detail Info -->
                                <div class="side-detail">
                                    <div
                                        class="detail-item"
                                        v-if="firstIn.jarak"
                                    >
                                        <span class="detail-label">Jarak:</span>
                                        <span class="detail-value"
                                            >{{
                                                Math.round(firstIn.jarak)
                                            }}m</span
                                        >
                                    </div>
                                    <div
                                        class="detail-item"
                                        v-if="firstIn.akurasi"
                                    >
                                        <span class="detail-label"
                                            >Akurasi GPS:</span
                                        >
                                        <span class="detail-value"
                                            >±{{
                                                Math.round(firstIn.akurasi)
                                            }}m</span
                                        >
                                    </div>
                                </div>

                                <!-- Foto Preview -->
                                <div v-if="firstIn.foto" class="side-photo">
                                    <img
                                        :src="firstIn.foto"
                                        alt="Foto Clock In"
                                    />
                                </div>
                            </div>

                            <div v-else class="side-empty">
                                <font-awesome-icon
                                    :icon="faClock"
                                    class="empty-icon"
                                />
                                <span>Belum ada data clock in</span>
                            </div>
                        </div>

                        <!-- Clock Out Section -->
                        <div class="side-section">
                            <div class="side-label">
                                <font-awesome-icon :icon="faRightFromBracket" />
                                Clock Out
                            </div>

                            <div v-if="lastOut" class="side-block">
                                <div class="side-time">{{ lastOut.time }}</div>

                                <div class="side-location">
                                    <span class="side-location-icon">
                                        <font-awesome-icon
                                            icon="location-dot"
                                        />
                                    </span>
                                    <span class="side-location-text">
                                        {{ lastOut.location }}
                                    </span>
                                </div>

                                <div
                                    class="side-note"
                                    :class="getStatusClass(lastOut.status)"
                                >
                                    <font-awesome-icon
                                        :icon="
                                            lastOut.status === 'hadir'
                                                ? faCircleCheck
                                                : faCircleExclamation
                                        "
                                    />
                                    {{ lastOut.note }}
                                </div>

                                <!-- Detail Info -->
                                <div class="side-detail">
                                    <div
                                        class="detail-item"
                                        v-if="lastOut.jarak"
                                    >
                                        <span class="detail-label">Jarak:</span>
                                        <span class="detail-value"
                                            >{{
                                                Math.round(lastOut.jarak)
                                            }}m</span
                                        >
                                    </div>
                                    <div
                                        class="detail-item"
                                        v-if="lastOut.akurasi"
                                    >
                                        <span class="detail-label"
                                            >Akurasi GPS:</span
                                        >
                                        <span class="detail-value"
                                            >±{{
                                                Math.round(lastOut.akurasi)
                                            }}m</span
                                        >
                                    </div>
                                </div>

                                <!-- Foto Preview -->
                                <div v-if="lastOut.foto" class="side-photo">
                                    <img
                                        :src="lastOut.foto"
                                        alt="Foto Clock Out"
                                    />
                                </div>
                            </div>

                            <div v-else class="side-empty waiting">
                                <font-awesome-icon
                                    :icon="faHourglassHalf"
                                    class="empty-icon"
                                />
                                <span>Menunggu clock out</span>
                            </div>
                        </div>

                        <!-- Total Jam Kerja (jika sudah clock out) -->
                        <div v-if="firstIn && lastOut" class="side-section">
                            <div class="side-label">
                                <font-awesome-icon :icon="faBriefcase" />
                                Total Jam Kerja
                            </div>
                            <div class="side-block total-work">
                                <div class="work-hours">
                                    {{
                                        calculateWorkHours(
                                            firstIn.time,
                                            lastOut.time,
                                        )
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- Refresh Button -->
                        <div class="side-actions">
                            <Button
                                variant="success"
                                :loading="isRefresh"
                                @click="refreshLog"
                                ><font-awesome-icon icon="rotate" /> Refresh
                                Data</Button
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script>
import Button from '@/components/Button.vue';
import CameraCapture from '@/components/CameraCapture.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import {
    faBriefcase,
    faBuilding,
    faCircleCheck,
    faCircleExclamation,
    faCircleInfo,
    faCircleXmark,
    faClock,
    faHourglassHalf,
    faLocationCrosshairs,
    faLocationDot,
    faRightFromBracket,
    faRightToBracket,
    faRotate,
    faSatelliteDish,
    faSpinner,
    faTriangleExclamation,
} from '@fortawesome/free-solid-svg-icons';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    name: 'AttendancePage',
    components: {
        CameraCapture,
        AppLayout,
        Button,
    },

    data() {
        const page = usePage();

        return {
            faBuilding,
            faRotate,
            faLocationCrosshairs,
            faSpinner,
            faClock,
            faRightToBracket,
            faRightFromBracket,
            faLocationDot,
            faCircleCheck,
            faCircleExclamation,
            faHourglassHalf,
            faBriefcase,
            faTriangleExclamation,
            faSatelliteDish,
            faCircleXmark,
            faCircleInfo,

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

            todayLog: null,
            firstIn: null,
            lastOut: null,
            isRefresh: false,
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

        // firstIn() {
        //     return this.todayAttendance.find((x) => x.type === 'Masuk') || null;
        // },

        // lastOut() {
        //     const outs = this.todayAttendance.filter(
        //         (x) => x.type === 'Pulang',
        //     );
        //     return outs.length ? outs[outs.length - 1] : null;
        // },

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
        this.fetchTodayLog();
    },

    beforeUnmount() {
        if (this.timer) clearInterval(this.timer);
    },

    methods: {
        async fetchTodayLog() {
            this.loading = true;
            const employee_id = this.user.employee_id;

            try {
                const response = await axios.get('/presensi/log-harian', {
                    params: {
                        employee_id: employee_id,
                        tanggal: new Date().toISOString().split('T')[0], // Format: YYYY-MM-DD
                    },
                });

                if (response.data.success) {
                    this.todayLog = response.data.data;
                    this.processLogData();
                }
            } catch (error) {
                console.error('Error fetching log:', error);
                triggerAlert('error', 'Gagal memuat data presensi hari ini');
            } finally {
                this.loading = false;
            }
            this.isRefresh = false;
        },

        processLogData() {
            if (
                !this.todayLog ||
                !this.todayLog.detail ||
                this.todayLog.detail.length === 0
            ) {
                this.firstIn = null;
                this.lastOut = null;
                return;
            }

            // Cari clock in (masuk) pertama
            const clockInData = this.todayLog.detail.find(
                (item) => item.jenis_presensi === 'masuk',
            );

            console.log(this.todayLog);

            if (clockInData) {
                this.firstIn = {
                    time: clockInData.waktu_formatted, // Format: HH:mm
                    location:
                        this.todayLog?.divisi?.nama_divisi ||
                        'Lokasi tidak tersedia',
                    note: this.getStatusNote(clockInData.status),
                    status: clockInData.status,
                    foto: clockInData.foto_presensi,
                    jarak: clockInData.jarak_dari_lokasi,
                    akurasi: clockInData.akurasi_gps,
                };
            } else {
                this.firstIn = null;
            }

            // Cari clock out (pulang) terakhir
            const clockOutData = this.todayLog.detail
                .slice()
                .reverse()
                .find((item) => item.jenis_presensi === 'pulang');

            if (clockOutData) {
                this.lastOut = {
                    time: clockOutData.waktu_formatted,
                    location:
                        this.todayLog?.divisi?.nama_divisi ||
                        'Lokasi tidak tersedia',
                    note: this.getStatusNote(clockOutData.status),
                    status: clockOutData.status,
                    foto: clockOutData.foto_presensi,
                    jarak: clockOutData.jarak_dari_lokasi,
                    akurasi: clockOutData.akurasi_gps,
                };
            } else {
                this.lastOut = null;
            }
        },

        getStatusNote(status) {
            const statusNotes = {
                hadir: 'Presensi berhasil',
                terlambat: 'Terlambat masuk',
                tidak_valid: 'Di luar jangkauan',
                perlu_verifikasi: 'Perlu verifikasi admin',
            };

            return statusNotes[status] || 'Status tidak diketahui';
        },

        getStatusClass(status) {
            const statusClasses = {
                hadir: 'status-success',
                terlambat: 'status-warning',
                tidak_valid: 'status-danger',
                perlu_verifikasi: 'status-info',
            };

            return statusClasses[status] || 'status-default';
        },

        // Method untuk refresh data setelah presensi berhasil
        refreshLog() {
            this.isRefresh = true;
            this.fetchTodayLog();
        },

        calculateWorkHours(clockIn, clockOut) {
            if (!clockIn || !clockOut) return '-';

            const [inHour, inMin] = clockIn.split(':').map(Number);
            const [outHour, outMin] = clockOut.split(':').map(Number);

            const inMinutes = inHour * 60 + inMin;
            const outMinutes = outHour * 60 + outMin;

            const diffMinutes = outMinutes - inMinutes;

            if (diffMinutes < 0) return '-';

            const hours = Math.floor(diffMinutes / 60);
            const minutes = diffMinutes % 60;

            return `${hours} jam ${minutes} menit`;
        },
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
/* =========================
   LAYOUT
========================= */
.attendance-page {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.attendance-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 420px;
    gap: 24px;
    align-items: start;
}

.attendance-grid .card {
    grid-column: auto;
}

/* =========================
   KIRI (CAMERA)
========================= */
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
    font-weight: 700;
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
    font-size: clamp(28px, 5vw, 40px);
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    letter-spacing: clamp(0.08em, 0.9vw, 0.14em);
    color: #111827;
}

/* =========================
   KARTU LOKASI (KIRI)
========================= */
.att-location-card {
    width: 100%;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
    padding: 14px 18px 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
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
    flex: 0 0 auto;
}

.att-location-texts {
    display: flex;
    flex-direction: column;
    gap: 2px;
    width: 100%;
    text-align: left;
    min-width: 0;
}

.att-location-name {
    font-size: 14px;
    font-weight: 700;
    color: #111827;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.att-actions {
    display: flex;
    gap: 8px;
    margin: 8px 0 6px;
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
    line-height: 1.35;
}

.att-location-error {
    display: block;
    font-size: 12px;
    color: #b91c1c;
    margin-top: 4px;
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
    flex: 0 0 auto;
}

.att-distance-text {
    font-size: 13px;
    min-width: 0;
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

.camera-column .camera-capture {
    width: 100%;
}

/* =========================
   KANAN (SIDEBAR)
========================= */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 22px 20px 18px;
    min-width: 0;
}

/* Panduan */
.guide-block {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-title {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #111827;
}

.info-list {
    margin: 0;
    padding-left: 18px;
    font-size: 14px;
    color: #4b5563;
}

.info-list li + li {
    margin-top: 4px;
}

.info-note {
    font-size: 13px;
    color: #6b7280;
    padding: 10px 12px;
    border-radius: 12px;
    background: #f9fafb;
    border: 1px dashed #e5e7eb;
}

.side-divider {
    height: 1px;
    width: 100%;
    background: #e5e7eb;
}

/* Header riwayat */
.side-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 6px 2px 2px;
}

.side-title-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

.side-icon {
    font-size: 18px;
    flex: 0 0 auto;
}

.side-title {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.side-date {
    font-size: 12px;
    color: #64748b;
    white-space: nowrap;
}

/* =========================
   SECTION CARD (Clock In/Out/Total)
========================= */
.side-section {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #ffffff;
    padding: 14px;
}

/* label */
.side-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 10px;
}

/* Isi: grid supaya foto thumbnail kecil di kanan */
.side-block {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 72px;
    gap: 12px;
    align-items: start;
}

/* isi kiri */
.side-time {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    font-variant-numeric: tabular-nums;
}

.side-location {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    margin-top: 6px;
    color: #64748b;
    font-size: 13px;
    min-width: 0;
}

.side-location-icon {
    font-size: 13px;
    margin-top: 2px;
    flex: 0 0 auto;
}

.side-location-text {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    color: #4b5563;
}

/* Status pill */
.side-note {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    width: fit-content;
    margin-top: 8px;
}

.side-note.status-success {
    background: #f0fdf4;
    color: #166534;
}
.side-note.status-warning {
    background: #fef3c7;
    color: #92400e;
}
.side-note.status-danger {
    background: #fef2f2;
    color: #991b1b;
}
.side-note.status-info {
    background: #eff6ff;
    color: #1e40af;
}
.side-note.status-default {
    background: #f1f5f9;
    color: #475569;
}

/* Detail jadi chip kecil */
.side-detail {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
    padding: 0;
    background: transparent;
    border-radius: 0;
    font-size: 12px;
}

.detail-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
}

.detail-label {
    color: #64748b;
    font-weight: 800;
}

.detail-value {
    font-weight: 900;
    color: #0f172a;
}

/* Foto: thumbnail kecil */
.side-photo {
    justify-self: end;
    width: 72px;
    height: 72px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: #ffffff;
}

.side-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Empty state */
.side-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 10px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px dashed #e2e8f0;
    color: #64748b;
    font-size: 13px;
    text-align: center;
}

.side-empty.waiting {
    background: #fffbeb;
    border-color: #fde68a;
    color: #92400e;
}

.empty-icon {
    font-size: 18px;
    opacity: 0.85;
}

/* Total Work */
.total-work {
    text-align: center;
}

.work-hours {
    font-size: 1.25rem;
    font-weight: 900;
    color: #10b981;
}

/* Actions */
.side-actions {
    margin-top: 0.5rem;
}

.btn-refresh {
    width: 100%;
    padding: 0.75rem;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #475569;
    font-size: 0.875rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-refresh:hover {
    background: #e2e8f0;
    border-color: #cbd5e1;
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 1024px) {
    .attendance-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .camera-column {
        padding: 20px 16px 24px;
    }

    .right-column {
        padding: 20px 18px;
    }
}

@media (max-width: 520px) {
    .att-actions {
        width: 100%;
    }

    .att-actions .btn,
    .att-actions button {
        width: 100%;
        justify-content: center;
    }

    .side-section {
        padding: 12px;
    }

    /* di HP: foto jadi strip kecil bawah (tidak besar) */
    .side-block {
        grid-template-columns: minmax(0, 1fr);
    }

    .side-photo {
        width: 100%;
        height: 120px;
        justify-self: stretch;
    }

    .side-photo img {
        height: 120px;
    }
}

.right-column .side-section {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #fff;
    padding: 14px;
}

.right-column .side-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 10px;
}

/* KUNCI LAYOUT CLOCK IN/OUT */
.right-column .side-block {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 76px; /* kanan khusus thumbnail */
    grid-template-areas:
        'time   photo'
        'loc    photo'
        'note   photo'
        'detail photo';
    column-gap: 12px;
    row-gap: 8px;
    align-items: start;
    min-width: 0;
}

.right-column .side-time {
    grid-area: time;
    font-size: 18px;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.1;
    font-variant-numeric: tabular-nums;
}

.right-column .side-location {
    grid-area: loc;
    display: flex;
    align-items: flex-start;
    gap: 6px;
    color: #64748b;
    font-size: 13px;
    min-width: 0;
}

.right-column .side-location-text {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    color: #4b5563;
}

.right-column .side-note {
    grid-area: note;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    width: fit-content;
}

/* DETAIL JARAK/AKURASI JADI CHIP KECIL (BUKAN KOTAK BESAR) */
.right-column .side-detail {
    grid-area: detail;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 0 !important;
    background: transparent !important;
    border-radius: 0 !important;
}

.right-column .detail-item {
    display: inline-flex;
    align-items: baseline;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 12px;
}

.right-column .detail-label {
    color: #64748b;
    font-weight: 800;
}

.right-column .detail-value {
    color: #0f172a;
    font-weight: 900;
}

/* FOTO THUMBNAIL KECIL */
.right-column .side-photo {
    grid-area: photo;
    width: 76px;
    height: 76px;
    margin: 0 !important;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: #fff;
    justify-self: end;
    align-self: start;
}

.right-column .side-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Empty state biar rapi */
.right-column .side-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 10px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px dashed #e2e8f0;
    color: #64748b;
    font-size: 13px;
    text-align: center;
    font-style: normal !important;
}

.right-column .side-empty.waiting {
    background: #fffbeb;
    border-color: #fde68a;
    color: #92400e;
}

.right-column .empty-icon {
    font-size: 18px;
    opacity: 0.85;
}

/* Mobile: stack, foto jadi strip kecil (tetap tidak besar) */
@media (max-width: 520px) {
    .right-column .side-block {
        grid-template-columns: minmax(0, 1fr);
        grid-template-areas:
            'time'
            'loc'
            'note'
            'detail'
            'photo';
    }

    .right-column .side-photo {
        width: 100%;
        height: 120px;
        justify-self: stretch;
    }
}
</style>
