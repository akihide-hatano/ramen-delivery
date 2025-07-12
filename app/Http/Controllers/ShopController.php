<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop; // Shopモデルをuseする

class ShopController extends Controller
{
    /**
     * 店舗一覧ページを表示
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $shops = Shop::all();
        dump($shops);
        return view('shops.index', compact('shops'));
    }
}