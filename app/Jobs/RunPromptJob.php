<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Run;
use OpenAI;

class RunPromptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $run;

    /**
     * Job için Run modelini al.
     */
    public function __construct(Run $run)
    {
        // queue’ye model nesnesini değil, sadece id’sini koymak daha güvenli
        $this->run = $run;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $run = $this->run->fresh();
        $promptText = $run->prompt->template;
        $input = $run->input_text;

        // Prompt şablonunda {input} varsa input ile değiştir
        $finalPrompt = str_replace('{input}', $input, $promptText);

        $client = OpenAI::client(config('services.openai.key'));

        try {
            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo', // veya 'gpt-4', 'gpt-5-nano' (erişimin varsa)
                'messages' => [
                    ['role' => 'user', 'content' => $finalPrompt],
                ],
            ]);

            $output = $response->choices[0]->message->content ?? 'Yanıt alınamadı.';

            $run->update([
                'output_text' => $output,
                'status' => 'completed',
            ]);
        } catch (\Exception $e) {
            $run->update([
                'output_text' => 'API Hatası: ' . $e->getMessage(),
                'status' => 'failed',
            ]);
        }
    }
}
