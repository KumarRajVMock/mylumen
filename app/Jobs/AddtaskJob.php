<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\Addtask;


class AddtaskJob extends Job implements ShouldQueue
{
    use SerializesModels;
    
    protected $email, $addtask;
    
    public function __construct($email, $addtask)
    {
        $this->email = $email;
        $this->addtask = $addtask;
    }
    
    public function handle()
    {
        Mail::to($this->email)->send(new Addtask($this->addtask));
    }
}