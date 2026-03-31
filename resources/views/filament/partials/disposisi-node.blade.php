@php
    $isCurrentRecord = $disposisi->id === $currentId;
    $statusColor = match ($disposisi->status) {
        'belum_diproses' => 'red',
        'sedang_diproses' => 'amber',
        'selesai' => 'green',
        default => 'gray',
    };
    $statusLabel = match ($disposisi->status) {
        'belum_diproses' => 'Belum Diproses',
        'sedang_diproses' => 'Sedang Diproses',
        'selesai' => 'Selesai',
        default => $disposisi->status,
    };
    $levelColors = ['blue', 'orange', 'purple', 'teal', 'pink'];
    $levelColor = $levelColors[($level - 1) % count($levelColors)];
@endphp

<div class="relative {{ $level > 1 ? 'ml-8 mt-3' : '' }}">
    {{-- Connector line --}}
    @if ($level > 1)
        <div class="absolute -left-4 top-0 bottom-0 w-px bg-gray-300 dark:bg-gray-600"></div>
        <div class="absolute -left-4 top-6 w-4 h-px bg-gray-300 dark:bg-gray-600"></div>
    @endif

    {{-- Node card --}}
    <div
        class="relative rounded-lg border-2 p-4 transition-all
        {{ $isCurrentRecord
            ? 'border-primary-500 bg-primary-50 dark:bg-primary-950/30 ring-2 ring-primary-200 dark:ring-primary-800'
            : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }}">

        {{-- Level badge --}}
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                    bg-{{ $levelColor }}-100 text-{{ $levelColor }}-800
                    dark:bg-{{ $levelColor }}-900/30 dark:text-{{ $levelColor }}-400">
                    Level {{ $level }}
                </span>
                @if ($isCurrentRecord)
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">
                        ← Sedang dilihat
                    </span>
                @endif
            </div>
            <span
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800
                dark:bg-{{ $statusColor }}-900/30 dark:text-{{ $statusColor }}-400">
                {{ $statusLabel }}
            </span>
        </div>

        {{-- Content --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Dari:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $disposisi->dariUser?->name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Kepada:</span>
                <span class="font-medium text-gray-900 dark:text-white">
                    {{ $disposisi->keUser?->name ?? ($disposisi->keUnit?->nama ?? '-') }}
                </span>
            </div>
            <div class="sm:col-span-2">
                <span class="text-gray-500 dark:text-gray-400">Instruksi:</span>
                <span class="text-gray-900 dark:text-white">{{ $disposisi->instruksi }}</span>
            </div>
            @if ($disposisi->catatan)
                <div class="sm:col-span-2">
                    <span class="text-gray-500 dark:text-gray-400">Catatan:</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $disposisi->catatan }}</span>
                </div>
            @endif
            <div>
                <span class="text-gray-500 dark:text-gray-400">Tanggal:</span>
                <span
                    class="text-gray-700 dark:text-gray-300">{{ $disposisi->created_at?->format('d M Y H:i') }}</span>
            </div>
            @if ($disposisi->batas_waktu)
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Batas Waktu:</span>
                    <span
                        class="{{ $disposisi->batas_waktu->isPast() && $disposisi->status !== 'selesai' ? 'text-red-600 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                        {{ $disposisi->batas_waktu->format('d M Y') }}
                        @if ($disposisi->batas_waktu->isPast() && $disposisi->status !== 'selesai')
                            (Terlambat)
                        @endif
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- Children --}}
    @if ($disposisi->childrenRecursive && $disposisi->childrenRecursive->count() > 0)
        @foreach ($disposisi->childrenRecursive as $child)
            @include('filament.partials.disposisi-node', [
                'disposisi' => $child,
                'level' => $level + 1,
                'currentId' => $currentId,
            ])
        @endforeach
    @endif
</div>
