<?php
if (!function_exists('get_image')) {
    function get_image($path = false, $type = 'Normal')
    {
        if ($path == false and $type == 'Empty') {
            $base64 = false;
        } elseif ($path !== false and file_exists($path) and is_file($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $base64 = false;
        }
        return $base64;
    }
}
if (!function_exists('randomKey')) {
    function randomKey($Length = 6)
    {
        $alphabet = 'abcd58efgh54ijk39lmnopqrst58uvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $Length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}
if (!function_exists('base64url_encode')) {
    function base64url_encode($str)
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }
}
if (!function_exists('base64url_decode')) {
    function base64url_decode($base64url)
    {
        return base64_decode(strtr($base64url, '-_', '+/'), '=');
    }
}
if (!function_exists('create_url')) {
    function create_url($encrypted_string)
    {
        $mykey = randomKey(20);
        return $mykey . '/' . base64url_encode(openssl_encrypt($encrypted_string, "AES-128-ECB", $mykey));
    }
}
if (!function_exists('check_url')) {
    // this function return false or array
    function check_url($mykey, $encrypted_string)
    {
        $data = openssl_decrypt(base64url_decode($encrypted_string), "AES-128-ECB", $mykey);
        if ($data !== false) {
            $data = json_decode($data, true);
            return $data;
        } else {
            return false;
        }
    }
}
if (!function_exists('create_file_url')) {
    function create_file_url($encrypted_string = '')
    {
        $mykey = randomKey(20);
        return $mykey . '/' . base64url_encode(openssl_encrypt($encrypted_string, "AES-128-ECB", $mykey));
    }
}
if (!function_exists('check_file_url')) {
    // this function return false or path for file
    function check_file_url($mykey, $encrypted_string) {
        $data = openssl_decrypt(base64url_decode($encrypted_string), "AES-128-ECB", $mykey);
        if (!empty($data) and file_exists($data) and is_file($data)) {
            return $data;
        } else if (!empty($data) and !file_exists($data)) {
            return $data;
        }
        return false;
    }
}
if (!function_exists('get_file')) {
    function get_file($path)
    {
        $CI = &get_instance();
        if (file_exists($path) and is_file($path)) {

            $file_contents = file_get_contents($path);
            $filename = basename($path);
            $file_extension = strtolower(substr(strrchr($filename, "."), 1));
            $file_size = filesize($path);
            $file_type = get_file_type($file_extension);
            if (!empty($file_type)) {
                $return_data = array(
                    'status' => true,
                    'base64' => base64_encode($file_contents),
                    'extension' => $file_type['extension'],
                    'content-type' => $file_type['content-type'],
                    'size' => FileSizeConvert($file_size),
                );
                api_return($return_data, 200);
            } else {
                $file = tempnam(FCPATH . "tmp", "zip");
                $zip = new ZipArchive();
                $zip->open($file, ZipArchive::OVERWRITE);
                $zip->addFile(realpath($path), 'new.' . $file_extension);
                $zip->close();
                $file_contents = file_get_contents($file);
                $return_data = array(
                    'status' => true,
                    'base64' => base64_encode($file_contents),
                    'extension' => 'zip',
                    'content-type' => 'application/zip',
                    'size' => FileSizeConvert($file_size),
                );
                unlink($file);
                api_return($return_data, 200);
            }
        } else {
            $return_data = array(
                'status' => false,
                'error' => 'File not found on server',
            );
            api_return($return_data, 200);
        }
    }
}
if (!function_exists('get_file_type')) {
    function get_file_type($file_extension = '')
    {
        switch ($file_extension) {
            case "txt":
                $data['extension'] = $file_extension;
                $data['content-type'] = "text/plain";
                break;
            case "gif":
                $data['extension'] = $file_extension;
                $data['content-type'] = "image/gif";
                break;
            case "png":
                $data['extension'] = $file_extension;
                $data['content-type'] = "image/png";
                break;
            case "PNG":
                $data['extension'] = $file_extension;
                $data['content-type'] = "image/png";
                break;
            case "jpeg":
                $data['extension'] = $file_extension;
                $data['content-type'] = "image/jpeg";
            case "jpg":
                $data['extension'] = $file_extension;
                $data['content-type'] = "image/jpg";
                break;
            case "JPG":
                $data['extension'] = $file_extension;
                $data['content-type'] = "image/jpg";
                break;
            case "JPEG":
                $data['extension'] = $file_extension;
                $data['content-type'] = "image/jpg";
                break;
            case "svg":
                $data['extension'] = $file_extension;
                $data['content-type'] = "image/svg+xml";
                break;
            case "pdf":
                $data['extension'] = $file_extension;
                $data['content-type'] = "application/pdf";
                break;
            case "zip":
                $data['extension'] = $file_extension;
                $data['content-type'] = "application/zip";
                break;
            case "xlsx":
                $data['extension'] = $file_extension;
                $data['content-type'] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                break;
            default:
                $data = null;
        }
        return $data;
    }
}
if (!function_exists('FileSizeConvert')) {
    function FileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4),
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3),
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2),
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024,
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1,
            ),
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
}

