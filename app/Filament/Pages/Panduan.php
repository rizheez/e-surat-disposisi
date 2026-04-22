<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Panduan extends Page
{
    protected string $view = 'filament.pages.panduan';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationLabel = 'Panduan';

    protected static ?string $title = 'Panduan';

    protected static ?int $navigationSort = 99;
}
