<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Support\Arr;

class ProductStats extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.product-stats';
    protected static ?string $navigationGroup = 'Thống kê';
    protected static ?string $navigationLabel = 'Biểu đồ sản phẩm';

    public $labels = [];
    public $series = [];
    public $allCategories = [];

    public function mount(): void
    {
        $selectedCategories = request()->input('categories', []);

        $this->allCategories = Category::select('id','name')->orderBy('name')->get()->toArray();

        $query = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->with('product');

        if (!empty($selectedCategories)) {
            $query->whereHas('product', function ($q) use ($selectedCategories) {
                $q->whereIn('category_id', Arr::wrap($selectedCategories));
            });
        }

        $rows = $query->get();
        $this->labels = $rows->map(fn($r) => optional($r->product)->name ?? $r->product_id)->toArray();
        $this->series = $rows->map(fn($r) => (int) $r->total_qty)->toArray();
    }
}
