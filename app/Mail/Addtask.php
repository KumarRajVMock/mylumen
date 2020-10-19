<?php

namespace App\Mail;

use App\Models\Registration;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Addtask extends Mailable
{
    use Queueable, SerializesModels;

    public $title, $description, $creator;

    public function __construct($addtask)
    {
        $this->title       = $addtask->title;
        $this->description = $addtask->description;
        $this->creator     = $addtask->creator;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.addtask',['title' => $this->title,'description' => $this->description,'creator' => $this->creator]);
    }
}
