<?php

namespace App\Listeners;

use App\Models\SessionHistory;
use App\Models\User;
use Carbon\Carbon;

class RecordSessionHistory
{
    public function handle($event)
    {
        if ($event instanceof \Illuminate\Auth\Events\Login) {
            // Registrar nuevo login
            SessionHistory::create([
                'session_id' => session()->getId(),
                'user_id' => $event->user->id,
                'user_email' => $event->user->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'login_at' => Carbon::now(),
                'last_activity_at' => Carbon::now()
            ]);
        } elseif ($event instanceof \Illuminate\Auth\Events\Logout) {
            // Actualizar registro con logout
            SessionHistory::where('user_id', $event->user->id)
                ->where('session_id', session()->getId())
                ->whereNull('logout_at')
                ->update([
                    'logout_at' => Carbon::now(),
                    'last_activity_at' => Carbon::now()
                ]);
        }
    }
}