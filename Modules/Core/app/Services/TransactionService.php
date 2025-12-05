<?php declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Services\OtpService;
use Modules\Law\Models\Attachment;

class TransactionService
{
    public OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * set transaction users
     *
     * @param Model $transaction
     *
     * @return void
     */
    public function setTransactionUsers(Model $transaction, mixed $args): void
    {
     
        $markers = ($args['need_mark']) ? collect($args['marker_ids'] ?? [])->map(function ($marker) {
            return [
                'user_id' => $marker,
            ];
        })->toArray() : [];
        $reviewers = ($args['need_review']) ? collect($args['reviewer_ids'] ?? [])->map(function ($reviewer) {
            return [
                'user_id' => $reviewer,
            ];
        })->toArray() : [];
        $stampUsers = ($args['need_stamp']) ? collect($args['stamp_users'] ?? [])->map(function ($stampUser) {
            return [
                'user_id' => $stampUser['user_id'],
                'title' => $stampUser['title'],
            ];
        })->toArray() : [];

        
        $transaction->markers()->delete();
        if (count($markers)) {
            $transaction->markers()->createMany($markers);
        }

        $transaction->reviewers()->delete();
        if (count($reviewers)) {
   
            $transaction->reviewers()->createMany($reviewers);
        }

        $transaction->stampUsers()->delete();

        if (count($stampUsers)) {
            $transaction->stampUsers()->createMany($stampUsers);
        }
    }

    public function setTransactionAttachments(Model $transaction, mixed $args): void
    {
        $attachments = collect($args['attachments'] ?? [])->filter(fn($item) => !empty($item['attachment']))->map(function ($attachment) {
            $attachmentFile = $attachment['attachment'];
            $fileName = $attachmentFile->getClientOriginalName();

            $filePath = Storage::putFile('transaction/attachments', $attachmentFile);
            $attachmentRow = Attachment::create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $attachmentFile->getSize(),
            ]);

            return [
                'title' => $attachment['title'],
                'attachment_id' => $attachmentRow->id,
            ];
        })->toArray();

        $transaction->attachments()->delete();
        $transaction->attachments()->createMany($attachments);
    }

    /**
     * mark as viewed signature and stamp actions
     *
     * @param Model $transaction
     *
     * @return void
     */
    public function markAsViewedSignatureAndStampActions(Model $transaction): void
    {
        $loggedUser = auth()->user();

        $loggedUserReview = $transaction->reviewers()->where('user_id', $loggedUser->id)->first();
        $loggedUserMarker = $transaction->markers()->where('user_id', $loggedUser->id)->first();
        $loggedUserStamp = $transaction->stampUsers()->where('user_id', $loggedUser->id)->first();

        if ($loggedUserReview && is_null($loggedUserReview->viewed_at)) {
            $loggedUserReview->update([
                'viewed_at' => now(),
            ]);
        }
        if ($loggedUserMarker && is_null($loggedUserMarker->viewed_at)) {
            $loggedUserMarker->update([
                'viewed_at' => now(),
            ]);
        }
        if ($loggedUserStamp && is_null($loggedUserStamp->viewed_at)) {
            $loggedUserStamp->update([
                'viewed_at' => now(),
            ]);
        }
    }

    /**
     * send transaction otp
     *
     * @param Model $transaction
     *
     * @return mixed
     */
    public function sendTransactionOtp(Model $transaction, String $bearer, array $actions)
    {
        if (is_null($transaction) || count($transaction->signature_and_stamp_actions) == 0) {
            return [
                'status' => false,
                'message' => __('core::messages.signature_and_stamp_actions_not_found'),
            ];
        }
        $user = auth()->user();

        $otpModelLines = $this->collectTransactionActionsModels($transaction, $actions);
        $otp = $this->otpService->initiateOtp($user, 'verify', $bearer, $otpModelLines);
        return [
            'status' => ($otp) ? true : false,
            'message' => ($otp) ? __('core::messages.otp_sent', ['bearer' => __('core::messages.otp_bearers.' . $bearer)]) : __('core::messages.otp_wait_expired_at'),
            'otp_length' => config('core.otp_length'),
            'otp_expired_seconds' => config('core.otp_expire_seconds'),
        ];
    }

    /**
     * collect transaction actions models
     *
     * @param Model $transaction
     *
     * @return mixed
     */
    public function collectTransactionActionsModels($transaction, $actions)
    {
        $otpLines = [];
        $loggedUser = auth()->user();

        $loggedUserReview = $transaction->reviewers()->where('completed_at', null)->where('user_id', $loggedUser->id)->first();
        $loggedUserMarker = $transaction->markers()->where('completed_at', null)->where('user_id', $loggedUser->id)->first();
        $loggedUserStamp = $transaction->stampUsers()->where('completed_at', null)->where('user_id', $loggedUser->id)->first();
        foreach ($actions ?? [] as $action) {
            if ($action == 'need_review' && $loggedUserReview) {
                $otpLines[] = [
                    'otpable_type' => get_class($loggedUserReview),
                    'otpable_id' => $loggedUserReview->id,
                ];
            }
            if ($action == 'need_mark' && $loggedUserMarker) {
                $otpLines[] = [
                    'otpable_type' => get_class($loggedUserMarker),
                    'otpable_id' => $loggedUserMarker->id,
                ];
            }
            if ($action == 'need_stamp' && $loggedUserStamp) {
                $otpLines[] = [
                    'otpable_type' => get_class($loggedUserStamp),
                    'otpable_id' => $loggedUserStamp->id,
                ];
            }
        }
        return $otpLines;
    }

    /**
     * verify transaction otp
     *
     * @param Model $transaction
     *
     * @return mixed
     */
    public function verifyTransactionOtp(Model $transaction, String $otpCode)
    {
        $user = auth()->user();
        $otpRow = $this->otpService->getOtp($user, 'verify', $otpCode);

        if (!$otpRow) {
            return [
                'status' => false,
                'message' => __('core::messages.otp_not_found'),
            ];
        }

        if (is_null($transaction) || count($transaction->signature_and_stamp_actions) == 0) {
            return [
                'status' => false,
                'message' => __('core::messages.signature_and_stamp_actions_not_found'),
            ];
        }

        $modelOtpables = $otpRow->modelOtp()->whereHas('otpable', function ($query) use ($transaction) {
            $query->where('quotation_id', $transaction->id);
        });
        if ($modelOtpables->count() == 0) {
            return [
                'status' => false,
                'message' => __('core::messages.signature_and_stamp_actions_not_found'),
            ];
        } else {
            foreach ($modelOtpables->get() as $modelOtpable) {
                $modelOtpable->otpable->update([
                    'completed_at' => now(),
                ]);
            }
            $otpRow->update([
                'status' => 1,
            ]);
            return [
                'status' => true,
                'message' => __('core::messages.signature_and_stamp_actions_completed'),
            ];
        }
    }
}
