<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class AccessGrant extends Model
{
    protected $connection;
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'access_grants';
    }

    protected $guarded = ['id'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function scope()
    {
        return $this->morphTo('scope', 'scope_type', 'scope_id');
    }

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }
}

