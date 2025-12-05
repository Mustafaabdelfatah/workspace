<?php

namespace Modules\Core\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Crypt;

// use Modules\Core\Database\Factories\NotificationFactory;


class Notification extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = [
        'title',
        'body',
        'image_url',
        'link_url',
        'sender_id',
        'is_global',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
        'is_global' => 'boolean',
    ];

    protected $translatable = ['title', 'body'];


    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withPivot(['read_at', 'delivered_at'])
            ->withTimestamps();
    }

    public function recipients(): BelongsToMany
    {
        return $this->users();
    }

    // Check if notification is read by specific user
    public function isReadBy(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->whereNotNull('notification_user.read_at')
            ->exists();
    }

    // Mark as read by specific user
    public function markAsReadBy(User $user): void
    {
        $this->users()->updateExistingPivot($user->id, [
            'read_at' => now()
        ]);
    }

    // Get action type based on link_url content
    public function getActionType(): string
    {
        if (empty($this->link_url)) {
            return 'none';
        }

        if (filter_var($this->link_url, FILTER_VALIDATE_URL)) {
            return 'url';
        }

        if (str_starts_with($this->link_url, 'file://') ||
            str_contains($this->link_url, 'storage/') ||
            pathinfo($this->link_url, PATHINFO_EXTENSION)) {
            return 'file';
        }

        return 'route';
    }

    public function getProcessedLink(): ?string
    {
        if (empty($this->link_url)) {
            return null;
        }

        $actionType = $this->getActionType();

        switch ($actionType) {
            case 'file':
                return str_starts_with($this->link_url, 'http')
                    ? $this->link_url
                    : asset($this->link_url);
            case 'url':
                return $this->link_url;
            default:
                return null;
        }
    }


    public function getProcessedImage(): ?string
    {
        if (empty($this->image_url)) {
            return null;
        }

                return str_starts_with($this->image_url, 'http')
                    ? $this->image_url
                    : asset($this->image_url);
        
    }

}

