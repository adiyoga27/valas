<?php

namespace App\Filament\Resources\Offices\Pages;

use App\Filament\Resources\Offices\OfficeResource;
use App\Filament\Resources\Offices\Schemas\OfficeForm;
use App\Models\Office;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class OfficeSetting extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = OfficeResource::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Data Master';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected ?string $heading = 'Setting Kantor'; // Gunakan $heading untuk Filament v3/v4

    protected string $view = 'filament.resources.offices.pages.office-setting';

    public Office $office;

    public array $data = [];

    public function mount(): void
    {
        $this->office = new Office();
        if($this->office->count() === 0) {
            $this->office->create([
                'name' => 'PT Monica Sejahtera',
                'address' => 'Jl. Danau Tamblingan, Sanur, Denpasar Selatan, Kota Denpasar, Bali',
                'phone' => '0361 289092',
            ]);
        }else{
            $this->office = $this->office->first();
        }
  
        $this->form->fill($this->office->toArray());
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')->required()->label('Nama Kantor'),
            Textarea::make('address')->label('Alamat'),
            TextInput::make('phone')->label('No. Telepon'),
    FileUpload::make('logo')
    ->label('Logo')
    ->disk('public')
    ->directory('office')
    ->visibility('public')
    ->image()
    // ->preserveFilenames(false)
    ->maxSize(2048),

        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function save(): void
    {
  $data = $this->data;

    // 🔥 HANDLE LOGO
    if (! empty($data['logo']) && is_array($data['logo'])) {
        $file = collect($data['logo'])->first();

        if ($file instanceof TemporaryUploadedFile) {

            // hapus logo lama (optional tapi disarankan)
            if ($this->office->logo) {
                Storage::disk('public')->delete($this->office->logo);
            }

            // simpan file & ambil path
            $data['logo'] = $file->store('office', 'public');
        }
    } else {
        // kalau tidak upload baru, jangan overwrite
        unset($data['logo']);
    }

    $this->office->update($data);
       Notification::make()
        ->title('Berhasil')
        ->body('Data kantor berhasil disimpan')
        ->success()
        ->send();
    }
}
