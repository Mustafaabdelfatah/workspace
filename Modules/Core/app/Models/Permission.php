<?php

namespace Modules\Core\Models;


use Illuminate\Database\Eloquent\Model;


class Permission extends Model
{

    /**
     * Define default database connection.
     */
    protected $connection;
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'permissions';
    }

}
