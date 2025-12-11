<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Logs ({{ $date }})</title>
</head>
<body>
    <h1>Application Logs ({{ $date }})</h1>

    {{-- Form pilih tanggal log --}}
    <form method="GET" action="{{ route('util.logs') }}" style="margin-bottom: 1rem;">
        <label>
            Tanggal:
            <input type="date" name="date" value="{{ $date }}">
        </label>
        <button type="submit">Load</button>
    </form>

    @if (! $exists)
        <div style="border: 1px solid #cc0; padding: 10px; margin-bottom: 10px;">
            File log untuk tanggal {{ $date }} tidak ditemukan.
        </div>
    @else
        <pre style="
            white-space: pre-wrap;
            background: #111;
            color: #eee;
            padding: 10px;
            max-height: 600px;
            overflow: auto;
            font-size: 12px;
        ">{{ $content }}</pre>
    @endif
</body>
</html>
