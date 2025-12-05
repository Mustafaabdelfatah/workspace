<?php

namespace Modules\Core\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;

class FileVisibility extends BaseModel
{
    use HasFactory, HasTranslations;

    /**
     * Define guarded attributes.
     */
    protected $guarded = ['id'];

    /**
     * Define translatable attributes.
     */
    protected $translatable = ['title'];

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
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'file_visibilities';
    }

    /**
     * Define title scope
     */
    public function scopeLikeTitle($query, $args)
    {
        return (isset($args['title']) && $args['title'] != '') ? $query->where('title', 'like', '%' . $args['title'] . '%') : $query;
    }
}
