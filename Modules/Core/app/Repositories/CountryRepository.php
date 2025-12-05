<?php
namespace Modules\Core\Repositories;
use Modules\Core\Models\Country;
use DB;
class CountryRepository
{


    /**
     * Get countries list without pagination
     * 
     * @param mixed $args
     * 
     * @return mixed
     */
    public function getCountriesList(mixed $args): mixed
    {
        $countries = Country::likeName($args)
            ->get();

        return [
            'status' => true,
            'message' => __('core::messages.countries_fetch_success'),
            'data' => $countries,
        ];
    }

    /**
     * Create or update branch
     * 
     * @param mixed $args
     * 
     * @return mixed
     */
    public function createOrUpdateBranch(mixed $args): mixed
    {
        try{
            DB::beginTransaction();
            $branch = Branch::updateOrCreate(['id' => $args['id']??NULL],['name'=> $args['name'] ]);
            DB::commit();

            return [
                'status' => true,
                'message' => __('accounting::messages.branch_saved_success'),
                'data' => [$branch],
            ];
        }catch(\Exception $e){
            DB::rollBack();
            return [
                'status' => false,
                'message' => __('core::messages.went_wrong'),
            ];
        }
    }

    /**
     * Create or update branch
     * 
     * @param mixed $args
     * 
     * @return mixed
     */
    public function deleteBranch(mixed $args): mixed
    {
        try{
            DB::beginTransaction();
            Branch::find($args['id'])?->delete();
            DB::commit();

            return [
                'status' => true,
                'message' => __('accounting::messages.branch_delete_success'),
            ];
        }catch(\Exception $e){
            DB::rollBack();
            return [
                'status' => false,
                'message' => __('core::messages.went_wrong'),
            ];
        }
    }
}