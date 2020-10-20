<?php

namespace App\Jobs;

use App\User;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\Resetpassword;

class ResetpasswordJob extends Job implements ShouldQueue
{
    use SerializesModels;
    
    protected $email, $query;
    
    public function __construct($email, $query)
    {
        $this->email = $email;
        $this->query = $query;
    }
    
    public function handle()
    {
        Mail::to($this->email)->send(new Resetpassword($this->query));

    }
}