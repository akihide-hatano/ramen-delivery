<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $shop->name }} のメニュー
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-bold mb-4">{{ $shop->name }}</h3>
                    <p class="text-gray-700 mb-2">住所: {{ $shop->address }}</p>
                    <p class="text-gray-700 mb-4">電話: {{ $shop->phone_number }}</p>

                    {{-- ★ここから配達可否メッセージの追加★ --}}
                    @if (isset($distanceKm))
                        <p class="text-lg mb-4">
                            現在地からこの店舗まで: <span class="font-bold">{{ number_format($distanceKm, 1) }} km</span>
                        </p>
                        @if ($isDeliverable)
                            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg font-semibold">
                                この店舗は現在地から{{ $deliveryRadiusKm }}km圏内のため配達可能です。
                            </div>
                        @else
                            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg font-semibold">
                                この店舗は現在地から{{ $deliveryRadiusKm }}km圏外（{{ number_format($distanceKm, 1) }}km）のため、配達できません。
                            </div>
                        @endif
                    @else
                        <div class="mb-4 p-3 bg-blue-100 text-blue-700 rounded-lg font-semibold">
                            位置情報が取得できなかったため、配達可否を判断できません。
                            <br>ホーム画面で位置情報を許可してください。
                        </div>
                    @endif
                    {{-- ★ここまで配達可否メッセージの追加★ --}}

                    {{-- Google Maps Embed API を使用して地図を埋め込む (Canvasなし) --}}
                    <h4 class="text-xl font-semibold mt-8 mb-4">店舗の場所</h4>
                    <div class="w-full h-64 bg-gray-200 rounded-md mb-8 flex items-center justify-center">
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
                            <p class="text-gray-500">地図データがありません。</p>
                        @endif
                    </div>

                    <h4 class="text-xl font-semibold mt-8 mb-4">メニュー一覧</h4>
                    @if ($products->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($products as $product)
                                <div class="bg-gray-50 p-4 rounded-lg shadow-md flex flex-col">
                                    @if ($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-40 object-cover rounded-md mb-3">
                                    @else
                                        <div class="w-full h-40 bg-gray-200 rounded-md flex items-center justify-center text-gray-500 mb-3">
                                            画像なし
                                        </div>
                                    @endif
                                    <h5 class="text-lg font-bold mb-1">{{ $product->name }}</h5>
                                    <p class="text-sm text-gray-600 mb-2">{{ $product->description }}</p>
                                    <p class="text-xl font-bold text-gray-900 mb-3">¥{{ number_format($product->price) }}</p>

                                    {{-- カートに追加フォーム --}}
                                    <form action="{{ route('cart.add') }}" method="POST" class="mt-auto flex items-center">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                                        <input type="number" name="quantity" value="1" min="1"
                                            class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center text-sm mr-2"
                                            {{-- ★追加: 配達不可の場合は数量入力を無効化★ --}}
                                            @unless($isDeliverable) disabled @endunless>
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-semibold
                                            {{-- ★追加: 配達不可の場合はボタンを無効化しスタイルを変更★ --}}
                                            @unless($isDeliverable) opacity-50 cursor-not-allowed @else hover:bg-green-700 @endunless"
                                            @unless($isDeliverable) disabled @endunless>
                                            カートに追加
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">この店舗にはまだ商品が登録されていません。</p>
                    @endif

                    <div class="mt-8 text-center">
                        <a href="{{ route('home.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            他の店舗を探す
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>