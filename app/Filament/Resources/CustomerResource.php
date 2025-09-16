<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Quáº£n lÃ½ ngÆ°á»i dÃ¹ng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('NgÆ°á»i dÃ¹ng')
                ->relationship('user', 'name')
                ->required(),
            Forms\Components\TextInput::make('phone')->label('Sá»‘ Ä‘iá»‡n thoáº¡i')->required(),
            Forms\Components\Textarea::make('address')->label('Äá»‹a chá»‰')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('NgÆ°á»i dÃ¹ng'),
            Tables\Columns\TextColumn::make('phone')->label('Sá»‘ Ä‘iá»‡n thoáº¡i'),
            Tables\Columns\TextColumn::make('address')->label('Äá»‹a chá»‰')->limit(30),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}






