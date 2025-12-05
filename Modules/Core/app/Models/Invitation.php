<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $connection;
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'invitations';
    }

    protected $guarded = ['id'];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->writer_id = auth()->id();
        });
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function items()
    {
        return $this->hasMany(InvitationItem::class, 'invitation_id');
    }
}

