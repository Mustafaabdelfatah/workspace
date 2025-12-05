<?php
namespace Modules\Core\Models;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\Core\Database\Factories\ModuleFactory;

class Module extends Model
{

    protected $connection;
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'modules';
    }

    use HasFactory, HasTranslations;

    /**
     * Define guarded attributes.
     */
    protected $guarded = ['id'];

    /**
     * Define translatable attributes.
     */
    protected $translatable = ['module_name'];


    // protected static function newFactory(): ModuleFactory
    // {
    //     // return ModuleFactory::new();
    // }

     public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

}
