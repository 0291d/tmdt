<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Quáº£n lÃ½ cá»­a hÃ ng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('TÃªn sáº£n pháº©m')
                    ->required(),

                Forms\Components\TextInput::make('brand')
                    ->label('ThÆ°Æ¡ng hiá»‡u')
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('GiÃ¡')
                    ->required()
                    ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(0)
                        ->thousandsSeparator('.')
                    )
                    ->dehydrateStateUsing(fn ($state) => $state !== null
                        ? (int) str_replace('.', '', (string) $state)
                        : null
                    ),

                Forms\Components\Textarea::make('description')
                    ->label('MÃ´ táº£'),

                Forms\Components\TextInput::make('stock')
                    ->label('Sá»‘ lÆ°á»£ng tá»“n')
                    ->numeric(),

                Forms\Components\Select::make('category_id')
                    ->label('Danh má»¥c')
                    ->relationship('category', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('TÃªn')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('brand')
                    ->label('ThÆ°Æ¡ng hiá»‡u'),

                Tables\Columns\TextColumn::make('price')
                    ->label('GiÃ¡')
                    ->formatStateUsing(fn ($state) => is_null($state)
                        ? ''
                        : number_format((float) $state, 0, ',', '.') . ' â‚«'
                    ),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh má»¥c'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('NgÃ y táº¡o')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        // Định tuyến trang admin: List/Create/Edit
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}







