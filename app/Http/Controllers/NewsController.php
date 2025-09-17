<?php

namespace App\Http\Controllers;

use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        // Danh sách tin tức kèm ảnh, phân trang 9 bài/ trang
        $news = News::with('images')->latest()->paginate(9);
        return view('pages.news.index', compact('news'));
    }

    public function show(News $news)
    {
        // Trang chi tiết tin: nạp ảnh liên quan
        $news->load('images');
        return view('pages.news.show', compact('news'));
    }
}
