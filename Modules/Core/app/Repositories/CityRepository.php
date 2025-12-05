<?php

namespace Modules\Core\Repositories;
use Illuminate\Support\Facades\Validator;
use DB;
use Hash;
use Modules\Core\Traits\ValidationMessageTraits;
use Modules\Core\Models\City;
class CityRepository
{
    use ValidationMessageTraits;


    public function createOrUpdateCity($args){
        // Validation
        $validator = Validator::make($args, [
            'name' => 'required',
            'name.en' => 'required|string|min:3',
            'name.ar' => 'required|string|min:3',
            'nationality'=> 'required|string'
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }
        DB::beginTransaction();
        try {
            City::updateOrCreate(
                ['id' => $args['id'] ?? null],
                ['name' => $args['name'],'nationality' => $args['nationality']]
            );

        DB::commit();

        return [
            'status' => true,
            'message' => __('lang_data_saved_successfully'),
        ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }

    public function getCities($args)
    {
        $page = $args['page'] ?? null;
        $perPage = $args['perPage'] ?? null;
        $query = City::query();

        $searchKey = $args['searchKey'] ?? null;

        if ($searchKey) {
            $query->where('name','like','%'.$searchKey.'%');
        }
        
        if($page && $perPage){
        $cities = $query->paginate($perPage, ['*'], 'page', $page);
        }
        else{
            $cities = $query->get();
        }

        if ($cities->isEmpty()) {
            return [
                'status' => false,
                'message' => __('lang_no_data_found'),
                'paging' => [
                    'total' => 0,
                    'current_page' => $page ?? 0,
                    'last_page' => 0,
                    'from' => 0,
                    'to' => 0,
                ],
                'records' => [],
            ];
        }

        return [
            'status' => true,
            'message' => __('lang_data_found'),
            'paging' => $page ?  [
                'total' => $cities->total(),
                'current_page' => $cities->currentPage(),
                'last_page' => $cities->lastPage(),
                'from' => $cities->firstItem(),
                'to' => $cities->lastItem(),
            ] : null,
            'records' => $cities

        ];
    }


    public function deleteCity($args){
        try {
            $city = City::find($args['id']);
            if (!$city) {
                return [
                    'status' => false,
                    'message' => __('lang_no_data_found'),
                ];
            }

            $city->delete();
            return [
                'status' => true,
                'message' => __('lang_data_deleted_successfully'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => __('exception'). $e->getMessage(),
            ];
        }
    }
    }
