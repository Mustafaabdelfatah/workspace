<?php declare(strict_types=1);

namespace Modules\Core\Services;

use Carbon\Carbon;
use Modules\Core\Models\User;

class OtpService
{
    /**
     * initiate otp for verification purposes
     *
     * @param User $user
     * @param string $actionType
     *
     * @return mixed
     */
    public function initiateOtp(User $user, string $actionType, string $bearer = 'mobile', array $otpModelLines = []): mixed
    {
        if ($user->otps()->where('action_type', $actionType)->where('status', 0)->where('expired_at', '>', Carbon::now())->orderBy('id', 'desc')->first()) {
            return null;
        }

        $min = pow(10, config('core.otp_length') - 1);
        $max = pow(10, config('core.otp_length') - 1) * 2;
        $otpCode = rand($min, $max);

        if (app()->isLocal()) {
            $otpCode = implode('', range(1, config('core.otp_length')));
        }

        $otpRow = $user->otps()->create([
            'code' => $otpCode,
            'action_type' => $actionType,
            'expired_at' => Carbon::now()->addSeconds(config('core.otp_expire_seconds')),
            'bearer' => $bearer
        ]);

        if (count($otpModelLines)) {
            $otpRow->modelOtp()->createMany($otpModelLines);
        }

        return $otpCode;
    }

    /**
     * get otp for verification purposes
     *
     * @param User $user
     * @param string $actionType
     *
     * @return mixed
     */
    public function getOtp(User $user, string $actionType, $otpCode): mixed
    {
        return $user->otps()->where('action_type', $actionType)->where('status', 0)->where('expired_at', '>', Carbon::now())->where('code', $otpCode)->orderBy('id', 'desc')->first();
    }
}
