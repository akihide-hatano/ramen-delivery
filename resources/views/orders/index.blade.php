<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('注文内容の確認') }}
        </h2>
    </x-slot>

<div class="container">
    <h1>注文履歴</h1>

    @if($orders->isEmpty())
        <p>まだ注文履歴がありません。</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>注文ID</th>
                    <th>店舗名</th> {{-- ここで表示 --}}
                    <th>合計金額</th>
                    <th>ステータス</th>
                    <th>注文日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->shop->name ?? '不明' }}</td> {{-- $order->shop から店舗名を取得 --}}
                        <td>¥{{ number_format($order->grand_total) }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ $order->created_at->format('Y/m/d H:i') }}</td>
                        <td><a href= {{route('orders.show', $order->id)}} class="btn btn-sm btn-info">詳細</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <a href="{{ route('cart.add') }}" class="btn btn-primary">引き続き買い物をする</a>
</div>
</x-app-layout>
