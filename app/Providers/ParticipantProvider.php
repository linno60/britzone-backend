<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Participant;

class ParticipantProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Participant::creating(function ($participant) {
            
            return $participant->touch();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        
    }
}
