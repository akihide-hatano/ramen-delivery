<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // 追加
use App\Http\Controllers\ShopController; // 追加
use App\Http\Controllers\ProductController; // 追加
use App\Http\Controllers\CartController; // 追加
use App\Http\Controllers\OrderController; // 追加

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// HomeControllerのルート
Route::get('/', [HomeController::class, 'index'])->name('home.index');


// ShopControllerのルート
Route::resource('shops', ShopController::class);

// ProductControllerのルート
Route::resource('products', ProductController::class);

// ProductControllerのルート (必要であれば追加)
// Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');


// CartControllerのルート
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});


// OrderControllerのルート
Route::middleware(['auth'])->prefix('orders')->name('orders.')->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    // ★★★ここを追加します★★★
    Route::post('/', [OrderController::class, 'store'])->name('store'); // 注文保存用
    Route::get('/complete', [OrderController::class, 'complete'])->name('complete'); // 注文完了ページ
    // ★★★追加ここまで★★★
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';