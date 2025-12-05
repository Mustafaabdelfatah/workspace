<?php

namespace Modules\Core\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $url;
    protected $user;
    protected $lang_key;

    public function __construct($url,$user, $lang_key)
    {
        $this->url = $url;
        $this->user = $user;
        $this->lang_key = $lang_key; 
    }

    public function build()
    {

        $options = [
            'link' => $this->url,
            'user' => $this->user,
            'lang_key' => $this->lang_key,
            'Subject' => 'Reset password',
            'subject' => 'Reset Password',
            'button_text' => 'Reset Password',
            'button_url' => $this->url,
        ];
        return $this->view('Core::email.reset-password',compact('options'));
    }
}

