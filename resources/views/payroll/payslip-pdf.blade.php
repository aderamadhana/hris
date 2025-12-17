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
    <div class="header">
        <h1>{{ $company_name }}</h1>
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
                <div class="info-label">Jabatan</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $employee['jabatan'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Divisi</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $employee['divisi'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">No. Rekening</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $employee['no_rek'] }}</div>
            </div>
        </div>
    </div>

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
                <div class="info-row">
                    <div class="info-label" style="width: 50%;">Hadir</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $attendance['hadir'] }} hari</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Mangkir</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $attendance['mangkir'] }} hari</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Terlambat</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $attendance['terlambat'] }} hari</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Cuti</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $attendance['cuti'] }} hari</div>
                </div>
            </div>
        </div>
        @endif
        
        @if($overtime)
        <div class="column">
            <strong>Lembur:</strong>
            <div class="info-grid" style="margin-top: 5px;">
                <div class="info-row">
                    <div class="info-label" style="width: 50%;">Total Hari</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $overtime['total_hari'] }} hari</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Total Jam</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $overtime['total_jam'] }} jam</div>
                </div>
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