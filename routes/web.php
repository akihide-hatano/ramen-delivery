<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController; // 一般ユーザー向けの商品コントローラー
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminShopController;
use App\Http\Controllers\Admin\AdminProductController; // 管理者向けの商品コントローラー
use App\Http\Controllers\Admin\AdminCategoryController;
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
Route::get('/shops/{shop}/products', [ProductController::class, 'index'])->name('shops.products.index');

// ★ここを修正しました★
// 全体の商品一覧を表示するルート（一般ユーザー向け）
// 例: /products
Route::get('/products', [ProductController::class, 'globalIndex'])->name('products.global_index');

// 個別商品詳細（一般ユーザー向け）
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

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