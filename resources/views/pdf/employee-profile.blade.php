<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profil Karyawan - {{ $employee->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2563eb;
        }
        .header h1 {
            color: #2563eb;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 12px;
        }
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #2563eb;
            color: white;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            page-break-after: avoid;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
            page-break-inside: avoid;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 5px 8px;
            font-weight: 600;
            color: #555;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-value {
            display: table-cell;
            width: 65%;
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            page-break-inside: auto;
        }
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        th {
            background-color: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #d1d5db;
            font-size: 10px;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
        }
        .photo {
            float: right;
            width: 100px;
            height: 120px;
            object-fit: cover;
            border: 2px solid #d1d5db;
            margin-left: 15px;
            margin-bottom: 10px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-active {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        /* Page break utilities */
        .page-break {
            page-break-after: always;
        }
        .no-break {
            page-break-inside: avoid;
        }
        /* Khusus untuk section yang panjang */
        .section-large {
            margin-bottom: 15px;
        }
        .section-large .section-title {
            page-break-after: avoid;
        }
        /* Hindari orphan rows di table */
        table tbody tr:first-child {
            page-break-before: avoid;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header no-break">
            <h1>PROFIL KARYAWAN</h1>
        </div>

        {{-- DATA KARYAWAN --}}
        <div class="section clearfix">
            @if($documents && $documents->pas_foto)
                <img src="{{ public_path(str_replace(url('/'), '', Storage::url($documents->pas_foto))) }}" 
                     alt="Foto" class="photo">
            @endif
            
            <div class="section-title">DATA KARYAWAN</div>
            
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">NRP</div>
                    <div class="info-value">{{ $employee->nrp }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value">{{ $employee->nama }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="badge {{ $employee->status_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $employee->status_active ? 'AKTIF' : 'TIDAK AKTIF' }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">NIK / KTP</div>
                    <div class="info-value">{{ $employee->no_ktp ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">No. KK</div>
                    <div class="info-value">{{ $employee->no_kk ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jenis Kelamin</div>
                    <div class="info-value">{{ $employee->jenis_kelamin ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tempat, Tanggal Lahir</div>
                    <div class="info-value">
                        {{ $employee->tempat_lahir ?? '-' }}, 
                        {{ $employee->tanggal_lahir ? \Carbon\Carbon::parse($employee->tanggal_lahir)->format('d M Y') : '-' }}
                        ({{ $employee->tanggal_lahir ? now()->diffInYears($employee->tanggal_lahir) . ' tahun' : '-' }})
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Agama</div>
                    <div class="info-value">{{ $employee->agama ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status Perkawinan</div>
                    <div class="info-value">{{ $employee->status_perkawinan ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Kewarganegaraan</div>
                    <div class="info-value">{{ $employee->kewarganegaraan ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- KONTAK --}}
        <div class="section no-break">
            <div class="section-title">KONTAK</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">No. WhatsApp</div>
                    <div class="info-value">{{ $employee->no_wa ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $employee->email ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- ALAMAT --}}
        <div class="section">
            <div class="section-title">ALAMAT</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Alamat KTP</div>
                    <div class="info-value">
                        {{ $employee->alamat_lengkap_ktp ?? '-' }}<br>
                        Desa: {{ $employee->desa_ktp ?? '-' }}, 
                        Kec: {{ $employee->kecamatan_ktp ?? '-' }}, 
                        {{ $employee->kota_ktp ?? '-' }} 
                        {{ $employee->kode_pos_ktp ? '(' . $employee->kode_pos_ktp . ')' : '' }}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Alamat Domisili</div>
                    <div class="info-value">
                        {{ $employee->alamat_lengkap_domisili ?? '-' }}<br>
                        Desa: {{ $employee->desa_domisili ?? '-' }}, 
                        Kec: {{ $employee->kecamatan_domisili ?? '-' }}, 
                        {{ $employee->kota_domisili ?? '-' }} 
                        {{ $employee->kode_pos_domisili ? '(' . $employee->kode_pos_domisili . ')' : '' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- BPJS & LISENSI --}}
        <div class="section">
            <div class="section-title">BPJS & LISENSI</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">BPJS Ketenagakerjaan</div>
                    <div class="info-value">{{ $employee->bpjs_tk ?? '-' }} ({{ $employee->jenis_bpjs_tk ?? '-' }})</div>
                </div>
                <div class="info-row">
                    <div class="info-label">BPJS Kesehatan</div>
                    <div class="info-value">{{ $employee->bpjs_kes ?? '-' }} ({{ $employee->status_bpjs_ks ?? '-' }})</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Faskes</div>
                    <div class="info-value">{{ $employee->nama_faskes ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">SKCK</div>
                    <div class="info-value">
                        {{ $employee->no_skck ?? '-' }}
                        @if($employee->masa_berlaku_skck)
                            (Berlaku s/d {{ \Carbon\Carbon::parse($employee->masa_berlaku_skck)->format('d M Y') }})
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Lisensi</div>
                    <div class="info-value">
                        {{ $employee->jenis_lisensi ?? '-' }} - {{ $employee->no_lisensi ?? '-' }}
                        @if($employee->masa_berlaku_lisensi)
                            (Berlaku s/d {{ \Carbon\Carbon::parse($employee->masa_berlaku_lisensi)->format('d M Y') }})
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- PENDIDIKAN --}}
        @if($pendidikan->count() > 0)
        <div class="section-large">
            <div class="section-title">RIWAYAT PENDIDIKAN</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 15%">Jenjang</th>
                        <th style="width: 20%">Jurusan</th>
                        <th style="width: 30%">Institusi</th>
                        <th style="width: 20%">Sekolah</th>
                        <th style="width: 10%">Lulus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendidikan as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $p->jenjang ?? '-' }}</td>
                        <td>{{ $p->jurusan ?? '-' }}</td>
                        <td>{{ $p->institusi ?? '-' }}</td>
                        <td>{{ $p->sekolah_asal ?? '-' }}</td>
                        <td>{{ $p->tahun_lulus ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- RIWAYAT PEKERJAAN --}}
        @if($pekerjaan->count() > 0)
        <div class="section-large">
            <div class="section-title">RIWAYAT PEKERJAAN</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 4%">No</th>
                        <th style="width: 18%">Perusahaan</th>
                        <th style="width: 13%">Jabatan</th>
                        <th style="width: 13%">Bagian</th>
                        <th style="width: 10%">Mulai</th>
                        <th style="width: 10%">Selesai</th>
                        <th style="width: 10%">Kontrak</th>
                        <th style="width: 12%">No. Kontrak</th>
                        <th style="width: 10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pekerjaan as $index => $j)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $j->perusahaan ?? '-' }}</td>
                        <td>{{ $j->jabatan ?? $j->job_roll ?? '-' }}</td>
                        <td>{{ $j->penempatan ?? '-' }}</td>
                        <td>{{ $j->tgl_awal_kerja ? \Carbon\Carbon::parse($j->tgl_awal_kerja)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $j->tgl_akhir_kerja ? \Carbon\Carbon::parse($j->tgl_akhir_kerja)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $j->jenis_kontrak ?? '-' }}</td>
                        <td>{{ $j->no_kontrak ?? '-' }}</td>
                        <td>{{ $j->keterangan_status ?? $j->status ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- DATA KELUARGA --}}
        @if($keluarga->count() > 0)
        <div class="section-large">
            <div class="section-title">DATA KELUARGA</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 25%">Nama</th>
                        <th style="width: 12%">Hubungan</th>
                        <th style="width: 25%">Tempat, Tanggal Lahir</th>
                        <th style="width: 13%">Pendidikan</th>
                        <th style="width: 20%">Pekerjaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($keluarga as $index => $f)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $f->nama ?? '-' }}</td>
                        <td>{{ $f->hubungan ?? '-' }}</td>
                        <td>
                            @if($f->tempat_lahir || $f->tanggal_lahir)
                                {{ $f->tempat_lahir ?? '-' }}{{ $f->tanggal_lahir ? ', ' . \Carbon\Carbon::parse($f->tanggal_lahir)->format('d M Y') : '' }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $f->pendidikan ?? '-' }}</td>
                        <td>{{ $f->pekerjaan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- DATA KESEHATAN --}}
        @if($kesehatan)
        <div class="section">
            <div class="section-title">DATA KESEHATAN</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Tanggal MCU</div>
                    <div class="info-value">{{ $kesehatan->tanggal_mcu ? \Carbon\Carbon::parse($kesehatan->tanggal_mcu)->format('d M Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Kesimpulan MCU</div>
                    <div class="info-value">{{ $kesehatan->kesimpulan_hasil_mcu ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tinggi / Berat Badan</div>
                    <div class="info-value">{{ $kesehatan->tinggi_badan ?? '-' }} cm / {{ $kesehatan->berat_badan ?? '-' }} kg</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Golongan Darah</div>
                    <div class="info-value">{{ $kesehatan->gol_darah ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Buta Warna</div>
                    <div class="info-value">{{ $kesehatan->buta_warna ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Drug Test</div>
                    <div class="info-value">
                        {{ $kesehatan->hasil_drug_test ?? '-' }}
                        @if($kesehatan->tanggal_drug_test)
                            ({{ \Carbon\Carbon::parse($kesehatan->tanggal_drug_test)->format('d M Y') }})
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Riwayat Penyakit</div>
                    <div class="info-value">{{ $kesehatan->riwayat_penyakit ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tensi / Nadi</div>
                    <div class="info-value">{{ $kesehatan->tensi ?? '-' }} / {{ $kesehatan->nadi ?? '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        {{-- INFORMASI LAINNYA --}}
        <div class="section">
            <div class="section-title">INFORMASI LAINNYA</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Bank & Rekening</div>
                    <div class="info-value">{{ $employee->bank ?? '-' }} - {{ $employee->no_rekening ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">No. CIF</div>
                    <div class="info-value">{{ $employee->no_cif ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">NPWP</div>
                    <div class="info-value">{{ $employee->npwp ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">PTKP</div>
                    <div class="info-value">{{ $employee->ptkp ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ukuran Sepatu / Seragam</div>
                    <div class="info-value">{{ $employee->shoe_size ?? '-' }} / {{ $employee->uniform_size ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Dokumen ini dicetak pada {{ now()->format('d F Y, H:i') }} WIB</p>
        </div>
    </div>
</body>
</html>