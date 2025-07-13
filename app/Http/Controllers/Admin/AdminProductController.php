<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Support\Facades\Storage; // 画像アップロードのために追加

class AdminProductController extends Controller
{
    /**
     * 管理者向け商品一覧の表示
     * (今回は新規登録に焦点を当てるため、シンプルな実装)
     */
    public function index()
    {
        // ★ここを修正しました：カテゴリの表示順でソートするように結合★
        $products = Product::with('category')
                            ->join('categories', 'products.category_id', '=', 'categories.id')
                            ->orderBy('categories.display_order') // カテゴリの表示順でソート
                            ->orderBy('products.id')           // 同じカテゴリ内では商品名でソート
                            ->select('products.*')               // productsテーブルの全てのカラムを選択（joinしたcategoriesのカラムが混ざらないように）
                            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * 商品新規登録フォームの表示
     */
    public function create()
    {
        // カテゴリを階層構造で取得し、フォームの選択肢として渡す
        $categories = Category::whereNull('parent_id')
                            ->with('children.children') // 3階層まで取得
                            ->orderBy('display_order')
                            ->get();
        // ★店舗データを取得し、ビューに渡す
        $shops = Shop::orderBy('name')->get(); // 店舗名を昇順で取得

        return view('admin.products.create', compact('categories','shops'));
    }

    /**
     * 商品データの保存
     */
    public function store(Request $request)
    {
        // 1. バリデーションルールを定義
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id', // categoriesテーブルのidが存在することを確認
            'image' => 'nullable|image|max:2048', // 画像ファイル、最大2MB
            'is_limited' => 'boolean',
            'limited_location' => 'nullable|string|max:255',
            'limited_type' => 'nullable|string|in:location,season', // 'location'または'season'のみ許可
        ]);

        // 2. 画像のアップロード処理
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public'); // storage/app/public/products に保存
            // storage/app/public へのシンボリックリンクが public/storage に張られていることを前提
            // sail artisan storage:link を実行済みであること
        }

        // 3. is_limited のチェックボックスが送信されなかった場合のデフォルト値を設定
        // チェックボックスはチェックされていないと送信されないため、falseを設定
        $validatedData['is_limited'] = $request->has('is_limited');

        // 4. limited_location と limited_type の条件付き設定
        if (!$validatedData['is_limited']) {
            $validatedData['limited_location'] = null;
            $validatedData['limited_type'] = null;
        } else {
            // is_limited が true の場合のみ、limited_location と limited_type を使用
            // バリデーションでnullableなので、ここで明示的にnullを設定する必要はないが、念のため
            if (empty($validatedData['limited_location'])) {
                $validatedData['limited_location'] = null;
            }
            if (empty($validatedData['limited_type'])) {
                $validatedData['limited_type'] = null;
            }
        }


        // 5. 商品の作成
        $product = Product::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'category_id' => $validatedData['category_id'],
            'image_url' => $imagePath ? Storage::url($imagePath) : null, // public/storage 経由でアクセスできるURL
            'is_limited' => $validatedData['is_limited'],
            'limited_location' => $validatedData['limited_location'],
            'limited_type' => $validatedData['limited_type'],
        ]);

        // 6. 成功メッセージと共にリダイレクト
        return redirect()->route('admin.products.index')->with('success', '商品が正常に登録されました。');
    }

    // show, edit, update, destroy メソッドは後で実装します
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::whereNull('parent_id')
                              ->with('children.children')
                              ->orderBy('display_order')
                              ->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'is_limited' => 'boolean',
            'limited_location' => 'nullable|string|max:255',
            'limited_type' => 'nullable|string|in:location,season',
        ]);

        $imagePath = $product->image_url; // 既存の画像URLを保持
        if ($request->hasFile('image')) {
            // 古い画像があれば削除
            if ($product->image_url) {
                Storage::delete(str_replace('/storage/', 'public/', $product->image_url));
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $imagePath = Storage::url($imagePath);
        } elseif ($request->input('delete_image')) { // 画像削除チェックボックスがオンの場合
            if ($product->image_url) {
                Storage::delete(str_replace('/storage/', 'public/', $product->image_url));
            }
            $imagePath = null;
        }


        $validatedData['is_limited'] = $request->has('is_limited');

        if (!$validatedData['is_limited']) {
            $validatedData['limited_location'] = null;
            $validatedData['limited_type'] = null;
        } else {
            if (empty($validatedData['limited_location'])) {
                $validatedData['limited_location'] = null;
            }
            if (empty($validatedData['limited_type'])) {
                $validatedData['limited_type'] = null;
            }
        }

        $product->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'category_id' => $validatedData['category_id'],
            'image_url' => $imagePath,
            'is_limited' => $validatedData['is_limited'],
            'limited_location' => $validatedData['limited_location'],
            'limited_type' => $validatedData['limited_type'],
        ]);

        return redirect()->route('admin.products.index')->with('success', '商品が正常に更新されました。');
    }

    public function destroy(Product $product)
    {
        // 画像があれば削除
        if ($product->image_url) {
            Storage::delete(str_replace('/storage/', 'public/', $product->image_url));
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', '商品が正常に削除されました。');
    }
}