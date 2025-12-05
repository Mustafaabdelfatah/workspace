<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;

class Otp extends BaseModel
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
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'otps';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function modelOtp()
    {
        return $this->hasMany(ModelOtp::class, 'otp_id');
    }
}
