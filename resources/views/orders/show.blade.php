<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('注文内容の詳細') }}
        </h2>
    </x-slot>

<div class="container py-8">
    <h1 class="text-3xl font-bold mb-6">注文詳細 (ID: {{ $order->id }})</h1>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold mb-4">注文情報</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><strong class="text-gray-700">注文日時:</strong> {{ $order->created_at->format('Y年m月d日 H:i') }}</p>
                <p><strong class="text-gray-700">ステータス:</strong> <span class="font-bold text-blue-600">{{ $order->status }}</span></p>
                <p><strong class="text-gray-700">店舗名:</strong> {{ $order->shop->name ?? '不明' }}</p>
                <p><strong class="text-gray-700">合計金額:</strong> ¥{{ number_format($order->grand_total) }} (内、配送料 ¥{{ number_format($order->delivery_fee) }})</p>
                <p><strong class="text-gray-700">支払い方法:</strong> {{ $order->payment_method == 'cash' ? '現金払い' : 'クレジットカード' }}</p>
            </div>
            <div>
                <p><strong class="text-gray-700">配達先住所:</strong> {{ $order->delivery_address }} ({{ $order->delivery_zone_name }})</p>
                <p><strong class="text-gray-700">電話番号:</strong> {{ $order->delivery_phone }}</p>
                <p><strong class="text-gray-700">希望配達時間:</strong> {{ $order->desired_delivery_time_slot ?? '指定なし' }}</p>
                @if ($order->delivery_notes)
                    <p><strong class="text-gray-700">配送メモ:</strong> {{ $order->delivery_notes }}</p>
                @endif
            </div>
        </div>
    </div>

    <h2 class="text-2xl font-semibold mb-4">注文商品</h2>
    <div class="overflow-x-auto mb-6 bg-white shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        商品名
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        単価
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
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->product->name ?? '不明な商品' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ¥{{ number_format($item->unit_price) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ¥{{ number_format($item->subtotal) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            注文履歴に戻る
        </a>
    </div>
</div>
</x-app-layout>
