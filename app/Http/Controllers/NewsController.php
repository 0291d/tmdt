<?php

namespace App\Http\Controllers;

use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('images')->latest()->paginate(9);
        return view('pages.news.index', compact('news'));
    }

    public function show(News $news)
    {
        $news->load('images');
        return view('pages.news.show', compact('news'));
    }
}

