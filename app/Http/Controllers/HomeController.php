<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;    // Shopモデルをuseする
use App\Models\Product; // Productモデルをuseする

class HomeController extends Controller
{
    /**
     * アプリケーションのトップページを表示
     */
    public function index()
    {
        // 例: おすすめの店舗を3つ取得
        $recommendedShops = Shop::all();

        // 例: 新着商品をいくつか取得
        $newProducts = Product::latest()->limit(5)->get();

        // ビューにデータを渡して表示
        return view('home', compact('recommendedShops', 'newProducts'));
    }
}