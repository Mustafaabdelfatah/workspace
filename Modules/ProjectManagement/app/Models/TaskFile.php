<?php

namespace Modules\ProjectManagement\App\Models;

use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskFile extends Model
{
    protected $fillable = [
        'task_id',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'file_type',
        'uploaded_by',
        'metadata',
        'description'
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getIsDocumentAttribute(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('api.tasks.files.download', $this->id);
    }
}
