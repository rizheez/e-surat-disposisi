<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        @if ($valid)
            {{-- VALID --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-200">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 text-center">
                    <div class="mx-auto w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Dokumen Terverifikasi</h1>
                    <p class="text-green-100 text-sm mt-1">Surat ini sah dan terverifikasi secara digital</p>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Nomor Surat</p>
                            <p class="font-semibold text-gray-900">{{ $surat->nomor_surat }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Tanggal</p>
                            <p class="font-semibold text-gray-900">{{ $surat->tanggal_surat?->format('d M Y') ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Perihal</p>
                        <p class="font-semibold text-gray-900">{{ $surat->perihal }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Tujuan</p>
                            <p class="font-medium text-gray-900">{{ $surat->tujuan }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Penandatangan</p>
                            <p class="font-medium text-gray-900">{{ $surat->penandatangan?->name ?? '-' }}</p>
                        </div>
                    </div>

                    @if ($surat->approved_at)
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <p class="text-xs text-green-600">
                                <strong>Disetujui pada:</strong> {{ $surat->approved_at->format('d M Y H:i') }} WIB
                            </p>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 px-6 py-4 text-center border-t">
                    <p class="text-xs text-gray-500">Diverifikasi oleh <strong>
                            {{-- {{ config('app.name') }} --}}
                            UNUKALTIM
                        </strong></p>
                </div>
            </div>
        @else
            {{-- INVALID --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-red-200">
                <div class="bg-gradient-to-r from-red-500 to-rose-600 p-6 text-center">
                    <div class="mx-auto w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Dokumen Tidak Valid</h1>
                    <p class="text-red-100 text-sm mt-1">QR code ini tidak terdaftar dalam sistem</p>
                </div>

                <div class="p-6 text-center">
                    <p class="text-gray-600">Surat dengan kode verifikasi ini tidak ditemukan. Pastikan QR code yang
                        Anda scan benar.</p>
                </div>

                <div class="bg-gray-50 px-6 py-4 text-center border-t">
                    <p class="text-xs text-gray-500"><strong>
                            {{-- {{ config('app.name') }} --}}
                            UNUKALTIM
                        </strong></p>
                </div>
            </div>
        @endif
    </div>
</body>

</html>
