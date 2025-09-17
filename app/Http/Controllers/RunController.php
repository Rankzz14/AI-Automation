<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Run;
use App\Models\GuestSession;
use Illuminate\Support\Facades\DB;
use App\Jobs\RunPromptJob;
use App\Models\Prompt;

class RunController extends Controller
{
    public function index()
    {
        $prompts = Prompt::all(); // Tüm promptları al
        return view('run', compact('prompts')); // run.blade.php’yi aç
    }
    public function run(Request $req)
    {
        $req->validate([
            'prompt_id' => 'required|exists:prompts,id',
            'input' => 'required|string|max:2000'
        ]);

        $user = $req->user();
        $guestUuid = $req->cookie('guest_uuid');
        $reserved = config('ai.max_tokens_per_run');

        $run = null;

        DB::transaction(function () use ($req, $user, $guestUuid, $reserved, &$run) {
            $run = Run::create([
                'prompt_id' => $req->prompt_id,
                'user_id' => $user?->id,
                'guest_uuid' => $user ? null : $guestUuid,
                'input_text' => $req->input,
                'reserved_tokens' => $reserved,
                'status' => 'queued'
            ]);

            if ($user) {
                $user->decrement('credits', $reserved);
            } else {
                GuestSession::where('guest_uuid', $guestUuid)
                    ->decrement('credits_remaining', $reserved);
            }
        });

        // Kuyruğa at
        RunPromptJob::dispatch($run);

        return response()->json([
            'run_id' => $run->id,
            'status' => 'queued'
        ]);
    }
    public function show($id)
    {
        $run = Run::findOrFail($id);
        return response()->json($run);
    }
}
