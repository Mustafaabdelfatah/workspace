<?php

namespace Modules\ProjectManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\ProjectManagement\Database\Factories\TimeEntryFactory;

class TimeEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): TimeEntryFactory
    // {
    //     // return TimeEntryFactory::new();
    // }
}
