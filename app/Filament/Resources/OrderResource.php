<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Quản lý cửa hàng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('customer_id')
                ->label('Khách hàng')
                ->relationship('customer', 'phone')
                ->required(),

            Forms\Components\Select::make('coupon_id')
                ->label('Mã giảm giá')
                ->relationship('coupon', 'code')
                ->nullable(),

            Forms\Components\Placeholder::make('total_display')
                ->label('Tổng sau giảm')
                ->content(function (?Order $record) {
                    return $record ? number_format((int) $record->total, 0, ',', '.') . ' VND' : null;
                })
                ->hiddenOn('create'),

            Forms\Components\Select::make('status')
                ->label('Trạng thái')
                ->options([
                    'pending' => 'Đang chờ',
                    'paid' => 'Đã thanh toán',
                    'completed' => 'Hoàn tất',
                    'canceled' => 'Hủy',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('customer.phone')->label('Khách hàng')->searchable(),
            Tables\Columns\TextColumn::make('coupon.code')->label('Mã giảm giá'),
            Tables\Columns\TextColumn::make('discount_amount')
                ->label('Giảm (VND)')
                ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.')),
            Tables\Columns\TextColumn::make('total')
                ->label('Tổng sau giảm')
                ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.')),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Trạng thái')
                ->enum([
                    'pending' => 'Đang chờ',
                    'paid' => 'Đã thanh toán',
                    'completed' => 'Hoàn tất',
                    'canceled' => 'Hủy',
                ]),
            Tables\Columns\TextColumn::make('created_at')->label('Ngày tạo')->dateTime('d/m/Y H:i'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('confirm')
                ->label('Confirm')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (Order $record) {
                    $record->status = 'completed';
                    $record->save();
                }),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

