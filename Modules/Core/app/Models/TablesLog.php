<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;


class TablesLog extends Model
{
    use HasFactory, HasTranslations;

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
    protected $connection   = 'core';

    public function loggable()
    {
        return $this->morphTo();
    }

    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }
}
