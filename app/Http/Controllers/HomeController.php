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
            // サブクエリを使用して距離を計算し、その結果を外部クエリでフィルタリング・ソート
            $nearbyShops = Shop::select('shops.*') // Shopモデルの全カラムを選択
                ->selectRaw('ST_Distance(location, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography) AS distance', [$userLon, $userLat])
                ->whereNotNull('location') // locationカラムがある店舗のみを対象
                ->orderBy('distance') // 距離が近い順にソート
                ->limit(5) // 最大5件取得
                ->get();

            // ここで、取得したコレクションに対してPHP側でフィルタリングを行う
            // データベース側でhavingを使わず、PHP側で処理することでエラーを回避
            $nearbyShops = $nearbyShops->filter(function ($shop) {
                return $shop->distance < 50000; // 50km (50000メートル) 以内の店舗
            })->values(); // フィルタリング後にインデックスをリセット

            // dd($nearbyShops); // ★デバッグ用: これでデータを確認★ // 動作確認後、この行は削除してください

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