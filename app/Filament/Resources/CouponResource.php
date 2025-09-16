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
    protected static ?string $navigationGroup = 'Quáº£n lÃ½ cá»­a hÃ ng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')->label('MÃ£ giáº£m giÃ¡')->required(),
            Forms\Components\TextInput::make('discount_percent')
                ->label('Pháº§n trÄƒm giáº£m')
                ->numeric()
                ->required(),
            Forms\Components\DatePicker::make('expiry_date')
                ->label('NgÃ y háº¿t háº¡n')
                ->required(),
            Forms\Components\TextInput::make('max_uses')
                ->label('Sá»‘ láº§n sá»­ dá»¥ng tá»‘i Ä‘a')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('used_count')
                ->label('ÄÃ£ sá»­ dá»¥ng')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->label('MÃ£ giáº£m giÃ¡')->searchable(),
            Tables\Columns\TextColumn::make('discount_percent')->label('Giáº£m (%)'),
            Tables\Columns\TextColumn::make('expiry_date')->label('NgÃ y háº¿t háº¡n')->date(),
            Tables\Columns\TextColumn::make('max_uses')->label('Tá»‘i Ä‘a'),
            Tables\Columns\TextColumn::make('used_count')->label('ÄÃ£ dÃ¹ng'),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}






