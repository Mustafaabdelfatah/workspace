<?php

namespace Modules\Core\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class InvitationMail extends Mailable /*implements ShouldQueue*/
{
    use Queueable, SerializesModels;

    protected $url;
    protected $lang_key;

    // Constructor receives URL and lang_key
    public function __construct($url, $lang_key)
    {
        $this->url = $url;
        $this->lang_key = $lang_key;  
    }

    public function build()
    {

        $options = [
            'link' => $this->url,
            'lang_key' => $this->lang_key,
            'Subject' => 'User Invitation',
            'subject' => 'User Invitation',
            'button_text' => 'Create User',
            'button_url' => $this->url,

        ];
        return $this->view('Core::email.send-invitation-token',compact('options'));
    }
}

