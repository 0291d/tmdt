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
    protected static ?string $navigationGroup = 'Quản lý cửa hàng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên sản phẩm')
                    ->required(),

                Forms\Components\TextInput::make('brand')
                    ->label('Thương hiệu')
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Giá')
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
                    ->label('Mô tả'),

                Forms\Components\TextInput::make('stock')
                    ->label('Số lượng tồn')
                    ->numeric(),

                Forms\Components\Select::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('brand')
                    ->label('Thương hiệu'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->formatStateUsing(fn ($state) => is_null($state)
                        ? ''
                        : number_format((float) $state, 0, ',', '.') . ' VND'
                    ),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i'),
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
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

