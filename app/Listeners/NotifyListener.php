<?php

namespace App\Listeners;

use App\Events\NotifyEvent;
use Pusher;

class NotifyListener
{
    public function __construct()
    {
        //
    }
    public function handle(NotifyEvent $event)
    {
        $options = array('cluster' => 'ap2', 'useTLS' => true);
        
        $pusher = new Pusher\Pusher('891c7f6c06b720face3c','d1a654e49002cde58d30',
            '1089373',$options);
        
        $pusher->trigger($event->data->channel, $event->data->event, $event->data);
    }
}
