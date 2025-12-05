<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationItem extends Model
{
    protected $connection;
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'invitation_items';
    }

    protected $guarded = ['id'];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

   
}

