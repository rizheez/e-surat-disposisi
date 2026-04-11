<x-filament-panels::page>
    <div class="space-y-6">
        @if ($generatedNomor)
            <x-filament::section>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor surat terakhir</p>
                        <p class="mt-1 text-2xl font-semibold tracking-normal text-gray-950 dark:text-white">
                            {{ $generatedNomor }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <x-filament::button tag="button" type="button" icon="heroicon-o-clipboard"
                            x-on:click="navigator.clipboard.writeText(@js($generatedNomor))">
                            Salin Nomor
                        </x-filament::button>

                        <p class="self-center text-sm text-gray-500 dark:text-gray-400">
                            Nomor sudah dicadangkan dan belum masuk ke data Surat Keluar.
                        </p>
                    </div>
                </div>
            </x-filament::section>
        @endif

        <form wire:submit="generateNomor" class="space-y-6">
            {{ $this->form }}

            <div class="flex">
                <x-filament::button class="ml-auto" type="submit" icon="heroicon-o-hashtag">
                    Generate Nomor
                </x-filament::button>
            </div>
        </form>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
