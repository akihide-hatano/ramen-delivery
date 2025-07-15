<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product; // Productモデルをuseに追加
use App\Models\Category; // Categoryモデルをuseに追加
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // ルートモデルバインディングで$shopオブジェクトは既に取得されているため、
        // 再度データベースから取得する必要はありません。
        // ただし、$shop->latと$shop->lonが確実にロードされていることを確認します。
        // もしShopモデルの$castsでlat/lonをキャストしていない場合、
        // ここで明示的にfloatにキャストすると安全です。
        $shop->lat = (float) $shop->lat;
        $shop->lon = (float) $shop->lon;

        // location_wktをパースするロジックは不要になります
        // if ($shop->location_wkt) { ... } のブロックは削除
        // $shop->lat と $shop->lon は既にモデルのプロパティとして利用可能です。
        // ★★★修正ここまで★★★

        $shop->load('products'); // 店舗に紐づく商品（メニュー）をロード

        $mapsApiKey = env('Maps_API_KEY');
        $products = $shop->products;

        $userLat = (float)$request->query('lat');
        $userLon = (float)$request->query('lon');
        $deliveryRadiusKm = 10; // 配達可能距離を10kmに設定

        $distanceKm = null;
        $isDeliverable = false;

        // ユーザーの現在地と店舗の緯度経度が両方ある場合のみ距離を計算
        // $shop->lat と $shop->lon は既に数値として利用可能です。
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