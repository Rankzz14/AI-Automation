<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Promtlarım
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <a href="{{ route('prompts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">Yeni Prompt Ekle</a>
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-2">{{ session('success') }}</div>
        @endif
        <table class="w-full border">
            <thead>
                <tr>
                    <th class="border px-2 py-1">Başlık</th>
                    <th class="border px-2 py-1">Şablon</th>
                    <th class="border px-2 py-1">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prompts as $prompt)
                <tr>
                    <td class="border px-2 py-1">{{ $prompt->title }}</td>
                    <td class="border px-2 py-1 text-xs">{{ $prompt->template }}</td>
                    <td class="border px-2 py-1">
                        <a href="{{ route('prompts.edit', $prompt) }}" class="text-blue-600">Düzenle</a>
                        <form action="{{ route('prompts.destroy', $prompt) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-600 ml-2" onclick="return confirm('Silinsin mi?')">Sil</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>