<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop; // Shopモデルをインポート
use Illuminate\Support\Facades\Storage; // 画像アップロードのために追加
use Illuminate\Validation\Rule; // バリデーションルールにRuleクラスを追加

class AdminShopController extends Controller
{
    /**
     * 管理者向け店舗一覧の表示
     */
    public function index()
    {
        $shops = Shop::orderBy('name')->paginate(10); // 店舗名を昇順でページネーション
        return view('admin.shops.index', compact('shops'));
    }

    /**
     * 店舗新規登録フォームの表示
     */
    public function create()
    {
        return view('admin.shops.create');
    }

    /**
     * 店舗データの保存
     */
    public function store(Request $request)
    {
        // 1. バリデーションルールを定義
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:shops,name', // 店舗名はユニーク
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'photo_1' => 'nullable|image|max:2048', // 画像ファイル、最大2MB
            'photo_2' => 'nullable|image|max:2048',
            'photo_3' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'has_parking' => 'boolean',
            'has_table_seats' => 'boolean',
            'has_counter_seats' => 'boolean',
            'business_hours' => 'nullable|string|max:255',
            'regular_holiday' => 'nullable|string|max:255',
            'accept_cash' => 'boolean',
            'accept_credit_card' => 'boolean',
            'accept_e_money' => 'boolean',
            // 'location' はDB::rawで挿入するため、ここではバリデーションしない
            // もしフォームで緯度経度を受け取る場合は、ここでバリデーションを追加
        ]);

        // 2. 画像のアップロード処理
        $photo1Path = $request->hasFile('photo_1') ? $request->file('photo_1')->store('shops', 'public') : null;
        $photo2Path = $request->hasFile('photo_2') ? $request->file('photo_2')->store('shops', 'public') : null;
        $photo3Path = $request->hasFile('photo_3') ? $request->file('photo_3')->store('shops', 'public') : null;

        // 3. チェックボックスが送信されなかった場合のデフォルト値を設定
        $validatedData['has_parking'] = $request->has('has_parking');
        $validatedData['has_table_seats'] = $request->has('has_table_seats');
        $validatedData['has_counter_seats'] = $request->has('has_counter_seats');
        $validatedData['accept_cash'] = $request->has('accept_cash');
        $validatedData['accept_credit_card'] = $request->has('accept_credit_card');
        $validatedData['accept_e_money'] = $request->has('accept_e_money');

        // 4. 店舗の作成
        $shop = Shop::create([
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'photo_1_url' => $photo1Path ? Storage::url($photo1Path) : null,
            'photo_2_url' => $photo2Path ? Storage::url($photo2Path) : null,
            'photo_3_url' => $photo3Path ? Storage::url($photo3Path) : null,
            'description' => $validatedData['description'],
            'has_parking' => $validatedData['has_parking'],
            'has_table_seats' => $validatedData['has_table_seats'],
            'has_counter_seats' => $validatedData['has_counter_seats'],
            'business_hours' => $validatedData['business_hours'],
            'regular_holiday' => $validatedData['regular_holiday'],
            'accept_cash' => $validatedData['accept_cash'],
            'accept_credit_card' => $validatedData['accept_credit_card'],
            'accept_e_money' => $validatedData['accept_e_money'],
            // 'location' はSeederで設定済み、または別途ジオコーディングで設定
            // 新規登録フォームで緯度経度を入力させる場合は、ここに追加ロジックが必要
            // 例: 'location' => DB::raw("ST_SetSRID(ST_MakePoint({$request->longitude}, {$request->latitude}), 4326)::geography"),
        ]);

        // 5. 成功メッセージと共にリダイレクト
        return redirect()->route('admin.shops.index')->with('success', '店舗が正常に登録されました。');
    }

    /**
     * 個別店舗詳細の表示 (管理者向け)
     */
    public function show(Shop $shop)
    {
        return view('admin.shops.show', compact('shop'));
    }

    /**
     * 店舗編集フォームの表示
     */
    public function edit(Shop $shop)
    {
        return view('admin.shops.edit', compact('shop'));
    }

    /**
     * 店舗データの更新
     */
    public function update(Request $request, Shop $shop)
    {
        // 1. バリデーションルールを定義
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shops')->ignore($shop->id), // 自身の名前は無視してユニーク性をチェック
            ],
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'photo_1' => 'nullable|image|max:2048',
            'photo_2' => 'nullable|image|max:2048',
            'photo_3' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'has_parking' => 'boolean',
            'has_table_seats' => 'boolean',
            'has_counter_seats' => 'boolean',
            'business_hours' => 'nullable|string|max:255',
            'regular_holiday' => 'nullable|string|max:255',
            'accept_cash' => 'boolean',
            'accept_credit_card' => 'boolean',
            'accept_e_money' => 'boolean',
            // 画像削除チェックボックス
            'delete_photo_1' => 'boolean',
            'delete_photo_2' => 'boolean',
            'delete_photo_3' => 'boolean',
        ]);

        // 2. 画像のアップロードと削除処理
        $photos = [
            'photo_1' => $shop->photo_1_url,
            'photo_2' => $shop->photo_2_url,
            'photo_3' => $shop->photo_3_url,
        ];

        foreach ($photos as $key => $currentUrl) {
            $fileInputName = str_replace('_url', '', $key); // photo_1_url -> photo_1
            $deleteCheckboxName = 'delete_' . $fileInputName; // delete_photo_1

            if ($request->hasFile($fileInputName)) {
                // 新しいファイルがアップロードされた場合、古いファイルを削除して新しいファイルを保存
                if ($currentUrl) {
                    Storage::delete(str_replace('/storage/', 'public/', $currentUrl));
                }
                $photos[$key] = Storage::url($request->file($fileInputName)->store('shops', 'public'));
            } elseif ($request->has($deleteCheckboxName)) {
                // 削除チェックボックスがオンの場合、ファイルを削除
                if ($currentUrl) {
                    Storage::delete(str_replace('/storage/', 'public/', $currentUrl));
                }
                $photos[$key] = null;
            }
        }

        // 3. チェックボックスが送信されなかった場合のデフォルト値を設定
        $validatedData['has_parking'] = $request->has('has_parking');
        $validatedData['has_table_seats'] = $request->has('has_table_seats');
        $validatedData['has_counter_seats'] = $request->has('has_counter_seats');
        $validatedData['accept_cash'] = $request->has('accept_cash');
        $validatedData['accept_credit_card'] = $request->has('accept_credit_card');
        $validatedData['accept_e_money'] = $request->has('accept_e_money');

        // 4. 店舗の更新
        $shop->update([
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'photo_1_url' => $photos['photo_1'],
            'photo_2_url' => $photos['photo_2'],
            'photo_3_url' => $photos['photo_3'],
            'description' => $validatedData['description'],
            'has_parking' => $validatedData['has_parking'],
            'has_table_seats' => $validatedData['has_table_seats'],
            'has_counter_seats' => $validatedData['has_counter_seats'],
            'business_hours' => $validatedData['business_hours'],
            'regular_holiday' => $validatedData['regular_holiday'],
            'accept_cash' => $validatedData['accept_cash'],
            'accept_credit_card' => $validatedData['accept_credit_card'],
            'accept_e_money' => $validatedData['accept_e_money'],
            // 'location' はここでは更新しない想定。更新する場合は別途ロジックが必要。
        ]);

        // 5. 成功メッセージと共にリダイレクト
        return redirect()->route('admin.shops.index')->with('success', '店舗が正常に更新されました。');
    }

    /**
     * 店舗データの削除
     */
    public function destroy(Shop $shop)
    {
        // 関連する画像を削除
        if ($shop->photo_1_url) {
            Storage::delete(str_replace('/storage/', 'public/', $shop->photo_1_url));
        }
        if ($shop->photo_2_url) {
            Storage::delete(str_replace('/storage/', 'public/', $shop->photo_2_url));
        }
        if ($shop->photo_3_url) {
            Storage::delete(str_replace('/storage/', 'public/', $shop->photo_3_url));
        }

        $shop->delete();
        return redirect()->route('admin.shops.index')->with('success', '店舗が正常に削除されました。');
    }
}