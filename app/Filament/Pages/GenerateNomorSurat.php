<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\GeneratedNomorSurat;
use App\Models\Klasifikasi;
use App\Models\SuratKeluar;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class GenerateNomorSurat extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.generate-nomor-surat';

    protected static string|UnitEnum|null $navigationGroup = 'Persuratan';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationLabel = 'Generate Nomor';

    protected static ?string $title = 'Generate Nomor Surat';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public ?string $generatedNomor = null;

    public function mount(): void
    {
        $this->form->fill([
            'tanggal_surat' => now()->toDateString(),
            'sifat_surat' => 'biasa',
        ]);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->canCreateSuratKeluar() ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Data Surat Manual')
                    ->description('Gunakan menu ini untuk mencadangkan nomor surat yang dokumennya dibuat di luar sistem, misalnya di Microsoft Word.')
                    ->schema([
                        Forms\Components\Select::make('klasifikasi')
                            ->label('Klasifikasi / Jenis Surat')
                            ->options(fn (): array => Klasifikasi::query()
                                ->select(['id', 'kode', 'nama'])
                                ->where('is_active', true)
                                ->orderBy('kode')
                                ->get()
                                ->mapWithKeys(fn (Klasifikasi $klasifikasi): array => [
                                    $klasifikasi->id => "{$klasifikasi->kode} - {$klasifikasi->nama}",
                                ])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_surat')
                            ->label('Tanggal Surat')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('tujuan')
                            ->label('Tujuan')
                            ->maxLength(255)
                            ->placeholder('Opsional'),
                        Forms\Components\Textarea::make('perihal')
                            ->label('Perihal')
                            ->rows(3)
                            ->placeholder('Opsional')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('sifat_surat')
                            ->label('Sifat Surat')
                            ->options([
                                'biasa' => 'Biasa',
                                'penting' => 'Penting',
                                'rahasia' => 'Rahasia',
                                'sangat_rahasia' => 'Sangat Rahasia',
                            ])
                            ->default('biasa')
                            ->required(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->placeholder('Opsional')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                GeneratedNomorSurat::query()
                    ->with(['generatedBy', 'klasifikasiSurat'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('klasifikasiSurat.kode')
                    ->label('Klasifikasi')
                    ->badge()
                    ->default('-'),
                Tables\Columns\TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->default('-')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->default('-')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'used' => 'success',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'used' => 'Terpakai',
                        default => 'Dicadangkan',
                    }),
                Tables\Columns\TextColumn::make('generatedBy.name')
                    ->label('Dibuat Oleh')
                    ->default('-')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'reserved' => 'Dicadangkan',
                        'used' => 'Terpakai',
                    ]),
            ]);
    }

    public function generateNomor(): void
    {
        $data = $this->form->getState();

        $generatedNomorSurat = DB::transaction(function () use ($data): GeneratedNomorSurat {
            return GeneratedNomorSurat::create([
                'nomor_surat' => SuratKeluar::generateNomorSurat((int) $data['klasifikasi'], $data['tanggal_surat']),
                'tanggal_surat' => $data['tanggal_surat'],
                'tujuan' => $data['tujuan'] ?? null,
                'perihal' => $data['perihal'] ?? null,
                'klasifikasi' => $data['klasifikasi'],
                'sifat_surat' => $data['sifat_surat'],
                'status' => 'reserved',
                'keterangan' => filled($data['keterangan'] ?? null)
                    ? $data['keterangan']
                    : 'Nomor dicadangkan untuk surat yang dibuat di luar sistem.',
                'generated_by' => auth()->id(),
            ]);
        });

        $this->generatedNomor = $generatedNomorSurat->nomor_surat;

        $this->form->fill([
            'tanggal_surat' => now()->toDateString(),
            'sifat_surat' => 'biasa',
        ]);

        Notification::make()
            ->title('Nomor surat berhasil dibuat')
            ->body($this->generatedNomor)
            ->success()
            ->send();
    }
}
