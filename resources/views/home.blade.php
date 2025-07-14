<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ホーム') }}
        </h2>
    </x-slot>

    <main>
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

        <section class="commitment-section">
            <div class="commitment-bg-pattern"></div>
            <div class="container mx-auto relative z-10">
                <h3 class="text-4xl font-bold text-center text-gray-800 mb-12">潮屋のこだわり</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="commitment-item bg-white p-6 rounded-lg shadow-lg text-center">
                        <img src="https://placehold.co/150x100/E0E0E0/000000?text=Noodle" alt="麺" class="w-full h-auto object-cover rounded-md mb-4">
                        <h4 class="text-2xl font-semibold text-gray-800 mb-3">麺へのこだわり</h4>
                        <p class="text-gray-700">厳選された小麦粉を使用し、独自の配合で打ち上げた特製麺は、スープとの絡みが絶妙です。</p>
                    </div>
                    <div class="commitment-item bg-white p-6 rounded-lg shadow-lg text-center">
                        <img src="https://placehold.co/150x100/E0E0E0/000000?text=Soup" alt="スープ" class="w-full h-auto object-cover rounded-md mb-4">
                        <h4 class="text-2xl font-semibold text-gray-800 mb-3">スープへのこだわり</h4>
                        <p class="text-gray-700">数種類の魚介と鶏ガラをじっくり煮込んだ、深みのあるあっさりとした潮屋特製スープ。</p>
                    </div>
                    <div class="commitment-item bg-white p-6 rounded-lg shadow-lg text-center">
                        <img src="https://placehold.co/150x100/E0E0E0/000000?text=Ingredients" alt="具材" class="w-full h-auto object-cover rounded-md mb-4">
                        <h4 class="text-2xl font-semibold text-gray-800 mb-3">具材へのこだわり</h4>
                        <p class="text-gray-700">特製のチャーシューや新鮮な野菜など、一杯を彩る具材にも一切の妥協はありません。</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="map-section">
            <div class="container mx-auto">
                <h3 class="text-3xl font-bold text-center text-gray-800 mb-8">お近くの店舗を探す</h3>

                {{-- メッセージ表示 --}}
                <div class="mb-4 p-3 bg-blue-100 text-blue-700 rounded-lg">
                    {{ $message }}
                </div>

                {{-- 近くの店舗がある場合のみ表示 --}}
                @if ($nearbyShops->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($nearbyShops as $shop)
                            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col"> {{-- 各店舗のカード --}}
                                <h4 class="text-2xl font-semibold text-gray-800 mb-4">{{ $shop->name }}</h4>
                                <p class="text-gray-700 mb-2">住所: {{ $shop->address }}</p>
                                <p class="text-gray-700 mb-2">電話: {{ $shop->phone_number }}</p>
                                <p class="text-gray-700 mb-4">営業時間: {{ $shop->business_hours ?? '不明' }}</p>
                                {{-- 距離を表示 --}}
                                @if (isset($shop->distance))
                                    <p class="text-gray-600 text-sm mb-4">
                                        現在地から約 **{{ number_format($shop->distance / 1000, 1) }} km**
                                    </p>
                                @endif
                                {{-- Google Maps Embed API を使用して地図を埋め込む --}}
                                <div class="w-full h-48 bg-gray-200 rounded-md mb-4 flex items-center justify-center">
                                    @if ($shop->lat && $shop->lon && $mapsApiKey)
                                        @php
                                            $embedSrc = "https://www.google.com/maps/embed/v1/place?key={$mapsApiKey}&q={$shop->lat},{$shop->lon}";
                                        @endphp
                                        <iframe
                                            width="100%"
                                            height="100%"
                                            frameborder="0"
                                            style="border:0"
                                            src="{{ $embedSrc }}"
                                            allowfullscreen
                                            loading="lazy"
                                        ></iframe>
                                    @elseif ($shop->address && $mapsApiKey)
                                        @php
                                            $encodedAddress = urlencode($shop->address);
                                            $embedSrc = "https://www.google.com/maps/embed/v1/place?key={$mapsApiKey}&q={$encodedAddress}";
                                        @endphp
                                        <iframe
                                            width="100%"
                                            height="100%"
                                            frameborder="0"
                                            style="border:0"
                                            src="{{ $embedSrc }}"
                                            allowfullscreen
                                            loading="lazy"
                                        ></iframe>
                                    @else
                                        <img src="https://placehold.co/600x300/E0E0E0/000000?text=Map+Data+Missing" alt="地図" class="w-full h-auto rounded-md shadow-md">
                                    @endif
                                </div>
                                {{-- ★修正: この店舗のメニューを見るリンクに緯度・経度をクエリパラメータとして渡す★ --}}
                                <a href="{{ route('shops.show', ['shop' => $shop->id, 'lat' => $latitude, 'lon' => $longitude]) }}" class="mt-auto inline-block bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 transition duration-300 text-center">
                                    この店舗のメニューを見る
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- 近くの店舗が見つからなかった場合、または位置情報がまだ取得されていない場合の表示 --}}
                    <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                        <p class="text-gray-700">
                            {{ $message }}
                        </p>
                    </div>
                @endif
                <div class="text-center mt-8">
                    <a href="{{ route('shops.index') }}" class="inline-block bg-gray-700 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-gray-800 transition duration-300">全ての店舗を見る</a>
                </div>
            </div>
        </section>

        <section class="featured-menu-section">
            <div class="container mx-auto">
                <h3 class="text-3xl font-bold text-center text-gray-800 mb-8">おすすめメニュー</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                    @forelse ($featuredProducts as $product)
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
                    @empty
                        <p class="col-span-full text-center text-gray-600">おすすめ商品がありません。</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- 全商品リストセクションは削除済み --}}
    </main>

    <footer class="bg-gray-800 text-white p-6 text-center mt-12">
        <p>© {{ date('Y') }} ラーメン潮屋. All rights reserved.</p>
    </footer>

