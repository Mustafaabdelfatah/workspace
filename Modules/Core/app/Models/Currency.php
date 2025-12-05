<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\User;
use Spatie\Translatable\HasTranslations;

class Currency extends Model
{
    use HasFactory , SoftDeletes , HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $connection = 'core';
    protected $table = 'currencies';

    protected $fillable = ['name','symbol','short_form','writer_id','editor_id'];
    public $translatable = ['name','short_form'];

    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->writer_id = Auth::id(); // Set the writer_id on create
        });

        static::updating(function ($model) {
            $model->editor_id = Auth::id(); // Set the editor_id on update
        });
    }

  
}