//if (!function_exists('fix_pdf')) {
//    function fix_pdf($file = null)
//    {
//        // $file is full path like 'files/transactions/FORM_C1/SnNdttt989OL.pdf'
//        if ($file != null) {
//            if (file_exists(FCPATH . $file)) {
//                $final = $file;
//                $path_parts = pathinfo(FCPATH . $file);
//                if (!empty($path_parts['extension']) and ($path_parts['extension'] == 'pdf' or $path_parts['extension'] == 'PDF')) {
//                    $oldFile = $path_parts['dirname'] . '/' . $path_parts['filename'] . '.' . $path_parts['extension'];
//                    $oldRename = $path_parts['dirname'] . '/' . $path_parts['filename'] . '_pdf_fixed.' . $path_parts['extension'];
//                    $newFile = $path_parts['dirname'] . '/' . $path_parts['filename'] . '.' . $path_parts['extension'];
//                    rename($oldFile, $oldRename);
//                    if (file_exists($oldRename)) {
//                        if (PHP_OS_FAMILY === "Windows") {
//                            if (file_exists("C:\Program Files\gs\gs9.54.0\bin\gswin64c.exe")) {
//                                putenv('PATH=C:\Program Files\gs\gs9.54.0\bin');
//                                $newFile = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $newFile);
//                                $oldRename = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $oldRename);
//                                shell_exec('gswin64c -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $newFile . '" "' . $oldRename . '"');
//                            } else {
//                                return array("status" => false, "error" => "Server Missing the ghostscript, Error 104");
//                            }
//                        } elseif (PHP_OS_FAMILY === "Linux") {
//                            if (file_exists("/usr/local/bin/gs")) {
//                                shell_exec('/usr/local/bin/gs -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $newFile . '" "' . $oldRename . '"');
//                            } else {
//                                return array("status" => false, "error" => "Server Missing the ghostscript, Error 105");
//                            }
//                        } else {
//                            return array("status" => false, "error" => "Server Unknown and ghostscript Unknown, Error 106");
//                        }
//
//                    }
//                    if (file_exists($newFile)) {
//                        $file = $final; // الرجوع بالمسار الاصلي
//                        unlink($oldRename); // حذف الملف القديم
//                    } else {
//                        rename($oldRename, $oldFile); // اعادة القديم لاسمه الطبيعي
//                    }
//
//                }
//            }
//        }
//        return $file;
//    }
//}
/**
 *
 * Settings Function
 *
 */
if (!function_exists('app_setting')) {
    function app_setting($module, $key = "") {
        return \Illuminate\Support\Facades\DB::table('settings')
            ->where('settings_key',$key)->where('module_name_key',$module)
            ->select('settings_val')->get();
    }
}
/**
 *
 * Upload File
 *
 */
if (!function_exists('build_file')) {
    function build_file($path, $file, $module, $filename = '', $types = false) {
        if(!empty($file)) {
            if (!is_dir($path)) {mkdir($path, 0777, true);}

            $config["upload_path"]      = 'uploads/'.$path;
            $config["overwrite"]        = true;
            $config["remove_spaces"]    = true;
            $config['file_name']        = ($filename) ? $filename : mt_rand(10000000, 99999999);
            $config["max_size"]         = app_setting($module, "file_size")->count() !== 0 ?( int) app_setting($module, "file_size")->first()->settings_val * 1024: 0;
            $ext = $file->getClientOriginalExtension();
            $newFilename = $config['file_name'] . '.' . $ext;
            $uploads = $file->move($config['upload_path'],$newFilename);
            if(in_array($ext, ["png","PNG","jpg","JPG","jpeg","JPEG"])) {
                $tpath = $path."thumbnail/";
                $base64Data = base64_encode(file_get_contents($path.$config['file_name']));
                $returnData['thumbNailFile'] = base64ToThumbNail($base64Data, $tpath, $ext, $config['file_name']);
            }
            $fileInfo = new \Symfony\Component\HttpFoundation\File\File($config['upload_path'].$newFilename);
            $recored = \Modules\Defualt\Entities\FileRecord::create([
                'module_key' => $module,
                'file_path' => $fileInfo->getPathname(),
                'file_size' => $fileInfo->getSize(),
                'file_extension' => $fileInfo->getExtension(),
                'file_name_en' => $fileInfo->getFilename(),
                'level_keys' => 'level_1',
                'user_id_writer' => auth_api()->id,
            ]);
            return $recored->file_records_id;
        }
    }
}
/**
 *
 * Build Multiple Files
 *
 */
