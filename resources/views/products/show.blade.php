<x-app-layout>
    {{-- メインコンテンツ --}}
    <main class="container mx-auto mt-8 p-4">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden md:flex">
            <div class="md:w-1/2">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-96 object-cover">
                @else
                    <div class="w-full h-96 bg-gray-200 flex items-center justify-center text-gray-500 text-2xl">
                        No Image
                    </div>
                @endif
            </div>
            <div class="md:w-1/2 p-8 flex flex-col justify-center text-center">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $product->name }}</h1>

                    {{-- 限定バッジの表示 --}}
                    @if ($product->is_limited)
                        @if ($product->limited_type === 'location' && $product->limited_location)
                            <span class="inline-block bg-yellow-500 text-white text-sm font-bold px-3 py-1 rounded-full shadow-md mb-4">
                                {{ $product->limited_location }}限定
                            </span>
                        @elseif ($product->limited_type === 'season')
                            <span class="inline-block bg-purple-600 text-white text-sm font-bold px-3 py-1 rounded-full shadow-md mb-4">
                                季節限定
                            </span>
                        @else
                            <span class="inline-block bg-red-600 text-white text-sm font-bold px-3 py-1 rounded-full shadow-md mb-4">
                                限定
                            </span>
                        @endif
                    @endif

                    <p class="text-gray-600 text-lg mb-6">{{ $product->description }}</p>

                    <div class="mb-6">
                        {{-- <p class="text-gray-700 text-xl mb-2">
                            <i class="fas fa-tags mr-2 text-blue-500"></i>カテゴリ:
                            <span class="font-semibold text-blue-700">{{ $product->category->name ?? 'N/A' }}</span>
                        </p> --}}
                        <p class="text-green-700 text-4xl font-extrabold">
                            ¥{{ number_format($product->price) }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col space-y-4">
                    {{-- カートに追加ボタン --}}
                    <form action="#" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="w-64 bg-green-600 text-white text-xl font-bold py-3 px-6 rounded-lg hover:bg-green-700 transition duration-300 shadow-lg">
                            カートに追加
                        </button>
                    </form>

                    {{-- 戻るボタン --}}
                    <a href="{{ url()->previous() }}" class="w-64 mx-auto text-center bg-gray-500 text-white text-xl font-bold py-3 px-6 rounded-lg hover:bg-gray-600 transition duration-300 shadow-lg">
                        商品一覧に戻る
                    </a>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>