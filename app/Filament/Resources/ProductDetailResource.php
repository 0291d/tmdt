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
    protected static ?string $navigationGroup = 'Quản lý cửa hàng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->label('Sản phẩm')
                ->relationship('product', 'name')
                ->required(),

            Forms\Components\TextInput::make('width')->label('Rộng (cm)')->numeric()->required(),
            Forms\Components\TextInput::make('length')->label('Dài (cm)')->numeric()->required(),
            Forms\Components\TextInput::make('height')->label('Cao (cm)')->numeric()->required(),
            Forms\Components\Textarea::make('origin')->label('Xuất xứ')->required(),
            Forms\Components\Textarea::make('finishes')->label('Hoàn thiện')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Sản phẩm')->searchable(),
                Tables\Columns\TextColumn::make('width')->label('Rộng'),
                Tables\Columns\TextColumn::make('length')->label('Dài'),
                Tables\Columns\TextColumn::make('height')->label('Cao'),
                Tables\Columns\TextColumn::make('origin')->label('Xuất xứ'),
                Tables\Columns\TextColumn::make('finishes')->label('Hoàn thiện'),
                Tables\Columns\TextColumn::make('created_at')->label('Ngày tạo')->dateTime('d/m/Y H:i'),
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
        return [
            'index' => Pages\ListProductDetails::route('/'),
            'create' => Pages\CreateProductDetail::route('/create'),
            'edit' => Pages\EditProductDetail::route('/{record}/edit'),
        ];
    }
}

