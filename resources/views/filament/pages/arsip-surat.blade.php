<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Tab Switcher --}}
        <div class="flex gap-2">
            <button wire:click="switchTab('masuk')"
                class="px-4 py-2 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'masuk' ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' }}">
                📥 Arsip Surat Masuk
            </button>
            <button wire:click="switchTab('keluar')"
                class="px-4 py-2 rounded-lg font-medium text-sm transition-all {{ $activeTab === 'keluar' ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' }}">
                📤 Arsip Surat Keluar
            </button>
        </div>

        {{-- Table --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
