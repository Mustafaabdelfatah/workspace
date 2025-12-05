<?php

namespace App\SOLID\Traits;

use Illuminate\Support\Facades\DB;
use Modules\Defualt\Models\FileLeveGroup;
use Modules\Defualt\Entities\FileRecord;
use Modules\Users\Entities\PermissionMentions;
use Modules\Users\Entities\User;

trait UserTraits
{

    function UserAllowed(array $whereAllow)
    {
        if(empty($whereAllow['module_key']) or empty($whereAllow['permission_key'])){
            return array('status'=>FALSE,'message'=> 'module_key and permission_key Are required');
        }
        $wheredata['workspace_id']                              = (!empty($whereAllow['workspace_id']) && isset($whereAllow['workspace_id']) ? $whereAllow['workspace_id'] : null);
        $wheredata["user_type_id"]                           = (!empty($whereAllow['user_type_id']) && isset($whereAllow['user_type_id']) ? $whereAllow['user_type_id'] : false);
        $wheredata["module_key"]                             = $whereAllow['module_key'];
        $wheredata["permission_key"]                         = $whereAllow['permission_key'];
        $wheredata["private_key"]                            = (!empty($whereAllow['private_key']) && isset($whereAllow['private_key']) ? $whereAllow['private_key'] : null);
        $wheredata["private_value"]                          = (!empty($whereAllow['private_value']) && isset($whereAllow['private_value']) ? $whereAllow['private_value'] : null);
        $wheredata["group_key"]                              = (!empty($whereAllow['group_key']) && isset($whereAllow['group_key']) ? $whereAllow['group_key'] : null);
        $wheredata['user_id']                                = $whereAllow['id'];
        return $this->check_user_mention($wheredata);
    }

    function fileLeveL($args)
    {
        if(FileRecord::where('file_path',$args)->where('level_keys','level_2')->count() != 0){
            return true;
        }
        elseif (FileRecord::where('file_path',$args)->where('level_keys','level_1')->count() != 0){
            return false;
        }
        else{
            if(auth_api()->is_admin == 1){
                return true;
            }
            else{
                $data = FileRecord::where('file_path',$args)->select('group_key')->get();
                $groupKeys = DB::table('file_users_group')
                    ->where('file_users_group.user_id', auth_api()->id)
                    ->leftJoin('groups', 'groups.group_key', '=', 'file_users_group.group_key')
                    ->select('file_users_group.group_key','groups.module_key')
                    ->whereIn('file_users_group.group_key',$data)
                    ->get();
                $levels = FileLeveGroup::whereIn('group_key',$data)->get();
                $levels->count() != 0 ? true: false;
            }
        }
    }

    public function check_user_mention(array $args){
        $dataUserAllowed = false;
        if(auth_api()->is_admin == 1){
            return array('status'=>TRUE,'message'=> 'By Admin');
        } else {
            $permissions_mentions = PermissionMentions::where('user_id',auth_api()->id)
                ->when($args['workspace_id'], function ($q) use ($args){
                    $q->where('workspace_id',$args['workspace_id']);
                })
                ->when($args['user_type_id'], function ($q) use ($args){
                    $q->where('user_type_id',$args['user_type_id']);
                })
                ->when($args['module_key'], function ($q) use ($args){
                    $q->where('module_key',$args['module_key']);
                })
                ->when($args['permission_key'], function ($q) use ($args){
                    $q->where('permission_key',$args['permission_key']);
                })
                ->when($args['private_key'], function ($q) use ($args){
                    $q->where('private_key',$args['private_key']);
                })
                ->when($args['private_value'], function ($q) use ($args){
                    $q->where('private_value',$args['private_value']);
                })
                ->when($args['group_key'], function ($q) use ($args){
                    $q->where('group_key',$args['group_key']);
                })
                ->count();
            if($permissions_mentions != 0 ){
                return array('status'=>TRUE,'message'=> 'Access By Permission');
            }
        }
        return array('status'=>FALSE,'message'=> 'Unauthorized');
    }

}

