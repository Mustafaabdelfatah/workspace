<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * Define guarded attributes.
     */
    protected $guarded = ['id'];

    /**
     * Define translatable attributes.
     */
    protected $translatable = ['name'];

    /**
     * Define database connection.
     */
    protected $connection;

    /**
     * Define table.
     */
    protected $table;


    public function __construct() {
        parent::__construct();
        $this->connection = config("core.database_connection");
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'countries';
    }

    /**
     * Define name scope
     */
    public function scopeLikeName($query,$args){
        return (isset($args['name']) && $args['name'] != '') ? $query->where('name','like','%'.$args['name'].'%') : $query;
    }
}
