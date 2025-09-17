<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AI Prompt Çalıştır') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="runPrompt()">

                <!-- Üst prompt textarea -->
                <textarea x-model="promptText" rows="4" 
                    class="w-full border-gray-300 rounded-lg p-3 mb-4"
                    placeholder="Prompt buraya gelecek..."></textarea>

                <div class="flex space-x-4">
                    <!-- Veri girişi textboxu -->
                    <div class="w-1/2">
                        <textarea x-model="input" rows="4"
                            class="w-full border-gray-300 rounded-lg p-3"
                            placeholder="Veri girişi..."></textarea>

                        <button @click="run()" 
                            class="mt-2 w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg"
                            :disabled="loading"
                            x-text="loading ? 'Çalışıyor...' : 'Çalıştır'">
                        </button>
                    </div>

                    <!-- Çıktı textboxu -->
                    <div class="w-1/2">
                        <textarea x-model="output" rows="7"
                            class="w-full border-gray-300 rounded-lg p-3 bg-gray-100"
                            readonly
                            placeholder="Çıktı burada gözükecek..."></textarea>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function runPrompt() {
            return {
                promptText: '', 
                input: '',
                output: '',
                loading: false,

                async run() {
                    this.loading = true;
                    this.output = 'İşleniyor...';

                    try {
                        const res = await fetch('/run', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                prompt: this.promptText,
                                input: this.input
                            })
                        });

                        const json = await res.json();

                        if (json.run_id) {
                            this.checkResult(json.run_id);
                        } else if (json.error) {
                            this.output = json.error;
                        }
                    } catch (err) {
                        this.output = 'Hata: ' + err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async checkResult(runId) {
                    try {
                        const res = await fetch(`/runs/${runId}`);
                        const json = await res.json();

                        if (json.status === 'completed' || json.status === 'failed') {
                            this.output = json.output_text ?? 'Hata!';
                        } else {
                            setTimeout(() => this.checkResult(runId), 1500);
                        }
                    } catch (err) {
                        this.output = 'Sonuç alınamadı: ' + err.message;
                    }
                }
            }
        }
    </script>
</x-app-layout>
