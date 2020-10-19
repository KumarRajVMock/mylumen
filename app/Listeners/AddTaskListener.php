<?php

namespace App\Listeners;

use App\Events\AddTaskEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ExampleListener
{
    public function __construct()
    {
        //
    }

    public function handle(AddTaskEvent $event)
    {
        //
    }
}
