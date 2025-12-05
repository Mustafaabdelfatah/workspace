<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\HasApiTokens;
use Modules\Chat\Models\ChattingGroup;
use Modules\Chat\Models\Message;
use Modules\Core\Models\Group;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;
use Storage;

use Illuminate\Broadcasting\PrivateChannel;

class User extends Authenticatable
{
    use HasFactory, HasTranslations, SoftDeletes, HasApiTokens, HasRoles, Notifiable;

    /**
     * Define default database connection.
     */
    protected $connection;

    protected $humanResourcesConnection;

    protected $humanResourcesDatabase;

    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'users';

        $this->humanResourcesConnection = config('law.database_connection');
        $this->humanResourcesDatabase = config('database.connections.' . $this->humanResourcesConnection . '.database');
    }

    /**
     * Define guarded attributes.
     */
    protected $guarded = ['id'];

    protected $casts = ['password' => 'hashed'];

 

    /**
     * Define hidden attributes.
     */
    protected $hidden = ['password'];

    /**
     * Define translatable attributes.
     */
    protected $translatable = ['name'];


    // Method to retrieve localized attribute based on lang_key header
    protected function getLocalizedAttribute($value, $attribute)
    {
        $langKey = Request::header('lang_key', 'en');  // Default to 'en' if header is not present
        $decodedValue = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedValue)) {
            return $decodedValue[$langKey] ?? $decodedValue['en'] ?? '';
        }

        return $value;  // Return the original value if decoding fails
    }

    // Method to get full name in localized format
    public function getLocalizedName($langKey = 'en')
    {
        $firstName = $this->getLocalizedAttribute($this->attributes['first_name'], 'first_name');
        $lastName = $this->getLocalizedAttribute($this->attributes['last_name'], 'last_name');

        return "{$firstName} {$lastName} ({$this->id})";
    }

    protected function getFullNameAttribute()
    {
       
        return $this->name;
    }



    public function getPhotoPathAttribute($value)
    {
        return (!empty($value)) ? Storage::url($value) : asset('assets/profile.jpg');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'model_has_groups', 'model_id', 'group_id')->withPivot('writer_id', 'created_at')->using(GroupUser::class);
    }

    public function inGroups(array $groups): bool
    {
        return $this->groups()->whereIn('name', $groups)->exists() || $this->is_admin;
    }

    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public static function getUsersMobile($usersID = 0)
    {
        $query = DB::table('users')
            ->select('mobile')
            ->whereNotNull('mobile');

        if (!empty($usersID) && is_array($usersID)) {
            $query->whereIn('id', $usersID);
        } else {
            $query->where('id', $usersID);
        }

        $results = $query->get();

        if ($results->isEmpty()) {
            return null;
        }

        if (!empty($usersID) && is_array($usersID)) {
            return $results->pluck('mobile')->filter(function ($phone) {
                return strlen(trim($phone)) > 0;
            })->all();
        } else {
            return $results->first()->user_phone;
        }
    }

    public function getFullName($language = 'en')
    {
        $fullName = [
            'first_name' => $this->getTranslation('first_name', $language),
            'second_name' => $this->getTranslation('second_name', $language),
            'third_name' => $this->getTranslation('third_name', $language),
            'last_name' => $this->getTranslation('last_name', $language),
        ];

        return implode(' ', array_filter($fullName));
    }

    public static function getUsersEmail(array $usersID = [])
    {
        $emails = self::whereIn('id', $usersID)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter(function ($email) {
                return strlen(trim($email)) > 0;
            })
            ->toArray();

        return !empty($emails) ? $emails : null;
    }

    public function hasPermission($permission, $groupKey = null, $branchId = null)
    {
        return ($this
            ->groups()
            ->when($groupKey, function ($q) use ($groupKey) {
                $q->where('group_key', $groupKey);
            })
            ->whereRelation('permissions', 'name', $permission)
            ->exists() && ($branchId ? $this->branches()->where('branches.id', $branchId)->exists() : true)) || $this->is_admin;
    }

  

    public function notifications(): BelongsToMany
    {
        return $this
            ->belongsToMany(Notification::class, 'notification_user')
            ->withPivot(['read_at', 'delivered_at'])
            ->withTimestamps();
    }

    public function sentNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    

   

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function routeNotificationForFcm($devices = [])
    {
        return $this->fcmTokens->when(!empty($devices), function ($query) use ($devices) {
            return $query->whereIn('agent', $devices);
        })->toArray();
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'user.' . $this->id;
    }

    public function accessGrants()
    {
        return $this->hasMany(AccessGrant::class,'user_id','id');
    }

    public function userGroups()
    {
        return $this->belongsToMany(UserGroup::class, 'user_group_user', 'user_id', 'user_group_id');
    }


    public function defaultWorkspace()
    {
        return $this->belongsTo(Workspace::class, 'default_workspace_id');
    }
}
