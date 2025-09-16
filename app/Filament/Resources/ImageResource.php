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
    protected static ?string $navigationGroup = 'Quáº£n lÃ½ cá»­a hÃ ng';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('imageable_type')
                ->label('Loáº¡i Ä‘á»‘i tÆ°á»£ng')
                ->options([
                    Product::class => 'Sáº£n pháº©m',
                    News::class => 'BÃ i viáº¿t',
                ])
                ->reactive()
                ->required(),

            Forms\Components\Select::make('imageable_id')
                ->label('Äá»‘i tÆ°á»£ng liÃªn káº¿t')
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
                ->label('ÄÆ°á»ng dáº«n áº£nh')
                ->required(),

            Forms\Components\Toggle::make('is_main')
                ->label('áº¢nh chÃ­nh')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('imageable_type')
                ->label('Loáº¡i')
                ->sortable(),

            Tables\Columns\TextColumn::make('imageable_id')
                ->label('ID liÃªn káº¿t'),

            Tables\Columns\TextColumn::make('path')
                ->label('ÄÆ°á»ng dáº«n'),

            Tables\Columns\IconColumn::make('is_main')
                ->boolean()
                ->label('áº¢nh chÃ­nh'),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime('d/m/Y H:i')
                ->label('NgÃ y táº¡o'),
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
            'index' => Pages\ListImages::route('/'),
            'create' => Pages\CreateImage::route('/create'),
            'edit' => Pages\EditImage::route('/{record}/edit'),
        ];
    }
}






