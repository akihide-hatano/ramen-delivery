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
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center">{{ $shop->name }}</h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- 店舗情報と画像ギャラリー --}}
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">店舗情報</h2>
                    <p class="text-lg mb-2"><i class="fas fa-map-marker-alt mr-2 text-red-500"></i>住所: {{ $shop->address }}</p>
                    <p class="text-lg mb-2"><i class="fas fa-phone mr-2 text-red-500"></i>電話: {{ $shop->phone_number }}</p>
                    <p class="text-lg mb-2"><i class="fas fa-clock mr-2 text-red-500"></i>営業時間: {{ $shop->business_hours ?? '不明' }}</p>
                    @if ($shop->regular_holiday)
                        <p class="text-lg mb-2"><i class="fas fa-calendar-times mr-2 text-red-500"></i>定休日: {{ $shop->regular_holiday }}</p>
                    @endif

                    @if ($shop->description)
                        <p class="text-gray-700 mt-4 leading-relaxed">{{ $shop->description }}</p>
                    @endif

                    {{-- 設備情報 --}}
                    <div class="mt-6">
                        <h3 class="text-xl font-semibold mb-3">設備</h3>
                        <ul class="grid grid-cols-2 gap-2 text-gray-700">
                            <li class="flex items-center"><i class="fas fa-parking mr-2 text-blue-500"></i>駐車場: {{ $shop->has_parking ? 'あり' : 'なし' }}</li>
                            <li class="flex items-center"><i class="fas fa-chair mr-2 text-blue-500"></i>テーブル席: {{ $shop->has_table_seats ? 'あり' : 'なし' }}</li>
                            <li class="flex items-center"><i class="fas fa-chair mr-2 text-blue-500"></i>カウンター席: {{ $shop->has_counter_seats ? 'あり' : 'なし' }}</li>
                            <li class="flex items-center"><i class="fas fa-money-bill-wave mr-2 text-green-500"></i>現金払い: {{ $shop->accept_cash ? '可' : '不可' }}</li>
                            <li class="flex items-center"><i class="fas fa-credit-card mr-2 text-green-500"></i>カード払い: {{ $shop->accept_credit_card ? '可' : '不可' }}</li>
                            <li class="flex items-center"><i class="fas fa-wallet mr-2 text-green-500"></i>電子マネー: {{ $shop->accept_e_money ? '可' : '不可' }}</li>
                        </ul>
                    </div>

                    {{-- 店舗画像ギャラリー --}}
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold mb-3">ギャラリー</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @if ($shop->photo_1_url)
                                <img src="{{ $shop->photo_1_url }}" alt="{{ $shop->name }} - 画像1" class="rounded-lg shadow-md w-full h-48 object-cover">
                            @endif
                            @if ($shop->photo_2_url)
                                <img src="{{ $shop->photo_2_url }}" alt="{{ $shop->name }} - 画像2" class="rounded-lg shadow-md w-full h-48 object-cover">
                            @endif
                            @if ($shop->photo_3_url)
                                <img src="{{ $shop->photo_3_url }}" alt="{{ $shop->name }} - 画像3" class="rounded-lg shadow-md w-full h-48 object-cover">
                            @endif
                            {{-- 画像がない場合のプレースホルダー --}}
                            @if (!$shop->photo_1_url && !$shop->photo_2_url && !$shop->photo_3_url)
                                <img src="https://placehold.co/600x400/E0E0E0/000000?text=No+Images" alt="画像なし" class="rounded-lg shadow-md w-full h-48 object-cover col-span-full">
                            @endif
                        </div>
                    </div>
                </div>

{{-- 地図 --}}
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">地図</h2>
                    @if ($shop->lat && $shop->lon)
@php
    $apiKey = env('Maps_API_KEY'); // .envのキー名に合わせてください
    $embedSrc = "https://www.google.com/maps/embed/v1/place?key=" . $apiKey . "&q=" . $shop->lat . "," . $shop->lon;
@endphp
                        <p>APIキーの値: <strong>{{ $apiKey }}</strong></p> {{-- ★ここを追加★ --}}
                        <iframe
                            width="100%"
                            height="450"
                            frameborder="0"
                            style="border:0"
                            src="{{ $embedSrc }}"
                            allowfullscreen
                            loading="lazy"
                            class="rounded-lg shadow-md"
                        ></iframe>
                    @elseif ($shop->address)
@php
    $apiKey = env('Maps_API_KEY'); // .envのキー名に合わせてください
    $encodedAddress = urlencode($shop->address);
    $embedSrc = "https://www.google.com/maps/embed/v1/place?key=" . $apiKey . "&q=" . $encodedAddress;
@endphp
                        <p>APIキーの値: <strong>{{ $apiKey }}</strong></p> {{-- ★ここにも追加★ --}}
                        <iframe
                            width="100%"
                            height="450"
                            frameborder="0"
                            style="border:0"
                            src="{{ $embedSrc }}"
                            allowfullscreen
                            loading="lazy"
                            class="rounded-lg shadow-md"
                        ></iframe>
                    @else
                        <div class="bg-gray-100 p-4 rounded-lg text-center text-gray-600 h-full flex items-center justify-center">
                            <p>地図情報がありません。</p>
                        </div>
                    @endif
                </div>

            {{-- この店舗が提供する商品リスト --}}
            @if ($shop->products->isNotEmpty())
                <div class="mt-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">この店舗のおすすめメニュー</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($shop->products as $product)
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
            @endif

            <div class="mt-12 text-center">
                <a href="{{ route('shops.index') }}" class="inline-block bg-gray-700 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-gray-800 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>店舗一覧に戻る
                </a>
            </div>
        </div>
    </main>


    {{-- フッター部分 --}}
    <footer class="bg-gray-800 text-white p-6 text-center mt-12">
        <p>&copy; {{ date('Y') }} ラーメン潮屋. All rights reserved.</p>
    </footer>
</x-app-layout>

{{-- 必要なJavaScriptをプッシュ（このページ固有のJSがあれば） --}}
@push('scripts')
{{-- Font Awesome のCDNをheadに含めていない場合、ここで読み込むとアイコンが表示されます --}}
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> --}}
@endpush