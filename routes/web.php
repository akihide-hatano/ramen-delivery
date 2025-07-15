<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // HomeControllerを忘れずにインポート
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminShopController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// --- 1. 基本的なルート ---
// トップページをHomeController@indexのまま維持し、そこから商品一覧へ遷移させる
Route::get('/', [HomeController::class, 'index'])->name('home');

// ダッシュボード（認証済みユーザー向け）
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- 2. プロフィール関連のルート ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- 3. 認証関連のルート ---
require __DIR__.'/auth.php';

// --- 4. 一般ユーザー向けのショップ・商品関連ルート ---
Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');

// 特定の店舗の商品一覧を表示するルート（一般ユーザー向け）
// 例: /shops/1/products
// Route::get('/shops/{shop}/products', [ProductController::class, 'index'])->name('shops.products.index'); // このルートは不要になる可能性が高い

// 全体の商品一覧を表示するルート（一般ユーザー向け）
// アプリケーションのメインの入り口となる商品一覧
Route::get('/products', [ProductController::class, 'index'])->name('products.index'); // globalIndexからindexに名称変更

// 個別商品詳細（一般ユーザー向け）
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// カート関連のルート
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index'); // カート表示
    Route::post('/add', [CartController::class, 'add'])->name('add'); // カートに追加
    Route::post('/update', [CartController::class, 'update'])->name('update'); // カート数量更新
    Route::post('/remove', [CartController::class, 'remove'])->name('remove'); // カートから削除
    Route::post('/clear', [CartController::class, 'clear'])->name('clear'); // カートクリア
    Route::get('confirm-clear', [CartController::class, 'confirmClearCart'])->name('confirm-clear'); // カートクリア確認ページ
});

// ★★★ここから注文関連ルートを追加・修正★★★
Route::prefix('orders')->name('orders.')->middleware(['auth'])->group(function () {
    // 注文情報入力・確認ページ（以前のcheckoutをcreateにリネーム）
    Route::get('/create', [OrderController::class, 'create'])->name('create');
    // 注文保存
    Route::post('/', [OrderController::class, 'store'])->name('store');
    // 注文完了ページ
    Route::get('/complete', [OrderController::class, 'complete'])->name('complete');

    // ★★★新しいフローのためのルート★★★
    // GET /orders/choose-shop/{product} で、商品に対する店舗選択ページを表示
    Route::get('choose-shop/{product}', [OrderController::class, 'chooseShopForProduct'])->name('choose-shop-for-product');
    // POST /orders/confirm-shop-add-to-cart で、選択された店舗と商品をカートに追加
    Route::post('confirm-shop-add-to-cart', [OrderController::class, 'confirmShopAndAddToCart'])->name('confirm-shop-add-to-cart');
    // ★★★新しいフローのためのルートここまで★★★
});
// ★★★注文関連ルートここまで★★★

// --- 5. 管理者向けのルート ---
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // 管理者ダッシュボード
    Route::get('/', [AdminHomeController::class, 'index'])->name('home');

    // 管理者による店舗管理
    Route::resource('shops', AdminShopController::class);
    // 管理者による商品管理（全てのCRUD操作を許可）
    Route::resource('products', AdminProductController::class);
    // 管理者によるカテゴリ管理
    Route::resource('categories', AdminCategoryController::class);
});