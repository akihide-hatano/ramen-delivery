<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ご注文完了') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg text-lg font-semibold">
                            {{ session('success') }}
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg text-lg font-semibold">
                            ご注文ありがとうございます！
                        </div>
                    @endif

                    <p class="text-gray-700 mb-8">
                        ご注文内容を確認後、準備を進めさせていただきます。
                        <br>しばらくお待ちください。
                    </p>

                    <div class="flex items-center justify-center space-x-4"> {{-- ボタンを横並びにするためのflexコンテナ --}}
                        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 transition ease-in-out duration-150">
                            ホームに戻る
                        </a>

                        {{-- ★注文履歴を見るボタンを追加★ --}}
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 transition ease-in-out duration-150">
                            注文履歴を見る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
