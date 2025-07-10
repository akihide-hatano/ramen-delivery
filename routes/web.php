<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // 例: ホームコントローラー
use App\Http\Controllers\ShopController; // 例: 店舗関連のコントローラー
use App\Http\Controllers\ProductController; // 例: 商品関連のコントローラー
use App\Http\Controllers\Admin\AdminHomeController; // 例: 管理者用ホームコントローラー

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
// アプリケーションのトップページ
Route::get('/', [HomeController::class, 'index'])->name('home');

// ダッシュボード（認証済みユーザー向け）
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- 2. プロフィール関連のルート（Laravel Breeze/Jetstream で自動生成される部分） ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- 3. 認証関連のルート（Laravel Breeze/Jetstream で自動生成される部分） ---
// 通常は require __DIR__.'/auth.php'; で読み込まれる
require __DIR__.'/auth.php';

// --- 4. 一般ユーザー向けのショップ・商品関連ルート ---
// 店舗一覧
Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
// 個別店舗の詳細
Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');

// 商品詳細（ショップ詳細ページから辿ることを想定）
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// --- 5. 管理者向けのルート ---
// 管理者のみアクセス可能なルートグループ
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // 管理者ダッシュボード
    Route::get('/', [AdminHomeController::class, 'index'])->name('home');

    // 例: 管理者による店舗管理
    Route::resource('shops', AdminShopController::class); // AdminShopController を作成する必要があります

    // 例: 管理者による商品管理
    Route::resource('products', AdminProductController::class); // AdminProductController を作成する必要があります

    // 例: 管理者によるカテゴリ管理
    Route::resource('categories', AdminCategoryController::class); // AdminCategoryController を作成する必要があります

    // 必要に応じて他の管理機能を追加...
});