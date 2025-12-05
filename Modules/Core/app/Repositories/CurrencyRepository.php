<?php

namespace Modules\Core\Repositories;

//use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DB;
use Hash;
use Modules\Core\Traits\ValidationMessageTraits;
use Modules\Core\Models\Currency;

class CurrencyRepository
{
    use ValidationMessageTraits;


    public function createOrUpdateCurrency($args){
        // Validation
        $validator = Validator::make($args, [
            'name' => 'required',
            'name.en' => 'required|string|min:3',
            'name.ar' => 'required|string|min:3',
            'symbol' => 'nullable|string',
            'short_form' => 'nullable',
            'short_form.en' => 'nullable|string|min:2|required_with:short_form.ar',
            'short_form.ar' => 'nullable|string|min:2|required_with:short_form.en',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }
        DB::beginTransaction();
        try {
        if (isset($args['id'])) {
            // Update existing currency
            $currency = Currency::find($args['id']);
            if (!$currency) {
                return [
                    'status' => false,
                    'message' => __('no_data_found'),
                ];
            }
            $currency->update([
                'name'=>$args['name'],
                'symbol'=>$args['symbol'],
                'short_form'=>$args['short_form'],
                ]);
        } else {
            // Create new currency
          Currency::create([
                'name'=>$args['name'],
                'symbol'=>$args['symbol'],
                'short_form'=>$args['short_form'],
            ]);
        }
            DB::commit();
        return [
            'status' => true,
            'message' => __('data_saved_successfully'),
        ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }

    public function getCurrencies($args)
    {
        $page = $args['page'];
        $perPage = $args['perPage'];
        $currencies = Currency::paginate($perPage, ['*'], 'page', $page);

        if ($currencies->isEmpty()) {
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
            'message' => __('data_found'),
            'paging' => [
                'total' => $currencies->total(),
                'current_page' => $currencies->currentPage(),
                'last_page' => $currencies->lastPage(),
                'from' => $currencies->firstItem(),
                'to' => $currencies->lastItem(),
            ],
            'records' => $currencies

        ];
    }

    public function searchCurrencies($args)
    {
        $searchKey = $args['searchKey'] ?? null;

        // Initialize query
        $query = Currency::query();
        // Apply search filter if searchTerm is provided
        if ($searchKey) {
            $query->where('name','like','%'.$searchKey.'%')
                ->orwhere('symbol','like','%'.$searchKey.'%')
                ->orwhere('short_form','like','%'.$searchKey.'%');
        }
        $currencies = $query->get();

        if ($currencies->isEmpty()) {
            return [
                'status' => false,
                'message' => __('no_data_found'),
             ];
        }

        return [
            'status' => true,
            'message' => __('data_found'),
            'records' => $currencies,
        ];
    }

    public function deleteCurrency($args){
        try {
            $currency = Currency::find($args['id']);
            if (!$currency) {
                return [
                    'status' => false,
                    'message' => __('no_data_found'),
                ];
            }

            $currency->delete();
            return [
                'status' => true,
                'message' => __('data_deleted_successfully'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => __('exception'). $e->getMessage(),
            ];
        }
    }
    }
