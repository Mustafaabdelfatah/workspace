<?php
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 *
 * Upload Files
 *
 */
if (!function_exists('uploadFiles')) {

    function uploadFiles($filesArray)
    {
        $finalData = [];
        $failedFiles = [];

        foreach ($filesArray as $filedata) {
            $path = !empty($filedata['path']) ? $filedata['path'] : "uploads/";
            $filename = !empty($filedata['filename']) ? $filedata['filename'] : null;
            $file = $filedata['file'];

            if ($file->isValid()) {
                $ext = $file->getClientOriginalExtension();
                $filesize = $file->getSize();
                $mimetype = $file->getClientMimeType();
                $filename = empty($filename) ? rand(1000000000, 9999999999) : $filename;
                $fullname = $filename . "." . $ext;
                $fullpath = $path . $fullname;

                // Store the file using Laravel's Storage facade
                Storage::putFileAs($path, $file, $fullname);

                // Verify the file exists in storage
                if (!Storage::exists($fullpath)) {
                    if (!empty($failedFiles)) {
                        foreach ($failedFiles as $failed) {
                            Storage::delete($failed);
                        }
                    }
                    return [];
                }

                // Create an index.html file in the directory
                Storage::put($path . "index.html", "");

                $isImage = isImage($mimetype); // Assume isImage is a helper function

                $fileData = [
                    "file_name" => $filename,
                    "file_extension" => $ext,
                    "fullname" => $fullname,
                    "path" => $path,
                    "fullpath" => $fullpath,
                    "file_size" => $filesize,
                    "file_type" => $mimetype,
                    "is_image" => $isImage,
                    "position" => !empty($filedata['position']) ? $filedata['position'] : null,
                   // "level_keys" => !empty($filedata['levels']) ? explode(',', $filedata['levels']) : ["level_1", "level_2"], // default private
                    "level_keys" => !empty($filedata['levels']) ?$filedata['levels']:  "level_1,level_2" , // default private
                    "thumb_nail_file" => $isImage ? base64ToThumbNail(base64_encode(Storage::get($fullpath)), $path . "thumbnail/", $ext, $filename) : null,
                ];

                // Ensure extra is an array before merging
                if (!empty($filedata['extra']) && is_array($filedata['extra'])) {
                    $fileData = array_merge($fileData, $filedata['extra']);
                }

                $finalData[] = $fileData;

                $failedFiles[] = $fullpath;
            }
        }

        return $finalData;
    }


}
// Function to check if the uploaded file is an image
function isImage($mimeType) {
    return (strpos($mimeType, 'image/') === 0) ? 1 : 0;
}
/**
 *
 * Convert Base64 Data To File
 *
 */
if (!function_exists("base64ToThumbNail")) {
    function base64ToThumbNail($base64Data, $path, $extention, $filename = "", $thumbnailSize = 200) {
        $originalImage = imagecreatefromstring(base64_decode($base64Data));
        $originalWidth = imagesx($originalImage);
        $originalHeight = imagesy($originalImage);
        $thumbnailWidth = $thumbnailSize;
        $thumbnailHeight = intval($originalHeight * ($thumbnailSize / $originalWidth));
        $thumbnailImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
        imagecopyresampled($thumbnailImage, $originalImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $originalWidth, $originalHeight);
        ob_start();
        imagejpeg($thumbnailImage);
        $thumbnailBase64 = base64_encode(ob_get_clean());
        imagedestroy($originalImage);
        imagedestroy($thumbnailImage);
        if (!is_dir($path)) {mkdir($path, 0777, true);}
        fopen($path . "index.html", "w");
        $filename   = (!empty($filename)) ? $filename.".".$extention : mt_rand(10000000, 99999999).".".$extention;
        $filestatus = file_put_contents($path.$filename, base64_decode($thumbnailBase64));
        if(!empty($filestatus)) {
            return $path.$filename;
        }
        return NULL;
    }
}

if (!function_exists("removeFiles")) {
    function removeFiles($files, $name) {
        foreach ($files as $file) {
            $filepath = public_path($file[$name]);
            if (File::exists($filepath)) {
                unlink($filepath);
            }
        }
    }
}

