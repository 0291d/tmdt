<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductDetailResource\Pages;
use App\Models\ProductDetail;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ProductDetailResource extends Resource
{
    protected static ?string $model = ProductDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Quáº£n lÃ½ cá»­a hÃ ng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->label('Sáº£n pháº©m')
                ->relationship('product', 'name')
                ->required(),

            Forms\Components\TextInput::make('width')->label('Rá»™ng (cm)')->numeric()->required(),
            Forms\Components\TextInput::make('length')->label('DÃ i (cm)')->numeric()->required(),
            Forms\Components\TextInput::make('height')->label('Cao (cm)')->numeric()->required(),
            Forms\Components\Textarea::make('origin')->label('Xuáº¥t xá»©')->required(),
            Forms\Components\Textarea::make('finishes')->label('HoÃ n thiá»‡n bá» máº·t')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Sáº£n pháº©m')->searchable(),
                Tables\Columns\TextColumn::make('width')->label('Rá»™ng'),
                Tables\Columns\TextColumn::make('length')->label('DÃ i'),
                Tables\Columns\TextColumn::make('height')->label('Cao'),
                Tables\Columns\TextColumn::make('origin')->label('Xuáº¥t xá»©'),
                Tables\Columns\TextColumn::make('finishes')->label('HoÃ n thiá»‡n'),
                Tables\Columns\TextColumn::make('created_at')->label('NgÃ y táº¡o')->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        // Định tuyến trang admin: List/Create/Edit
        return [
            'index' => Pages\ListProductDetails::route('/'),
            'create' => Pages\CreateProductDetail::route('/create'),
            'edit' => Pages\EditProductDetail::route('/{record}/edit'),
        ];
    }
}






