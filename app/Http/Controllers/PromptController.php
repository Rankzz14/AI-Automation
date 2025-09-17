<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prompt;

class PromptController extends Controller
{
    public function index()
    {
        $prompts = Prompt::all();
        return view('prompts.index', compact('prompts'));
    }

    public function create()
    {
        return view('prompts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'template' => 'required|string|max:2000',
        ]);

        Prompt::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'template' => $request->template,
            'public' => true,
        ]);

        return redirect()->route('prompts.index')->with('success', 'Prompt eklendi.');
    }

    public function edit(Prompt $prompt)
    {
        return view('prompts.edit', compact('prompt'));
    }

    public function update(Request $request, Prompt $prompt)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'template' => 'required|string|max:2000',
        ]);

        $prompt->update([
            'title' => $request->title,
            'template' => $request->template,
        ]);

        return redirect()->route('prompts.index')->with('success', 'Prompt güncellendi.');
    }

    public function destroy(Prompt $prompt)
    {
        $prompt->delete();
        return redirect()->route('prompts.index')->with('success', 'Prompt silindi.');
    }
}