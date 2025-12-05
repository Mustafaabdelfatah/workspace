<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;
use Modules\Core\Models\Module;

class Group extends Model
{
    use HasFactory, HasRoles, HasTranslations;
    protected $fillable = ['name', 'module_id','group_key'];
    public $translatable = ['name'];

    protected $connection;
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = config('core.database_connection');
        $this->table = config('database.connections.' . $this->connection . '.database') . '.' . 'groups';
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'group_has_permissions');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_groups');
    }

    public function syncPermissions(array $permissions)
    {
        $permissionIds = Permission::whereIn('name', $permissions)->pluck('id')->toArray();
        $this->permissions()->sync($permissionIds);
    }

    public function getAttribute($key)
    {
        // Check if the attribute is translatable
        if (in_array($key, $this->translatable)) {
            // Retrieve the current language from the request header
            $locale = request()->header('Accept-Language');

            // If no language is specified, fallback to the default locale
            if (!$locale) {
                $locale = app()->getLocale();
            }

            // Retrieve the translated value of the attribute based on the current language
            return $this->getTranslation($key, $locale);
        }

        // If the attribute is not translatable, return the default behavior
        return parent::getAttribute($key);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
