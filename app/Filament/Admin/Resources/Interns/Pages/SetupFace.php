<?php

namespace App\Filament\Admin\Resources\Interns\Pages;

use App\Filament\Admin\Resources\Interns\InternResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use App\Models\Intern;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;

class SetupFace extends Page
{
    use InteractsWithRecord;
    protected static string $resource = InternResource::class;

    protected string $view = 'filament.admin.resources.interns.pages.setup-face';
    protected static ?string $title = 'Setup Wajah Magang';

    public Intern $intern;

    public function mount(int|string $record) {
        $this->record = $this->resolveRecord($record);
    }

     protected function getHeaderActions(): array {
        return [
            Action::make('back')
            ->label('Kembali')
            ->color('gray')
            ->icon(HeroIcon::OutlinedArrowLeft)
            ->iconSize('xs')
            ->url($this->getResource()::getUrl('index')),
        ];
     }

}
