<?php

namespace App\Filament\Resources\Offices\Pages;

use App\Filament\Resources\Offices\OfficeResource;
use App\Models\Office;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOffices extends ListRecords
{

    protected static string $resource = OfficeResource::class;

   
    public function mount(): void
    {
        $office = Office::first();

        if ($office) {
            $this->redirect(
                OfficeResource::getUrl('edit', ['record' => $office->id])
            );
        }

        $this->redirect(
            OfficeResource::getUrl('create')
        );
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
