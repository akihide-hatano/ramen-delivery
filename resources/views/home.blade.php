<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ラーメン潮屋 - 公式サイト＆宅配アプリ</title>
    <!-- Tailwind CSS を使用する場合 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-red-600">ラーメン潮屋</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="{{ route('home') }}" class="text-gray-700 hover:text-red-600">ホーム</a></li>
                    <li><a href="{{ route('shops.index') }}" class="text-gray-700 hover:text-red-600">店舗一覧</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-red-600">ダッシュボード</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-red-600">ログアウト</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-gray-700 hover:text-red-600">ログイン</a></li>
                        <li><a href="{{ route('register') }}" class="text-gray-700 hover:text-red-600">新規登録</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6">
        <section class="text-center my-8">
            <h2 class="text-4xl font-extrabold text-red-700 mb-4">潮屋の絶品ラーメンをあなたに！</h2>
            <p class="text-lg text-gray-700">全国のラーメン潮屋から、できたての一杯をご自宅までお届けします。</p>
            <a href="{{ route('shops.index') }}" class="mt-6 inline-block bg-red-600 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-red-700 transition duration-300 shadow-lg">今すぐ注文する</a>
        </section>

        <section class="my-12">
            <h3 class="text-3xl font-bold text-gray-800 mb-6 text-center">おすすめの店舗</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($recommendedShops as $shop)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                        @if ($shop->photo_1_url)
                            <img src="{{ $shop->photo_1_url }}" alt="{{ $shop->name }}" class="w-full h-48 object-cover">
                        @else
                            <img src="https://placehold.co/600x400/E0E0E0/000000?text=No+Image" alt="No Image" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4">
                            <h4 class="text-xl font-semibold text-gray-800 mb-2">{{ $shop->name }}</h4>
                            <p class="text-gray-600 text-sm mb-3">{{ $shop->description }}</p>
                            <a href="{{ route('shops.show', $shop) }}" class="block text-center bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">詳細を見る</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="my-12">
            <h3 class="text-3xl font-bold text-gray-800 mb-6 text-center">新着商品</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($newProducts as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-40 object-cover">
                        @else
                            <img src="https://placehold.co/400x300/E0E0E0/000000?text=No+Image" alt="No Image" class="w-full h-40 object-cover">
                        @endif
                        <div class="p-4">
                            <h4 class="text-lg font-semibold text-gray-800 truncate">{{ $product->name }}</h4>
                            <p class="text-red-600 font-bold text-xl mt-1 mb-2">¥{{ number_format($product->price) }}</p>
                            <p class="text-gray-600 text-sm mb-3 truncate">{{ $product->description }}</p>
                            <a href="{{ route('products.show', $product) }}" class="block text-center bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300">詳細を見る</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </main>

    <footer class="bg-gray-800 text-white p-6 text-center mt-12">
        <p>&copy; {{ date('Y') }} ラーメン潮屋. All rights reserved.</p>
    </footer>
</body>
</html>