<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Quản lý người dùng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Tên')->required(),
            Forms\Components\TextInput::make('email')->label('Email')->email()->required(),
            Forms\Components\TextInput::make('password')
                ->label('Mật khẩu')
                ->password()
                ->required(fn ($record) => $record === null), 
            Forms\Components\Select::make('role')
                ->label('Vai trò')
                ->options([
                    'admin' => 'Admin',
                    'customer' => 'Customer',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Tên')->searchable(),
            Tables\Columns\TextColumn::make('email')->label('Email'),
            Tables\Columns\TextColumn::make('role')->label('Vai trò'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
