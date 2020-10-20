<?php

namespace App\Listeners;

use App\Events\AddTaskEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ExampleListener
{
    public $message;
    public function __construct()
    {
        //
    }

    public function handle(AddTaskEvent $event)
    {
        $message = $event->message;
    }
}
