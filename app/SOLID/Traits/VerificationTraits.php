<?php

namespace App\SOLID\Traits;

trait VerificationTraits
{
//    public function send_verify_code($data)
//    {
//        if($data['user_id'] && $data['user_type_id'] != "1"){
//            $result     = $this->mregister->5($user_data);
//            if($result) {
//                $code   = rand(1000, 9999);
//                $param = array(
//                    "verification_type"             => $data['verification_method'],
//                    "verification_code"             => $code,
//                    "verification_status"           => 0,
//                    "verification_time"             => strtotime('+2 minutes', strtotime(date('Y-m-d H:i:s'))),
//                );
//                $allow = $this->mregister->checkverification($param, $user_data);
//                if($allow['status']) {
//                    if ($data['verification_method'] == "email") {
//                        if($user_data['lang_key'] =="ar") {
//                            $ss['name']                     = $result['user_registration_firstname_ar'].' '.$result['user_registration_lastname_ar'];
//                        } else {
//                            $ss['name']                     = $result['user_registration_firstname_en'].' '.$result['user_registration_lastname_en'];
//                        }
//                        $options['Subject']                 = 'Verification Code';
//                        $ss['subject']                      = $options['Subject'];
//                        $ss['clienturl']                    = $this->client_url;
//                        $ss['lang_key']                     = $user_data['lang_key'];
//                        $ss['code']                         = array_map('intval', str_split($code));
//                        $ss['data']                         = $ss;
//                        $options['Body']                    = $this->load->view('sendcode', $ss, true);
//                        $options['addAddress']              = array($result['user_email']);
//                        $s                                  = $this->sender->send_email($options);
//                    } else if ($data['verification_method'] == "mobile") {
//                        $options['Message']         = 'Verification Code : ' . $code;
//                        $options['Mobiles']         = array($result['user_phone']);
//                        $s                          = $this->sender->send_sms($options);
//                    } else if ($data['verification_method'] == "whatsapp") {
//                        $id                         = $user_data['user_id'];
//                        $options['lang_key']        = (!empty($user_data['lang_key'])) ? $user_data['lang_key'] : 'ar';
//                        $options['template_name']   = "login";
//                        $options['msg1']            = $code;
//                        $options['msg2']            = ($options['lang_key'] == "ar") ? "التوقيع-$id" : "signature-$id";
//                        $options['Mobile']          = $result['user_phone'];
//                        $s                          = $this->sender->send_whatsapp($options);
//                    } else {
//                        error_response('E');
//                    }
//                    if ($s['status']) {
//                        $this->mregister->update_signature_code($param, $user_data);
//                        $return_data = array(
//                            'status'    => true,
//                            'msg'       => 'code has been send successfully',
//                        );
//                        api_return($return_data, 200);
//                    } else {
//                        $return_data = array(
//                            'status' => false,
//                            'error' => $s['error'],
//                        );
//                        api_return($return_data, 200);
//                    }
//                } else {
//                    $return_data = array(
//                        'status' => false,
//                        'error' => $allow['msg'],
//                    );
//                    api_return($return_data, 200);
//                }
//            } else {
//                error_response('N');
//            }
//        }
//    }
}
