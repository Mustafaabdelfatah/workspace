<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasPermissions;

class Group extends Model
{
    use HasPermissions;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_groups');
    }
}
