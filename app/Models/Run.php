<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    protected $fillable = ['prompt_id', 'user_id', 'guest_uuid', 'input_text', 'output_text', 'reserved_tokens', 'tokens_used', 'cost_cents', 'status'];
    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
