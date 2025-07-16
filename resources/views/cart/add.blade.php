{{-- resources/views/cart/add.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('カートに商品を追加') }}
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
                            @if ($shops->isEmpty())
                                <p class="text-gray-600">現在、利用可能な店舗がありません。</p>
                                <div class="mt-4">
                                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        ホームに戻る
                                    </a>
                                </div>
                            @else
                                <form action="{{ route('cart.create') }}" method="GET" class="space-y-4">
                                    <label for="shop_select" class="block text-lg font-medium text-gray-700">店舗を選択:</label>
                                    <select name="shop_id" id="shop_select" required
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-lg rounded-md">
                                        <option value="">-- 店舗を選択してください --</option>
                                        @foreach ($shops as $shop)
                                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        この店舗の商品を見る
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    {{-- ステップ2: 商品選択 (店舗が選択された場合のみ表示) --}}
                    @if ($selectedShop)
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h4 class="text-xl font-bold text-gray-800 mb-4">ステップ2: 「{{ $selectedShop->name }}」の商品を選んでください</h4>
                            <p class="text-gray-600 mb-6">※現在、カートには「{{ $selectedShop->name }}」以外の店舗の商品は追加できません。別の店舗の商品を追加したい場合は、一度カートを空にする必要があります。</p>

                            @if ($deliverableProducts->isEmpty())
                                <p class="text-gray-600">「{{ $selectedShop->name }}」には現在、配達可能な商品がありません。</p>
                                <div class="mt-4">
                                    <a href="{{ route('cart.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
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
                                                           value="1" min="1"
                                                           class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                                    {{-- hidden field for product_id - the name 'items[product_id]' itself implies the product_id --}}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-8 flex justify-between items-center">
                                        <a href="{{ route('cart.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
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
</x-app-layout>