{{-- home.blade.php の JavaScript コード --}}
@push('scripts')
<script>
// style タグ内の CSS は app.css に移動すべきですが、ここではデバッグのために一時的に残します。
// 最終的には resources/css/app.css に移動してください。
// hero-section と commitment-section の背景画像とオーバーレイ、 commitment-circle のスタイルは app.css に移すのがベストです。
// 一時的なスタイル (app.css に移すべきもの)
const style = document.createElement('style');
style.innerHTML = `
    body {
        font-family: 'Inter', sans-serif;
        background-image: url('https://placehold.co/100x100/F0F0F0/000000?text=Pattern');
        background-repeat: repeat;
    }
    .hero-section {
        background-image: url('https://placehold.co/1920x800/2C3E50/FFFFFF?text=Ramen+Hero+Image');
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
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }
    .hero-content {
        position: relative;
        z-index: 2;
    }
    .commitment-section {
        background-color: #f7f7f7;
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }
    .commitment-bg-pattern {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://placehold.co/100x100/E5E5E5/000000?text=LightPattern');
        background-repeat: repeat;
        opacity: 0.5;
        z-index: 0;
    }
    .commitment-item {
        position: relative;
        z-index: 1;
    }
    .commitment-circle {
        background-color: #FFD700;
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
    .map-section {
        background-color: #e0e0e0;
        padding: 4rem 0;
    }
    .featured-menu-section {
        padding: 4rem 0;
        background-color: #f0f0f0;
    }
`;
document.head.appendChild(style);


document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript is running!');

    const urlParams = new URLSearchParams(window.location.search);
    const lat = urlParams.get('lat');
    const lon = urlParams.get('lon');

    if (!lat || !lon) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const userLat = position.coords.latitude;
                const userLon = position.coords.longitude;

                console.log('位置情報を取得しました:', userLat, userLon);

                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('lat', userLat);
                newUrl.searchParams.set('lon', userLon);
                window.location.href = newUrl.toString();
            }, function(error) {
                console.error('Geolocation position error:', error.message);
                let errorMessage = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = '位置情報の利用が許可されませんでした。';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = '位置情報を取得できませんでした。';
                        break;
                    case error.TIMEOUT:
                        errorMessage = '位置情報の取得がタイムアウトしました。';
                        break;
                    default:
                        errorMessage = '不明なエラーが発生しました。';
                        break;
                }
                console.error('Geolocation error code:', error.code, 'message:', errorMessage);
            });
        } else {
            console.warn('Geolocation is not supported by this browser.');
        }
    } else {
        console.log('緯度・経度はURLに既に存在します:', lat, lon);
    }
});
</script>
@endpush
</x-app-layout>