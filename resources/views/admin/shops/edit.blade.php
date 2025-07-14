<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('店舗編集') }}
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

                    <form method="POST" action="{{ route('admin.shops.update', $shop) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- PUTメソッドを偽装 --}}

                        {{-- 店舗名 --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">店舗名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $shop->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- 住所 --}}
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">住所 <span class="text-red-500">*</span></label>
                            <input type="text" name="address" id="address" value="{{ old('address', $shop->address) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- 電話番号 --}}
                        <div class="mb-4">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">電話番号 <span class="text-red-500">*</span></label>
                            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $shop->phone_number) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- メールアドレス --}}
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $shop->email) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- 説明 --}}
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">説明</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $shop->description) }}</textarea>
                        </div>

                        {{-- 営業時間 --}}
                        <div class="mb-4">
                            <label for="business_hours" class="block text-sm font-medium text-gray-700">営業時間</label>
                            <input type="text" name="business_hours" id="business_hours" value="{{ old('business_hours', $shop->business_hours) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="例: 11:00-23:00">
                        </div>

                        {{-- 定休日 --}}
                        <div class="mb-4">
                            <label for="regular_holiday" class="block text-sm font-medium text-gray-700">定休日</label>
                            <input type="text" name="regular_holiday" id="regular_holiday" value="{{ old('regular_holiday', $shop->regular_holiday) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="例: 不定休, 水曜日">
                        </div>

                        {{-- ★緯度・経度入力フィールドを削除★ --}}
                        {{-- <div class="mb-4">
                            <label for="latitude" class="block text-sm font-medium text-gray-700">緯度</label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $latitude) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="例: 34.6667">
                        </div>
                        <div class="mb-4">
                            <label for="longitude" class="block text-sm font-medium text-gray-700">経度</label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $longitude) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="例: 135.5000">
                        </div> --}}

                        {{-- 現在の画像と新しい画像のアップロード --}}
                        @php
                            $photoFields = [
                                'photo_1' => $shop->photo_1_url,
                                'photo_2' => $shop->photo_2_url,
                                'photo_3' => $shop->photo_3_url,
                            ];
                        @endphp

                        @foreach ($photoFields as $field => $url)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">現在の店舗画像 {{ substr($field, -1) }}</label>
                                @if ($url)
                                    <div class="mt-1 flex items-center">
                                        <img src="{{ $url }}" alt="{{ $shop->name }}" class="h-20 w-20 object-cover rounded-md mr-4">
                                        <label for="delete_{{ $field }}" class="inline-flex items-center">
                                            <input type="checkbox" name="delete_{{ $field }}" id="delete_{{ $field }}" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">画像を削除する</span>
                                        </label>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 mt-1">画像は登録されていません。</p>
                                @endif

                                <label for="{{ $field }}" class="block text-sm font-medium text-gray-700 mt-4">新しい店舗画像 {{ substr($field, -1) }} (変更する場合)</label>
                                <input type="file" name="{{ $field }}" id="{{ $field }}" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        @endforeach

                        {{-- チェックボックス項目 --}}
                        <div class="mb-4">
                            <label for="has_parking" class="inline-flex items-center">
                                <input type="checkbox" name="has_parking" id="has_parking" value="1" {{ old('has_parking', $shop->has_parking) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">駐車場あり</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="has_table_seats" class="inline-flex items-center">
                                <input type="checkbox" name="has_table_seats" id="has_table_seats" value="1" {{ old('has_table_seats', $shop->has_table_seats) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">テーブル席あり</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="has_counter_seats" class="inline-flex items-center">
                                <input type="checkbox" name="has_counter_seats" id="has_counter_seats" value="1" {{ old('has_counter_seats', $shop->has_counter_seats) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">カウンター席あり</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="accept_cash" class="inline-flex items-center">
                                <input type="checkbox" name="accept_cash" id="accept_cash" value="1" {{ old('accept_cash', $shop->accept_cash) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">現金支払い可</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="accept_credit_card" class="inline-flex items-center">
                                <input type="checkbox" name="accept_credit_card" id="accept_credit_card" value="1" {{ old('accept_credit_card', $shop->accept_credit_card) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">クレジットカード支払い可</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="accept_e_money" class="inline-flex items-center">
                                <input type="checkbox" name="accept_e_money" id="accept_e_money" value="1" {{ old('accept_e_money', $shop->accept_e_money) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">電子マネー支払い可</span>
                            </label>
                        </div>

                        {{-- 送信ボタン --}}
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                更新する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>