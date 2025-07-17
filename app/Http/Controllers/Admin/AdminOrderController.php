<?php

namespace App\Http\Controllers\Admin; // ★この名前空間が正しいか確認

use App\Http\Controllers\Controller;
use App\Models\Order; // Orderモデルを使用
use Illuminate\Http\Request;
use App\Models\User; // Userモデルを使用（Orderモデルのリレーションで必要）
use App\Models\Shop; // Shopモデルを使用（Orderモデルのリレーションで必要）
use Illuminate\Support\Facades\Log; // ログ出力のために追加
use Illuminate\Support\Facades\DB; // トランザクションのために追加

class AdminOrderController extends Controller // ★このクラス名が正しいか確認
{
        /**
     * Display a listing of the resource (all orders for admin).
     * 管理者向けに全ての注文を一覧表示します。
     *
     * @return \Illuminate\View\View
     */

    public function index(Request $request){
 // 基本となるクエリ
        $query = Order::with(['user', 'shop'])->latest();

        // 1. 店舗IDでの絞り込み (ドロップダウン用)
        if ($request->filled('shop_id') && $request->input('shop_id') !== '') {
            $shopId = $request->input('shop_id');
            $query->where('shop_id', $shopId);
        }

        // 2. ユーザーIDでの絞り込み (ドロップダウン用)
        if ($request->filled('user_id') && $request->input('user_id') !== '') {
            $userId = $request->input('user_id');
            $query->where('user_id', $userId);
        }

        // 3. ステータスでの絞り込み
        if ($request->filled('status') && $request->input('status') !== '') {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        // 最終的な注文データを取得し、ページネーションを適用
        $orders = $query->paginate(10);

        // ドロップダウンの選択肢として全ての店舗とユーザーを取得
        $shops = Shop::orderBy('name')->get(['id', 'name']); // IDと名前だけ取得
        $users = User::orderBy('name')->get(['id', 'name']); // IDと名前だけ取得

        // フィルタリングのために使用したリクエストデータと選択肢データをビューに渡す
        return view('admin.orders.index', compact('orders', 'shops', 'users'))->with($request->query());
    }
    }