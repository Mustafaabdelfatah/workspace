<?php
namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Models\Module;


class ModulesRepository
{

    public function getModules(array $args)
    {
        $page = $args['page'];
        $perPage = $args['perPage'];


        $query = Module::when(!auth()->user()?->is_admin,fn($q)=> $q->whereIn('module_key',auth()->user()?->modules ?? []))->orderBy('created_at', 'asc');


        $models = $query->paginate($perPage, ['*'], 'page', $page);
        return [
            'status'    => !$models->isEmpty(),
            'message'   => (($models->isEmpty())) ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => [
                'total' => $models->total(),
                'current_page' => $models->currentPage(),
                'last_page' => $models->lastPage(),
                'from' => $models->firstItem(),
                'to' => $models->lastItem(),
            ],
            'records' => $models,
        ];

    }



    public function editModule($args)
    {
        // Validation
        $validator = Validator::make($args, [
            'is_enabled' => 'required|boolean',
            'frontend_slug' => 'required|string|max:255|unique:modules,frontend_slug,' . $args['id'],
            'module_name' => 'required|array',
            'module_name.en' => 'required|string|max:255',
            'module_name.ar' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => implode(' ', $validator->errors()->all()),
            ];
        }

        try {
            DB::beginTransaction();

            $module = Module::find($args['id']);
            if (!$module) {
                return [
                    'status' => false,
                    'message' => __('lang_no_data_found'),
                ];
            }

            // Force is_enabled to true if module_key is 'Core'
            $isEnabled = ($module->module_key === 'Core') ? true : $args['is_enabled'];

            $module->update([
                'is_enabled' => $isEnabled,
                'frontend_slug' => $args['frontend_slug'],
                'module_name' => $args['module_name'],
                'editor_name' => auth()->check() ? auth()->user()->id : 0,
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
                'message' => __('lang_unexpected_error') . ' ' . $e->getMessage(),
            ];
        }
    }

    public function enableDisableModule($args)
    {

        $modulesFile = base_path('modules_statuses.json');
        $modules = json_decode(file_get_contents($modulesFile), true);



        try {
            DB::beginTransaction();

            $module = Module::find($args['id']);
            if (!$module) {
                return [
                    'status' => false,
                    'message' => __('lang_no_data_found'),
                ];
            }

            // Prevent changing status if module_key is 'Core'
            if ($module->module_key === 'Core') {
                return [
                    'status' => false,
                    'message' => __('lang_action_not_allowed_on_core_module'),
                ];
            }

            // Toggle is_enabled
            $module->is_enabled = !$module->is_enabled;
            $module->editor_id = auth()->check() ? auth()->user()->id : 0;
            $module->save();

            $modules[$module->module_key] = $module->is_enabled ? true : false;

            file_put_contents($modulesFile, json_encode($modules, JSON_PRETTY_PRINT));

            DB::commit();

            return [
                'status' => true,
                'message' => __('lang_data_saved_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => __('lang_unexpected_error') . ' ' . $e->getMessage(),
            ];
        }
    }


}
