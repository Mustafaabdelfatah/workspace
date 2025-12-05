<?php

namespace App\SOLID;

use App\Models\User;
use App\Models\UserOthers;
use App\Models\UserSingup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterRepository
{
    public function inexRegister(array $args)
    {
        DB::beginTransaction();

        try {

            $createdUser = User::create([
                'user_username' => $args['user_username'],
                'user_email' => $args['user_email'],
                'user_phone' => $args['user_phone'],
                'user_password' => bcrypt($args['user_password']),
                'user_type_id' => $args['user_type_id'],
            ]);

            $args['user_regsitration_id'] = $createdUser->id;
            $args['user_writer_id'] = $createdUser->id;
            $args['user_title_id'] = $createdUser->id;
            $args['user_registration_link'] = generateRandomToken();
            $singupUser = UserSingup::create($args);
            $others = UserOthers::create($args);
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
        }

        return true;
    }

    public function login($data)
    {

        if (Auth::attempt(['user_email' => $data['user_email'], 'user_password' => $data['user_password']])) {
            return collect([
                "user" => auth()->user(),
                "token" => auth()->user()->createToken('LaravelAuthApp')->accessToken,
            ]);
        }
    }

    public function indexRegisterValdation($args)
    {
        return Validator::make($args, [
            'module_key' => [
                'required',
                function ($attribute, $value, $fail) {
                    if(!empty($args['module_key']) && $args['module_key'] != "projects") {
                        dd('sdafdsfa');
                        exit();
                        $whereAllow['workspace_id']        = $args['workspace_id'];
                        $whereAllow['module_key']       = $args['module_key'];
                        $whereAllow['permission_key']   = $args['permission_key'];
                        $whereAllow['private_key']      = $args['private_key'];
                        $whereAllow['private_value']    = $args['private_value'];
                        $whereAllow['group_key']        = $args['group_key'];
                        $allow                          = UserAllowed($this->data['user_data'], $whereAllow);
                        return dd($allow);
                    }
                    return true;
                },
            ],
            'user_username' => [
                'required',
                'string',
            ],
            'user_email' => ['required', 'email', 'unique:users'],
            'user_phone' => ['required'],
            'user_password' => ['required'],
            'permission_user_type' => [
                'required',
                'numeric',
                'in:1,2,3'
            ],
            'user_type_id' => [
                'required',
                'numeric',
            ],
            'workspace_id' => [
                'required',
                'numeric',
            ],
            'permission_key' => [
                'required',
            ],
            'private_key' => [
                'required',
            ],
            'group_key' => [
                'required',
            ],
            'user_registration_type' => [
                'required',
                'numeric',
            ],
            'user_registration_firstname_en' => [
                'required',
            ],
            'user_registration_lastname_en' => [
                'required',
            ],
            'user_registration_firstname_ar' => [
                'required',
            ],
            'user_registration_lastname_ar' => [
                'required',
            ],
            'user_mobile_number' => [
                'required',
                'unique:users,user_phone',
//                    'unique:users_signup,user_mobile_number',
            ],
            'user_email_address' => [
                'required',
                'email',
                'unique:users,user_email',
//                    'unique:users_signup,user_email_address',
            ],
            'user_registration_name' => [
                'required',
            ],
            'personal_id_number' => [
                'required',
                'digits:10',
                'unique:users_signup,personal_id_number',
                'unique:users_others,personal_id_number',
//                    'unique:human_resources_employee_details,personal_id_number',
            ],
            'co_module_key' => [
                'required_if:user_registration_type,5'
            ],
            'co_private_key' => [
                'required_if:user_registration_type,5'
            ],
            'co_private_value' => [
                'required_if:user_registration_type,5',
                'numeric',
            ],
            'projects_work_area_id' => [
                'required_if:user_registration_type,5'
            ],
            'project_work_area_group_key' => [
                'required_if:user_registration_type,5'
            ],
        ]);
    }
}
