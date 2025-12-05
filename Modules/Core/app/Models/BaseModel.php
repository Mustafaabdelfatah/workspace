<?php
namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            $model->log()->create(['writer_id' => auth()->user()->id ?? 1]);
        });
        static::updated(function ($model) {
            $model->log()->create(['editor_id' => auth()->user()->id ?? 1]);
        });
    }

    public function log()
    {
        return $this->morphMany(TablesLog::class, 'loggable');
    }

    /**
     * Interact with the model's last writer.
     *
     * @return Attribute
     */
    protected function getWriterAttribute()
    {
        $latestLog = $this->log()->where('writer_id', '!=', null)->orderBy('id', 'desc')->first();
        return ($latestLog?->writer) ? $latestLog->writer : new User(['fullName' => '-', 'email' => '-', 'mobile' => '-']);
    }

    /**
     * Interact with the model's last editor.
     */
    protected function getEditorAttribute()
    {
        $latestLog = $this->log()->where('editor_id', '!=', null)->orderBy('id', 'desc')->first();
        return ($latestLog?->editor) ? $latestLog->editor : new User(['fullName' => '-', 'email' => '-', 'mobile' => '-']);
    }
}
