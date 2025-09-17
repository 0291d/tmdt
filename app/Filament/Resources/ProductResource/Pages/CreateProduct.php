<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductDetail;
use Illuminate\Support\Str;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $product = $this->record;
        if ($product && !$product->detail()->exists()) {
            ProductDetail::create([
                'id' => (string) Str::uuid(),
                'product_id' => $product->id,
                'width' => 0,
                'length' => 0,
                'height' => 0,
                'origin' => '',
                'finishes' => '',
            ]);
        }
    }
}
