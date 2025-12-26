<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $employee['nama'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }

        .info-section {
            margin-bottom: 15px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 3px 0;
            font-weight: bold;
        }

        .info-separator {
            display: table-cell;
            width: 5%;
            padding: 3px 0;
        }

        .info-value {
            display: table-cell;
            width: 65%;
            padding: 3px 0;
        }

        .section-title {
            background-color: #f0f0f0;
            padding: 6px 10px;
            margin-top: 15px;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 12px;
            border-left: 4px solid #333;
        }

        .table-container {
            width: 100%;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 4px 8px;
            border-bottom: 1px solid #ddd;
        }

        .item-label {
            width: 70%;
        }

        .item-amount {
            width: 30%;
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .subtotal-row td {
            font-weight: bold;
            background-color: #f8f8f8;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 6px 8px;
        }

        .total-row td {
            font-weight: bold;
            font-size: 13px;
            background-color: #e8e8e8;
            border-top: 3px double #333;
            border-bottom: 3px double #333;
            padding: 8px;
        }

        .summary-box {
            background-color: #f9f9f9;
            border: 2px solid #333;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
        }

        .summary-box .label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-box .amount {
            font-size: 20px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #2c5f2d;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        .two-column {
            display: table;
            width: 100%;
        }

        .column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding: 0 1%;
        }

        .no-data {
            color: #999;
            font-style: italic;
            padding: 10px;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header" style="width:100%;">
        <img
            src="{{ public_path('assets/images/logo_print.png') }}"
            alt="Logo"
            style="display:block; width:100%; max-width:none; height:auto; margin:0 0 10px 0;"
        >
        <h2>Slip Gaji Karyawan</h2>
        <div style="margin-top: 8px; font-size: 11px;">
            <strong>{{ $period_name }}</strong><br>
            {{ $period_range }}
        </div>
    </div>


    <!-- INFORMASI KARYAWAN -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $employee['nama'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">NIK</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $employee['nik'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Penempatan</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $company_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jabatan</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $employee['jabatan'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Divisi</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $employee['divisi'] }}</div>
            </div>
            {{-- <div class="info-row">
                <div class="info-label">No. Rekening</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $employee['no_rek'] }}</div>
            </div> --}}
        </div>
    </div>

    <!-- KONFIGURASI GAJI (RATE) -->
    @if(!empty($salary_configuration) && (
        !is_null($salary_configuration['gaji_pokok_rate'] ?? null) ||
        !is_null($salary_configuration['gaji_per_hari_rate'] ?? null) ||
        !is_null($salary_configuration['gaji_hk_rate'] ?? null) ||
        !is_null($salary_configuration['gaji_train_hk_rate'] ?? null) ||
        !is_null($salary_configuration['gaji_train_upah_per_jam_rate'] ?? null) ||
        !is_null($salary_configuration['lembur_per_hari_rate'] ?? null) ||
        !is_null($salary_configuration['lembur_per_jam_rate'] ?? null)
    ))
        <div class="section-title" style="margin-top: 12px;">KONFIGURASI GAJI (RATE)</div>
        <div class="table-container">
            <table>
                @if(!empty($salary_configuration['effective_date']))
                    <tr>
                        <td class="item-label">Effective Date</td>
                        <td class="item-amount">{{ $salary_configuration['effective_date'] }}</td>
                    </tr>
                @endif

                @if(!is_null($salary_configuration['gaji_pokok_rate'] ?? null))
                    <tr>
                        <td class="item-label">Gaji Pokok</td>
                        <td class="item-amount">Rp {{ number_format($salary_configuration['gaji_pokok_rate'], 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if(!is_null($salary_configuration['gaji_per_hari_rate'] ?? null))
                    <tr>
                        <td class="item-label">Gaji Per Hari</td>
                        <td class="item-amount">Rp {{ number_format($salary_configuration['gaji_per_hari_rate'], 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if(!is_null($salary_configuration['gaji_hk_rate'] ?? null))
                    <tr>
                        <td class="item-label">Gaji HK</td>
                        <td class="item-amount">Rp {{ number_format($salary_configuration['gaji_hk_rate'], 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if(!is_null($salary_configuration['gaji_train_hk_rate'] ?? null))
                    <tr>
                        <td class="item-label">Gaji Training HK</td>
                        <td class="item-amount">Rp {{ number_format($salary_configuration['gaji_train_hk_rate'], 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if(!is_null($salary_configuration['gaji_train_upah_per_jam_rate'] ?? null))
                    <tr>
                        <td class="item-label">Gaji Training / Jam</td>
                        <td class="item-amount">Rp {{ number_format($salary_configuration['gaji_train_upah_per_jam_rate'], 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if(!is_null($salary_configuration['lembur_per_hari_rate'] ?? null))
                    <tr>
                        <td class="item-label">Lembur / Hari</td>
                        <td class="item-amount">Rp {{ number_format($salary_configuration['lembur_per_hari_rate'], 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if(!is_null($salary_configuration['lembur_per_jam_rate'] ?? null))
                    <tr>
                        <td class="item-label">Lembur / Jam</td>
                        <td class="item-amount">Rp {{ number_format($salary_configuration['lembur_per_jam_rate'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    <!-- PENDAPATAN UTAMA -->
    @if($earnings->count() > 0)
        <div class="section-title">PENDAPATAN UTAMA</div>
        <div class="table-container">
            <table>
                @foreach($earnings as $item)
                    <tr>
                        <td class="item-label">{{ $item['label'] }}</td>
                        <td class="item-amount">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td>SUBTOTAL PENDAPATAN UTAMA</td>
                    <td class="item-amount">Rp {{ number_format($total_earnings, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- TUNJANGAN -->
    @if($allowances->count() > 0)
        <div class="section-title">TUNJANGAN</div>
        <div class="table-container">
            <table>
                @foreach($allowances as $item)
                    <tr>
                        <td class="item-label">{{ $item['label'] }}</td>
                        <td class="item-amount">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td>SUBTOTAL TUNJANGAN</td>
                    <td class="item-amount">Rp {{ number_format($total_allowances, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- PENDAPATAN TAMBAHAN -->
    @if($additional_earnings->count() > 0)
        <div class="section-title">PENDAPATAN TAMBAHAN</div>
        <div class="table-container">
            <table>
                @foreach($additional_earnings as $item)
                    <tr>
                        <td class="item-label">{{ $item['label'] }}</td>
                        <td class="item-amount">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td>SUBTOTAL PENDAPATAN TAMBAHAN</td>
                    <td class="item-amount">Rp {{ number_format($total_additional_earnings, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- DETAIL ANJEM & BORONGAN (JIKA ADA NILAI) -->
    @php
        $showAdditionalInfo =
            !empty($additional_earnings_info) && (
                !is_null($additional_earnings_info['anjem_hari'] ?? null) ||
                !is_null($additional_earnings_info['anjem_jam'] ?? null) ||
                !is_null($additional_earnings_info['borongan_kg'] ?? null)
            );
    @endphp
    @if($showAdditionalInfo)
        <div class="section-title" style="margin-top: 12px;">DETAIL ANJEM & BORONGAN</div>
        <div class="table-container">
            <table>
                @if(!is_null($additional_earnings_info['anjem_hari'] ?? null))
                    <tr>
                        <td class="item-label">Anjem (Hari)</td>
                        <td class="item-amount">{{ $additional_earnings_info['anjem_hari'] }} hari</td>
                    </tr>
                @endif
                @if(!is_null($additional_earnings_info['anjem_jam'] ?? null))
                    <tr>
                        <td class="item-label">Anjem (Jam)</td>
                        <td class="item-amount">{{ $additional_earnings_info['anjem_jam'] }} jam</td>
                    </tr>
                @endif
                @if(!is_null($additional_earnings_info['borongan_kg'] ?? null))
                    <tr>
                        <td class="item-label">Borongan (Kg)</td>
                        <td class="item-amount">{{ $additional_earnings_info['borongan_kg'] }} kg</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    <!-- TOTAL PENDAPATAN -->
    <div class="table-container" style="margin-top: 15px;">
        <table>
            <tr class="total-row">
                <td>TOTAL PENDAPATAN</td>
                <td class="item-amount">Rp {{ number_format($total_income, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- POTONGAN -->
    @if($deductions->count() > 0)
        <div class="section-title" style="margin-top: 20px;">POTONGAN</div>
        <div class="table-container">
            <table>
                @foreach($deductions as $item)
                    <tr>
                        <td class="item-label">{{ $item['label'] }}</td>
                        <td class="item-amount">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td>TOTAL POTONGAN</td>
                    <td class="item-amount">Rp {{ number_format($total_deductions, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- TAKE HOME PAY -->
    <div class="summary-box">
        <div class="label">GAJI BERSIH (TAKE HOME PAY)</div>
        <div class="amount">Rp {{ number_format($grand_total, 0, ',', '.') }}</div>
    </div>

    <!-- INFORMASI KEHADIRAN & LEMBUR -->
    @if($attendance || $overtime)
        <div class="section-title" style="margin-top: 20px;">INFORMASI TAMBAHAN</div>
        <div class="two-column">
            @if($attendance)
                <div class="column">
                    <strong>Kehadiran:</strong>
                    <div class="info-grid" style="margin-top: 5px;">
                        @php
                            $hadir = $attendance['hadir'] ?? null;

                            // support key lama & baru
                            $mangkir = $attendance['mangkir'] ?? ($attendance['mangkir_hari'] ?? null);
                            $terlambat = $attendance['terlambat'] ?? ($attendance['terlambat_hari'] ?? null);
                            $cuti = $attendance['cuti'] ?? ($attendance['cuti_dibayar'] ?? null);

                            $tidakMasukHari = $attendance['tidak_masuk_hari'] ?? null;
                            $tidakMasukUpah = $attendance['tidak_masuk_upah'] ?? null;

                            $terlambatMenit = $attendance['terlambat_menit'] ?? null;
                            $terlambatJam = $attendance['terlambat_jam'] ?? null;

                            $jamKerja = $attendance['jam_kerja'] ?? null;
                            $jamHk = $attendance['jam_hk'] ?? null;
                            $jamHl = $attendance['jam_hl'] ?? null;
                            $jamHr = $attendance['jam_hr'] ?? null;

                            $jumlahHl = $attendance['jumlah_hl'] ?? null;
                            $jumlahHr = $attendance['jumlah_hr'] ?? null;

                            $ijinPulang = $attendance['ijin_pulang'] ?? null;
                        @endphp

                        @if(!is_null($hadir))
                            <div class="info-row">
                                <div class="info-label" style="width: 50%;">Hadir</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $hadir }} hari</div>
                            </div>
                        @endif

                        @if(!is_null($jamKerja))
                            <div class="info-row">
                                <div class="info-label">Jam Kerja</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $jamKerja }} jam</div>
                            </div>
                        @endif

                        @if(!is_null($jamHk))
                            <div class="info-row">
                                <div class="info-label">Jam HK</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $jamHk }} jam</div>
                            </div>
                        @endif
                        @if(!is_null($jamHl))
                            <div class="info-row">
                                <div class="info-label">Jam HL</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $jamHl }} jam</div>
                            </div>
                        @endif
                        @if(!is_null($jamHr))
                            <div class="info-row">
                                <div class="info-label">Jam HR</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $jamHr }} jam</div>
                            </div>
                        @endif

                        @if(!is_null($jumlahHl))
                            <div class="info-row">
                                <div class="info-label">Hari Libur</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $jumlahHl }} hari</div>
                            </div>
                        @endif
                        @if(!is_null($jumlahHr))
                            <div class="info-row">
                                <div class="info-label">Hari Raya</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $jumlahHr }} hari</div>
                            </div>
                        @endif

                        @if(!is_null($mangkir))
                            <div class="info-row">
                                <div class="info-label">Mangkir</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $mangkir }} hari</div>
                            </div>
                        @endif

                        @if(!is_null($tidakMasukHari))
                            <div class="info-row">
                                <div class="info-label">Tidak Masuk</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $tidakMasukHari }} hari</div>
                            </div>
                        @endif

                        @if(!is_null($tidakMasukUpah))
                            <div class="info-row">
                                <div class="info-label">Pot. Tidak Masuk (Upah)</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">Rp {{ number_format($tidakMasukUpah, 0, ',', '.') }}</div>
                            </div>
                        @endif

                        @if(!is_null($terlambat))
                            <div class="info-row">
                                <div class="info-label">Terlambat</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">
                                    {{ $terlambat }} hari
                                    @if(!is_null($terlambatMenit))
                                        ({{ $terlambatMenit }} menit)
                                    @endif
                                    @if(!is_null($terlambatJam))
                                        ({{ $terlambatJam }} jam)
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(!is_null($ijinPulang))
                            <div class="info-row">
                                <div class="info-label">Ijin Pulang</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $ijinPulang }}</div>
                            </div>
                        @endif

                        @if(!is_null($cuti))
                            <div class="info-row">
                                <div class="info-label">Cuti</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $cuti }} hari</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($overtime)
                <div class="column">
                    <strong>Lembur:</strong>
                    <div class="info-grid" style="margin-top: 5px;">
                        @php
                            $totalHari = $overtime['total_hari'] ?? ($overtime['lembur_hari'] ?? null);
                            $totalJam  = $overtime['total_jam'] ?? ($overtime['lembur_jam'] ?? null);

                            $overtimeJam = $overtime['overtime_jam'] ?? null;
                            $jamBiasa = $overtime['lembur_jam_biasa'] ?? null;
                            $jamKhusus = $overtime['lembur_jam_khusus'] ?? null;

                            $lemburLibur = $overtime['lembur_libur'] ?? null;

                            $m2 = $overtime['lembur_minggu_2'] ?? null;
                            $m3 = $overtime['lembur_minggu_3'] ?? null;
                            $m4 = $overtime['lembur_minggu_4'] ?? null;
                            $m5 = $overtime['lembur_minggu_5'] ?? null;
                            $m6 = $overtime['lembur_minggu_6'] ?? null;
                            $m7 = $overtime['lembur_minggu_7'] ?? null;
                            $lembur2 = $overtime['lembur_2'] ?? null;
                        @endphp

                        @if(!is_null($totalHari))
                            <div class="info-row">
                                <div class="info-label" style="width: 50%;">Total Hari</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $totalHari }} hari</div>
                            </div>
                        @endif

                        @if(!is_null($totalJam))
                            <div class="info-row">
                                <div class="info-label">Total Jam</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $totalJam }} jam</div>
                            </div>
                        @endif

                        @if(!is_null($overtimeJam))
                            <div class="info-row">
                                <div class="info-label">Overtime (Jam)</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $overtimeJam }} jam</div>
                            </div>
                        @endif

                        @if(!is_null($jamBiasa))
                            <div class="info-row">
                                <div class="info-label">Lembur Biasa</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $jamBiasa }} jam</div>
                            </div>
                        @endif

                        @if(!is_null($jamKhusus))
                            <div class="info-row">
                                <div class="info-label">Lembur Khusus</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $jamKhusus }} jam</div>
                            </div>
                        @endif

                        @if(!is_null($lemburLibur))
                            <div class="info-row">
                                <div class="info-label">Lembur Hari Libur</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">Rp {{ number_format($lemburLibur, 0, ',', '.') }}</div>
                            </div>
                        @endif

                        @if(!is_null($m2))
                            <div class="info-row">
                                <div class="info-label">Lembur Minggu 2</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">Rp {{ number_format($m2, 0, ',', '.') }}</div>
                            </div>
                        @endif
                        @if(!is_null($m3))
                            <div class="info-row">
                                <div class="info-label">Lembur Minggu 3</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">Rp {{ number_format($m3, 0, ',', '.') }}</div>
                            </div>
                        @endif
                        @if(!is_null($m4))
                            <div class="info-row">
                                <div class="info-label">Lembur Minggu 4</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">Rp {{ number_format($m4, 0, ',', '.') }}</div>
                            </div>
                        @endif
                        @if(!is_null($m5))
                            <div class="info-row">
                                <div class="info-label">Lembur Minggu 5</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">Rp {{ number_format($m5, 0, ',', '.') }}</div>
                            </div>
                        @endif
                        @if(!is_null($m6))
                            <div class="info-row">
                                <div class="info-label">Lembur Minggu 6</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">Rp {{ number_format($m6, 0, ',', '.') }}</div>
                            </div>
                        @endif
                        @if(!is_null($m7))
                            <div class="info-row">
                                <div class="info-label">Lembur Minggu 7</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">Rp {{ number_format($m7, 0, ',', '.') }}</div>
                            </div>
                        @endif
                        @if(!is_null($lembur2))
                            <div class="info-row">
                                <div class="info-label">Lembur 2</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">Rp {{ number_format($lembur2, 0, ',', '.') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem dan tidak memerlukan tanda tangan.</p>
        <p>Harap disimpan sebagai bukti pembayaran gaji.</p>
        <p style="margin-top: 8px;">Dicetak pada: {{ date('d F Y H:i') }} WIB</p>
    </div>
</body>

</html>
