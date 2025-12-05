<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class Translation extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = ['module', 'phrase', 'key', 'writer_id', 'editor_id'];
    public $translatable = ['phrase'];

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
            $model->writer_id = Auth::id();
        });

        static::updating(function ($model) {
            $model->editor_id = Auth::id();
        });

        static::created(function ($translation) {
            self::exportTranslations();
        });
    }

    public static function exportTranslations()
    {
        $translations = self::all();
        $phrases = [
            'en' => [],
            'ar' => [],
        ];
        foreach ($translations as $translation) {
             // Use the translate method provided by Spatie
            $phrases['en'][$translation->key] = $translation->getTranslation('phrase','en');
            $phrases['ar'][$translation->key] = $translation->getTranslation('phrase','ar');
        }


        Storage::disk('local')->put('lang/en.json', json_encode($phrases['en'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        Storage::disk('local')->put('lang/ar.json', json_encode($phrases['ar'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return true ;
    }



    // Call this after bulk insert/upsert operations
    public static function bulkSaveTranslations($data)
    {
        self::upsert($data, ['key'], ['phrase', 'module']); // example using 'key' as unique
        self::exportTranslations();
    }



    // protected static function newFactory(): TranslationFactory
    // {
    //     // return TranslationFactory::new();
    // }
}
