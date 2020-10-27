<?php

namespace App\Jobs;

use App\User;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\Adduserverify;


class AdduserJob extends Job implements ShouldQueue
{
    use SerializesModels;
    
    protected $email, $adduser;
    
    public function __construct($email, $adduser)
    {
        $this->email = $email;
        $this->adduser = $adduser;
    }
    
    public function handle()
    {
        Mail::to($this->email)->send(new Adduserverify($this->adduser));
    }
}