<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('商品新規登録') }}
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

                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- 商品名 --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">商品名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- 説明 --}}
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">説明</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                        </div>

                        {{-- 価格 --}}
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">価格 <span class="text-red-500">*</span></label>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- カテゴリ --}}
                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700">カテゴリ <span class="text-red-500">*</span></label>
                            <select name="category_id" id="category_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">カテゴリを選択してください</option>
                                @foreach ($categories as $mainCategory)
                                    <option value="{{ $mainCategory->id }}" {{ old('category_id') == $mainCategory->id ? 'selected' : '' }}>
                                        {{ $mainCategory->name }}
                                    </option>
                                    @foreach ($mainCategory->children as $subCategory)
                                        <option value="{{ $subCategory->id }}" {{ old('category_id') == $subCategory->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;&nbsp;-- {{ $subCategory->name }}
                                        </option>
                                        @foreach ($subCategory->children as $deepSubCategory)
                                            <option value="{{ $deepSubCategory->id }}" {{ old('category_id') == $deepSubCategory->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{ $deepSubCategory->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        {{-- 画像ファイル --}}
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">商品画像</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        {{-- 限定商品チェックボックス --}}
                        <div class="mb-4">
                            <label for="is_limited" class="inline-flex items-center">
                                <input type="checkbox" name="is_limited" id="is_limited" value="1" {{ old('is_limited') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">限定商品</span>
                            </label>
                        </div>

                        {{-- 限定場所 (is_limitedがチェックされたら表示/非表示を切り替えるJavaScriptが必要) --}}
                        <div class="mb-4" id="limited_location_field" style="{{ old('is_limited') ? '' : 'display:none;' }}">
                            <label for="limited_location" class="block text-sm font-medium text-gray-700">限定場所</label>
                            <select name="limited_location" id="limited_location"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">選択してください</option>
                                @foreach ($shops as $shop)
                                    <option value="{{ $shop->name }}" {{ old('limited_location') == $shop->name ? 'selected' : '' }}>
                                        {{ $shop->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 限定タイプ (is_limitedがチェックされたら表示/非表示を切り替えるJavaScriptが必要) --}}
                        <div class="mb-4" id="limited_type_field" style="{{ old('is_limited') ? '' : 'display:none;' }}">
                            <label for="limited_type" class="block text-sm font-medium text-gray-700">限定タイプ</label>
                            <select name="limited_type" id="limited_type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">選択してください</option>
                                <option value="location" {{ old('limited_type') == 'location' ? 'selected' : '' }}>店舗限定</option>
                                <option value="season" {{ old('limited_type') == 'season' ? 'selected' : '' }}>季節限定</option>
                            </select>
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

    {{-- JavaScript for conditional display of limited fields --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isLimitedCheckbox = document.getElementById('is_limited');
            const limitedLocationField = document.getElementById('limited_location_field');
            const limitedTypeField = document.getElementById('limited_type_field');

            function toggleLimitedFields() {
                if (isLimitedCheckbox.checked) {
                    limitedLocationField.style.display = 'block';
                    limitedTypeField.style.display = 'block';
                } else {
                    limitedLocationField.style.display = 'none';
                    limitedTypeField.style.display = 'none';
                }
            }

            isLimitedCheckbox.addEventListener('change', toggleLimitedFields);

            // Initial call to set correct state on page load (e.g., after validation error redirect)
            toggleLimitedFields();
        });
    </script>
</x-app-layout>