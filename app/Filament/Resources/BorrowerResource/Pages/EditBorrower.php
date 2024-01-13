<?php

namespace App\Filament\Resources\BorrowerResource\Pages;

use Illuminate\Database\Eloquent\Model;

use App\Filament\Resources\BorrowerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBorrower extends EditRecord
{
    protected static string $resource = BorrowerResource::class;
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['mobile'];
        $record->update($data);

        return $record;
    }
    protected function getHeaderActions(): array
    {


        return [
            Actions\DeleteAction::make(),
        ];
    }
}
