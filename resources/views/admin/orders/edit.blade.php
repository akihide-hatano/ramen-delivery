<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('注文情報編集 (管理者用)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">注文ID: {{ $order->id }} の編集</h3>

                    {{-- バリデーションエラーの表示 --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                        @csrf
                        @method('PATCH') {{-- PUTでも可。Route::resourceはPATCH/PUT両方に対応 --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- 注文ステータス --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">ステータス</label>
                                <select id="status" name="status" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ old('status', $order->status) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 配送先住所 --}}
                            <div>
                                <label for="delivery_address" class="block text-sm font-medium text-gray-700">配送先住所</label>
                                <input type="text" id="delivery_address" name="delivery_address" value="{{ old('delivery_address', $order->delivery_address) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            {{-- 電話番号 --}}
                            <div>
                                <label for="delivery_phone" class="block text-sm font-medium text-gray-700">電話番号</label>
                                <input type="text" id="delivery_phone" name="delivery_phone" value="{{ old('delivery_phone', $order->delivery_phone) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            {{-- 希望配達時間 --}}
                            <div>
                                <label for="desired_delivery_time_slot" class="block text-sm font-medium text-gray-700">希望配達時間 (例: 12:00-14:00)</label>
                                <input type="text" id="desired_delivery_time_slot" name="desired_delivery_time_slot" value="{{ old('desired_delivery_time_slot', $order->desired_delivery_time_slot) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            {{-- 配送メモ --}}
                            <div class="col-span-1 md:col-span-2">
                                <label for="delivery_notes" class="block text-sm font-medium text-gray-700">配送メモ</label>
                                <textarea id="delivery_notes" name="delivery_notes" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('delivery_notes', $order->delivery_notes) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-semibold leading-6 text-gray-900">キャンセル</a>
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                更新
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>