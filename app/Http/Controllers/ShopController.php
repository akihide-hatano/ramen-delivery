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
        // クエリパラメータから 'prefecture' を取得します。
        // デフォルトはnullで、全ての店舗を意味します。
        $prefecture = $request->query('prefecture');
        $search = $request->query('search');

        // Shopモデルのクエリビルダを開始
        $query = Shop::query();
        // 'prefecture' パラメータが存在する場合、住所で絞り込みます。
        // 例: '京都府' や '大阪府' を含む住所を検索
        if ($prefecture) {
            $query->where('address', 'like', $prefecture . '%');
        }
        // 'search' パラメータが存在する場合、店舗名で絞り込みます。
        // 部分一致検索のため、両端に '%' を追加します。
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // 絞り込まれた店舗を取得します。
        // 店舗数が多い場合は、Shop::paginate(10) のようにページネーションを使うことを検討してください。
        $shops = $query->get();
        // 取得した店舗の数をカウント
        $shopCount = $shops->count(); // ← ここを追加

        // dd('$search');
        // 取得した店舗データと現在のフィルタリング状態を 'shops.index' ビューに渡します。
        return view('shops.index', compact('shops', 'prefecture','search','shopCount'));
    }

    public function show(Shop $shop)
    {
         $shop->load('products');

        // ★★★ここを追加してください★★★
        // 環境変数と$shopオブジェクトの内容を同時に確認
        // dd([
        //     'Maps_API_KEY_from_env' => env('Maps_API_KEY'),
        //     'shop_lat' => $shop->lat,
        //     'shop_lon' => $shop->lon,
        //     'shop_address' => $shop->address,
        // ]);
                // ★★★ここを追加★★★
        // APIキーをコントローラーで取得し、ビューに渡す
        $apiKey = env('Maps_API_KEY');
        return view('shops.show', compact('shop','apiKey'));
    }

}