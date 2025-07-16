<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('カート') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">あなたのカート</h3>

                    {{-- 成功メッセージの表示 --}}
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- エラーメッセージの表示 --}}
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- ★追加: 警告メッセージの表示 ★ --}}
                    @if ($warningMessage)
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ $warningMessage }}
                        </div>
                    @endif
                    {{-- ★追加ここまで★ --}}

                    @if ($items->isEmpty())
                        <p class="text-gray-600">カートに商品はありません。</p>
                        <div class="mt-4">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                商品一覧に戻る
                            </a>
                        </div>
                    @else
                        {{-- カート内の商品表示 (既存のコード) --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            商品
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            店舗
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            配達可能
                                        </th> {{-- ★追加: 配達可能カラム ★ --}}
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            価格
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            数量
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            小計
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            アクション
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php $totalPrice = 0; @endphp
                                    @foreach ($items as $item)
                                        @php
                                            $subtotal = $item['product']->price * $item['quantity'];
                                            $totalPrice += $subtotal;
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if ($item['product']->image_url)
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}">
                                                        </div>
                                                    @endif
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $item['product']->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $item['shop_name'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> {{-- ★追加: 配達可能の表示 ★ --}}
                                                @if ($item['product']->is_delivery)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        はい
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        いいえ
                                                    </span>
                                                @endif
                                            </td> {{-- ★追加ここまで★ --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                ¥{{ number_format($item['product']->price) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="{{ route('cart.update') }}" method="POST" class="flex items-center">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                                    <input type="hidden" name="shop_id" value="{{ $item['shop_id'] }}"> {{-- shop_idも渡す --}}
                                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-input w-20 text-center text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    <button type="submit" class="ml-2 px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">更新</button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                ¥{{ number_format($subtotal) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form action="{{ route('cart.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                                    <input type="hidden" name="shop_id" value="{{ $item['shop_id'] }}"> {{-- shop_idも渡す --}}
                                                    <button type="submit" class="text-red-600 hover:text-red-900">削除</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 text-right">
                            <p class="text-xl font-bold text-gray-900">合計: ¥{{ number_format($totalPrice) }}</p>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                買い物を続ける
                            </a>

                            <div class="flex space-x-4">
                                <form action="{{ route('cart.clear') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        カートをクリア
                                    </button>
                                </form>

                                {{-- 注文に進むボタン --}}
                                {{-- カート内に配達不可商品が含まれている場合は、配達注文に進めないようにするか、
                                     ボタンのテキストを変更するなどのUI調整が必要になる場合があります。
                                     ここでは、シンプルに注文に進むボタンを配置。OrderController側で再度チェック。 --}}
                                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    注文に進む
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>