<script>
import AppLayout from '@/layouts/AppLayout.vue';
import { triggerAlert } from '@/utils/alert';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: '/assets/images/marker-icon-2x.png',
    iconUrl: '/assets/images/marker-icon.png',
    shadowUrl: '/assets/images/marker-shadow.png',
});

export default {
    components: { AppLayout, Link },

    data() {
        return {
            processing: false,
            errors: {},
            maps: [],

            // debounce + abort per divisi
            searchTimeouts: [null],
            searchControllers: [null],

            form: {
                kode_perusahaan: '',
                nama_perusahaan: '',
                alamat: '',
                status: 'aktif',
                divisi: [this.defaultDivisi()],
                tanggal_akhir_mou: '',
                tanggal_awal_mou: '',
            },
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.initMap(0);
        });
    },

    methods: {
        // ================= BASE =================
        defaultDivisi() {
            return {
                nama_divisi: '',
                status: 'aktif',
                search: '',
                searchResults: [],
                searching: false, // <- penting untuk spinner
                alamat_penempatan: '',
                latitude: null,
                longitude: null,
                radius_presensi: 100,
                tanggal_akhir_mou: '',
                tanggal_awal_mou: '',
            };
        },

        tambahDivisi() {
            const index = this.form.divisi.length;
            this.form.divisi.push(this.defaultDivisi());

            this.searchTimeouts.push(null);
            this.searchControllers.push(null);

            this.$nextTick(() => {
                this.initMap(index);
            });
        },

        hapusDivisi(index) {
            if (this.form.divisi.length === 1) {
                triggerAlert('warning', 'Minimal harus ada 1 divisi');
                return;
            }

            // stop debounce + abort request
            if (this.searchTimeouts[index])
                clearTimeout(this.searchTimeouts[index]);
            if (this.searchControllers[index])
                this.searchControllers[index].abort();

            // remove map instance
            if (this.maps[index]) this.maps[index].map.remove();

            this.form.divisi.splice(index, 1);
            this.maps.splice(index, 1);

            this.searchTimeouts.splice(index, 1);
            this.searchControllers.splice(index, 1);
        },

        // ================= MAP =================
        async initMap(index) {
            // Guard: jangan init ulang kalau sudah ada
            if (this.maps?.[index]?.map) {
                // kalau map muncul di tab/modal, kadang perlu refresh ukuran
                setTimeout(() => this.maps[index].map.invalidateSize(), 0);
                return;
            }

            // kalau ini Vue dan element dibuat via v-for, pastikan DOM sudah jadi
            if (this.$nextTick) await this.$nextTick();

            const el = document.getElementById(`map-${index}`);
            if (!el) return;

            // Pastikan container punya tinggi (kalau 0px, map/marker bisa "hilang")
            if (!el.style.height) el.style.height = '400px';

            const center = [-7.96662, 112.632632]; // Malang
            const map = L.map(el, { zoomControl: true }).setView(center, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 19,
            }).addTo(map);

            const marker = L.marker(center, {
                draggable: true,
                title: 'Drag untuk pindah lokasi',
                // icon: new L.Icon.Default(), // optional; default sudah di-fix via mergeOptions
            }).addTo(map);

            const radius = Number(
                this.form.divisi?.[index]?.radius_presensi ?? 0,
            );

            const circle = L.circle(center, {
                radius,
                color: '#2563eb',
                fillColor: '#2563eb',
                fillOpacity: 0.15,
                weight: 2,
            }).addTo(map);

            const updateLocation = async (latlng) => {
                marker.setLatLng(latlng);
                circle.setLatLng(latlng);

                const { lat, lng } = latlng;

                this.form.divisi[index].latitude = Number(lat.toFixed(6));
                this.form.divisi[index].longitude = Number(lng.toFixed(6));

                try {
                    const res = await axios.get(
                        'https://nominatim.openstreetmap.org/reverse',
                        {
                            params: { format: 'json', lat, lon: lng },
                            headers: { 'Accept-Language': 'id' },
                        },
                    );

                    this.form.divisi[index].alamat_penempatan =
                        res.data.display_name || '';
                } catch (e) {
                    console.warn('Reverse geocoding gagal:', e);
                }
            };

            map.on('click', (e) => updateLocation(e.latlng));
            marker.on('dragend', (e) => updateLocation(e.target.getLatLng()));

            this.maps[index] = { map, marker, circle };

            // Penting kalau map muncul di area yang baru dirender / modal / tab
            setTimeout(() => map.invalidateSize(), 0);
        },

        updateRadius(index) {
            const mapObj = this.maps[index];
            if (!mapObj) return;

            const radius =
                parseInt(this.form.divisi[index].radius_presensi) || 100;
            mapObj.circle.setRadius(radius);
        },

        // ================= SEARCH (DEBOUNCE + ABORT + SPINNER) =================
        async searchLocation(index) {
            const q = this.form.divisi[index].search.trim();

            // clear pending debounce
            if (this.searchTimeouts[index]) {
                clearTimeout(this.searchTimeouts[index]);
                this.searchTimeouts[index] = null;
            }

            // query pendek: reset
            if (!q || q.length < 3) {
                this.form.divisi[index].searchResults = [];
                this.form.divisi[index].searching = false;

                if (this.searchControllers[index]) {
                    this.searchControllers[index].abort();
                    this.searchControllers[index] = null;
                }
                return;
            }

            // debounce 400ms
            this.searchTimeouts[index] = setTimeout(() => {
                this.doSearchLocation(index, q);
            }, 400);
        },

        async doSearchLocation(index, q) {
            let controller; // <- biar aman dipakai di finally

            try {
                // abort request sebelumnya
                if (this.searchControllers[index]) {
                    this.searchControllers[index].abort();
                }

                controller = new AbortController();
                this.searchControllers[index] = controller;

                this.form.divisi[index].searching = true;

                const res = await axios.get(
                    'https://nominatim.openstreetmap.org/search',
                    {
                        params: {
                            q,
                            format: 'json',
                            limit: 5,
                            countrycodes: 'id',
                        },
                        headers: { 'Accept-Language': 'id' },
                        signal: controller.signal,
                    },
                );

                // kalau user sudah ngetik query baru, jangan overwrite
                if (this.form.divisi[index].search.trim() !== q) return;

                this.form.divisi[index].searchResults = res.data;
            } catch (e) {
                if (e?.code === 'ERR_CANCELED' || e?.name === 'CanceledError')
                    return;

                console.warn('Search lokasi gagal:', e);
                triggerAlert('error', 'Pencarian lokasi gagal, coba lagi');
            } finally {
                // matikan spinner hanya kalau request ini adalah request terakhir untuk index tsb
                if (
                    controller &&
                    this.searchControllers[index] === controller
                ) {
                    this.form.divisi[index].searching = false;
                }
            }
        },

        selectLocation(index, item) {
            // stop debounce & request
            if (this.searchTimeouts[index]) {
                clearTimeout(this.searchTimeouts[index]);
                this.searchTimeouts[index] = null;
            }
            if (this.searchControllers[index]) {
                this.searchControllers[index].abort();
                this.searchControllers[index] = null;
            }

            const lat = parseFloat(item.lat);
            const lng = parseFloat(item.lon);

            const mapObj = this.maps[index];
            if (!mapObj) return;

            const latlng = { lat, lng };

            mapObj.map.setView(latlng, 16);
            mapObj.marker.setLatLng(latlng);
            mapObj.circle.setLatLng(latlng);

            this.form.divisi[index].latitude = parseFloat(lat.toFixed(6));
            this.form.divisi[index].longitude = parseFloat(lng.toFixed(6));
            this.form.divisi[index].alamat_penempatan = item.display_name;

            this.form.divisi[index].search = '';
            this.form.divisi[index].searchResults = [];
            this.form.divisi[index].searching = false;
        },

        // ================= VALIDATION =================
        validateForm() {
            this.errors = {};

            if (!this.form.kode_perusahaan) {
                this.errors.kode_perusahaan = 'Kode perusahaan harus diisi';
            }

            if (!this.form.nama_perusahaan) {
                this.errors.nama_perusahaan = 'Nama perusahaan harus diisi';
            }

            this.form.divisi.forEach((divisi, index) => {
                if (!divisi.nama_divisi) {
                    this.errors[`divisi.${index}.nama_divisi`] =
                        'Nama divisi harus diisi';
                }

                if (!divisi.latitude || !divisi.longitude) {
                    this.errors[`divisi.${index}.lokasi`] =
                        'Lokasi harus dipilih di peta';
                }
            });

            return Object.keys(this.errors).length === 0;
        },

        // ================= SUBMIT =================
        submitForm() {
            if (!this.validateForm()) {
                triggerAlert('error', 'Periksa kembali form yang harus diisi');
                return;
            }

            this.processing = true;

            router.post('/master/perusahaan', this.form, {
                onSuccess: () => {
                    triggerAlert(
                        'success',
                        'Data perusahaan berhasil disimpan',
                    );
                },
                onError: (errors) => {
                    this.errors = errors;
                    triggerAlert(
                        'error',
                        'Terjadi kesalahan, periksa kembali form',
                    );
                },
                onFinish: () => {
                    this.processing = false;
                },
            });
        },
    },
};
</script>

