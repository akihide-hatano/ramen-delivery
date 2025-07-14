<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('店舗新規登録') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- バリデーションエラーメッセージの表示 --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.shops.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- 店舗名 --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">店舗名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- 住所 --}}
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">住所 <span class="text-red-500">*</span></label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- 電話番号 --}}
                        <div class="mb-4">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">電話番号 <span class="text-red-500">*</span></label>
                            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- メールアドレス --}}
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- 説明 --}}
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">説明</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                        </div>

                        {{-- 営業時間 --}}
                        <div class="mb-4">
                            <label for="business_hours" class="block text-sm font-medium text-gray-700">営業時間</label>
                            <input type="text" name="business_hours" id="business_hours" value="{{ old('business_hours') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="例: 11:00-23:00">
                        </div>

                        {{-- 定休日 --}}
                        <div class="mb-4">
                            <label for="regular_holiday" class="block text-sm font-medium text-gray-700">定休日</label>
                            <input type="text" name="regular_holiday" id="regular_holiday" value="{{ old('regular_holiday') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="例: 不定休, 水曜日">
                        </div>

                        {{-- 画像ファイル (3枚) --}}
                        <div class="mb-4">
                            <label for="photo_1" class="block text-sm font-medium text-gray-700">店舗画像 1</label>
                            <input type="file" name="photo_1" id="photo_1" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div class="mb-4">
                            <label for="photo_2" class="block text-sm font-medium text-gray-700">店舗画像 2</label>
                            <input type="file" name="photo_2" id="photo_2" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div class="mb-4">
                            <label for="photo_3" class="block text-sm font-medium text-gray-700">店舗画像 3</label>
                            <input type="file" name="photo_3" id="photo_3" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        {{-- チェックボックス項目 --}}
                        <div class="mb-4">
                            <label for="has_parking" class="inline-flex items-center">
                                <input type="checkbox" name="has_parking" id="has_parking" value="1" {{ old('has_parking') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">駐車場あり</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="has_table_seats" class="inline-flex items-center">
                                <input type="checkbox" name="has_table_seats" id="has_table_seats" value="1" {{ old('has_table_seats') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">テーブル席あり</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="has_counter_seats" class="inline-flex items-center">
                                <input type="checkbox" name="has_counter_seats" id="has_counter_seats" value="1" {{ old('has_counter_seats') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">カウンター席あり</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="accept_cash" class="inline-flex items-center">
                                <input type="checkbox" name="accept_cash" id="accept_cash" value="1" {{ old('accept_cash') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">現金支払い可</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="accept_credit_card" class="inline-flex items-center">
                                <input type="checkbox" name="accept_credit_card" id="accept_credit_card" value="1" {{ old('accept_credit_card') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">クレジットカード支払い可</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="accept_e_money" class="inline-flex items-center">
                                <input type="checkbox" name="accept_e_money" id="accept_e_money" value="1" {{ old('accept_e_money') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">電子マネー支払い可</span>
                            </label>
                        </div>

                        {{-- 送信ボタン --}}
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                登録する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>