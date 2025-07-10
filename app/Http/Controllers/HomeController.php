<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;    // Shopモデルをuseする
use App\Models\Product; // Productモデルをuseする
use Illuminate\Support\Facades\DB; // DBファサードをuseする (ST_Distance_Sphere用)

class HomeController extends Controller
{
    /**
     * アプリケーションのトップページを表示
     */
    public function index(Request $request)
    {
        $recommendedShops = Shop::inRandomOrder()->limit(3)->get();
        $newProducts = Product::latest()->limit(5)->get();

        $nearbyShops = collect(); // 近くの店舗を格納するコレクションを初期化

        // ユーザーの位置情報がクエリパラメータで渡された場合
        $userLat = $request->query('lat');
        $userLon = $request->query('lon');

        if ($userLat && $userLon) {
            // ユーザーの緯度・経度を数値に変換（安全のため）
            $userLat = (float)$userLat;
            $userLon = (float)$userLon;

            // データベースのST_Distance_Sphere関数を使用し、距離を計算して取得
            // 距離はメートル単位で返される
            $nearbyShops = Shop::select('shops.*') // Shopモデルの全カラムを選択
                // ST_Distance_Sphere(point(経度, 緯度), point(ユーザー経度, ユーザー緯度))
                ->selectRaw('ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) AS distance', [$userLon, $userLat])
                ->whereNotNull('latitude') // 緯度・経度データがある店舗のみを対象
                ->whereNotNull('longitude')
                ->having('distance', '<', 50000) // 例: 50km (50000メートル) 以内の店舗
                ->orderBy('distance') // 距離が近い順にソート
                ->limit(5) // 最大5件取得
                ->get();

            if ($nearbyShops->isEmpty()) {
                // 近くに店舗がない場合のメッセージ
                session()->flash('info', 'お近くに店舗は見つかりませんでした。');
            }
        } else {
            // 位置情報が渡されていない場合のメッセージ
            session()->flash('info', '位置情報を許可すると、お近くの店舗が表示されます。');
        }

        return view('home', compact('recommendedShops', 'newProducts', 'nearbyShops'));
    }
}