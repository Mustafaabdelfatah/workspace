<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Database\Factories\AttachmentsUploadHistoryFactory;

class AttachmentsUploadHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'upload_type',
        'upload_extra_key1',
        'upload_extra_value1',
        'upload_extra_key2',
        'upload_extra_value2',
        'upload_extra_key3',
        'upload_extra_value3',
        'attachments_url',
        'attachments_type',
        'attachments_size',
        'writer_id',
        'editor_id',
        ];


    public function writer()
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function getAttachmentsUrlAttribute()
    {
        // Check if the file exists on the storage
        if (Storage::disk('local')->exists($this->attributes['attachments_url'])) {
            // Get the file path and contents
            $filePath = $this->attributes['attachments_url'];
            $fileContents = Storage::disk('local')->get($filePath);

            // Get the file type and MIME type
            $fileType = pathinfo($filePath, PATHINFO_EXTENSION); // File extension
            $mimeType = Storage::disk('local')->mimeType($filePath); // MIME type
            $fileName = basename($filePath); // File name

            // Return the file data in the format expected by FileDataType
            return [
                'file_type' => $fileType,
                'content' => base64_encode($fileContents), // Base64 encoded content
                'file_name' => $fileName,
                'mime_type' => $mimeType,
            ];
        }

        return null; // Return null if the file doesn't exist
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
    protected static function newFactory(): AttachmentsUploadHistoryFactory
    {
        //return AttachmentsUploadHistoryFactory::new();
    }
}
