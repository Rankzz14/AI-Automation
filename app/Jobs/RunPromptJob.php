<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use App\Models\Run;
use App\Models\User;
use App\Models\GuestSession;

class RunPromptJob implements ShouldQueue
{
    use Queueable;

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
        $run = Run::find($this->run->id);
        if (! $run) {
            return;
        }

        $prompt = $run->prompt;
        $model = config('ai.default_model');

        $finalPrompt = str_replace('{input}', $run->input_text, $prompt->template);

        try {
            $resp = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Sen bir işlemci...'],
                        ['role' => 'user', 'content' => $finalPrompt],
                    ],
                    'max_tokens' => config('ai.max_tokens_per_run')
                ]);

            $json = $resp->json();
            $output = $json['choices'][0]['message']['content'] ?? null;
            $used = $json['usage']['total_tokens'] ?? null;

        } catch (\Exception $e) {
            $run->update([
                'status' => 'failed',
                'output_text' => $e->getMessage()
            ]);

            $this->refundReserved($run, $run->reserved_tokens);
            return;
        }

        $tokensUsed = (int) ($used ?? $run->reserved_tokens);
        $refund = $run->reserved_tokens - $tokensUsed;

        $run->update([
            'output_text' => $output,
            'tokens_used' => $tokensUsed,
            'cost_cents' => $this->calculateCostCents($tokensUsed),
            'status' => 'completed'
        ]);

        if ($refund > 0) {
            $this->refundReserved($run, $refund);
        }
    }

    protected function refundReserved(Run $run, int $refund)
    {
        if ($run->user_id) {
            User::where('id', $run->user_id)
                ->update(['credits' => \DB::raw("credits + $refund")]);
        } else {
            GuestSession::where('guest_uuid', $run->guest_uuid)
                ->update(['credits_remaining' => \DB::raw("credits_remaining + $refund")]);
        }
    }

    protected function calculateCostCents(int $tokens)
    {
        $per1000 = config('ai.model_price_per_1000_tokens_cents');
        return (int) round($tokens * ($per1000 / 1000));
    }
}
