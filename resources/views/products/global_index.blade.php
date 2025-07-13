<x-app-layout>
    {{-- ヘッダー部分 --}}
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

    {{-- メインコンテンツ --}}
    <main class="container mx-auto mt-8 p-4">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-8">全商品一覧</h2>

        @if ($groupedProducts->isEmpty())
            <p class="text-center text-gray-600">まだ商品が登録されていません。</p>
        @else
            @foreach ($groupedProducts as $categoryName => $products)
                <h3 class="text-2xl font-semibold mt-8 mb-4 border-b-2 border-gray-300 pb-2 text-gray-700">{{ $categoryName }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($products as $product)
                        <div class="border rounded-lg p-4 flex flex-col items-center text-center bg-gray-50 hover:shadow-lg transition-shadow duration-300">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-md mb-4">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-md mb-4 text-gray-500">
                                    No Image
                                </div>
                            @endif
                            <h3 class="text-xl font-bold mb-2 text-gray-800">{{ $product->name }}</h3>
                            <p class="text-gray-600 mb-2 flex-grow">{{ $product->description }}</p>
                            <p class="text-lg font-semibold text-green-600">¥{{ number_format($product->price) }}</p>
                            <a href="{{ route('products.show', $product) }}" class="mt-4 block text-center bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                                詳細を見る
                            </a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif

        <div class="text-center mt-8">
            <a href="{{ route('home') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                ホームへ戻る
            </a>
        </div>
    </main>
</x-app-layout>