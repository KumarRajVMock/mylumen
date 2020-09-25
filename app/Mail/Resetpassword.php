<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Resetpassword extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($query)
    {
        $this->token = $query->token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reset',['token' => $this->token,]);
    }
}
