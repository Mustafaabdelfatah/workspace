<?php
namespace App\SOLID\Traits;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\RawMessageFromArray;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;
class Sender {
//	private $CI;
//	public $codes = [
//		"1" => "Success",
//		"M0000" => "Success",
//		"M0001" => "Variables missing",
//		"M0002" => "Invalid login info",
//		"M0022" => "Exceed number of senders allowed",
//		"M0023" => "Sender Name is active or under activation or refused",
//		"M0024" => "Sender Name should be in English or number",
//		"M0025" => "Invalid Sender Name Length",
//		"M0026" => "Sender Name is already activated or not found",
//		"M0027" => "Activation Code is not Correct",
//		"M0029" => "Invalid Sender Name : Sender Name should contain only letters, numbers and the maximum length should be 11 characters",
//		"M0030" => "Sender Name should ended with AD",
//		"M0031" => "Maximum allowed size of uploaded file is 5 MB",
//		"M0032" => "Only pdf,png,jpg and jpeg files are allowed!",
//		"M0033" => "Sender Type should be normal or whitelist only",
//		"M0034" => "Please Use POST Method",
//		"M0036" => "There is no any sender",
//		"1010" => "Variables missing",
//		"1020" => "Invalid login info",
//		"1050" => "MSG body is empty",
//		"1060" => "Balance is not enough",
//		"1061" => "MSG duplicated",
//		"1064" => "Free OTP , Invalid MSG content you should use 'Pin Code is: xxxx' or 'Verification Code: xxxx' or 'رمز التحقق: 1234' , or upgrade your account and activate your sender to send any content",
//		"1110" => "Sender name is missing or incorrect",
//		"1120" => "Mobile numbers is not correct",
//		"1140" => "MSG length is too long"
//	];
//	//send firebase
//	function send_notify($options = array()) {
//		if (!empty((bool)$_SERVER['fEnabled'])) {
//			$this->CI =& get_instance();
//			$options['title'] 			= (!empty($options['title'])) ? $options['title'] : '';
//			$options['body'] 			= (!empty($options['body'])) ? $options['body'] : '';
//			$options['ar']['title'] 	= (!empty($options['ar']['title'])) ? $options['ar']['title'] : $options['title'];
//			$options['ar']['body'] 		= (!empty($options['ar']['body'])) ? $options['ar']['body'] : $options['body'];
//			$options['en']['title'] 	= (!empty($options['en']['title'])) ? $options['en']['title'] : $options['title'];
//			$options['en']['body'] 		= (!empty($options['en']['body'])) ? $options['en']['body'] : $options['body'];
//			$options['icon'] 			= (!empty($options['icon'])) ? $options['icon'] : '';
//			$options['image'] 			= (!empty($options['image'])) ? $options['image'] : '';
//			$options['data'] 			= (!empty($options['data'])) ? $options['data'] : false;
//			$options['UsersIDArray'] 	= (!empty($options['UsersIDArray'])) ? $options['UsersIDArray'] : false;
//			$this->CI->load->model('../modules/users/models/musers');
//			$UsersData = $this->CI->musers->get_notify_registrationIds($options['UsersIDArray']);
//			if (!empty($UsersData) and $UsersData != false) {
//				$datetime 	= time();
//				$myDate 	= mnabr_date_conversion('T', $datetime, '/', 'en');
//				$records 	= array();
//				$records['noty_messages'] = array(
//					'noty_messages_id' => null,
//					'noty_messages_ar_title' => $options['ar']['title'],
//					'noty_messages_ar_body'  => $options['ar']['body'],
//					'noty_messages_en_title' => $options['en']['title'],
//					'noty_messages_en_body'  => $options['en']['body'],
//					'noty_messages_image'    => $options['image'],
//					'noty_messages_date_time' => $myDate['TE']['TD'],
//					'noty_messages_timestamp' => $myDate['T'],
//				);
//				$options['image'] = base_url($options['image']);
//				$noty_messages_id = $this->CI->musers->insert_noty_messages($records['noty_messages']);
//				if (!empty($options['data']) and is_array($options['data'])) {
//					foreach ($options['data'] as $key => $val) {
//						$records['noty_data'][] = array(
//						'noty_data_id' 		=> null,
//						'noty_messages_id' 	=> $noty_messages_id,
//						'noty_data_key' 	=> $key,
//						'noty_data_val' 	=> $val
//						);
//					}
//					$this->CI->musers->insert_noty_data($records['noty_data']);
//				}
//				$registrationIds = array();
//				foreach ($UsersData as $key => $val) {
//					$records['noty_users'][$val['user_id']] = array(
//						'noty_users_id' 	=> null,
//						'noty_messages_id' 	=> $noty_messages_id,
//						'user_id' 			=> $val['user_id']
//					);
//					if(!empty($val['noti_registrationId']) or $val['noti_registrationId'] != '') {
//						array_push($registrationIds, $val['noti_registrationId']);
//					}
//				}
//				$this->CI->musers->insert_noty_users($records['noty_users']);
//				if (!empty($registrationIds)) {
//					$fbaseData = json_encode([
//						"type" 							=> $_SERVER['type'],
//						"project_id" 					=> $_SERVER['project_id'],
//						"private_key_id" 				=> $_SERVER['private_key_id'],
//						"private_key" 					=> $_SERVER['private_key'],
//						"client_email" 					=> $_SERVER['client_email'],
//						"client_id" 					=> $_SERVER['client_id'],
//						"auth_uri" 						=> $_SERVER['auth_uri'],
//						"token_uri" 					=> $_SERVER['token_uri'],
//						"auth_provider_x509_cert_url" 	=> $_SERVER['auth_provider_x509_cert_url'],
//						"client_x509_cert_url" 			=> $_SERVER['client_x509_cert_url'],
//						"universe_domain"				=> $_SERVER['universe_domain'] ?? "googleapis.com",
//					]);
//					$factory = (new Factory)->withServiceAccount($fbaseData);
//					$cloudMessaging = $factory->createMessaging();
//					$notify_data = array();
//					$notify_data['notification'] = array(
//						// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#notification
//						'title' => $options['title'],
//						'body' => $options['body'],
//						'image' => $options['image'],
//					);
//					if (!empty($options['data']) and is_array($options['data'])) {
//						$notify_data['data'] = $options['data'];
//					}
//					$notify_data['android'] = array(
//						// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
//						'ttl' => '3600s',
//						'priority' => 'normal',
//						'notification' => array(
//							'title' => $options['title'],
//							'body' => $options['body'],
//							'icon' => $options['icon'],
//							'color' => '#f45342',
//							'sound' => 'default'
//						)
//					);
//					$notify_data['apns'] = array(
//						// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
//						'headers' => array(
//							'apns-priority' => '10',
//						),
//						'payload' => array(
//							'aps' => array(
//								'alert' => array(
//									'title' => $options['title'],
//									'body' => $options['body'],
//								),
//								'badge' => 42,
//								'sound' => 'notification3.wav',
//							),
//						)
//					);
//					$notify_data['webpush'] = array(
//						// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#webpushconfig
//						'headers' => array(
//							'Urgency' => 'normal',
//						),
//						'notification' => array(
//							'title' => $options['title'],
//							'body' => $options['body'],
//							'icon' => $options['icon']
//						)
//					);
//					$result = $cloudMessaging->validateRegistrationTokens($registrationIds);
//					foreach ($UsersData as $key => $val) {
//						if (!empty($result['valid']) and in_array($val['noti_registrationId'], $result['valid'])) {
//							$notify_data['token'] = $val['noti_registrationId'];
//							if (!empty($val['lang_key']) and $val['lang_key'] == 'ar') {
//								$options['title'] = $options['ar']['title'];
//								$options['body'] = $options['ar']['body'];
//							} else {
//								$options['title'] = $options['en']['title'];
//								$options['body'] = $options['en']['body'];
//							}
//							$notify_data['notification']['title'] = $options['title'];
//							$notify_data['notification']['body'] = $options['body'];
//							$notify_data['android']['notification']['title'] = $options['title'];
//							$notify_data['android']['notification']['body'] = $options['body'];
//							$notify_data['apns']['payload']['aps']['alert']['title'] = $options['title'];
//							$notify_data['apns']['payload']['aps']['alert']['body'] = $options['body'];
//							$notify_data['apns']['payload']['aps']['badge'] = (int)$val['badge'] + 1;
//							$notify_data['webpush']['notification']['title'] = $options['title'];
//							$notify_data['webpush']['notification']['body'] = $options['body'];
//							$message = new RawMessageFromArray($notify_data);
//							$report = $cloudMessaging->send($message);
//							if (!empty($report['name'])) {
//								$records['noty_status'][] = array(
//									'noty_status_id' => null,
//									'noty_messages_id' => $noty_messages_id,
//									'user_id' => $val['user_id'],
//									'user_token_plat_forms' => $val['user_token_plat_forms'],
//									'noty_status_code' => '200',
//									'noty_status_result' => "done",
//								);
//							}
//							} elseif (!empty($result['unknown']) and in_array($val['noti_registrationId'], $result['unknown'])) {
//							$records['noty_status'][] = array(
//								'noty_status_id' => null,
//								'noty_messages_id' => $noty_messages_id,
//								'user_id' => $val['user_id'],
//								'user_token_plat_forms' => $val['user_token_plat_forms'],
//								'noty_status_code' => '400',
//								'noty_status_result' => 'token unknown',
//							);
//							} elseif (!empty($result['invalid']) and in_array($val['noti_registrationId'], $result['invalid'])) {
//							$records['noty_status'][] = array(
//							'noty_status_id' => null,
//							'noty_messages_id' => $noty_messages_id,
//							'user_id' => $val['user_id'],
//							'user_token_plat_forms' => $val['user_token_plat_forms'],
//							'noty_status_code' => '400',
//							'noty_status_result' => 'token invalid',
//							);
//							} else {
//							$records['noty_status'][] = array(
//							'noty_status_id' => null,
//							'noty_messages_id' => $noty_messages_id,
//							'user_id' => $val['user_id'],
//							'user_token_plat_forms' => $val['user_token_plat_forms'],
//							'noty_status_code' => '400',
//							'noty_status_result' => 'token is null',
//							);
//						}
//					}
//					if (!empty($records['noty_status'])) {
//						$this->CI->musers->insert_noty_status($records['noty_status']);
//					}
//				}
//			}
//		}
//	}
//	//send mail
//	function send_email($options = array()) {
//		if (empty((bool)$_SERVER['eEnabled'])) {
//			$res['status'] 	= false;
//			$res['error'] 	= 'config send email is Disabled. Error: 100';
//		} else {
//			$options['Subject'] 		= (!empty($options['Subject'])) ? $options['Subject'] : '';
//			$options['Body'] 			= (!empty($options['Body'])) ? $options['Body'] : '';
//			$options['addAddress'] 		= (!empty($options['addAddress'])) ? $options['addAddress'] : false;
//			$options['addReplyTo'] 		= (!empty($options['addReplyTo'])) ? $options['addReplyTo'] : false;
//			$options['addCC'] 			= (!empty($options['addCC'])) ? $options['addCC'] : false;
//			$options['addBCC'] 			= (!empty($options['addBCC'])) ? $options['addBCC'] : false;
//			$options['addAttachment'] 	= (!empty($options['addAttachment'])) ? $options['addAttachment'] : false;
//			$mail 						= new PHPMailer(true);
//			try {
//				$mail->isSMTP();
//				$mail->Host 		= $_SERVER['Host'];
//				$mail->SMTPAuth 	= $_SERVER['SMTPAuth'];
//				$mail->Username 	= $_SERVER['Username'];
//				$mail->Password 	= $_SERVER['Password'];
//				$mail->SMTPSecure 	= PHPMailer::ENCRYPTION_STARTTLS;
//				$mail->Port 		= $_SERVER['Port'];
//				$mail->CharSet 		= 'UTF-8';
//				$mail->setFrom($_SERVER['SenderFrom'], $_SERVER['Name']);
//				if (!empty($options['addAddress']) and is_array($options['addAddress'])) {
//					foreach ($options['addAddress'] as $Email) {
//						$mail->addAddress($Email);
//					}
//				}
//				if (!empty($options['addReplyTo']) and is_array($options['addReplyTo'])) {
//					foreach ($options['addReplyTo'] as $Email) {
//						$mail->addReplyTo($Email);
//					}
//				}
//				if (!empty($options['addCC']) and is_array($options['addCC'])) {
//					foreach ($options['addCC'] as $Email) {
//						$mail->addCC($Email);
//					}
//				}
//				if (!empty($options['addBCC']) and is_array($options['addBCC'])) {
//					foreach ($options['addBCC'] as $Email) {
//						$mail->addBCC($Email);
//					}
//				}
//				if (!empty($options['addAttachment']) and is_array($options['addAttachment'])) {
//					foreach ($options['addAttachment'] as $Attachment) {
//						if (file_exists($Attachment)) {
//							$mail->addAttachment($Attachment);
//						}
//					}
//				}
//				$mail->isHTML(true);
//				$mail->Subject = $options['Subject'];
//				$mail->Body = $options['Body'];
//				$res['status'] = true;
//				if (!$mail->send()) {
//					$res['status'] = false;
//					$res['error'] = $mail->ErrorInfo;
//				} else {
//					$res['status'] = true;
//				}
//			} catch (Exception $e) {
//				$res['status'] = false;
//				$res['error'] = $mail->ErrorInfo;
//			}
//		}
//		if(!empty($res['status'])) {
//			return $res;
//		} else {
//			api_return([
//				'status' => FALSE,
//				'error' => $res['error']
//			], 200);
//		}
//	}
//	//send sms
//	function send_sms($options = array()) {
//		if(!empty((bool)$_SERVER['mEnabled'])) {
//			if(empty($options['Mobiles'])) {
//				api_return([
//					'status' => false,
//					'error' => "you must provide valid mobile numbers and should be 966xxxxxxxxx format"
//				], 200);
//			}
//			$numbers 	= (is_array($options['Mobiles'])) ? implode(',',$options['Mobiles']) : $options['Mobiles'];
//			$postfields = json_encode([
//				"userName" 		=> $_SERVER['User'],
//				"userSender" 	=> $_SERVER['Sender'],
//				"apiKey" 		=> $_SERVER['ApiKey'],
//				"numbers" 		=> $numbers,
//				"msg" 			=> $options['Message']
//			]);
//			$curl = curl_init();
//			curl_setopt_array($curl, array(
//				CURLOPT_URL => $_SERVER['Url'],
//				CURLOPT_RETURNTRANSFER => true,
//				CURLOPT_ENCODING => '',
//				CURLOPT_MAXREDIRS => 10,
//				CURLOPT_TIMEOUT => 0,
//				CURLOPT_FOLLOWLOCATION => true,
//				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//				CURLOPT_CUSTOMREQUEST => 'POST',
//				CURLOPT_POSTFIELDS =>$postfields,
//				CURLOPT_HTTPHEADER => array(
//					'Content-Type: application/json',
//					'Cookie: userCurrency=SAR; SERVERID=MBE1; userLang=Ar'
//				),
//			));
//			$response = curl_exec($curl);
//			curl_close($curl);
//			$res = (array)json_decode($response);
//			if(!empty($res['code']) && ($res['code'] == "1" || $res['code'] == "M0001")) {
//				return [
//					'status' 	=> true,
//					'msg' 		=> 'Success'
//				];
//			} else {
//				api_return([
//					'status' => false,
//					'error' => (!empty($res)) ? $this->codes[$res['code']] : "Error 103"
//				], 200);
//			}
//		}
//		api_return([
//			'status' => false,
//			'error' => "SMS configuration is disabled by admin."
//		], 200);
//	}
//	//send whatsapp msg
//	function send_whatsapp($options = array()) {
//		if (empty((bool)$_SERVER['wEnabled'])) {
//			api_return([
//				"status" => false,
//				'error' => 'config send whatsapp is Disabled. Error: 100'
//			], 200);
//		} else {
//			$this->CI =& get_instance();
//			$options['Message'] = (!empty($options['Message'])) ? $options['Message'] : '';
//			if (!empty($options['Mobile']) and isset($options['Mobile']) and !is_array($options['Mobile'])) {
//				if(!empty($options['OTPFrom']) && $options['OTPFrom'] == "login") {
//					$this->CI->load->model('musers');
//					$status = $this->CI->musers->check_today_count($options['Mobile'], "whatsapp");
//					if(!empty($status)) {
//						api_return([
//							"status" => FALSE,
//							"error" => "We are Sorry. Your Limit Has been exceeded. Pls try with SMS or Email...."
//						], 200);
//					}
//					insert("user_otp_limits", ["mobile_number" => $options['Mobile'], "method" => "whatsapp", "otp_from" => "login"]);
//				}
//				$template_name 	= (!empty($options['template_name'])) ? $options['template_name'] : 'document_signature';
//				$parameters 	= [];
//				if(in_array($template_name, ["login","document_signature"])) { // one parameters
//					$parameters = [
//						[
//							"type" => "text",
//							"text" => $options['msg1']
//						]
//					];
//				} else {
//					$parameters = [
//						[
//							"type" => "text",
//							"text" => $options['msg1']
//						],
//						[
//							"type" => "text",
//							"text" => $options['msg2']
//						],
//					];
//				}
//				$postFields = [
//					"messaging_product" => "whatsapp",
//					"to"                => $options['Mobile'],
//					"type"              => "template",
//					"template"          => [
//						"name"       => $template_name,
//						"language"   => [
//							"code" => (!empty($options['lang_key'])) ? $options['lang_key'] : "ar",
//						],
//						"components" => [
//							[
//								"type" 			=> "body",
//								"parameters" 	=> $parameters
//							],
//							[
//								"type" 			=> "button",
//								"sub_type" 		=> "URL",
//								"index" 		=> 0,
//								"parameters" 	=> $parameters
//							]
//						],
//					],
//				];
//				$headers 		= ['Content-Type:application/json','Authorization:Bearer ' . $_SERVER['waToken']];
//				$wacApiResponse = $this->callCurlRequest('https://graph.facebook.com/v13.0/' . $_SERVER['waPhonenumberID'] . '/messages', 'POST', $postFields, $headers, 100, false, true);
//				$return_data 	= json_decode($wacApiResponse, TRUE);
//				if(!empty($return_data['messages'][0]['id'])) {
//					return [
//						"status" => TRUE,
//						"msg" => $return_data['messages'][0]['id']
//					];
//				} else {
//					api_return([
//						"status" => false,
//						'error' => 'There is Error in facebook API'
//					], 200);
//				}
//			} else {
//				api_return([
//					"status" => false,
//					'error' => 'There is no valid mobile number'
//				], 200);
//			}
//		}
//		return ['status' => FALSE];
//	}
//	private function callCurlRequest($url, $method = 'GET', $postFields = array(), $headers = array(), $timeout = 100, $asynch = false, $postAsBodyParam = false) {
//        try {
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url);
//            if ($method == 'GET') {
//                curl_setopt($ch, CURLOPT_POST, false);
//            }
//            if ($method == 'POST') {
//                curl_setopt($ch, CURLOPT_POST, TRUE);
//                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
//                if (is_array($postFields) && count($postFields)) {
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
//                } else {
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
//                }
//                if ($postAsBodyParam === true) {
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, @json_encode($postFields));
//                } elseif ($postAsBodyParam === 5) {
//                    // $postAsBodyParam = 5 means put as it is
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
//                }
//            }
//            if ($method == 'DELETE') {
//                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
//                if (is_array($postFields) && count($postFields)) {
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
//                }
//            }
//            if ($method == 'PUT') {
//                if (is_array($headers)) {
//                    $headers = array_merge($headers, array('X-HTTP-Method-Override: PUT'));
//                }
//                if (is_array($postFields) && count($postFields)) {
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
//                }
//            }
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            if (is_array($headers) && count($headers)) {
//                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            } else {
//                $headers = array("Content-Type: application/json");
//                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            }
//            // Asynchronous Request
//            if ($asynch === true) {
//                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
//                curl_setopt($ch, CURLOPT_TIMEOUT, 1);
//            } else {
//                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
//            }
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            $response = curl_exec($ch);
//            $result = curl_getinfo($ch);
//            curl_close($ch);
//            if ($asynch || (isset($result['http_code']) && $result['http_code'] == 200)) {
//                return $response;
//            } else {
//                throw new Exception($response, $result['http_code']);
//            }
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//	// send Push Notify
//	public function send_topic($options = array()){
//		$this->CI =& get_instance();
//		// load api config file
//		$this->CI->load->config('api');
//		$url = $this->CI->config->item('aborahaf_api_node_server');
//		if(!empty($options['topic']) and is_string($options['topic']) and !empty($options['UsersIDArray']) and is_array($options['UsersIDArray'])){
//			foreach ($options['UsersIDArray'] as $key => $val) {
//				$users[] = (object) ["user_id"=>$val];
//			}
//			if(!empty($options['data']) and is_array($options['data'])) {
//				$finalData =  [json_decode(json_encode($options['data']), FALSE)];
//			} else{
//				$finalData = null;
//			}
//			$sendData = [
//				"topic"	=> $options['topic'],
//				"users"	=> $users,
//				"data" 	=> $finalData
//			];
//			$ch  = curl_init();
//			curl_setopt($ch, CURLOPT_URL, $url);
//			curl_setopt($ch, CURLOPT_POST, 1);
//			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendData));
//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//			$res = curl_exec($ch);
//			curl_close($ch);
//		}
//	}
}
