<?php

namespace App\SOLID\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;

trait SenderTraits
{
    private $codes = [
        "1" => "Success",
        "M0000" => "Success",
        "M0001" => "Variables missing",
        "M0002" => "Invalid login info",
        "M0022" => "Exceed number of senders allowed",
        "M0023" => "Sender Name is active or under activation or refused",
        "M0024" => "Sender Name should be in English or number",
        "M0025" => "Invalid Sender Name Length",
        "M0026" => "Sender Name is already activated or not found",
        "M0027" => "Activation Code is not Correct",
        "M0029" => "Invalid Sender Name : Sender Name should contain only letters, numbers and the maximum length should be 11 characters",
        "M0030" => "Sender Name should ended with AD",
        "M0031" => "Maximum allowed size of uploaded file is 5 MB",
        "M0032" => "Only pdf,png,jpg and jpeg files are allowed!",
        "M0033" => "Sender Type should be normal or whitelist only",
        "M0034" => "Please Use POST Method",
        "M0036" => "There is no any sender",
        "1010" => "Variables missing",
        "1020" => "Invalid login info",
        "1050" => "MSG body is empty",
        "1060" => "Balance is not enough",
        "1061" => "MSG duplicated",
        "1064" => "Free OTP , Invalid MSG content you should use 'Pin Code is: xxxx' or 'Verification Code: xxxx' or 'رمز التحقق: 1234' , or upgrade your account and activate your sender to send any content",
        "1110" => "Sender name is missing or incorrect",
        "1120" => "Mobile numbers is not correct",
        "1140" => "MSG length is too long"
    ];
    public function send_email_code($options)
    {
        $options['Subject'] 		= (!empty($options['Subject'])) ? $options['Subject'] : '';
        $options['Body'] 			= (!empty($options['Body'])) ? $options['Body'] : '';
        $options['addAddress'] 		= (!empty($options['addAddress'])) ? $options['addAddress'] : false;
        $options['addReplyTo'] 		= (!empty($options['addReplyTo'])) ? $options['addReplyTo'] : false;
        $options['addCC'] 			= (!empty($options['addCC'])) ? $options['addCC'] : false;
        $options['addBCC'] 			= (!empty($options['addBCC'])) ? $options['addBCC'] : false;
        $options['addAttachment'] 	= (!empty($options['addAttachment'])) ? $options['addAttachment'] : false;
        $mail 						= new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host 		= env('MAIL_HOST'); //first if you need env this is better way env('') just it
            $mail->SMTPAuth 	= env('SMTPAuth');
            $mail->Username 	= env('MAIL_USERNAME');
            $mail->Password 	= env('MAIL_PASSWORD');
            $mail->SMTPSecure 	= PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port 		= env('MAIL_PORT');
            $mail->CharSet 		= 'UTF-8';
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            if (!empty($options['addAddress']) and is_array($options['addAddress'])) {
                foreach ($options['addAddress'] as $Email) {
                    $mail->addAddress($Email);
                }
            }
            if (!empty($options['addReplyTo']) and is_array($options['addReplyTo'])) {
                foreach ($options['addReplyTo'] as $Email) {
                    $mail->addReplyTo($Email);
                }
            }
            if (!empty($options['addCC']) and is_array($options['addCC'])) {
                foreach ($options['addCC'] as $Email) {
                    $mail->addCC($Email);
                }
            }
            if (!empty($options['addBCC']) and is_array($options['addBCC'])) {
                foreach ($options['addBCC'] as $Email) {
                    $mail->addBCC($Email);
                }
            }
            if (!empty($options['addAttachment']) and is_array($options['addAttachment'])) {
                foreach ($options['addAttachment'] as $Attachment) {
                    if (file_exists($Attachment)) {
                        $mail->addAttachment($Attachment);
                    }
                }
            }
            $mail->isHTML(true);
            $mail->Subject = $options['Subject'];
            $mail->Body = $options['Body'];
            if (!$mail->send()) {
                return [
                    'status' => false,
                    'message' => 'not send',
                ];
            } else {
                return [
                    'status' => TRUE,
                    'message' => 'Successful',
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'not send',
            ];
        }
    }


    public function send_whatsapp_code($options)
    {
        $template_name = !empty($options['template_name']) ? $options['template_name'] : 'document_signature';

        $parameters = [
            [
                "type" => "text",
                "text" => $options['msg1']
            ]
        ];

        $postFields = [
            "messaging_product" => "whatsapp",
            "to"                => $options['Mobile'],
            "type"              => "template",
            "template"          => [
                "name"       => $template_name,
                "language"   => [
                    "code" => !empty($options['lang_key']) ? $options['lang_key'] : "ar",
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => $parameters
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "URL",
                        "index" => 0,
                        "parameters" => $parameters
                    ]
                ],
            ],
        ];

