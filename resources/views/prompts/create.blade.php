<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Prompt Ekle
        </h2>
    </x-slot>
    <div class="py-6 max-w-xl mx-auto">
        <form method="POST" action="{{ route('prompts.store') }}">
            @csrf
            <input name="title" class="border p-2 w-full mb-2" placeholder="Şablon başlığı" value="{{ old('title') }}">
            <textarea name="template" class="border p-2 w-full mb-2" rows="6" placeholder="Örnek: 'Sana x,y,z vericem... {input}'">{{ old('template') }}</textarea>
            <button class="bg-blue-600 text-white px-4 py-2">Kaydet</button>
        </form>
    </div>
</x-app-layout>