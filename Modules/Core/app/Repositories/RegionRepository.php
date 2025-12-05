<?php

namespace Modules\Core\Repositories;
use DB;
use Modules\Core\Models\Region;

class RegionRepository
{
    
    public function createOrUpdateRegion($args){
        DB::beginTransaction();
        try {
           $region =  Region::updateOrCreate(
                ['id' => $args['id'] ?? null],
                [
            'name' => $args['name'],
            'country'=> $args['country']
        ]);

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


    public function getRegions($args)
    {
        $page = $args['page'] ?? null;
        $perPage = $args['perPage'] ?? null;
        $query = Region::query();

        $searchKey = $args['searchKey'] ?? null;

        if ($searchKey) {
            $query->where('name','like','%'.$searchKey.'%')
            ->orWhere('country','like','%'.$searchKey.'%');
        }

        if(!empty($args['country'])){
            $query->where('country', $args['country']);
        }
        
        if($page && $perPage){
        $regions = $query->paginate($perPage, ['*'], 'page', $page);
        }
        else{
            $regions = $query->get();
        }


           return [
            'status'    => !$regions->isEmpty(),
            'message'   => (($regions->isEmpty())) ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => $page ? [
                'total' => $regions->total(),
                'current_page' => $regions->currentPage(),
                'last_page' => $regions->lastPage(),
                'from' => $regions->firstItem(),
                'to' => $regions->lastItem(),
            ] : [],
            'records' => $regions,
        ];

    }



      public function listRegions($args)
    {
        $page = $args['page'] ?? null;
        $perPage = $args['perPage'] ?? null;
        $query = Region::query();

        $searchKey = $args['searchKey'] ?? null;

        if ($searchKey) {
            $query->where('name','like','%'.$searchKey.'%')
            ->orWhere('country','like','%'.$searchKey.'%');
        }

        if(!empty($args['country'])){
            $query->where('country', $args['country']);
        }
        
        if($page && $perPage){
        $regions = $query->paginate($perPage, ['*'], 'page', $page);
        }
        else{
            $regions = $query->get();
        }


           return [
            'status'    => !$regions->isEmpty(),
            'message'   => (($regions->isEmpty())) ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => $page ? [
                'total' => $regions->total(),
                'current_page' => $regions->currentPage(),
                'last_page' => $regions->lastPage(),
                'from' => $regions->firstItem(),
                'to' => $regions->lastItem(),
            ] : [],
            'records' => $regions,
        ];

    }


    public function deleteRegion($args){
        try {
            $region = Region::find($args['id']);
            if (!$region) {
                return [
                    'status' => false,
                    'message' => __('lang_no_data_found'),
                ];
            }

            $region->delete();
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
