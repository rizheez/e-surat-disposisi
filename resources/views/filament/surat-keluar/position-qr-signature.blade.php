<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Info Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $record->nomor_surat ?? 'Draft' }}
                    </h3>
                    <p class="text-sm text-gray-500">{{ $record->perihal }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">
                        <strong id="pos-display">X: {{ $posX }}, Y: {{ $posY }}, Ukuran:
                            {{ $qrSize }}px</strong>
                    </span>
                    <x-filament::button type="button" onclick="saveCurrentPosition()" color="info"
                        icon="heroicon-o-document-check">
                        Simpan Posisi
                    </x-filament::button>

                    <x-filament::button wire:click="approveAndSave"
                        wire:confirm="Setujui surat ini dan simpan posisi QR?" color="success"
                        icon="heroicon-o-check-circle">
                        Setujui & Simpan
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Preview Area --}}
        <div class="flex justify-center" style="display: flex; justify-content: center;">
            <div id="preview-area" class="relative bg-white shadow-2xl border border-gray-300"
                style="position: relative; width: 595px; height: 842px; overflow: hidden; background-color: white;">

                {{-- PDF Background --}}
                <div id="pdf-loading" class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10"
                    style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; background-color: #f9fafb; z-index: 10;">
                    <p class="text-gray-500 flex flex-col items-center"
                        style="color: #6b7280; display: flex; flex-direction: column; align-items: center;">
                        <svg class="animate-spin h-8 w-8 text-blue-600 mb-2"
                            style="height: 2rem; width: 2rem; color: #2563eb; margin-bottom: 0.5rem;"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Memuat Pratinjau Dokumen...
                    </p>
                </div>
                <canvas id="pdf-canvas" class="absolute top-0 left-0 w-full h-full object-contain pointer-events-none"
                    style="position: absolute; top: 0; left: 0;"></canvas>

                {{-- Draggable QR Code --}}
                <div id="qr-draggable" class="absolute cursor-move select-none z-50 transition-shadow"
                    style="position: absolute; width: {{ $qrSize }}px; height: {{ $qrSize }}px; cursor: move; z-index: 50; user-select: none; touch-action: none; left: {{ $posX }}px; top: {{ $posY }}px;"
                    data-x="{{ $posX }}" data-y="{{ $posY }}">
                    <div style="position: relative; width: 100%; height: 100%;">
                        @if ($qrDataUri)
                            <img src="{{ $qrDataUri }}" alt="QR Code"
                                style="width: 100%; height: 100%; object-fit: contain; border: 2px solid #3b82f6; border-radius: 4px; background-color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"
                                draggable="false">
                        @else
                            <div
                                style="width: 100%; height: 100%; border: 2px dashed #9ca3af; border-radius: 4px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 10px; color: #6b7280; text-align: center;">QR
                                    Code<br>Error</span>
                            </div>
                        @endif
                        <div
                            style="position: absolute; top: -20px; left: 0; right: 0; text-align: center; pointer-events: none;">
                            <span
                                style="background-color: #2563eb; color: white; font-size: 10px; padding: 2px 8px; border-radius: 999px; white-space: nowrap; box-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                                Geser & Resize
                            </span>
                        </div>
                        {{-- Resize Handle --}}
                        <div class="resize-handle"
                            style="position: absolute; width: 15px; height: 15px; background: #2563eb; right: -5px; bottom: -5px; cursor: nwse-resize; border-radius: 50%; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.3); z-index: 60;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center text-sm text-gray-500">
            <p>Drag QR code untuk memindah, dan tarik pojok kanan-bawah untuk memperbesar/memperkecil. Klik
                <strong>Setujui & Simpan</strong> jika sudah pas.</p>
        </div>
    </div>

    {{-- PDF.js for rendering PDF background --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>
    {{-- Interact.js via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // PDF Loading Logic
            const pdfUrl = "{{ route('pdf.surat-keluar.background', $record) }}";
            const loadingIndicator = document.getElementById('pdf-loading');

            pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                pdf.getPage(1).then(page => {
                    const canvas = document.getElementById('pdf-canvas');
                    const context = canvas.getContext('2d');

                    // Calculate scale to match our container dimension
                    const containerWidth = 595;
                    const viewport = page.getViewport({
                        scale: 1.0
                    });
                    const scale = containerWidth / viewport.width;

                    // Render scale (improve visual quality on retina displays)
                    const renderScale = scale * 2;
                    const scaledViewport = page.getViewport({
                        scale: renderScale
                    });

                    canvas.width = scaledViewport.width;
                    canvas.height = scaledViewport.height;

                    // Force CSS dimensions to exactly match the container
                    canvas.style.width = '100%';
                    canvas.style.height = '100%';

                    const renderContext = {
                        canvasContext: context,
                        viewport: scaledViewport
                    };

                    page.render(renderContext).promise.then(() => {
                        loadingIndicator.style.display = 'none';
                    });
                });
            }).catch(error => {
                console.error("Error loading PDF preview:", error);
                loadingIndicator.innerHTML =
                    '<p class="text-red-500 bg-red-50 p-2 rounded">Gagal memuat pratinjau dokumen.</p>';
            });

            const previewArea = document.getElementById('preview-area');
            const qrElement = document.getElementById('qr-draggable');
            const posDisplay = document.getElementById('pos-display');

            let currentX = {{ $posX }};
            let currentY = {{ $posY }};
            let currentSize = {{ $qrSize }};

            interact('#qr-draggable')
                .draggable({
                    inertia: true,
                    modifiers: [
                        interact.modifiers.restrictRect({
                            restriction: '#preview-area',
                            endOnly: true,
                        })
                    ],
                    autoScroll: false,
                    listeners: {
                        move(event) {
                            currentX += event.dx;
                            currentY += event.dy;

                            // Clamp within preview area
                            const maxX = previewArea.offsetWidth - currentSize;
                            const maxY = previewArea.offsetHeight - currentSize;
                            currentX = Math.max(0, Math.min(currentX, maxX));
                            currentY = Math.max(0, Math.min(currentY, maxY));

                            qrElement.style.left = currentX + 'px';
                            qrElement.style.top = currentY + 'px';

                            posDisplay.textContent =
                                `X: ${Math.round(currentX)}, Y: ${Math.round(currentY)}, Ukuran: ${Math.round(currentSize)}px`;
                        }
                    }
                })
                .resizable({
                    edges: {
                        left: false,
                        right: '.resize-handle',
                        bottom: '.resize-handle',
                        top: false
                    },
                    modifiers: [
                        interact.modifiers.restrictEdges({
                            outer: '#preview-area'
                        }),
                        interact.modifiers.restrictSize({
                            min: {
                                width: 50,
                                height: 50
                            },
                            max: {
                                width: 200,
                                height: 200
                            }
                        })
                    ],
                    listeners: {
                        move: function(event) {
                            // Maintain aspect ratio globally
                            let size = Math.max(event.rect.width, event.rect.height);

                            // clamp max size based on remaining right/bottom space of the container
                            const maxW = previewArea.offsetWidth - currentX;
                            const maxH = previewArea.offsetHeight - currentY;
                            size = Math.min(size, maxW, maxH);
                            currentSize = size;

                            qrElement.style.width = size + 'px';
                            qrElement.style.height = size + 'px';

                            posDisplay.textContent =
                                `X: ${Math.round(currentX)}, Y: ${Math.round(currentY)}, Ukuran: ${Math.round(currentSize)}px`;
                        }
                    }
                });

            window.saveCurrentPosition = function() {
                @this.call('savePosition', Math.round(currentX), Math.round(currentY), Math.round(currentSize));
            };
        });
    </script>
</x-filament-panels::page>
