<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class SetupFace extends Page
{
    protected static ?string $title =
        'Setup Face';

    protected static ?string $slug =
        'setup-face';

    protected static bool
        $shouldRegisterNavigation = true;

    protected static string|BackedEnum|null
        $navigationIcon =
        'heroicon-o-face-smile';

    protected string $view =
        'filament.pages.setup-face';
}