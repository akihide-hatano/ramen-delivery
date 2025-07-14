<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product;
use App\Models\Category;
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
        $latitude = (float)$request->query('lat');
        $longitude = (float)$request->query('lon');
        $radiusKm = 50; // 検索半径（km）

        $nearbyShops = collect(); // 初期化
        $message = '位置情報を許可すると、お近くの店舗が表示されます。';

        if ($latitude && $longitude) {
            // ユーザーの現在地
            $userLat = $latitude;
            $userLon = $longitude;

            // データベースからすべての店舗を取得し、location_wktとして取得
            // ここでは距離計算は行わない
            $shops = Shop::whereNotNull('location')
                         ->select('*') // 全てのカラムを選択
                         ->selectRaw("ST_AsText(location) AS location_wkt") // locationをWKT形式の文字列として取得
                         ->get();

            $filteredShops = collect();

            foreach ($shops as $shop) {
                if ($shop->location_wkt) {
                    // "POINT(経度 緯度)" の形式から緯度・経度を正規表現で抽出
                    if (preg_match('/POINT\(([\d\.\-]+)\s+([\d\.\-]+)\)/', $shop->location_wkt, $matches)) {
                        $shopLon = (float)$matches[1]; // 店舗の経度
                        $shopLat = (float)$matches[2]; // 店舗の緯度

                        // PHPでハバーサインの公式を使って距離を計算（メートル単位）
                        $theta = $userLon - $shopLon;
                        $dist = sin(deg2rad($userLat)) * sin(deg2rad($shopLat)) + cos(deg2rad($userLat)) * cos(deg2rad($shopLat)) * cos(deg2rad($theta));
                        $dist = acos($dist);
                        $dist = rad2deg($dist);
                        $meters = $dist * 60 * 1.1515 * 1609.344; // マイルからメートルに変換

                        $shop->distance = $meters; // 店舗オブジェクトに距離を追加
                        $shop->lat = $shopLat; // 店舗オブジェクトに緯度を追加
                        $shop->lon = $shopLon; // 店舗オブジェクトに経度を追加

                        // 50km圏内の店舗のみをフィルタリング
                        if ($shop->distance <= $radiusKm * 1000) {
                            $filteredShops->push($shop);
                        }
                    } else {
                        Log::warning("Failed to parse location_wkt for shop ID {$shop->id}: " . $shop->location_wkt);
                    }
                }
            }

            // 距離でソート
            $nearbyShops = $filteredShops->sortBy('distance')->values();

            if ($nearbyShops->isNotEmpty()) {
                $message = '現在地から' . $radiusKm . 'km圏内に店舗が見つかりました。';
            } else {
                $message = '現在地から' . $radiusKm . 'km圏内に店舗が見つかりませんでした。';
            }
        } else {
            // 位置情報が取得できなかった場合のメッセージ
             $message = '位置情報を許可すると、お近くの店舗が表示されます。';
        }

        // おすすめメニューの取得
        $ramenCategoryId = Category::where('name', 'ラーメン')->first()?->id;
        $featuredProducts = collect();

        if ($ramenCategoryId) {
            $featuredProducts = Product::where('category_id', $ramenCategoryId)
                                        ->inRandomOrder()
                                        ->limit(6)
                                        ->get();
        }

        // 全商品リストの取得
        $allProducts = Product::orderBy('name')->get();

        // Google Maps APIキーをビューに渡す
        $mapsApiKey = env('MAPS_API_KEY');

        return view('home', compact('nearbyShops', 'message', 'featuredProducts', 'allProducts', 'mapsApiKey'));
    }
}