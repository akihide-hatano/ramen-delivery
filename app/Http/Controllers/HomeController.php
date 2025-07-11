<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;    // Shopモデルをuseする
use App\Models\Product; // Productモデルをuseする
use Illuminate\Support\Facades\DB; // DBファサードをuseする

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
        $userLat = (float)$request->query('lat'); // floatにキャスト
        $userLon = (float)$request->query('lon'); // floatにキャスト

        if ($userLat && $userLon) {
            // データベースのST_Distance関数を使用し、距離を計算して取得
            // 距離はメートル単位で返されます
            $nearbyShops = Shop::select('shops.*') // Shopモデルの全カラムを選択
                // ★ここをPostgreSQL/PostGIS用に修正★
                // ST_Distance(店舗のlocationカラム, ユーザーの位置情報を表すPointオブジェクト)
                // ST_MakePoint(経度, 緯度) - PostGISは経度、緯度の順！
                // ST_SetSRID(..., 4326): WGS84座標系 (4326) を設定
                // ::geography: geography型にキャストして地球の丸みを考慮した正確な距離計算を行う
                ->selectRaw('ST_Distance(location, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography) AS distance', [$userLon, $userLat])
                ->whereNotNull('location') // locationカラムがある店舗のみを対象
                ->having('distance', '<', 50000) // 例: 50km (50000メートル) 以内の店舗
                ->orderBy('distance') // 距離が近い順にソート
                ->limit(5) // 最大5件取得
                ->get();
            
            dd($nearbyShops); // ★デバッグ用: これでデータを確認★

            if ($nearbyShops->isEmpty()) {
                session()->flash('info', 'お近くに店舗は見つかりませんでした。');
            }
        } else {
            // 位置情報が渡されていない場合のメッセージ
            session()->flash('info', '位置情報を許可すると、お近くの店舗が表示されます。');
        }

        return view('home', compact('recommendedShops', 'newProducts', 'nearbyShops'));
    }
}