if (!function_exists('build_multiple_files')) {
    function build_multiple_files($path, $file, $module, $types = false) {
        $CI = &get_instance();
        if (!is_dir($path)) {mkdir($path, 0777, true);}
        fopen($path . "index.html", "w");
        $config["upload_path"]      = $path;
        $config["allowed_types"]    = ($types) ? $types : app_setting($module, "file_types");
        $config["overwrite"]        = true;
        $config["remove_spaces"]    = true;
        $config["max_size"]         = (int) app_setting($module, "file_size") * 1024;
        $fileArray                  = [];
        $failedfiles                = [];
        $files                      = $_FILES;
        $cpt                        = count($_FILES[$file]['name']);
        for ($i = 0; $i < $cpt; $i++) {
            $config['file_name']        = mt_rand(10000000, 99999999);
            $_FILES[$file]['name']      = $files[$file]['name'][$i];
            $_FILES[$file]['type']      = $files[$file]['type'][$i];
            $_FILES[$file]['tmp_name']  = $files[$file]['tmp_name'][$i];
            $_FILES[$file]['error']     = $files[$file]['error'][$i];
            $_FILES[$file]['size']      = $files[$file]['size'][$i];
            $CI->load->library('upload', $config);
            $CI->upload->initialize($config);
            if (!$CI->upload->do_upload($file)) {
                if (!empty($failedfiles)) {
                    foreach ($failedfiles as $failed) {
                        unlink($failed);
                    }
                }
                api_return(array(
                    'status'        => false,
                    'status_code'   => 200,
                    'error'         => $CI->upload->display_errors(). " " .$files[$file]['name'][$i],
                ), 200);
            } else {
                $data                       = $CI->upload->data();
                $data['custom_filesize']    = FileSizeConvert($files[$file]['size'][$i]);
                $data['additional_param']   = (!empty($files[$file]['additional_param'][$i])) ? $files[$file]['additional_param'][$i] : [];
                if(!empty($files[$file]['additional'][$i])) {
                    $data = array_merge($data, $files[$file]['additional'][$i]);
                }
                $ext = pathinfo($data['file_name'], PATHINFO_EXTENSION);
                if(in_array($ext, ["png","PNG","jpg","JPG","jpeg","JPEG"])) {
                    $tpath      = $path."thumbnail/";
                    $base64Data = base64_encode(file_get_contents($path.$data['file_name']));
                    $data['thumbNailFile'] = base64ToThumbNail($base64Data, $tpath, $ext, $config['file_name']);
                }
                $fileArray[]                = $data;
                $failedfiles[]              = $path . $data['file_name'];
            }
        }
        return $fileArray;
    }
}
/**
 *
 * Resize The Image
 *
 */
if (!function_exists("resizeImage")) {
    function resizeImage($file = FALSE, $w = 200, $h = 200) {
        $CI = &get_instance();
        $CI->load->library('image_lib');
        $config['image_library']    = 'gd2';
        $config['source_image']     = $file;
        $config['quality']          = 100;
        $config['create_thumb']     = FALSE;
        $config['maintain_ratio']   = TRUE;
        $config['width']            = $w;
        $config['height']           = $h;
        $CI->image_lib->clear();
        $CI->image_lib->initialize($config);
        $CI->image_lib->resize();
        $CI->image_lib->clear();
        return $file;
    }
}
/**
 *
 * Convert Base64 Data T File
 *
 */
if (!function_exists("base64ToThumbNail")) {
    function base64ToThumbNail($base64Data, $path, $extention, $filename = "", $thumbnailSize = 250) {
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
