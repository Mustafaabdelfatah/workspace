<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Modules\Law\Models\Bill;

class Workspace extends Model
{
    use HasTranslations, SoftDeletes;

    protected $connection;
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'workspaces';
    }

    public $translatable = ['name'];

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        if(auth()->check() && !auth()->user()->is_admin) {
            static::addGlobalScope('owned', function ($q) {
                $q->where('owner_id', auth()->id())
                ->orwhereHas('accessGrants');
            });
        }

        static::creating(function ($model) {
            $model->writer_id = auth()->id();
        });

        static::updating(function ($model) {
            $model->editor_id = auth()->id();
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function accessGrants()
    {
        return $this->hasMany(AccessGrant::class)->where(function ($query) {
            $query->where('user_id', auth()->id())
            ->orWhereHas('userGroup.users', function ($query) {
                $query->where('id', auth()->id());
            });
        });
    }

    public function getIsOwnerAttribute()
    {
        return $this->owner_id == auth()->id();
    }

   public function bills()
   {
       return $this->hasMany(Bill::class, 'workspace_id');
   }

}

