<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Quản lý cửa hàng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')->label('Mã giảm giá')->required(),
            Forms\Components\TextInput::make('discount_percent')
                ->label('Phần trăm giảm')
                ->numeric()
                ->required(),
            Forms\Components\DatePicker::make('expiry_date')
                ->label('Ngày hết hạn')
                ->required(),
            Forms\Components\TextInput::make('max_uses')
                ->label('Số lần sử dụng tối đa')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('used_count')
                ->label('Đã dùng')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->label('Ma giam gia')->searchable(),
            Tables\Columns\TextColumn::make('discount_percent')->label('Giam (%)'),
            Tables\Columns\TextColumn::make('expiry_date')->label('Ngay het han')->date(),
            Tables\Columns\TextColumn::make('max_uses')->label('Toi da'),
            Tables\Columns\TextColumn::make('used_count')->label('Da dung'),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}

