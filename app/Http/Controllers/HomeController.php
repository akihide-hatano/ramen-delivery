<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop; // Shopモデルをuse
use App\Models\Product; // Productモデルをuse
use App\Models\Category; // Categoryモデルをuse
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * トップページを表示
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // home.blade.phpで表示するデータをここに記述
        // 例: おすすめ店舗、おすすめ商品など

        $latitude = (float)$request->query('lat');
        $longitude = (float)$request->query('lon');
        $radiusKm = 20; // 検索半径（km）

        $nearbyShops = collect(); // 初期化
        $message = '位置情報を許可すると、お近くの店舗が表示されます。';

        if ($latitude && $longitude) {
            $userLat = $latitude;
            $userLon = $longitude;

            $shops = Shop::select('*')
                        ->whereNotNull('lat')
                        ->whereNotNull('lon')
                        ->get();

            $filteredShops = collect();

            foreach ($shops as $shop) {
                $shopLat = (float)$shop->lat;
                $shopLon = (float)$shop->lon;

                $theta = $userLon - $shopLon;
                $dist = sin(deg2rad($userLat)) * sin(deg2rad($shopLat)) + cos(deg2rad($userLat)) * cos(deg2rad($shopLat)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $meters = $dist * 60 * 1.1515 * 1609.344;

                $shop->distance = $meters;
                if ($shop->distance <= $radiusKm * 1000) {
                    $filteredShops->push($shop);
                }
            }

            $nearbyShops = $filteredShops->sortBy('distance')->values();

            if ($nearbyShops->isNotEmpty()) {
                $message = '現在地から' . $radiusKm . 'km圏内に店舗が見つかりました。';
            } else {
                $message = '現在地から' . $radiusKm . 'km圏内に店舗が見つかりませんでした。';
            }
        } else {
            $message = '位置情報を許可すると、お近くの店舗が表示されます。';
        }

        $featuredProducts = Product::with('shops') // 紐づく店舗情報もロード（home.blade.phpで表示するため必要）
            ->whereIn('name', ['潮屋塩ラーメン', '味玉潮屋塩ラーメン']) // 特定の2つの商品名で絞り込み
            ->where('is_limited', false) // 限定商品ではないことを確認
            ->where('is_delivery', true) // 配達可能であることを確認
            ->get(); // 取得

        $mapsApiKey = env('Maps_API_KEY');

        return view('home', compact('nearbyShops', 'message', 'featuredProducts','mapsApiKey', 'latitude', 'longitude'));
    }
}