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
    protected static ?string $navigationGroup = 'Quáº£n lÃ½ cá»­a hÃ ng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('customer_id')
                ->label('KhÃ¡ch hÃ ng')
                ->relationship('customer', 'phone')
                ->required(),

            Forms\Components\Select::make('coupon_id')
                ->label('MÃ£ giáº£m giÃ¡')
                ->relationship('coupon', 'code')
                ->nullable(),

            Forms\Components\Placeholder::make('total_display')
                ->label('Tá»•ng sau giáº£m')
                ->content(function (?Order $record) {
                    return $record ? number_format((int) $record->total, 0, ',', '.') . ' VND' : null;
                })
                ->hiddenOn('create'),

            Forms\Components\Select::make('status')
                ->label('Tráº¡ng thÃ¡i')
                ->options([
                    'pending' => 'Äang chá»',
                    'paid' => 'ÄÃ£ thanh toÃ¡n',
                    'completed' => 'HoÃ n táº¥t',
                    'canceled' => 'Há»§y',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('customer.phone')->label('KhÃ¡ch hÃ ng')->searchable(),
            Tables\Columns\TextColumn::make('coupon.code')->label('MÃ£ giáº£m giÃ¡'),
            Tables\Columns\TextColumn::make('discount_amount')
                ->label('Giáº£m (VND)')
                ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.')),
            Tables\Columns\TextColumn::make('total')
                ->label('Tá»•ng sau giáº£m')
                ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.')),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Tráº¡ng thÃ¡i')
                ->enum([
                    'pending' => 'Äang chá»',
                    'paid' => 'ÄÃ£ thanh toÃ¡n',
                    'completed' => 'HoÃ n táº¥t',
                    'canceled' => 'Há»§y',
                ]),
            Tables\Columns\TextColumn::make('created_at')->label('NgÃ y táº¡o')->dateTime('d/m/Y H:i'),
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
        // Định tuyến trang admin: List/Create/Edit
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}







