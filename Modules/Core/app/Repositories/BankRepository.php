<?php
namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Core\Traits\ValidationMessageTraits;
use Modules\Core\Models\Bank; 
use Illuminate\Support\Facades\Validator;


class BankRepository
{
    use ValidationMessageTraits;
    protected $hr_conn = 'human_resources';

    public function createOrUpdateBank($args)
    {
        $hr_conn = $this->hr_conn;
        // Validation rules
        $rules = [
            'bank_name' => ['required'],
            'bank_name.ar' => ['required','min:3'],
            'bank_name.en' => ['required','min:3'],
            'bank_short_code' => ['required'],
        ];

        // Validate the inputs
        $validator = Validator::make($args, $rules);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        DB::beginTransaction();
        try {
            Bank::updateOrCreate(
                 ['id' => $args['id'] ?? null],
                 ['bank_name' => $args['bank_name'],
                  'bank_short_code' => $args['bank_short_code']
                 ]
             );

            DB::commit();
            return [
                'status' => true,
                'message' => isset($args['id']) ? __('bank_updated_successfully') : __('bank_added_successfully'),
                ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }


    public function getBanks($args){

        $page = $args['page'];
        $perPage = $args['perPage'];
        $query = Bank::query();

        if (!empty($args['search_key'])) {
            $searchKey = strtolower($args['search_key']);
            $query->where(function($subQuery) use ($searchKey) {
                $subQuery->whereRaw('LOWER(bank_name) LIKE ?', ['%' . $searchKey . '%'])
                    ->orWhereRaw('LOWER(bank_short_code) LIKE ?', ['%' . $searchKey . '%']);
            });
        }

        $banks = $query->orderBy('id', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($banks->isEmpty()) {
            return [
                'status' => false,
                'message' => __('no_data_found'),
                'paging' => [
                    'total' => 0,
                    'current_page' => $page,
                    'last_page' => 0,
                    'from' => 0,
                    'to' => 0,
                ],
                'records' => [],
            ];
        }

        return [
            'status' => true,
            'message' =>  __('data_found'),
            'paging' => [
                'total' => $banks->total(),
                'current_page' => $banks->currentPage(),
                'last_page' => $banks->lastPage(),
                'from' => $banks->firstItem(),
                'to' => $banks->lastItem(),
            ],
            'records' => $banks,
        ];

    }

     public function deleteBank($args){
        $bank = Bank::find($args['bank_id']);

        if (!$bank) {
            return [
                'status' => false,
                'message' => __('no_data_found'),
            ];
        }
        DB::beginTransaction();
        try {
            $bank->delete();
            DB::commit();
            return [
                'status' => true,
                'message' => __('data_deleted_successfully'),
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }

    }
    
    public function searchBank($args){

        $query = Bank::query();

        if (!empty($args['search_key'])) {
            $searchKey = strtolower($args['search_key']);
            $query->where(function($subQuery) use ($searchKey) {
                $subQuery->whereRaw('LOWER(bank_name) LIKE ?', ['%' . $searchKey . '%'])
                    ->orWhereRaw('LOWER(bank_short_code) LIKE ?', ['%' . $searchKey . '%']);
            });
        }

        $banks = $query->orderBy('id', 'DESC')->get();

        if ($banks->isEmpty()) {
            return [
                'status' => false,
                'message' => __('no_data_found'),
                'records' => [],
            ];
        }

        return [
            'status' => true,
            'message' => __('data_found'),
            'records' => $banks,
        ];

    }
}
