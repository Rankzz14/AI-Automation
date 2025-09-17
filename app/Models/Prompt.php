<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model {
  protected $fillable = ['user_id','title','template','public'];
  public function user() { return $this->belongsTo(User::class); }
  public function runs() { return $this->hasMany(Run::class); }
}