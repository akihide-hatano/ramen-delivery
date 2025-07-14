<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\DB; // DBファサードをuseする
use Illuminate\Support\Facades\Log; // Logファサードをuseする

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

    public function show(Request $request, Shop $shop)
    {
        // ★★★ここを修正します★★★
        // ルートモデルバインディングで取得した$shopはlat/lonを持たないので、
        // location_wktを取得し、lat/lonをパースして$shopオブジェクトに設定し直す
        $shop = Shop::where('id', $shop->id)
                    ->select('*') // 全てのカラムを選択
                    ->selectRaw("ST_AsText(location) AS location_wkt") // locationをWKT形式の文字列として取得
                    ->firstOrFail(); // 該当店舗が見つからない場合は404エラー

        // location_wktから緯度・経度をPHPでパースする
        if ($shop->location_wkt) {
            // "POINT(経度 緯度)" の形式から緯度・経度を正規表現で抽出
            if (preg_match('/POINT\(([\d\.\-]+)\s+([\d\.\-]+)\)/', $shop->location_wkt, $matches)) {
                $shop->lon = (float)$matches[1]; // 店舗の経度
                $shop->lat = (float)$matches[2]; // 店舗の緯度
            } else {
                Log::warning("Failed to parse location_wkt in ShopController@show for shop ID {$shop->id}: " . $shop->location_wkt);
                $shop->lat = null;
                $shop->lon = null;
            }
        } else {
            $shop->lat = null;
            $shop->lon = null;
        }
        // ★★★修正ここまで★★★

        $shop->load('products'); // 店舗に紐づく商品（メニュー）をロード

        $mapsApiKey = env('MAPS_API_KEY');
        $products = $shop->products;

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