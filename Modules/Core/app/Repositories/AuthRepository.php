<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Core\Emails\ResetPasswordMail;
use Modules\Core\Models\User;
use DB;
use Hash;
use Modules\Core\Models\Otp;
use Carbon\Carbon;
use Modules\Core\Services\OtpService;
class AuthRepository
{

    public OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function register(array $args)
    {
        DB::beginTransaction();


        if(isset($args['otp']))
        {
            $otp = Otp::where(['email_mobile'=>$args['mobile'],'code' => $args['otp'], 'email_mobile' => $args['mobile'], 'action_type' => 'register', 'status' => 0])->where('expired_at', '>', Carbon::now())->orderBy('id', 'desc')->first();
            if(!$otp)
            {
                return [
                    'status' => false,
                    'message' => __('lang_invalid_otp'),
                ];
            }

            $otp->update(['status' => 1]);
        }
        else{

        if (Otp::where(['email_mobile'=>$args['mobile'], 'email_mobile' => $args['mobile'], 'action_type' => 'register', 'status' => 0])->where('expired_at', '>', Carbon::now())->orderBy('id', 'desc')->first()) {
            return [
                'status' => false,
                'message' => __('lang_cannot_request_otp_again_try_later'),
            ];
        }

            $min = pow(10, config('core.otp_length') - 1);
            $max = pow(10, config('core.otp_length') - 1) * 2;
            $otpCode = rand($min, $max);
            if (app()->isLocal()) {
                $otpCode = implode('', range(1, config('core.otp_length')));
            }
            $otp = Otp::create([
                'email_mobile' => $args['mobile'],
                'bearer'=>'mobile',
                'action_type' => 'register',
                'code' => $otpCode,
                'expired_at' => now()->addSeconds(config('core.otp_expire_seconds')),
            ]);

        return [
            'status' => true,
            'message' => __('lang_need_otp'),
            'need_otp' => true,
            'otp_length' => config('core.otp_length'),
            'otp_expired_seconds' => config('core.otp_expire_seconds'),
        ];

        }

        try {
            $user = User::create([
                'email' => $args['email'],
                'mobile' => $args['mobile'],
                'name' => [
                    'ar' => $args['name'],
                    'en' => $args['name'],
                ]
            ]);
            DB::commit();

            $tokenResult = $user->createToken('API Access');
            $token = $tokenResult->accessToken;
            $tokenResult->token->expires_at = now()->addDay();
            $tokenResult->token->save();
            return [
                'status' => true,
                'message' => __('lang_user_registered_successfully'),
                'token' => $token,
                'expire' => $tokenResult->token->expires_at->diffForHumans(),
                'data' => $user,

            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'User registration failed, please try again.' . $e->getMessage(),
            ];
        }
    }


    /**
     * Login exists user
     *
     * @param array $args
     *
     * @return array
     */
    public function login(array $args): array
    {
        DB::beginTransaction();  // Starting the transaction
        try {
            $loginField = filter_var($args['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
            $loginValue = $args['email'];
            $user = User::where($loginField, $loginValue)->where('status', 1)->first();

            if (!$user) {
                return [
                    'status' => false,
                    'message' => __('lang_user_not_found'),
                ];
            }

            if(empty($args['otp'])){

                $otp = $this->otpService->initiateOtp($user, 'login', 'mobile');

                return [
                    'status' => $otp ? true : false,
                    'message' => $otp ? __('lang_need_otp') : __('lang_cannot_request_otp_again_try_later'),
                    'otp_length' => config('core.otp_length'),
                    'otp_expired_seconds' => config('core.otp_expire_seconds'),
                ];
            }
            else{
                $otpRow = $this->otpService->getOtp($user, 'login', $args['otp']);

                if (!$otpRow) {
                    return [
                        'status' => false,
                        'message' => __('lang_invalid_otp'),
                    ];
                }
                $otpRow->update(['status' => 1]);
            }

            $tokenResult = $user->createToken('API Access');

            $token = $tokenResult->accessToken;
            $tokenResult->token->expires_at = now()->addDay();
            $tokenResult->token->save();

            DB::commit();

            return [
                'status' => true,
                'message' => __('logged_in_successfully'),
                'token' => $token,
                'expire' => $tokenResult->token->expires_at->diffForHumans(),
                'data' => $user,
            ];
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback transaction in case of error

            return [
                'status' => false,
                'message' => 'Login failed, please try again.' . $e->getMessage(),
            ];
        }
    }


    public function requestPasswordReset($args)
    {
        $email = $args['email'];
        $user = User::firstWhere('email', $email);
        if ($user) {
            $token = Str::random(32);

            DB::table('password_resets')->where('email', $email)->delete();
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => now(),
            ]);
            $frontendUrl = env('SITE_CLIENT_URL', '');
            $url = $frontendUrl . "/auth/reset_password/{$token}";
            Mail::to($email)->send(new ResetPasswordMail($url, $user, app()->getLocale()));
        }
        return [
            'status' => true,
            'message' => 'lang_We have sent an email if your address exists in our records'
        ];
    }

    public function checkPasswordResetToken($args)
    {
        $token = $args['token'];

        $token = DB::table('password_resets')->where('token', $token)->first();

        if ($token) {
            return [
                'message' => __('lang_data_found'),
                'status' => true
            ];
        } else {
            return [
                'message' => __('lang_data_not_found'),
                'status' => false
            ];
        }
    }

    public function resetPassword($args)
    {
        $token = DB::table('password_resets')->where('token', $args['token'])->first();

        $user = User::firstWhere('email', $token->email);

        $user->update([
            'password' => $args['password']
        ]);

        DB::table('password_resets')->where('email', $token->email)->delete();

        return [
            'status' => true,
            'message' => __('lang_password_has_been_reset_successfully')
        ];
    }

}
