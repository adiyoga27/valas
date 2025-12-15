<?php

namespace App\Filament\Resources\Offices\Pages;

use App\Filament\Resources\Offices\OfficeResource;
use App\Models\Office;
use Filament\Resources\Pages\EditRecord;

class EditOffice extends EditRecord
{
    protected static string $resource = OfficeResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = Office::first()->toArray();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
