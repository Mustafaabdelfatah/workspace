<?php

use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\User;
use Modules\Core\Notifications\SysNotification;
use Illuminate\Support\Facades\DB;

if (!function_exists('UserAllowed')) {
    function UserAllowed($user_data = [], $whereAllow = [])
    {
        if(empty($user_data)){
            $dataUserAllowed = array('status'=>false,'error'=>'user_data is empty');
            return $dataUserAllowed;
        }
        if(empty($whereAllow['module_key']) or empty($whereAllow['permission_key'])){
            $dataUserAllowed = array('status'=>false,'error'=> 'module_key and permission_key Are required');
            return $dataUserAllowed;
        }
        $CI = app(\App\SOLID\Traits\UserTraits::class);
        $wheredata['workspace_id']                              = (!empty($whereAllow['workspace_id']) && isset($whereAllow['workspace_id']) ? $whereAllow['workspace_id'] : null);
//        $wheredata['search']["user_type_id"]                           = (!empty($whereAllow['user_type_id']) && isset($whereAllow['user_type_id']) ? $whereAllow['user_type_id'] : false);
        $wheredata["module_key"]                             = $whereAllow['module_key'];
        $wheredata["permission_key"]                         = $whereAllow['permission_key'];
        $wheredata["private_key"]                            = (!empty($whereAllow['private_key']) && isset($whereAllow['private_key']) ? $whereAllow['private_key'] : null);
        $wheredata["private_value"]                          = (!empty($whereAllow['private_value']) && isset($whereAllow['private_value']) ? $whereAllow['private_value'] : null);
        $wheredata["group_key"]                              = (!empty($whereAllow['group_key']) && isset($whereAllow['group_key']) ? $whereAllow['group_key'] : null);
        $wheredata['is_admin']                               = $user_data['is_admin'];
        $wheredata['user_id']                                = $user_data['user_id'];
        $dataUserAllowed = $CI->check_user_mention($wheredata);
        return $dataUserAllowed;
    }
}


if(!function_exists('generateRandomToken')) {
    function generateRandomToken($length = 30) {
        $token          = "";
        $codeAlphabet   = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet   .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet   .= "0123456789";
        $max = strlen($codeAlphabet);
        for ($i=0; $i < $length; $i++) {
            $token      .= $codeAlphabet[random_int(0, $max-1)];
        }
        return $token;
    }
}

if (!function_exists('convert_base_to_imagenp')) {
    function convert_base_to_imagenp($base64data = "", $id = null, $name = null, $w = null, $h = null) {
        $CI = &get_instance();
        $url = "files/signature/" . $id . "/";
        if (!is_dir($url)) {
            mkdir($url, 0777, true);
        }
        $fullurl = $url . $name . '.png';
        $bdata = str_replace('[removed]', '', $base64data);
        file_put_contents($fullurl, base64_decode($bdata));
        resizeImage($fullurl, $w, $h);
        return $fullurl;
    }
}

if(!function_exists('auth_api')) {
    function auth_api()
    {
        return \Illuminate\Support\Facades\Auth::guard('api')->user();
    }
}

if(!function_exists('get_lang')) {
    function get_lang()
    {
        return request()->hasHeader('lang_key') ?  request()->header('lang_key') : 'en';
    }
}

if(!function_exists('chechAuth')) {
    function chechAuth()
    {
        if(!Auth::guard('api')->check()){
            return [
                'status' => false,
                'message' => 'unauthorized',
                'data' => null,
            ];
        }
    }
}

function notAuth()
{
    return [
        'status' => false,
        'message' => 'unauthorized',
        'data' => null,
    ];
}

if(!function_exists('buildError')) {
    function buildError($errors = []) {
        $finalErrors = "";
        foreach ($errors as $key => $value) {
            $finalErrors .= "<p>$value</p>";
        }
        return $finalErrors;
    }
}

if(!function_exists('edateconv')) {
    function edateconv($stringdate = null, $f = NULL) {
        if (!empty($stringdate) && $stringdate) {
            $dte = explode("GMT", $stringdate);
            $newdt = strtotime($dte[0]);
            $date = date("Y/m/d", $newdt);
            $datetime = date("Y/m/d H:i:s", $newdt);
            if (preg_match("/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
                if($date && $date !== '1970/01/01') {
                    return ($f == "T") ? $datetime : $date;
                }
            }
        }
        return '';
    }
}
if(!function_exists('adateconv')) {
    function adateconv($stringdate = null) {
        if (!empty($stringdate) && $stringdate) {
            $d = explode("/", $stringdate);
            $orgdate = (!empty($d[0]) && !empty($d[1]) && !empty($d[2])) ? $d[2] . '/' . $d[1] . '/' . $d[0] : '';
            if (preg_match("/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/", $stringdate)) {
                return $stringdate;
            } else if (preg_match("/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/", $orgdate)) {
                return $orgdate;
            }
        }
        return '';
    }
}

function arabic_world($text){
    $text = preg_replace("/(أ|إ|ا|آ)/","(أ|إ|ا|آ)",$text);
    $text = preg_replace("%(ه|ة)%","(ه|ة)",$text);
    $text = preg_replace("%(ي|ى)%","(ي|ى)",$text);
    $text = preg_replace("%(ؤ|و)%","(ؤ|و)",$text);
    $text = preg_replace('/\s+/i'," ",$text);
    return $text;
}
function arabic_world2($key_column = '', $text = ''){
    $data = false;
    $text_array = explode(' ', $text);
    if(!empty($text_array) and is_array($text_array)){
        $i = 0;
        foreach($text_array as $key => $val){
            if(!empty($val) and isset($val)){
                $val = preg_replace("/(أ|إ|ا|آ)/","(أ|إ|ا|آ)",$val);
                $val = preg_replace("%(ه|ة)%","(ه|ة)",$val);
                $val = preg_replace("%(ي|ى)%","(ي|ى)",$val);
                $val = preg_replace("%(ؤ|و)%","(ؤ|و)",$val);
                $val = preg_replace('/\s+/i'," ",$val);
                if($i == 0){
                    $data = " (".$key_column." rlike '".$val."')";
                }
                else{
                    $data .= " OR (".$key_column." rlike '".$val."')";
                }
                $i++;
            }
        }
    }
    return $data;
}

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Models\Notification as NotificationModel;

if(!function_exists('applang')) {
    function applang($key, $lang_key = "ar") {
        $filePath = public_path("lang/$lang_key.json");
        if (File::exists($filePath)) {
            $jsonContent = File::get($filePath);
            $data = json_decode($jsonContent, true);
            return $data[$key] ?? $key;
        }
        return $key;
    }
}


if (!function_exists('sendSystemNotification')) {

    function sendSystemNotification($data = [],$userIds = null) {
        
        if(!$userIds){
            $data['is_global'] = true;
        }

        $notification = NotificationModel::create($data);
        if($userIds){

            $userIds = User::whereIn('id', $userIds)->pluck('id')->toArray();

            $userData = array_map(function ($userId) use ($notification) {
                return ['user_id' => $userId,'delivered_at' => now()->toDateTimeString(),'notification_id' => $notification->id];
            }, $userIds);
            DB::table('notification_user')->insert($userData);
            Notification::send(User::whereIn('id', $userIds)->get(), new SysNotification($notification));
        }
        else{
            Notification::send(User::all(), new SysNotification($notification));
        }
        return true;
    }
    
}
