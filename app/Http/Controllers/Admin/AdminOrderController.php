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

    public function show(Order $order){
    // 注文詳細ページで必要なリレーション（user, shop, orderItems.product）をEager Loadします。
    // これにより、ビューで $order->user->name や $order->shop->name、商品情報などが使えます。
    $order->load(['user','shop','orderItems.product']);
    return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     * 指定された注文の編集フォームを表示します。
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function edit(Order $order){
        // 編集フォームで表示するために、関連するユーザーと店舗の情報をEager Loadします。
        // OrderItemの編集は複雑になるため、ここでは注文のヘッダー情報（ステータス、配送先など）のみを対象とします。
        $order->load(['user', 'shop']);

        $statuses = [
            'pending'    => '保留中',
            'preparing'  => '準備中',
            'delivering' => '配達中',
            'completed'  => '完了',
            'cancelled'  => 'キャンセル',
        ];

        return view('admin.orders.edit',compact('order','statuses'));
    }

    public function update(Request $request ,Order $order){
        //バリデーションルール
        $validatedData = $request->validate([
            'status' => 'required|string|in:pending,preparing,delivering,completed,cancelled',
            'delivery_address' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'desired_delivery_time_slot' => 'nullable|string|max:50',
            'delivery_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // 注文データを更新
            $order->update($validatedData);

            DB::commit();
            Log::info('Admin: Order updated successfully.', ['order_id' => $order->id, 'admin_user_id' => auth()->id()]);
            return redirect()->route('admin.orders.show', $order)->with('success', '注文情報を更新しました。');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin: Failed to update order.', [
                'order_id' => $order->id,
                'admin_user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', '注文情報の更新に失敗しました。');
        }
    }
    }