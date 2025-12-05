<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class UserGroup extends Model
{
    use HasTranslations;
    protected $connection;
    protected $table;
    protected $database;

    public $translatable = ['name', 'description'];

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->database = config('database.connections.' . $this->connection . '.database');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'user_groups';
    }

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->writer_id = auth()->id();
        });
        static::updating(function ($model) {
            $model->editor_id = auth()->id();
        });
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class,   $this->database . '.user_group_user', 'user_group_id', 'user_id');
    }
}

