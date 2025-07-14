<tr>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
        {{ $category->id }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        {{ str_repeat('&nbsp;&nbsp;&nbsp;', $level) }}
        @if ($level > 0) -- @endif
        {{ $category->name }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        {{ $category->display_order }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        {{ $category->parent->name ?? 'なし' }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">編集</a>
        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('本当にこのカテゴリを削除しますか？');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-900">削除</button>
        </form>
    </td>
</tr>