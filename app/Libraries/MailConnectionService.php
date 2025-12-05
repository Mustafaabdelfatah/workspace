<?php
namespace App\Libraries;

use Illuminate\Support\Facades\Auth;
use MailSo\Log\Logger;
use MailSo\Mail\MailClient;
use MailSo\Net\Enumerations\ConnectionSecurityType;
use MailSo\Smtp\SmtpClient;
use Modules\MailingSystem\Models\CompanyEmail;


class MailConnectionService
{
    protected $imapClient;
    protected $smtpClient;
    protected $selectedMailConnection;
    protected $selectedMail;
    protected $displayName;
    protected $username;
    protected $password;

    public function __construct() {}

    public function setSelectedMailConnection($mailId)
    {
        if (filter_var($mailId, FILTER_VALIDATE_EMAIL)) {
     
            $companyEmail = CompanyEmail::where('username', $mailId)->first();
            $this->selectedMail = $companyEmail;
        } else {
            $user = Auth::user();
            if (is_null($mailId) || empty($mailId)) {
                $this->selectedMail = $user->userDefaultMail;
            } else {
                $this->selectedMail = $user->userAssignedMail()->where('id', $mailId)->first()?->companyEmail;
            }
        }
        if (!is_null($this->selectedMail)) {
            $this->selectedMailConnection = $this->selectedMail->mailConnection;

        } else {
            throw new \Exception('Mail not found');
        }

    
        $secretKey = $this->selectedMailConnection->api_secret_key;

        $this->displayName = $this->selectedMail->display_name;
        $this->username = $this->selectedMail->username;
        $this->password = $this->decryptedPassword($this->selectedMail->password, $secretKey);

        return $this;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function decryptedPassword($password, $secretKey)
    {
        $str = base64_decode(strtr($password, '-_', '+/'), '=');
        $string = openssl_decrypt($str, 'AES-128-ECB', $secretKey);
        $dataArray = unserialize($string);
        return $dataArray;
    }

    public function getImapClient()
    {
        return $this->imapClient;
    }

    public function getSmtpClient()
    {
        return $this->smtpClient;
    }

    public function smtpConnect()
    {
        try {
            $this->smtpClient = SmtpClient::NewInstance();
            $this->smtpClient->SetLogger(Logger::NewInstance());
            $this->smtpClient->Connect(
                $this->selectedMailConnection->smtp_url,
                (int) $this->selectedMailConnection->smtp_port,
                SmtpClient::EhloHelper(),
                \MailSo\Net\Enumerations\ConnectionSecurityType::AUTO_DETECT,
                false,
                true
            )->Login($this->username, (!is_bool($this->password) ? $this->password : '-'));
            return $this;
        } catch (\Exception $e) {
            $this->smtpClient = $e;
            return $this;
        }
    }

    public function imapConnect()
    {
        try {
            $this->imapClient = MailClient::NewInstance();
            $this->imapClient->SetLogger(Logger::NewInstance());
            if (!$this->imapClient->IsLoggined()) {
                $this->imapClient->Connect($this->selectedMailConnection->imap_url, (int) $this->selectedMailConnection->imap_port, ConnectionSecurityType::AUTO_DETECT);
                $this->imapClient->Login($this->username, (!is_bool($this->password) ? $this->password : '-'));
            }
            return $this;
        } catch (\Exception $e) {
            $this->imapClient = $e;
            return $this;
        }
    }
}
