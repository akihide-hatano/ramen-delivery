<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $recommendedShops = Shop::inRandomOrder()->limit(3)->get();
        $newProducts = Product::latest()->limit(5)->get();

        $nearbyShops = collect();

        $userLat = (float)$request->query('lat');
        $userLon = (float)$request->query('lon');

        if ($userLat && $userLon) {
            $nearbyShops = Shop::select('shops.*')
                ->selectRaw('ST_Distance(location, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography) AS distance', [$userLon, $userLat])
                // ★ここを修正します★
                // geography型からgeometry型にキャストしてからST_Y/ST_Xを使用
                ->selectRaw('ST_Y(location::geometry) AS lat, ST_X(location::geometry) AS lon')
                ->whereNotNull('location')
                ->orderBy('distance')
                ->limit(5)
                ->get();

            $nearbyShops = $nearbyShops->filter(function ($shop) {
                return $shop->distance < 50000;
            })->values();

            if ($nearbyShops->isEmpty()) {
                session()->flash('info', 'お近くに店舗は見つかりませんでした。');
            }
        } else {
            session()->flash('info', '位置情報を許可すると、お近くの店舗が表示されます。');
        }

        return view('home', compact('recommendedShops', 'newProducts', 'nearbyShops'));
    }
}