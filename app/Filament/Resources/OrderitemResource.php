<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-list';
    protected static ?string $navigationGroup = 'Quản lý cửa hàng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('order_id')
                ->label('Đơn hàng')
                ->relationship('order', 'id')
                ->required(),
            Forms\Components\Select::make('product_id')
                ->label('Sản phẩm')
                ->relationship('product', 'name')
                ->required(),
            Forms\Components\TextInput::make('quantity')->label('Số lượng')->numeric()->required(),
            Forms\Components\Placeholder::make('price_display')
                ->label('Giá')
                ->content(function ($get, ?OrderItem $record) {
                    $pid = $get('product_id');
                    $price = $record?->price ?? ($pid ? Product::whereKey($pid)->value('price') : null);
                    return $price ? number_format($price, 0, ',', '.') . ' VND' : '-';
                })
                ->columnSpan('full'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('order.id')->label('Đơn hàng')->searchable(),
            Tables\Columns\TextColumn::make('order.customer.phone')->label('SĐT khách')->searchable(),
            Tables\Columns\TextColumn::make('product.name')->label('Sản phẩm')->searchable(),
            Tables\Columns\TextColumn::make('quantity')->label('Số lượng'),
            Tables\Columns\TextColumn::make('price')->label('Giá'),
        ])
        ->filters([
            Tables\Filters\Filter::make('phone')
                ->form([
                    Forms\Components\TextInput::make('phone')->label('SĐT khách')
                ])
                ->query(function ($query, array $data) {
                    $phone = trim((string)($data['phone'] ?? ''));
                    if ($phone === '') return $query;
                    return $query->whereHas('order.customer', function ($q) use ($phone) {
                        $q->where('phone', 'like', "%{$phone}%");
                    });
                }),
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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
