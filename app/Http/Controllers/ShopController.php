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

        // Shopモデルのクエリビルダを開始
        $query = Shop::query();

        // 'prefecture' パラメータが存在する場合、住所で絞り込みます。
        // 例: '京都府' や '大阪府' を含む住所を検索
        if ($prefecture) {
            $query->where('address', 'like', $prefecture . '%');
        }

        // 絞り込まれた店舗を取得します。
        // 店舗数が多い場合は、Shop::paginate(10) のようにページネーションを使うことを検討してください。
        $shops = $query->get();

        // 取得した店舗データと現在のフィルタリング状態を 'shops.index' ビューに渡します。
        return view('shops.index', compact('shops', 'prefecture'));
    }

    // 必要であれば、ここに他のメソッド（例: show, create, storeなど）を追加します。
}