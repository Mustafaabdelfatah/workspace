<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;

class ModelOtp extends BaseModel
{
    use HasFactory;

    /**
     * Define guarded attributes.
     */
    protected $guarded = ['id'];

    /**
     * Define database connection.
     */
    protected $connection;

    /**
     * Define table.
     */
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'model_otps';
    }

    public function otpable()
    {
        return $this->morphTo();
    }
}
