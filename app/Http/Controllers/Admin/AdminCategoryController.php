<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category; // Categoryモデルをインポート
use Illuminate\Validation\Rule; // バリデーションルールにRuleクラスを追加

class AdminCategoryController extends Controller
{
    /**
     * 管理者向けカテゴリ一覧の表示
     */
    public function index()
    {
        // 親カテゴリを持たない最上位カテゴリを取得し、その子カテゴリもロード
        // display_order 順にソート
        $categories = Category::whereNull('parent_id')
                            ->with('children.children') // 最大3階層までロード
                            ->orderBy('display_order')
                            ->paginate(10); // ページネーションを追加

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * カテゴリ新規登録フォームの表示
     */
    public function create()
    {
        // 親カテゴリとして選択可能なカテゴリを取得 (自分自身は親になれないため、新規作成時は全カテゴリ)
        // 階層表示のために全カテゴリを取得し、整形して渡す
        $allCategories = Category::orderBy('display_order')->get();

        return view('admin.categories.create', compact('allCategories'));
    }

    /**
     * カテゴリデータの保存
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name', // カテゴリ名はユニーク
            'display_order' => 'required|integer|min:0',
            'parent_id' => 'nullable|exists:categories,id', // 既存のカテゴリIDであること、またはnull
        ]);

        // カテゴリの作成
        Category::create([
            'name' => $validatedData['name'],
            'display_order' => $validatedData['display_order'],
            'parent_id' => $validatedData['parent_id'],
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'カテゴリが正常に登録されました。');
    }

    /**
     * 個別カテゴリ詳細の表示 (管理者向け) - 今回はビューは作成しないが、メソッドは定義
     */
    public function show(Category $category)
    {
        // 必要であれば詳細ビューを返す
        return view('admin.categories.show', compact('category'));
    }

    /**
     * カテゴリ編集フォームの表示
     */
    public function edit(Category $category)
    {
        // 親カテゴリとして選択可能なカテゴリを取得
        // 編集対象のカテゴリ自身と、その子孫カテゴリは親にできないため除外する
        $allCategories = Category::where('id', '!=', $category->id) // 自身を除外
                                ->where(function ($query) use ($category) {
                                     // 編集対象カテゴリの子孫カテゴリを除外するロジック
                                     // これは複雑になるため、ここではシンプルに「自身以外」とする
                                     // より厳密には再帰的なクエリや、ツリー構造を考慮した除外が必要
                                })
                                ->orderBy('display_order')
                                ->get();

        return view('admin.categories.edit', compact('category', 'allCategories'));
    }

    /**
     * カテゴリデータの更新
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id), // 自身の名前は無視してユニーク性をチェック
            ],
            'display_order' => 'required|integer|min:0',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                // 自身を親にできないようにする
                Rule::notIn([$category->id]),
                // 自身の子孫を親にできないようにする（より厳密なチェックが必要な場合）
                // Rule::unique('categories')->where(function ($query) use ($category) {
                //     $query->whereIn('id', $category->descendants()->pluck('id'));
                // }),
            ],
        ]);

        // 親カテゴリが自身の子孫でないことを確認する追加チェック
        if ($validatedData['parent_id'] && $category->id === $validatedData['parent_id']) {
            return back()->withErrors(['parent_id' => 'カテゴリ自身を親に設定することはできません。'])->withInput();
        }
        // ここでさらに、選択されたparent_idが$categoryの子孫ではないことを確認するロジックを追加することも可能ですが、
        // 現状のCategoryモデルにdescendantsリレーションがないため、一旦シンプルにします。
        // 厳密なツリー構造のチェックが必要な場合は、別途ライブラリ導入や再帰クエリ検討。

        // カテゴリの更新
        $category->update([
            'name' => $validatedData['name'],
            'display_order' => $validatedData['display_order'],
            'parent_id' => $validatedData['parent_id'],
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'カテゴリが正常に更新されました。');
    }

    /**
     * カテゴリデータの削除
     */
    public function destroy(Category $category)
    {
        // カテゴリに紐づく商品がある場合、または子カテゴリがある場合は削除を制限するか、関連付けを解除する
        if ($category->products()->exists()) {
            return back()->with('error', 'このカテゴリには商品が紐付いているため削除できません。先に商品を別のカテゴリに移動してください。');
        }

        if ($category->children()->exists()) {
            return back()->with('error', 'このカテゴリには子カテゴリが存在するため削除できません。先に子カテゴリを削除するか、親カテゴリを変更してください。');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'カテゴリが正常に削除されました。');
    }
}