<x-app-layout>
    {{-- x-app-layout がヘッダーを提供するため、ここでは重複するヘッダーを削除します --}}
    {{-- <header class="bg-gray-800 text-white p-4 shadow-md">...</header> --}}

    {{-- メインコンテンツ --}}
    <main class="container mx-auto mt-8 p-4">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-8">全商品一覧</h2>

        @if ($finalGroupedProducts->isEmpty())
            <p class="text-center text-gray-600">まだ商品が登録されていません。</p>
        @else
            {{-- 「共通商品」と「限定商品」のトップレベルループ --}}
            @foreach ($finalGroupedProducts as $groupType => $groupedCategories)
                {{-- 「共通商品」または「限定商品」の見出し --}}
                <h3 class="text-4xl font-bold mt-12 mb-6 text-center text-gray-800 border-b-4 border-blue-500 pb-2">{{ $groupType }}</h3>

                @if ($groupedCategories->isEmpty())
                    <p class="text-center text-gray-600 mb-8">このグループには商品がありません。</p>
                @else
                    {{-- 各カテゴリグループのループ (ラーメン、サイドメニュー、ドリンクなど) --}}
                    @foreach ($groupedCategories as $mainCategoryName => $subGroupsOrProducts)
                        {{-- 最上位カテゴリの見出し --}}
                        <h4 class="text-3xl font-bold mt-10 mb-4 border-b-4 border-red-500 pb-2 text-gray-800">{{ $mainCategoryName }}</h4>

                        @if ($subGroupsOrProducts instanceof \Illuminate\Support\Collection && $subGroupsOrProducts->first() instanceof \App\Models\Product)
                            {{-- 最上位カテゴリの直下に商品がある場合（例：ラーメン、トッピング） --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($subGroupsOrProducts as $product)
                                    <div class="border rounded-lg p-4 flex flex-col items-center text-center bg-gray-50 hover:shadow-lg transition-shadow duration-300 relative">
                                        {{-- 限定バッジはトップレベルで分けているのでここでは不要 --}}
                                        @if ($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-md mb-4">
                                        @else
                                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-md mb-4 text-gray-500">
                                                No Image
                                            </div>
                                        @endif
                                        <h5 class="text-xl font-bold mb-2 text-gray-800">{{ $product->name }}</h5>
                                        <p class="text-gray-600 mb-2 flex-grow">{{ $product->description }}</p>
                                        <p class="text-lg font-semibold text-green-600">¥{{ number_format($product->price) }}</p>
                                        <a href="{{ route('products.show', $product) }}" class="mt-4 block text-center bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                                            詳細を見る
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- さらに子カテゴリを持つ最上位カテゴリ（例：ドリンク、サイドメニュー） --}}
                            @foreach ($subGroupsOrProducts as $subCategoryName => $nestedGroupsOrProducts)
                                {{-- 中間カテゴリの見出し --}}
                                <h5 class="text-2xl font-semibold mt-6 mb-3 border-b-2 border-gray-400 pb-1 text-gray-700 ml-4">{{ $subCategoryName }}</h5>

                                @if ($nestedGroupsOrProducts instanceof \Illuminate\Support\Collection && $nestedGroupsOrProducts->first() instanceof \App\Models\Product)
                                    {{-- 中間カテゴリの直下に商品がある場合 --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 ml-8">
                                        @foreach ($nestedGroupsOrProducts as $product)
                                            <div class="border rounded-lg p-4 flex flex-col items-center text-center bg-gray-50 hover:shadow-lg transition-shadow duration-300 relative">
                                                @if ($product->image_url)
                                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-md mb-4">
                                                @else
                                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-md mb-4 text-gray-500">
                                                        No Image
                                                    </div>
                                                @endif
                                                <h6 class="text-lg font-bold mb-2 text-gray-800">{{ $product->name }}</h6>
                                                <p class="text-gray-600 mb-2 flex-grow">{{ $product->description }}</p>
                                                <p class="text-md font-semibold text-green-600">¥{{ number_format($product->price) }}</p>
                                                <a href="{{ route('products.show', $product) }}" class="mt-4 block text-center bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                                                    詳細を見る
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- さらに子カテゴリがある場合 --}}
                                    @foreach ($nestedGroupsOrProducts as $deepSubCategoryName => $products)
                                        {{-- 最下層カテゴリの見出し --}}
                                        <h6 class="text-xl font-semibold mt-4 mb-2 border-b border-gray-300 pb-1 text-gray-600 ml-8">{{ $deepSubCategoryName }}</h6>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 ml-12">
                                            @foreach ($products as $product)
                                                <div class="border rounded-lg p-4 flex flex-col items-center text-center bg-gray-50 hover:shadow-lg transition-shadow duration-300 relative">
                                                    @if ($product->image_url)
                                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-md mb-4">
                                                    @else
                                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-md mb-4 text-gray-500">
                                                            No Image
                                                        </div>
                                                    @endif
                                                    <h6 class="text-md font-bold mb-2 text-gray-800">{{ $product->name }}</h6>
                                                    <p class="text-gray-600 mb-2 flex-grow">{{ $product->description }}</p>
                                                    <p class="text-sm font-semibold text-green-600">¥{{ number_format($product->price) }}</p>
                                                    <a href="{{ route('products.show', $product) }}" class="mt-4 block text-center bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                                                        詳細を見る
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
        @endif

        <div class="text-center mt-8">
            <a href="{{ route('home') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                ホームへ戻る
            </a>
        </div>
    </main>
</x-app-layout>