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

    public function index(){
        $orders = Order::with(['user','shop'])->latest()->paginate(10);
        // 'admin.orders.index' ビューに取得した注文データを渡して表示します。
        return view('admin.orders.index', compact('orders'));
    }
}