<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as BaseExcel;
use Modules\Core\Exports\TranslationDataExport;
use Modules\Core\Exports\TranslationTemplateExport;
use Modules\Core\Imports\ImportExcelData;
use Modules\Core\Models\AttachmentsUploadHistory;
use Modules\Core\Models\Translation;
use Modules\Core\Traits\ValidationMessageTraits;
use Modules\Law\Models\Religion;
use DB;
use Hash;

class TranslationRepository
{
    use ValidationMessageTraits;

    public static function addTranslations($args)
    {
        // Validate inputs
        $validator = Validator::make($args, [
            'module' => 'required|string',
            'translations' => 'required|array',
            'translations.*.key' => 'required|unique:translations,key|regex:/^lang_/',
            'translations.*.phrases' => 'required|array',
            'translations.*.phrases.ar' => 'required|string|min:2',
            'translations.*.phrases.en' => 'required|string|min:2',
        ]);

        // Check for duplicate keys in the translations array
        $keys = array_column($args['translations'], 'key');
        $duplicates = array_unique(array_diff_assoc($keys, array_unique($keys)));

        if (!empty($duplicates)) {
            return [
                'status' => false,
                'message' => __('lang_duplicate_found') . ' ' . implode(', ', $duplicates),
            ];
        }

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => implode(' ', $validator->errors()->all()),
            ];
        }

        // Prepare data for bulk insert
        $data = array_map(function ($translationData) use ($args) {
            return [
                'module' => $args['module'],
                'key' => $translationData['key'],
                'phrase' => json_encode($translationData['phrases'], JSON_UNESCAPED_UNICODE),
                'writer_id' => Auth::id() ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $args['translations']);

        // Start a transaction
        DB::beginTransaction();

        try {
            // Insert the translations
            Translation::insert($data);

            // Commit the transaction
            DB::commit();

            return [
                'status' => true,
                'message' => __('lang_data_saved_successfully'),
            ];
        } catch (\Exception $e) {
            // Rollback the transaction if something failed
            DB::rollBack();

            return [
                'status' => false,
                'message' => __('lang_data_save_failed') . ': ' . $e->getMessage(),
            ];
        }
    }

    public static function updateTranslation($args)
    {
        // Validate inputs
        $validator = Validator::make($args, [
            'id' => 'required|exists:translations,id',
            'module' => 'required|string',
            'key' => [
                'required',
                'regex:/^lang_/',
                Rule::unique('translations', 'key')->ignore($args['id']),
            ],
            'phrases' => 'required|array',
            'phrases.ar' => 'required|string|min:2',
            'phrases.en' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => implode(' ', $validator->errors()->all()),
            ];
        }

        $translation = Translation::findOrFail($args['id']);

        if (!$translation) {
            return [
                'status' => false,
                'message' => __('lang_no_data_found'),
            ];
        }

        DB::beginTransaction();

        try {
            $translation->update(
                ['module' => $args['module'],
                    'key' => $args['key'],
                    'phrase' => $args['phrases']]
            );

            DB::commit();

            return [
                'status' => true,
                'message' => __('lang_data_updated_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => __('lang_data_update_failed') . ': ' . $e->getMessage(),
            ];
        }
    }

    public function getTranslations(array $args)
    {
        $page = $args['page'];
        $perPage = $args['perPage'];
        $query = Translation::query();

        if (!empty($args['search_key'])) {
            $searchKey = strtolower($args['search_key']);
            $query->where(function ($subQuery) use ($searchKey) {
                // Search for the key, module, or phrase
                $subQuery
                    ->where('key', 'LIKE', "%{$searchKey}%")
                    ->orWhere('module', 'LIKE', "%{$searchKey}%")
                    ->orWhere('phrase', 'LIKE', "%{$searchKey}%");
            });
        }

        // Apply pagination
        $translations = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'status' => !$translations->isEmpty(),
            'message' => (($translations->isEmpty())) ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => [
                'total' => $translations->total(),
                'current_page' => $translations->currentPage(),
                'last_page' => $translations->lastPage(),
                'from' => $translations->firstItem(),
                'to' => $translations->lastItem(),
            ],
            'records' => $translations,
        ];
    }

    public function deleteTranslation($args)
    {
        DB::beginTransaction();
        try {
            // Delete all translations with the provided IDs
            Translation::whereIn('id', $args['ids'])->delete();

            DB::commit();
            return [
                'status' => true,
                'message' => __('lang_data_deleted_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => __('lang_data_save_failed') . ': ' . $e->getMessage(),
            ];
        }
    }

    public function downloadTranslationTemplate()
    {
        // Generate the Excel content
        $export = new TranslationTemplateExport();
        $excelContent = Excel::raw($export, BaseExcel::XLSX);

        // Return the Excel data as a base64-encoded string
        return [
            'status' => true,
            'message' => __('lang_downloaded_successfully'),
            'data' => [
                'export_type' => 'excel',
                'content' => base64_encode($excelContent),
                'file_name' => 'TranslationTemplate.xlsx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        ];
    }

    public function importTranslationData($args)
    {
        $validator = Validator::make($args, [
            'translation_template' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => implode(' ', $validator->errors()->all()),
            ];
        }

        $validationRules = [
            'module' => 'required|string',
            'key' => 'required|regex:/^lang_/',
            'phrase_en' => 'required|string|min:2',
            'phrase_ar' => 'required|string|min:2',
        ];

        $fieldMappings = [
            'module' => 'module',
            'key' => 'key',
            'phrase_en' => 'phrase_en',
            'phrase_ar' => 'phrase_ar',
        ];

        try {
            $import = new ImportExcelData($validationRules, $fieldMappings);
            Excel::import($import, $args['translation_template']);
            $excelData = $import->getImportedData();
            $validData = [];
            $validationErrors = [];

            foreach ($excelData as $data) {
                $excelValidator = Validator::make($data, $validationRules);
                if ($excelValidator->fails()) {
                    $validationErrors[] = [
                        'data' => $data,
                        'errors' => $excelValidator->errors()->all(),
                    ];
                } else {
                    $validData[] = [
                        'module' => $data['module'],
                        'key' => $data['key'],
                        'phrase' => json_encode([
                            'en' => $data['phrase_en'],
                            'ar' => $data['phrase_ar'],
                        ], JSON_UNESCAPED_UNICODE),
                        'writer_id' => auth()->id(),
                        'editor_id' => auth()->id(),
                    ];
                }
            }

            if (!empty($validationErrors)) {
                $errorMessages = implode(' ', array_map(function ($error) {
                    return "Key: {$error['data']['key']} - " . implode(', ', $error['errors']);
                }, $validationErrors));

                return [
                    'status' => false,
                    'message' => $errorMessages,
                ];
            }

            DB::beginTransaction();

            $file = $args['translation_template'];
            $filePath = Storage::disk('local')->putFile('uploads/translation_histories/', $file);
            $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
            $fileSize = $file->getSize();

            AttachmentsUploadHistory::create([
                'upload_type' => 'translation_history',
                'attachments_url' => $filePath,
                'attachments_type' => $fileType,
                'attachments_size' => $fileSize,
            ]);
            // Step 6: Upsert the valid records in the database
            // Translation::bulkSaveTranslations($validData);
            Translation::upsert($validData, ['key'], ['module', 'phrase', 'writer_id', 'editor_id']);

            DB::commit();

            return [
                'status' => true,
                'message' => __('lang_data_updated_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => __('lang_data_save_failed') . ': ' . $e->getMessage(),
            ];
        }
    }

    public function getTranslationUploadsHistory($args)
    {
        $page = $args['page'];
        $perPage = $args['perPage'];

        try {
            $ImportsHistory = AttachmentsUploadHistory::where('upload_type', 'translation_history')
                ->orderBy('id', 'DESC')
                ->paginate($perPage, ['*'], 'page', $page);
            return [
                'status' => !$ImportsHistory->isEmpty(),
                'message' => (($ImportsHistory->isEmpty())) ? __('lang_no_data_found') : __('lang_data_found'),
                'records' => $ImportsHistory,
                'paging' => [
                    'total' => $ImportsHistory->total(),
                    'current_page' => $ImportsHistory->currentPage(),
                    'last_page' => $ImportsHistory->lastPage(),
                    'from' => $ImportsHistory->firstItem(),
                    'to' => $ImportsHistory->lastItem(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => __('lang_data_save_failed') . ': ' . $e->getMessage(),
            ];
        }
    }

    public function getTranslationsJson($args = [])
    {
        try {
            Translation::exportTranslations();
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => __('lang_data_save_failed') . ': ' . $e->getMessage(),
            ];
        }

        // Determine the language from $args or fall back to the request header
        $language = $args['lang_key'] ?? request()->header('lang_key', 'en');
        $fileName = "lang/{$language}.json";

        // Check if the translation file exists
        if (!Storage::disk('local')->exists($fileName)) {
            return [
                'status' => false,
                'message' => 'Translation File Not Found',
            ];
        }

        $fileContents = Storage::disk('local')->get($fileName);
        $base64File = base64_encode($fileContents);

        return [
            'status' => true,
            'message' => __('lang_downloaded_successfully'),
            'data' => [
                'file_type' => 'json',
                'content' => $base64File,
                'file_name' => "{$language}.json",
                'mime_type' => 'application/json'
            ]
        ];
    }

    public function exportTranslationData($args)
    {
        $export = new TranslationDataExport($args['search_key'] ?? null);
        $excelContent = Excel::raw($export, BaseExcel::XLSX);

        // Return the Excel data as a base64-encoded string
        return [
            'status' => true,
            'message' => __('lang_downloaded_successfully'),
            'data' => [
                'export_type' => 'excel',
                'content' => base64_encode($excelContent),
                'file_name' => 'Translations.xlsx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        ];
    }
}
