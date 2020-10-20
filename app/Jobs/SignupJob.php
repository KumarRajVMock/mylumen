<?php

namespace App\Jobs;

use App\User;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\Signupverify;


class SignupJob extends Job implements ShouldQueue
{
    use SerializesModels;
    
    protected $email, $registration;
    
    public function __construct($email, $registration)
    {
        $this->email = $email;
        $this->registration = $registration;
    }
    
    public function handle()
    {
        Mail::to($this->email)->send(new Signupverify($this->registration));
    }
}