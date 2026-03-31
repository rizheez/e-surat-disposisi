<div class="p-6">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <x-heroicon-o-queue-list class="w-5 h-5 text-primary-500" />
            Alur Disposisi
        </h3>

        <div class="relative">
            {{-- Root node --}}
            @include('filament.partials.disposisi-node', [
                'disposisi' => $root,
                'level' => 1,
                'currentId' => $currentId,
            ])
        </div>
    </div>
</div>
