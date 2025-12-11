<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DB Tools - migrate:fresh --seed</title>
</head>
<body>
    <h1>DB Tools (Local Only)</h1>

    {{-- Notifikasi status --}}
    @if (session('status'))
        <div style="border: 1px solid #0a0; padding: 10px; margin-bottom: 10px;">
            <strong>{{ session('status') }}</strong>
        </div>
    @endif

    {{-- Output lengkap dari Artisan --}}
    @if (session('output'))
        <pre style="white-space: pre-wrap; background: #f5f5f5; padding: 10px; max-height: 400px; overflow: auto;">
{{ session('output') }}
        </pre>
    @endif

    {{-- Form trigger migrate:fresh --seed --}}
    <form method="POST"
          action="{{ route('util.run-migrate-fresh-seed') }}"
          onsubmit="return confirm('Yakin? Semua tabel akan di-DROP dan di-seed ulang.');">
        @csrf
        <button type="submit">
            Run migrate:fresh --seed
        </button>
    </form>
</body>
</html>