<template>
    <AppLayout>
        <div class="page-container">
            <div class="page-header mb-6">
                <div>
                    <h1 class="page-title">Tambah Client</h1>
                    <p class="text-sm text-slate-600 mt-1">
                        Isi data perusahaan client dan divisi beserta lokasi
                        penempatan
                    </p>
                </div>
            </div>

            <form @submit.prevent="submitForm">
                <div class="gap-6 lg:grid-cols-3 grid grid-cols-1">
                    <!-- PERUSAHAAN -->
                    <div class="lg:col-span-1">
                        <div class="card lg:sticky lg:top-6">
                            <div class="card-header border-b">
                                <div>
                                    <h3 class="card-title">Data Perusahaan</h3>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Informasi dasar perusahaan client
                                    </p>
                                </div>
                            </div>

                            <div class="card-body space-y-4">
                                <div class="form-group">
                                    <label class="field-label"
                                        >Kode Perusahaan
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.kode_perusahaan"
                                        class="form-input"
                                        placeholder="Contoh: ABC"
                                        maxlength="10"
                                        :class="{
                                            'border-red-300':
                                                errors.kode_perusahaan,
                                        }"
                                    />
                                    <p
                                        v-if="errors.kode_perusahaan"
                                        class="field-error"
                                    >
                                        {{ errors.kode_perusahaan }}
                                    </p>
                                    <p class="field-hint">
                                        Digunakan untuk generate nomor kontrak
                                        karyawan
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="field-label"
                                        >Nama Perusahaan
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.nama_perusahaan"
                                        class="form-input"
                                        placeholder="Contoh: PT ABC Indonesia"
                                        :class="{
                                            'border-red-300':
                                                errors.nama_perusahaan,
                                        }"
                                    />
                                    <p
                                        v-if="errors.nama_perusahaan"
                                        class="field-error"
                                    >
                                        {{ errors.nama_perusahaan }}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label class="field-label"
                                        >Alamat Kantor Pusat</label
                                    >
                                    <textarea
                                        v-model="form.alamat"
                                        class="form-input"
                                        rows="3"
                                        placeholder="Alamat lengkap kantor pusat"
                                    ></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="" class="field-label"
                                        >Tanggal Awal MOU</label
                                    >
                                    <input
                                        type="date"
                                        v-model="form.tanggal_awal_mou"
                                        id=""
                                        class="form-input"
                                    />
                                </div>
                                <div class="form-group">
                                    <label for="" class="field-label"
                                        >Tanggal Akhir MOU</label
                                    >
                                    <input
                                        type="date"
                                        v-model="form.tanggal_akhir_mou"
                                        :min="form.tanggal_awal_mou"
                                        id=""
                                        class="form-input"
                                    />
                                </div>

                                <div class="form-group">
                                    <label class="field-label">Status</label>
                                    <select
                                        v-model="form.status"
                                        class="form-input"
                                    >
                                        <option value="aktif">Aktif</option>
                                        <option value="tidak_aktif">
                                            Tidak Aktif
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DIVISI -->
                    <div class="lg:col-span-2 space-y-6">
                        <div
                            v-for="(divisi, index) in form.divisi"
                            :key="index"
                            class="card"
                        >
                            <div class="card-header between border-b">
                                <div>
                                    <h3 class="card-title">
                                        Divisi {{ index + 1 }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Tentukan lokasi penempatan untuk
                                        validasi presensi
                                    </p>
                                </div>
                                <button
                                    v-if="form.divisi.length > 1"
                                    type="button"
                                    class="btn btn-sm btn-danger-soft"
                                    @click="hapusDivisi(index)"
                                >
                                    <i class="ti ti-trash"></i>
                                    Hapus
                                </button>
                            </div>

                            <div class="card-body space-y-5">
                                <div
                                    class="gap-4 sm:grid-cols-2 grid grid-cols-1"
                                >
                                    <div class="form-group">
                                        <label class="field-label"
                                            >Nama Divisi
                                            <span class="text-danger"
                                                >*</span
                                            ></label
                                        >
                                        <input
                                            v-model="divisi.nama_divisi"
                                            class="form-input"
                                            placeholder="Contoh: Divisi Produksi"
                                            :class="{
                                                'border-red-300':
                                                    errors[
                                                        `divisi.${index}.nama_divisi`
                                                    ],
                                            }"
                                        />
                                        <p
                                            v-if="
                                                errors[
                                                    `divisi.${index}.nama_divisi`
                                                ]
                                            "
                                            class="field-error"
                                        >
                                            {{
                                                errors[
                                                    `divisi.${index}.nama_divisi`
                                                ]
                                            }}
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label class="field-label"
                                            >Status</label
                                        >
                                        <select
                                            v-model="divisi.status"
                                            class="form-input"
                                        >
                                            <option value="aktif">Aktif</option>
                                            <option value="tidak_aktif">
                                                Tidak Aktif
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div
                                    class="gap-4 sm:grid-cols-2 grid grid-cols-1"
                                >
                                    <div class="form-group">
                                        <label for="" class="field-label"
                                            >Tanggal Awal MOU</label
                                        >
                                        <input
                                            type="date"
                                            v-model="divisi.tanggal_awal_mou"
                                            id=""
                                            class="form-input"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="field-label"
                                            >Tanggal Akhir MOU</label
                                        >
                                        <input
                                            type="date"
                                            v-model="divisi.tanggal_akhir_mou"
                                            :min="divisi.tanggal_awal_mou"
                                            id=""
                                            class="form-input"
                                        />
                                    </div>
                                </div>

                                <div class="pt-4 border-t">
                                    <h4
                                        class="font-semibold text-sm text-slate-700 mb-1"
                                    >
                                        Lokasi Penempatan
                                    </h4>
                                    <p class="field-hint">
                                        Klik pada peta atau cari lokasi untuk
                                        menentukan titik presensi
                                    </p>
                                </div>

                                <!-- SEARCH -->
                                <div class="form-group">
                                    <label class="field-label">
                                        <i class="ti ti-search"></i>
                                        Cari Lokasi
                                    </label>

                                    <div class="search-input-wrap">
                                        <input
                                            v-model="divisi.search"
                                            class="form-input"
                                            placeholder="Ketik minimal 3 karakter (Contoh: Jalan Ijen, Malang)"
                                            @input="searchLocation(index)"
                                        />
                                        <div
                                            class="table-spinner"
                                            v-if="divisi.searching"
                                        >
                                            <span class="spinner"></span>
                                            <span class="spinner-text"
                                                >Memuat data...</span
                                            >
                                        </div>
                                    </div>

                                    <ul
                                        v-if="divisi.searchResults.length"
                                        class="search-dropdown"
                                    >
                                        <li
                                            v-for="(
                                                item, i
                                            ) in divisi.searchResults"
                                            :key="i"
                                            @click="selectLocation(index, item)"
                                        >
                                            <i
                                                class="ti ti-map-pin text-blue-500"
                                            ></i>
                                            {{ item.display_name }}
                                        </li>
                                    </ul>
                                </div>

                                <!-- MAP -->
                                <!-- kunci z-index Leaflet di dalam wrapper -->
                                <div class="map-wrap relative w-full">
                                    <div
                                        :id="`map-${index}`"
                                        class="rounded-xl w-full overflow-hidden border-2"
                                        :class="{
                                            'border-red-300':
                                                errors[
                                                    `divisi.${index}.lokasi`
                                                ],
                                        }"
                                        style="height: 450px; min-height: 450px"
                                    ></div>

                                    <p
                                        v-if="errors[`divisi.${index}.lokasi`]"
                                        class="field-error mt-2"
                                    >
                                        {{ errors[`divisi.${index}.lokasi`] }}
                                    </p>

                                    <div class="map-hint">
                                        <i class="ti ti-info-circle"></i>
                                        Klik pada peta atau drag marker untuk
                                        memilih lokasi
                                    </div>
                                </div>

                                <!-- DETAILS -->
                                <div class="space-y-4">
                                    <div class="form-group">
                                        <label class="field-label">
                                            <i class="ti ti-map-pin"></i>
                                            Alamat Penempatan
                                        </label>
                                        <textarea
                                            v-model="divisi.alamat_penempatan"
                                            class="form-input"
                                            rows="2"
                                            readonly
                                            placeholder="Alamat akan terisi otomatis setelah memilih lokasi"
                                        ></textarea>
                                    </div>

                                    <div
                                        class="gap-4 sm:grid-cols-3 grid grid-cols-1"
                                    >
                                        <div class="form-group">
                                            <label class="field-label text-xs"
                                                >Latitude</label
                                            >
                                            <input
                                                v-model="divisi.latitude"
                                                class="form-input text-sm"
                                                readonly
                                                placeholder="-7.966620"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label text-xs"
                                                >Longitude</label
                                            >
                                            <input
                                                v-model="divisi.longitude"
                                                class="form-input text-sm"
                                                readonly
                                                placeholder="112.632632"
                                            />
                                        </div>

                                        <div class="form-group">
                                            <label class="field-label text-xs"
                                                >Radius Presensi (meter)</label
                                            >
                                            <input
                                                type="number"
                                                v-model="divisi.radius_presensi"
                                                class="form-input text-sm"
                                                min="50"
                                                max="5000"
                                                step="50"
                                                @input="updateRadius(index)"
                                            />
                                        </div>
                                    </div>

                                    <div
                                        class="bg-blue-50 border-blue-200 rounded-lg p-3 border"
                                    >
                                        <p class="field-hint">
                                            <i class="ti ti-info-circle"></i>
                                            <strong>Radius Presensi:</strong>
                                            Jarak maksimal karyawan dari titik
                                            ini untuk bisa melakukan presensi.
                                            Area biru di peta menunjukkan radius
                                            yang valid.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn btn-success w-full"
                            @click="tambahDivisi"
                        >
                            <i class="ti ti-plus"></i>
                            Tambah Divisi Lainnya
                        </button>
                    </div>
                </div>

                <!-- ACTION -->
                <div
                    class="gap-3 mt-8 bottom-0 bg-white py-4 sticky z-10 flex justify-end border-t"
                >
                    <Link
                        href="/master/client/aktif/all"
                        class="btn btn-secondary"
                    >
                        <i class="ti ti-x"></i>
                        Batal
                    </Link>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        :disabled="processing"
                    >
                        <i class="ti ti-check" v-if="!processing"></i>
                        <i class="ti ti-loader animate-spin" v-else></i>
                        {{ processing ? 'Menyimpan...' : 'Simpan Data' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
