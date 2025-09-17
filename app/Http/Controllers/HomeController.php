<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Models\GuestSession;

class HomeController extends Controller
{
    // HomeController@index
    public function index(Request $req)
    {
        if (!$req->cookie('guest_uuid')) {
            $uuid = Str::uuid()->toString();
            GuestSession::create([
                'guest_uuid' => $uuid,
                'ip' => $req->ip(),
                'credits_remaining' => config('ai.guest_credits'),
                'expires_at' => now()->addDays(config('ai.guest_expires_days')),
            ]);
            Cookie::queue('guest_uuid', $uuid, 60 * 24 * config('ai.guest_expires_days')); // dakika
        }
        return view('welcome');
    }
}
