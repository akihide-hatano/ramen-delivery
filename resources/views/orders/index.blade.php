<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('注文内容の確認') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-bold mb-6">ご注文内容</h3>

                    @if ($shop)
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <p class="text-lg font-semibold text-blue-800">注文店舗: {{ $shop->name }}</p>
                            <p class="text-sm text-blue-700">住所: {{ $shop->address }}</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        商品名
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        価格
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        数量
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        小計
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($cartItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item['product']->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ¥{{ number_format($item['product']->price) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item['quantity'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ¥{{ number_format($item['subtotal']) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right text-2xl font-bold mb-8">
                        合計金額: <span class="text-red-600">¥{{ number_format($totalPrice) }}</span>
                    </div>

                    <h3 class="text-xl font-bold mb-4">配送先情報</h3>
                    {{-- 注文保存用のフォーム (actionは後でOrderControllerのstoreメソッドに設定) --}}
                    <form action="#" method="POST"> {{-- ★★★後でroute('orders.store')に修正★★★ --}}
                        @csrf

                        <div class="mb-4">
                            <label for="delivery_address" class="block text-gray-700 text-sm font-bold mb-2">
                                配送先住所:
                            </label>
                            <input type="text" name="delivery_address" id="delivery_address"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="例: 京都府京都市中京区〇〇1-2-3" required
                                value="{{ Auth::user()->address ?? '' }}"> {{-- 認証済みユーザーの住所をデフォルト値に (あれば) --}}
                        </div>

                        <div class="mb-6">
                            <label for="delivery_notes" class="block text-gray-700 text-sm font-bold mb-2">
                                配送に関するメモ (任意):
                            </label>
                            <textarea name="delivery_notes" id="delivery_notes" rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="例: 玄関前に置いてください、インターホンは押さないでください"></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('cart.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                カートに戻る
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                注文を確定する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>