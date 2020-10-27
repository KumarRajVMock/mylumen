<?php

namespace App\Events;

use App\Models\Notify;

class NotifyEvent extends Event
{
    public $data;
    public function __construct(Notify $data)
    {
        $this->data = $data;
    }
}