        $token = env('WA_TOKEN', $_SERVER['waToken']);
        $phoneNumberID = env('WA_PHONENUMBER_ID', $_SERVER['waPhonenumberID']);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];

        $client = new Client();

        try {
            $response = $client->post("https://graph.facebook.com/v15.0/{$phoneNumberID}/messages", [
                'headers' => $headers,
                'json'    => $postFields,
                'timeout' => 100,
            ]);

            // Decode the response
            $return_data = json_decode($response->getBody(), true);

            if (!empty($return_data['messages'][0]['id'])) {
                return [
                    'status' => true,
                    'message' => $return_data['messages'][0]['id']
                ];
            }

            return [
                'status' => false,
                'message' => "There was an error in the Facebook API response."
            ];

        } catch (RequestException $e) {
            Log::error('Error sending WhatsApp message', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);

            return [
                'status' => false,
                'message' => 'An error occurred while sending the WhatsApp message.'
            ];
        }
    }


    public function send_mobile_code(array $options = [])
    {
        $numbers = is_array($options['Mobiles']) ? implode(',', $options['Mobiles']) : $options['Mobiles'];

        $postfields = [
            "userName" => env('User'),
            "userSender" => env('Sender'),
            "apiKey" => env('ApiKey'),
            "numbers" => $numbers,
            "msg" => $options['Message']
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Cookie' => 'userCurrency=SAR; SERVERID=MBE1; userLang=Ar'
        ])->post(env('Url'), $postfields);

        $res = $response->json();

        if (!empty($res['code']) && ($res['code'] == "1" || $res['code'] == "M0001")) {
            return [
                'status' => true,
                'message' => 'Success'
            ];
        } else {
            return [
                'status' => false,
                'message' => !empty($res['code']) ? $this->codes[$res['code']] : "Error 103"
            ];
        }
    }
    public function hide_mobile($number){
        if(!empty($number) and isset($number)){
            return str_pad(substr($number, -2), strlen($number), '*', STR_PAD_LEFT);
        }
        else
        {
            return '########';
        }
    }

    public function hide_email($email, $minLength = 2, $maxLength = 2, $mask = "********")
    {
        if(!empty($email) and isset($email)){

            $em = $this->checkEmail($email);
            if($em){
                $atPos = strrpos($email, "@");
                $name = substr($email, 0, $atPos);
                $len = strlen($name);
                $domain = substr($email, $atPos);

                if (($len / 2) < $maxLength) $maxLength = ($len / 2);

                $shortenedEmail = (($len > $minLength) ? substr($name, 0, $maxLength) : "");
                return  "{$shortenedEmail}{$mask}{$domain}";
            }
            else{
                return '########';
            }
        }
        else{
            return '########';
        }
    }
    public function checkEmail($email) {
        if ( strpos($email, '@') !== false ) {
            $split = explode('@', $email);
            return (strpos($split['1'], '.') !== false ? true : false);
        }
        else {
            return false;
        }
    }

    function getErrorCode(String $code)
    {
        $errorMessages = [
            "1" => "Success",
            "M0000" => "Success",
            "M0001" => "Variables missing",
            "M0002" => "Invalid login info",
            "M0022" => "Exceed number of senders allowed",
            "M0023" => "Sender Name is active or under activation or refused",
            "M0024" => "Sender Name should be in English or number",
            "M0025" => "Invalid Sender Name Length",
            "M0026" => "Sender Name is already activated or not found",
            "M0027" => "Activation Code is not Correct",
            "M0029" => "Invalid Sender Name : Sender Name should contain only letters, numbers and the maximum length should be 11 characters",
            "M0030" => "Sender Name should ended with AD",
            "M0031" => "Maximum allowed size of uploaded file is 5 MB",
            "M0032" => "Only pdf,png,jpg and jpeg files are allowed!",
            "M0033" => "Sender Type should be normal or whitelist only",
            "M0034" => "Please Use POST Method",
            "M0036" => "There is no any sender",
            "1010" => "Variables missing",
            "1020" => "Invalid login info",
            "1050" => "MSG body is empty",
            "1060" => "Balance is not enough",
            "1061" => "MSG duplicated",
            "1064" => "Free OTP , Invalid MSG content you should use 'Pin Code is: xxxx' or 'Verification Code: xxxx' or 'رمز التحقق: 1234' , or upgrade your account and activate your sender to send any content",
            "1110" => "Sender name is missing or incorrect",
            "1120" => "Mobile numbers is not correct",
            "1140" => "MSG length is too long"
        ];
        if (isset($errorMessages[$code])) {
            return $errorMessages[$code];
        } else {
            return "Error: Code '$code' not found";
        }
    }

}
