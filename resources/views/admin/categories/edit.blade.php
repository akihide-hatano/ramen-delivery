<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('カテゴリ編集') }}
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

                    {{-- フォームのactionはupdateメソッド、methodはPUT/PATCH --}}
                    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                        @csrf
                        @method('PUT') {{-- PUTメソッドを偽装 --}}

                        {{-- カテゴリ名 --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">カテゴリ名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- 表示順 --}}
                        <div class="mb-4">
                            <label for="display_order" class="block text-sm font-medium text-gray-700">表示順 <span class="text-red-500">*</span></label>
                            <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $category->display_order) }}" required min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        {{-- 親カテゴリ --}}
                        <div class="mb-4">
                            <label for="parent_id" class="block text-sm font-medium text-gray-700">親カテゴリ</label>
                            <select name="parent_id" id="parent_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">なし (最上位カテゴリ)</option>
                                @foreach ($allCategories as $cat)
                                    {{-- 編集対象カテゴリ自身は親にできない --}}
                                    @if ($cat->id !== $category->id)
                                        <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
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