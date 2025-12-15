<?php

namespace App\Filament\Resources\Offices\Pages;

use App\Filament\Resources\Offices\OfficeResource;
use App\Models\Office;
use Filament\Resources\Pages\CreateRecord;

class CreateOffice extends CreateRecord
{
    protected static string $resource = OfficeResource::class;

    public function mount(): void
    {
        if (Office::exists()) {
            $this->redirect(
                OfficeResource::getUrl('edit', ['record' => Office::first()->id])
            );
        }
    }
}
