<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
// use App\Models\Product; // Productモデルを使うならuseしておく

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
        $prefecture = $request->query('prefecture');
        $search = $request->query('search');

        $query = Shop::query();
        if ($prefecture) {
            $query->where('address', 'like', $prefecture . '%');
        }
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $shops = $query->get();
        $shopCount = $shops->count();

        return view('shops.index', compact('shops', 'prefecture','search','shopCount'));
    }

    public function show(Request $request, Shop $shop) // ★Request $request を追加★
    {
        $shop->load('products');

        $mapsApiKey = env('MAPS_API_KEY');
        $products = $shop->products;

        // ★★★ここから追加★★★
        $userLat = (float)$request->query('lat');
        $userLon = (float)$request->query('lon');
        $deliveryRadiusKm = 10; // 配達可能距離を10kmに設定

        $distanceKm = null;
        $isDeliverable = false;

        // ユーザーの現在地と店舗の緯度経度が両方ある場合のみ距離を計算
        if ($userLat && $userLon && $shop->lat && $shop->lon) {
            $theta = $userLon - $shop->lon;
            $dist = sin(deg2rad($userLat)) * sin(deg2rad($shop->lat)) + cos(deg2rad($userLat)) * cos(deg2rad($shop->lat)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $meters = $dist * 60 * 1.1515 * 1609.344; // マイルからメートルに変換
            $distanceKm = $meters / 1000; // メートルをキロメートルに変換

            if ($distanceKm <= $deliveryRadiusKm) {
                $isDeliverable = true;
            }
        }

        // ビューに距離と配達可能フラグ、配達可能距離を渡す
        return view('shops.show', compact('shop', 'mapsApiKey', 'products', 'distanceKm', 'isDeliverable', 'deliveryRadiusKm'));
    }
}