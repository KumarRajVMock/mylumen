<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Adduserverify extends Mailable
{
    use Queueable, SerializesModels;

    public $token, $password, $name;

    public function __construct($registration)
    {
        $this->token = $registration->token;
        $this->password = $registration->password;
        $this->name = $registration->name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.addverify',['token' => $this->token,'name' => $this->name,'password' => $this->password]);
    }
}
