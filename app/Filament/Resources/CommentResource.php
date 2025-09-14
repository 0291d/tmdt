<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat';
    protected static ?string $navigationGroup = 'Quản lý cửa hàng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Người dùng')
                ->relationship('user', 'name')
                ->required(),
            Forms\Components\Select::make('product_id')
                ->label('Sản phẩm')
                ->relationship('product', 'name')
                ->required(),
            Forms\Components\Textarea::make('content')
                ->label('Nội dung bình luận')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('Người dùng'),
            Tables\Columns\TextColumn::make('product.name')->label('Sản phẩm'),
            Tables\Columns\TextColumn::make('content')->label('Nội dung')->limit(50),
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
