<?php

namespace App\Jobs;

use App\Notifications\EmailverfyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $isMaill;

    public function __construct(User $user)
    {
        $this->user = $user;
   
    }

    public function handle()
    {
       
            $this->user->notify(new EmailverfyNotification());
      
    }
}