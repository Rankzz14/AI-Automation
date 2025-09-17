<form method="POST" action="{{ route('prompts.store') }}">
  @csrf
  <input name="title" class="border p-2 w-full" placeholder="Şablon başlığı">
  <textarea name="template" class="border p-2 w-full mt-2" rows="6" placeholder="Örnek: 'Sana x,y,z vericem... {input}'"></textarea>
  <button class="bg-blue-600 text-white px-4 py-2 mt-2">Kaydet</button>
</form>