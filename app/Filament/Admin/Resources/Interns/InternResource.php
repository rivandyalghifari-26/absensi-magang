<?php

namespace App\Filament\Admin\Resources\Interns;

use App\Filament\Admin\Resources\Interns\Pages\CreateIntern;
use App\Filament\Admin\Resources\Interns\Pages\EditIntern;
use App\Filament\Admin\Resources\Interns\Pages\ListInterns;
use App\Filament\Admin\Resources\Interns\Schemas\InternForm;
use App\Filament\Admin\Resources\Interns\Tables\InternsTable;
use App\Models\Intern;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Admin\Resources\Interns\Pages\SetupFace;
class InternResource extends Resource
{
    protected static ?string $model = Intern::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen SDM';

    protected static ?string $navigationLabel = 'Magang';
    protected static ?string $modelLabel = 'Magang';
    protected static ?string $slug = 'magang';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'intern_code';


    public static function form(Schema $schema): Schema
    {
        return InternForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InternsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInterns::route('/'),
            'create' => CreateIntern::route('/create'),
            'edit' => EditIntern::route('/{record}/edit'),
            'set-face' => SetupFace::route('/{record}/set-face'),
        ];
    }
}
