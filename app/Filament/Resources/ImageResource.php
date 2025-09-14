<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageResource\Pages;
use App\Models\Image;
use App\Models\Product;
use App\Models\News;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ImageResource extends Resource
{
    protected static ?string $model = Image::class;

    protected static ?string $navigationIcon = 'heroicon-o-photograph';
    protected static ?string $navigationGroup = 'Quản lý cửa hàng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('imageable_type')
                ->label('Loại đối tượng')
                ->options([
                    Product::class => 'Sản phẩm',
                    News::class => 'Bài viết',
                ])
                ->reactive()
                ->required(),

            Forms\Components\Select::make('imageable_id')
                ->label('Đối tượng liên kết')
                ->options(function (callable $get) {
                    $type = $get('imageable_type');

                    if ($type === Product::class) {
                        return Product::pluck('name', 'id');
                    }

                    if ($type === News::class) {
                        return News::pluck('title', 'id');
                    }

                    return [];
                })
                ->required(),

            Forms\Components\FileUpload::make('path')
                ->image()
                ->disk('public')
                ->directory('images')
                ->visibility('public')
                ->label('Đường dẫn ảnh')
                ->required(),

            Forms\Components\Toggle::make('is_main')
                ->label('Ảnh chính')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('imageable_type')
                ->label('Loại')
                ->sortable(),

            Tables\Columns\TextColumn::make('imageable_id')
                ->label('ID liên kết'),

            Tables\Columns\TextColumn::make('path')
                ->label('Đường dẫn'),

            Tables\Columns\IconColumn::make('is_main')
                ->boolean()
                ->label('Ảnh chính'),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime('d/m/Y H:i')
                ->label('Ngày tạo'),
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
            'index' => Pages\ListImages::route('/'),
            'create' => Pages\CreateImage::route('/create'),
            'edit' => Pages\EditImage::route('/{record}/edit'),
        ];
    }
}
