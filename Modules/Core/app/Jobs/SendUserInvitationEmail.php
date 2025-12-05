<?php

namespace Modules\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\Emails\UserInvitationMail;

class SendUserInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $token;
    protected $lang_key;

    // The constructor receives the email, token, and lang_key
    public function __construct($email, $token, $lang_key = 'en')
    {
        $this->email = $email;
        $this->token = $token;
        $this->lang_key = $lang_key;  // Store the lang_key here
    }

    public function handle()
    {
        $frontendUrl = env("SITE_CLIENT_URL", "");
        $url = $frontendUrl . "/auth/invitation/create_user_from_invitation/{$this->token}";

        // Pass the lang_key along with the URL when sending the email
        Mail::to($this->email)->send(new UserInvitationMail($url, $this->lang_key));
    }
}
