<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ラーメン潮屋 - 公式サイト＆宅配アプリ</title>
    <!-- Tailwind CSS を使用 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* 背景のパターンを画像から取得し、繰り返す */
            background-image: url('https://placehold.co/100x100/F0F0F0/000000?text=Pattern'); /* 仮のパターン画像 */
            background-repeat: repeat;
        }
        /* ヒーローセクションの背景画像とオーバーレイ */
        .hero-section {
            background-image: url('https://placehold.co/1920x800/2C3E50/FFFFFF?text=Ramen+Hero+Image'); /* ヒーロー画像のURL */
            background-size: cover;
            background-position: center;
            position: relative;
            color: white;
            padding: 8rem 0;
            text-align: center;
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* 半透明のオーバーレイ */
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        /* こだわりセクションの背景パターン */
        .commitment-section {
            background-color: #f7f7f7; /* 明るい背景色 */
            padding: 4rem 0;
            position: relative;
            overflow: hidden; /* はみ出す要素を隠す */
        }
        .commitment-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://placehold.co/100x100/E5E5E5/000000?text=LightPattern'); /* 明るい背景パターン */
            background-repeat: repeat;
            opacity: 0.5;
            z-index: 0;
        }
        .commitment-item {
            position: relative;
            z-index: 1;
        }
        .commitment-circle {
            background-color: #FFD700; /* ゴールドっぽい色 */
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #333;
            margin: 0 auto 1rem;
        }
        /* マップセクションの背景 */
        .map-section {
            background-color: #e0e0e0; /* マップセクションの背景色 */
            padding: 4rem 0;
        }
        /* おすすめメニューセクション */
        .featured-menu-section {
            padding: 4rem 0;
            background-color: #f0f0f0; /* おすすめメニューの背景色 */
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-gray-800 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-red-500">ラーメン潮屋</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="{{ route('home') }}" class="hover:text-red-500">ホーム</a></li>
                    <li><a href="{{ route('shops.index') }}" class="hover:text-red-500">店舗一覧</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="hover:text-red-500">ダッシュボード</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="hover:text-red-500">ログアウト</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-red-500">ログイン</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-red-500">新規登録</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <!-- ヒーローセクション: 麺とスープにこだわり抜いた一杯 -->
        <section class="hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content container mx-auto">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="md:w-1/2 text-left p-4">
                        <p class="text-xl mb-2">麺とスープに</p>
                        <h2 class="text-5xl font-extrabold mb-4">こだわり抜いた<br>幸福の一杯</h2>
                        <p class="text-lg">潮屋が誇る、選び抜かれた素材と熟練の技が織りなす至高のラーメン。</p>
                        <a href="{{ route('shops.index') }}" class="mt-8 inline-block bg-red-600 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-red-700 transition duration-300 shadow-lg">今すぐ注文する</a>
                    </div>
                    <div class="md:w-1/2 flex justify-center p-4">
                        <img src="https://placehold.co/500x350/F0F0F0/000000?text=Main+Ramen" alt="メインラーメン" class="rounded-lg shadow-xl">
                    </div>
                </div>
            </div>
        </section>

        <!-- こだわりセクション -->
        <section class="commitment-section">
            <div class="commitment-bg-pattern"></div>
            <div class="container mx-auto relative z-10">
                <h3 class="text-4xl font-bold text-center text-gray-800 mb-12">潮屋のこだわり</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- こだわり1: 麺へのこだわり -->
                    <div class="commitment-item bg-white p-6 rounded-lg shadow-lg text-center">
                        <img src="https://placehold.co/150x100/E0E0E0/000000?text=Noodle" alt="麺" class="w-full h-auto object-cover rounded-md mb-4">
                        <h4 class="text-2xl font-semibold text-gray-800 mb-3">麺へのこだわり</h4>
                        <p class="text-gray-700">厳選された小麦粉を使用し、独自の配合で打ち上げた特製麺は、スープとの絡みが絶妙です。</p>
                    </div>
                    <!-- こだわり2: スープへのこだわり -->
                    <div class="commitment-item bg-white p-6 rounded-lg shadow-lg text-center">
                        <img src="https://placehold.co/150x100/E0E0E0/000000?text=Soup" alt="スープ" class="w-full h-auto object-cover rounded-md mb-4">
                        <h4 class="text-2xl font-semibold text-gray-800 mb-3">スープへのこだわり</h4>
                        <p class="text-gray-700">数種類の魚介と鶏ガラをじっくり煮込んだ、深みのあるあっさりとした潮屋特製スープ。</p>
                    </div>
                    <!-- こだわり3: 具材へのこだわり -->
                    <div class="commitment-item bg-white p-6 rounded-lg shadow-lg text-center">
                        <img src="https://placehold.co/150x100/E0E0E0/000000?text=Ingredients" alt="具材" class="w-full h-auto object-cover rounded-md mb-4">
                        <h4 class="text-2xl font-semibold text-gray-800 mb-3">具材へのこだわり</h4>
                        <p class="text-gray-700">特製のチャーシューや新鮮な野菜など、一杯を彩る具材にも一切の妥協はありません。</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 店舗情報・地図セクション -->
        <section class="map-section">
            <div class="container mx-auto">
                <h3 class="text-3xl font-bold text-center text-gray-800 mb-8">お近くの店舗を探す</h3>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="md:flex md:items-center">
                        <div class="md:w-1/2 p-4">
                            <h4 class="text-2xl font-semibold text-gray-800 mb-4">大阪難波店</h4>
                            <p class="text-gray-700 mb-2">住所: 大阪府大阪市中央区難波</p>
                            <p class="text-gray-700 mb-2">電話: 06-1111-2222</p>
                            <p class="text-gray-700 mb-4">営業時間: 11:00 - 23:00</p>
                            <a href="{{ route('shops.show', ['shop' => \App\Models\Shop::where('name', 'ラーメン潮屋 大阪難波店')->first()->id ?? 1]) }}" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 transition duration-300">店舗詳細を見る</a>
                        </div>
                        <div class="md:w-1/2 p-4">
                            <!-- Google Maps の埋め込みコードをここに配置 -->
                            <!-- 例: <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d..." width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe> -->
                            <img src="https://placehold.co/600x300/E0E0E0/000000?text=Google+Map" alt="地図" class="w-full h-auto rounded-md shadow-md">
                        </div>
                    </div>
                </div>
                <div class="text-center mt-8">
                    <a href="{{ route('shops.index') }}" class="inline-block bg-gray-700 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-gray-800 transition duration-300">全ての店舗を見る</a>
                </div>
            </div>
        </section>

        <!-- おすすめメニューセクション -->
        <section class="featured-menu-section">
            <div class="container mx-auto">
                <h3 class="text-3xl font-bold text-center text-gray-800 mb-8">おすすめメニュー</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    @php
                        // シーダーで投入した潮屋の共通商品からいくつかピックアップ
                        $shioyaShopIds = \App\Models\Shop::where('name', 'like', 'ラーメン潮屋%')->pluck('id');
                        $featuredProducts = \App\Models\Product::whereIn('shop_id', $shioyaShopIds)
                                            ->inRandomOrder()
                                            ->limit(6) // 6個表示
                                            ->get();
                    @endphp

                    @foreach ($featuredProducts as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-32 object-cover">
                            @else
                                <img src="https://placehold.co/400x300/E0E0E0/000000?text=No+Image" alt="No Image" class="w-full h-32 object-cover">
                            @endif
                            <div class="p-3 text-center">
                                <h4 class="text-md font-semibold text-gray-800 truncate">{{ $product->name }}</h4>
                                <p class="text-red-600 font-bold text-lg mt-1">¥{{ number_format($product->price) }}</p>
                                <a href="{{ route('products.show', $product) }}" class="block text-center bg-green-500 text-white text-sm px-3 py-1 rounded-md mt-2 hover:bg-green-600 transition duration-300">詳細</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-gray-800 text-white p-6 text-center mt-12">
        <p>&copy; {{ date('Y') }} ラーメン潮屋. All rights reserved.</p>
    </footer>
</body>
</html>
