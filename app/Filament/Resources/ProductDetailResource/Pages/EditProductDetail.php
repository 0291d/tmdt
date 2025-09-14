<?php

namespace App\Filament\Resources\ProductDetailResource\Pages;

use App\Filament\Resources\ProductDetailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductDetail extends EditRecord
{
    protected static string $resource = ProductDetailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
