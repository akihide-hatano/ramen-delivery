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
        $shopCount = $shops->count();

        // 取得した店舗データと現在のフィルタリング状態を 'shops.index' ビューに渡します。
        return view('shops.index', compact('shops', 'prefecture','search','shopCount'));
    }

    public function show(Shop $shop)
    {
        // 店舗と関連する商品（メニュー）をロード
        $shop->load('products'); // ★ここに修正はないが、この行は重要★

        // ★★★ここを修正します★★★
        // APIキーをコントローラーで取得し、ビューに渡す
        $mapsApiKey = env('MAPS_API_KEY'); // 変数名を $mapsApiKey に統一

        // ロードした商品を変数に格納し、ビューに渡す
        $products = $shop->products; // ★この行を追加★

        return view('shops.show', compact('shop', 'mapsApiKey', 'products')); // ★'products' も渡すように修正★
    }
}