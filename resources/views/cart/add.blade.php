{{-- resources/views/cart/add.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('商品をカートに追加') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">商品をカートに追加</h3>

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- ステップ1: 店舗選択 --}}
                    @if (!$selectedShop)
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h4 class="text-xl font-bold text-gray-800 mb-4">ステップ1: 配達を希望する店舗を選択してください</h4>

                            {{-- 位置情報メッセージ --}}
                            <div class="mb-4 p-3 bg-blue-100 text-blue-700 rounded-lg">
                                {{ $message }}
                            </div>

                            @if ($nearbyShops->isEmpty())
                                <p class="text-gray-600">現在地から20km圏内に店舗が見つかりませんでした。</p>
                                <div class="mt-4">
                                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        ホームに戻る
                                    </a>
                                </div>
                            @else
                                <p class="text-gray-700 mb-4">以下の店舗から1つ選んでください:</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach ($nearbyShops as $shop)
                                        <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col justify-between">
                                            <div>
                                                <h4 class="text-2xl font-semibold text-gray-800 mb-2">{{ $shop->name }}</h4>
                                                <p class="text-gray-700 text-sm mb-1">{{ $shop->address }}</p>
                                                <p class="text-gray-700 text-sm mb-4">
                                                    営業時間: {{ $shop->business_hours ?? '不明' }}
                                                </p>
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
                                            </div>
                                            {{-- この店舗を選ぶボタン --}}
                                            <a href="{{ route('cart.create', ['shop_id' => $shop->id, 'lat' => $latitude, 'lon' => $longitude]) }}"
                                               class="mt-auto inline-block text-center bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-300">
                                                この店舗を選ぶ
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- ステップ2: 商品選択 (店舗が選択された場合のみ表示) --}}
                    @if ($selectedShop)
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h4 class="text-xl font-bold text-gray-800 mb-4">ステップ2: 「{{ $selectedShop->name }}」の商品を選んでカートに追加</h4>
                            <p class="text-gray-600 mb-6">※現在、カートには「{{ $selectedShop->name }}」以外の店舗の商品は追加できません。別の店舗の商品を追加したい場合は、一度カートを空にする必要があります。</p>

                            @if ($deliverableProducts->isEmpty())
                                <p class="text-gray-600">「{{ $selectedShop->name }}」には現在、配達可能な商品がありません。</p>
                                <div class="mt-4">
                                    <a href="{{ route('cart.create', ['lat' => $latitude, 'lon' => $longitude]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                                        別の店舗を選択する
                                    </a>
                                </div>
                            @else
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="selected_shop_id_for_cart" value="{{ $selectedShop->id }}"> {{-- 選択された店舗IDを送信 --}}

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        @foreach ($deliverableProducts as $product)
                                            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col border border-gray-200">
                                                <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ $product->name }}</h4>
                                                @if ($product->image_url)
                                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-32 object-cover rounded-md mb-3">
                                                @else
                                                    <img src="https://placehold.co/400x300/E0E0E0/000000?text=No+Image" alt="No Image" class="w-full h-32 object-cover rounded-md mb-3">
                                                @endif
                                                <p class="text-gray-600 text-sm mb-2 line-clamp-3">{{ $product->description }}</p>
                                                <p class="text-red-600 font-bold text-xl mb-3">¥{{ number_format($product->price) }}</p>

                                                <div class="flex items-center mt-auto">
                                                    <label for="quantity_{{ $product->id }}" class="mr-2 text-sm text-gray-700">数量:</label>
                                                    <input type="number" name="items[{{ $product->id }}][quantity]" id="quantity_{{ $product->id }}"
                                                           value="0" min="0"
                                                           class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-8 flex justify-between items-center">
                                        <a href="{{ route('cart.create', ['lat' => $latitude, 'lon' => $longitude]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                                            別の店舗を選択する
                                        </a>
                                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            選択した商品をカートに追加
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @endif

                    <div class="mt-8 text-center">
                        <a href="{{ route('cart.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            カートの中身を見る
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{-- ★ここから追加するJavaScriptコード★ --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('cart/add.blade.php JavaScript is running!');
            
            const urlParams = new URLSearchParams(window.location.search);
            const lat = urlParams.get('lat');
            const lon = urlParams.get('lon');
            const shopId = urlParams.get('shop_id'); // 店舗が既に選択されているか確認
            
            // 緯度・経度がURLにない、かつ店舗も選択されていない場合に位置情報を取得
            if ((!lat || !lon) && !shopId) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const userLat = position.coords.latitude;
                        const userLon = position.coords.longitude;
                        
                        console.log('位置情報を取得しました:', userLat, userLon);
                        
                        const newUrl = new URL(window.location.href);
                        newUrl.searchParams.set('lat', userLat);
                        newUrl.searchParams.set('lon', userLon);
                        window.location.href = newUrl.toString(); // URLを更新してページをリロード
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
                                        // エラーメッセージをユーザーに表示する処理をここに追加することもできます
                                        console.error('Geolocation error code:', error.code, 'message:', errorMessage);
                                    });
                                } else {
                                    console.warn('Geolocation is not supported by this browser.');
                                    // Geolocationがサポートされていない場合のメッセージ表示
                                }
                            } else {
                                console.log('緯度・経度または店舗がURLに既に存在します。');
                            }
                        });
                    </script>
@endpush
</x-app-layout>