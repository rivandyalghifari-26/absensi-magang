<?php

namespace App\Filament\Resources\LeaveRequests;

use App\Filament\Resources\LeaveRequests\Pages\CreateLeaveRequests;
use App\Filament\Resources\LeaveRequests\Pages\EditLeaveRequests;
use App\Filament\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Resources\LeaveRequests\Pages\ViewLeaveRequests;
use App\Filament\Resources\LeaveRequests\Schemas\LeaveRequestsForm;
use App\Filament\Resources\LeaveRequests\Schemas\LeaveRequestsInfolist;
use App\Filament\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use App\Models\LeaveRequest;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeaveRequestsResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    
    protected static ?string $navigationLabel = 'Pengajuan Izin';
    protected static ?string $modelLabel = 'Pengajuan Izin';
    protected static ?string $slug = 'pengajuan-izin';
    protected static ?int $navigationSort = 3;

    

    public static function getEloquentQuery(): Builder 
    {
        return parent::getEloquentQuery()->whereInternId(Auth::user()->intern->id);
    }

   public static function canEdit(Model $record): bool
   {
    return $record->status === 'pending';
   } 

   public static function canDelete(Model $record): bool 
   {
     return $record->status === 'pending';
   }



    public static function form(Schema $schema): Schema
    {
        return LeaveRequestsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeaveRequestsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveRequestsTable::configure($table);
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
            'index' => ListLeaveRequests::route('/'),
            'create' => CreateLeaveRequests::route('/create'),
            'view' => ViewLeaveRequests::route('/{record}'),
            'edit' => EditLeaveRequests::route('/{record}/edit'),
        ];
    }
}
