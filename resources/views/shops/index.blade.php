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
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-8">全ての店舗</h2>

        {{-- ★ここからフィルターリンクを追加★ --}}
        <div class="flex justify-center space-x-4 mb-8">
            <a href="{{ route('shops.index') }}"
               class="px-5 py-2 rounded-full font-semibold transition duration-300
               {{ !$prefecture ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                全ての店舗
            </a>
            <a href="{{ route('shops.index', ['prefecture' => '京都府']) }}"
               class="px-5 py-2 rounded-full font-semibold transition duration-300
               {{ $prefecture === '京都府' ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                京都の店舗
            </a>
            <a href="{{ route('shops.index', ['prefecture' => '大阪府']) }}"
               class="px-5 py-2 rounded-full font-semibold transition duration-300
               {{ $prefecture === '大阪府' ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                大阪の店舗
            </a>
        </div>
         {{-- ★ここから検索フォームを追加★ --}}
        <div class="flex justify-center mb-8">
            <form action="{{ route('shops.index') }}" method="GET" class="flex items-center space-x-2 w-full max-w-md">
                {{-- 隠しフィールドで現在の都道府県フィルターを保持 --}}
                @if ($prefecture)
                    <input type="hidden" name="prefecture" value="{{ $prefecture }}">
                @endif
                <input type="text" name="search" placeholder="店舗名で検索..."
                       value="{{ $search ?? '' }}" {{-- 現在の検索キーワードを保持 --}}
                       class="flex-grow p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                <button type="submit" class="bg-blue-500 text-white px-5 py-3 rounded-lg font-semibold hover:bg-blue-600 transition duration-300">
                    検索
                </button>
            </form>
        </div>

        {{-- ★ここから検索件数の表示を追加★ --}}
        <div class="text-center text-xl text-gray-700 mb-8">
            <p>{{ $shopCount }}件見つかりました</p>
        </div>
        {{-- ★検索件数表示ここまで★ --}}
        {{-- ★検索フォームここまで★ --}}
        {{-- ★フィルターリンクここまで★ --}}

        @if ($shops->isEmpty())
            <p class="text-center text-gray-600">現在、登録されている店舗はありません。</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($shops as $shop)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105">
                        {{-- 店舗のメイン画像があれば表示、なければプレースホルダー --}}
                        @if ($shop->photo_1_url)
                            <img src="{{ $shop->photo_1_url }}" alt="{{ $shop->name }}" class="w-full h-48 object-cover">
                        @else
                            <img src="https://placehold.co/600x300/E0E0E0/000000?text=Shop+Image" alt="店舗画像" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-5">
                            <h3 class="text-2xl font-semibold text-gray-800 mb-2 truncate">{{ $shop->name }}</h3>
                            <p class="text-gray-700 mb-1"><i class="fas fa-map-marker-alt mr-2"></i>住所: {{ $shop->address }}</p>
                            <p class="text-gray-700 mb-1"><i class="fas fa-phone mr-2"></i>電話: {{ $shop->phone_number }}</p>
                            <p class="text-gray-700 mb-4"><i class="fas fa-clock mr-2"></i>営業時間: {{ $shop->business_hours ?? '不明' }}</p>
                            <a href="{{ route('shops.show', $shop) }}" class="block text-center bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                                店舗詳細を見る
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
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